<?php
/**
 * Catalog block below the content of single Asset posts.
 * Data comes from the Assets Data mu-plugin's meta fields; every part is
 * optional, and the whole block disappears when a post has no catalog data.
 *
 * catalog_status is deliberately not shown: it is pipeline bookkeeping.
 */

if (!defined('ABSPATH')) {
	exit;
}

$post_id        = get_the_ID();
$product_url    = (string) get_post_meta($post_id, 'product_url', true);
$item_count     = (int) get_post_meta($post_id, 'item_count', true);
$engine_version = (string) get_post_meta($post_id, 'engine_version', true);
$pack_size      = (int) get_post_meta($post_id, 'pack_size_bytes', true);
$manifest_url   = (string) get_post_meta($post_id, 'manifest_url', true);
$folder_summary = (string) get_post_meta($post_id, 'folder_summary', true);
$sheet_ids      = get_post_meta($post_id, 'contact_sheet_ids', true);

// only keep IDs that are real images in the media library
$sheet_ids = array_values(array_filter(is_array($sheet_ids) ? array_map('absint', $sheet_ids) : array(), 'wp_attachment_is_image'));

$stats = array();
if ($item_count) {
	$stats[] = sprintf(_n('%s item', '%s items', $item_count), number_format_i18n($item_count));
}
if ('' !== $engine_version) {
	$stats[] = 'Unreal Engine ' . $engine_version;
}
if ($pack_size) {
	$stats[] = size_format($pack_size, 1);
}

if ('' === $product_url && !$stats && !$sheet_ids && '' === $manifest_url && '' === $folder_summary) {
	return; // pre-catalog post: render nothing
}
?>

<section class="asset-catalog" aria-label="Catalog details">

	<?php if ('' !== $product_url || $stats) : ?>
		<div class="asset-catalog__header">
			<?php if ('' !== $product_url) : ?>
				<a class="button gradient asset-catalog__product" href="<?php echo esc_url(guru_marketplace_link($product_url)); ?>" target="_blank" rel="noopener"><?php echo esc_html(guru_product_link_label($product_url)); ?></a>
			<?php endif; ?>
			<?php if ($stats) : ?>
				<p class="asset-catalog__stats"><?php echo esc_html(implode(' · ', $stats)); ?></p>
			<?php endif; ?>
		</div>
	<?php endif; ?>

	<?php if ($sheet_ids) : ?>
		<div class="asset-catalog__sheets">
			<?php foreach ($sheet_ids as $sheet_id) : ?>
				<a class="asset-catalog__sheet" href="<?php echo esc_url(wp_get_attachment_image_url($sheet_id, 'full')); ?>">
					<?php echo wp_get_attachment_image($sheet_id, 'medium_large', false, array('loading' => 'lazy')); ?>
				</a>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>

	<?php if ('' !== $folder_summary) : ?>
		<details class="asset-catalog__folders">
			<summary>Folder structure</summary>
			<pre><?php echo esc_html($folder_summary); ?></pre>
		</details>
	<?php endif; ?>

	<?php if ('' !== $manifest_url) : ?>
		<p class="asset-catalog__manifest"><a href="<?php echo esc_url($manifest_url); ?>" target="_blank" rel="noopener">Full item list (manifest JSON)</a></p>
	<?php endif; ?>

</section>
