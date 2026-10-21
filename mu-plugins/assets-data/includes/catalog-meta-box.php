<?php
/************************************/
/*
/* CATALOG META BOX
/* Lets Jay check and correct catalog values by hand in the post editor.
/*
/************************************/

function assets_data_add_catalog_meta_box()
{
  add_meta_box(
    'assets_data_catalog_meta_box',
    'Catalog Data',
    'assets_data_display_catalog_meta_box',
    ASSETS_DATA_POST_TYPE,
    'normal',
    'default'
  );
}
add_action('add_meta_boxes', 'assets_data_add_catalog_meta_box');

function assets_data_display_catalog_meta_box($post)
{
  wp_nonce_field('assets_data_catalog_meta', 'assets_data_catalog_nonce');
  echo '<table class="form-table" role="presentation"><tbody>';

  foreach (assets_data_catalog_fields() as $key => $field) {
    $value = get_post_meta($post->ID, $key, true);
    $id = 'assets_data_' . $key;

    if ('ids' === $field['type']) {
      $value = is_array($value) ? implode(', ', $value) : '';
    } elseif ('integer' === $field['type'] && !metadata_exists('post', $post->ID, $key)) {
      $value = '';
    }

    echo '<tr><th scope="row"><label for="' . esc_attr($id) . '">' . esc_html($field['label']) . '</label></th><td>';

    switch ($field['type']) {
      case 'text':
        echo '<textarea name="assets_data[' . esc_attr($key) . ']" id="' . esc_attr($id) . '" rows="4" class="large-text">' . esc_textarea($value) . '</textarea>';
        break;
      case 'url':
        echo '<input type="url" name="assets_data[' . esc_attr($key) . ']" id="' . esc_attr($id) . '" class="large-text" value="' . esc_attr($value) . '">';
        break;
      case 'integer':
        echo '<input type="number" min="0" step="1" name="assets_data[' . esc_attr($key) . ']" id="' . esc_attr($id) . '" class="regular-text" value="' . esc_attr($value) . '">';
        break;
      default:
        echo '<input type="text" name="assets_data[' . esc_attr($key) . ']" id="' . esc_attr($id) . '" class="regular-text" value="' . esc_attr($value) . '">';
    }

    echo '<p class="description">' . esc_html($field['description']);
    if ('ids' === $field['type']) {
      echo ' Comma-separated.';
    }
    echo '</p></td></tr>';
  }

  echo '</tbody></table>';
}

function assets_data_save_catalog_meta($post_id)
{
  if (!isset($_POST['assets_data_catalog_nonce']) || !wp_verify_nonce($_POST['assets_data_catalog_nonce'], 'assets_data_catalog_meta')) {
    return;
  }
  if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
    return;
  }
  if (!current_user_can('edit_post', $post_id)) {
    return;
  }

  $input = isset($_POST['assets_data']) && is_array($_POST['assets_data']) ? wp_unslash($_POST['assets_data']) : array();

  foreach (assets_data_catalog_fields() as $key => $field) {
    $raw = trim((string) ($input[$key] ?? ''));

    // an emptied field removes the meta, so "no value" and "never set" look the same
    if ('' === $raw) {
      delete_post_meta($post_id, $key);
    } else {
      update_post_meta($post_id, $key, $raw); // registered sanitize_callback does the cleaning
    }
  }
}
add_action('save_post_' . ASSETS_DATA_POST_TYPE, 'assets_data_save_catalog_meta');
