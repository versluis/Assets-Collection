<?php
/************************************/
/*
/* CATALOG META FIELDS
/* Structured, REST-writable metadata for the Fab Library Catalog pipeline.
/* Post bodies stay as Jay's notes - everything machine-written lives here.
/*
/************************************/

// single source of truth for the catalog fields
// type: url | string | text | integer | ids
function assets_data_catalog_fields()
{
  return array(
    'fab_url'           => array('type' => 'url',     'label' => 'Fab URL',           'description' => 'Link to the pack\'s Fab listing.'),
    'item_count'        => array('type' => 'integer', 'label' => 'Item count',        'description' => 'Number of items in the pack.'),
    'manifest_url'      => array('type' => 'url',     'label' => 'Manifest URL',      'description' => 'Link to the pack\'s manifest JSON.'),
    'engine_version'    => array('type' => 'string',  'label' => 'Engine version',    'description' => 'The pack\'s original Unreal Engine version.'),
    'pack_size_bytes'   => array('type' => 'integer', 'label' => 'Pack size (bytes)', 'description' => 'Download size of the pack in bytes.'),
    'catalog_status'    => array('type' => 'string',  'label' => 'Catalog status',    'description' => 'Mirrors the catalog pipeline queue state.'),
    'contact_sheet_ids' => array('type' => 'ids',     'label' => 'Contact sheet IDs', 'description' => 'Media library attachment IDs of the contact sheets.'),
    'folder_summary'    => array('type' => 'text',    'label' => 'Folder summary',    'description' => 'Summary of the pack\'s folder structure.'),
  );
}

// anyone who can edit the post may edit its meta (so the Editor role is enough)
function assets_data_can_edit_meta($allowed, $meta_key, $post_id)
{
  return current_user_can('edit_post', $post_id);
}

// sanitise a value according to its field type
function assets_data_sanitize_field($type, $value)
{
  switch ($type) {
    case 'url':
      return esc_url_raw(trim((string) $value));
    case 'integer':
      return ('' === $value || null === $value) ? 0 : absint($value);
    case 'text':
      return sanitize_textarea_field((string) $value);
    case 'ids':
      if (!is_array($value)) {
        $value = preg_split('/[\s,]+/', (string) $value, -1, PREG_SPLIT_NO_EMPTY);
      }
      return array_values(array_filter(array_map('absint', $value)));
    default:
      return sanitize_text_field((string) $value);
  }
}

// register all catalog fields on the Asset post type
function assets_data_register_catalog_meta()
{
  foreach (assets_data_catalog_fields() as $key => $field) {
    $type = $field['type'];
    $args = array(
      'single' => true,
      'description' => $field['description'],
      'sanitize_callback' => function ($value) use ($type) {
        return assets_data_sanitize_field($type, $value);
      },
      'auth_callback' => 'assets_data_can_edit_meta',
      'show_in_rest' => true,
    );

    if ('ids' === $type) {
      $args['type'] = 'array';
      $args['default'] = array();
      $args['show_in_rest'] = array(
        'schema' => array(
          'type' => 'array',
          'items' => array('type' => 'integer'),
        ),
      );
    } elseif ('integer' === $type) {
      $args['type'] = 'integer';
    } else {
      $args['type'] = 'string';
    }

    register_post_meta(ASSETS_DATA_POST_TYPE, $key, $args);
  }
}
add_action('init', 'assets_data_register_catalog_meta');
