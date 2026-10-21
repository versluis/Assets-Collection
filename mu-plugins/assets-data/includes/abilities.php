<?php
/************************************/
/*
/* ABILITIES (MCP TOOLS)
/* Read and write catalog meta through the WordPress Abilities API, so MCP
/* connectors (AtlasMCP) can offer them as tools to non-admin users. AtlasMCP's
/* generic REST tool requires manage_options; these only need edit_post.
/*
/************************************/

function assets_data_register_ability_category()
{
  wp_register_ability_category('assets-data', array(
    'label' => 'Assets Data',
    'description' => 'Catalog metadata on Asset posts.',
  ));
}
add_action('wp_abilities_api_categories_init', 'assets_data_register_ability_category');

// JSON schema for one catalog field; null clears the field on update
function assets_data_field_schema($field)
{
  switch ($field['type']) {
    case 'integer':
      return array('type' => array('integer', 'null'), 'minimum' => 0, 'description' => $field['description']);
    case 'ids':
      return array('type' => array('array', 'null'), 'items' => array('type' => 'integer'), 'description' => $field['description']);
    default:
      return array('type' => array('string', 'null'), 'description' => $field['description']);
  }
}

// the asset post for an ability call, or an error
function assets_data_ability_post($input)
{
  $post = get_post(absint($input['id'] ?? 0));
  if (!$post || ASSETS_DATA_POST_TYPE !== $post->post_type) {
    return new WP_Error('assets_data_not_found', 'No Asset post with that ID.');
  }
  return $post;
}

// anyone who can edit the post may use these abilities (so the Editor role is enough)
function assets_data_ability_permission($input)
{
  $post = assets_data_ability_post($input);
  return !is_wp_error($post) && current_user_can('edit_post', $post->ID);
}

// catalog fields of a post, as the REST API would return them
function assets_data_catalog_values($post_id)
{
  $values = array();
  foreach (assets_data_catalog_fields() as $key => $field) {
    $value = get_post_meta($post_id, $key, true);
    if ('ids' === $field['type']) {
      $value = is_array($value) ? array_map('intval', $value) : array();
    } elseif ('integer' === $field['type']) {
      $value = (int) $value;
    } else {
      $value = (string) $value;
    }
    $values[$key] = $value;
  }
  return $values;
}

function assets_data_ability_get($input)
{
  $post = assets_data_ability_post($input);
  if (is_wp_error($post)) {
    return $post;
  }
  $legacy = get_post_meta($post->ID, 'guru_fields', true);

  return array(
    'id' => $post->ID,
    'title' => get_the_title($post),
    'link' => get_permalink($post),
    'fields' => assets_data_catalog_values($post->ID),
    'legacy_marketplace_url' => is_array($legacy) ? (string) ($legacy['steamURL'] ?? '') : '',
  );
}

function assets_data_ability_update($input)
{
  $post = assets_data_ability_post($input);
  if (is_wp_error($post)) {
    return $post;
  }

  $fields = assets_data_catalog_fields();
  $updated = array();
  foreach ((array) ($input['fields'] ?? array()) as $key => $value) {
    if (!isset($fields[$key])) {
      continue; // schema already rejects unknown keys; belt and braces
    }
    if (null === $value || '' === $value || array() === $value) {
      delete_post_meta($post->ID, $key);
    } else {
      update_post_meta($post->ID, $key, $value); // registered sanitize_callback does the cleaning
    }
    $updated[] = $key;
  }

  return array(
    'id' => $post->ID,
    'updated' => $updated,
    'fields' => assets_data_catalog_values($post->ID),
  );
}

function assets_data_register_abilities()
{
  $field_props = array();
  foreach (assets_data_catalog_fields() as $key => $field) {
    $field_props[$key] = assets_data_field_schema($field);
  }
  $id_prop = array('type' => 'integer', 'minimum' => 1, 'description' => 'ID of the Asset post.');

  wp_register_ability('assets-data/get-catalog-meta', array(
    'label' => 'Get Asset Catalog Data',
    'description' => 'Read the catalog fields (product_url, item_count, manifest_url, engine_version, pack_size_bytes, catalog_status, contact_sheet_ids, folder_summary) and the legacy marketplace URL of one Asset post.',
    'category' => 'assets-data',
    'input_schema' => array(
      'type' => 'object',
      'required' => array('id'),
      'properties' => array('id' => $id_prop),
      'additionalProperties' => false,
    ),
    'execute_callback' => 'assets_data_ability_get',
    'permission_callback' => 'assets_data_ability_permission',
    'meta' => array(
      'show_in_rest' => true,
      'annotations' => array('readonly' => true, 'destructive' => false, 'idempotent' => true),
      'mcp' => array('public' => true),
    ),
  ));

  wp_register_ability('assets-data/update-catalog-meta', array(
    'label' => 'Update Asset Catalog Data',
    'description' => 'Write one or more catalog fields on one Asset post. Only the fields passed are changed; null or an empty value removes a field. Never touches the post body.',
    'category' => 'assets-data',
    'input_schema' => array(
      'type' => 'object',
      'required' => array('id', 'fields'),
      'properties' => array(
        'id' => $id_prop,
        'fields' => array(
          'type' => 'object',
          'properties' => $field_props,
          'additionalProperties' => false,
          'minProperties' => 1,
        ),
      ),
      'additionalProperties' => false,
    ),
    'execute_callback' => 'assets_data_ability_update',
    'permission_callback' => 'assets_data_ability_permission',
    'meta' => array(
      'show_in_rest' => true,
      'annotations' => array('readonly' => false, 'destructive' => false, 'idempotent' => true),
      'mcp' => array('public' => true),
    ),
  ));
}
add_action('wp_abilities_api_init', 'assets_data_register_abilities');

// offer our abilities as tools on the AtlasMCP server
add_filter('awfah_mcp_server_ability_ids', function ($ids) {
  $ids[] = 'assets-data/get-catalog-meta';
  $ids[] = 'assets-data/update-catalog-meta';
  return $ids;
});
