<?php
/************************************/
/*
/* CUSTOM META FIELDS
/*
/************************************/  

// add a meta box to post input dialogue
function guru_add_meta_box()
{
  add_meta_box(
    'guru_fields_meta_box', // $id
    'Game Store Links', // $title
    'guru_display_meta_box', // $callback
    'games', // $screen
    'normal', // $context
    'high' // $priority
  );
}
add_action('add_meta_boxes', 'guru_add_meta_box');

// contents of our custom field box
function guru_display_meta_box()
{
  global $post;
  $meta = get_post_meta($post->ID, 'guru_fields', true); ?>

  <input type="hidden" name="guru_meta_box_nonce" value="<?php echo wp_create_nonce(basename(__FILE__)); ?>">

  <!-- All fields will go here -->
  <p>Add a link to the Steam store page to include a snazzy widget ;-)</p>

  <p>
    <label for="guru_fields[steamURL]">Steam URL: </label>
    <input type="text" name="guru_fields[steamURL]" id="guru_fields[steamURL]" 
    class="regular-text" value="<?php echo $meta['steamURL']; ?>">
  </p>

<?php }

// save custom fields
function guru_save_fields_meta($post_id)
{
  // verify nonce
  if (!wp_verify_nonce($_POST['guru_meta_box_nonce'], basename(__FILE__))) {
    return $post_id;
  }
  // check autosave
  if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
    return $post_id;
  }
  // check permissions
  if ('page' === $_POST['post_type']) {
    if (!current_user_can('edit_page', $post_id)) {
      return $post_id;
    } elseif (!current_user_can('edit_post', $post_id)) {
      return $post_id;
    }
  }

  $old = get_post_meta($post_id, 'guru_fields', true);
  $new = $_POST['guru_fields'];
  // $new = sanitize_text_field ($new);
  // $new = normalize_whitespace ($new);
  // $new = trim($new);

  if ($new && $new !== $old) {
    update_post_meta($post_id, 'guru_fields', $new);
  } elseif ('' === $new && $old) {
    delete_post_meta($post_id, 'guru_fields', $old);
  }
}
add_action('save_post', 'guru_save_fields_meta');
