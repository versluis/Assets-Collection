<?php
/**
 * The template for displaying single posts.
 *
 * @package GeneratePress
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?> <?php generate_do_microdata( 'article' ); ?>>
	<div class="inside-article">
		<?php
		/**
		 * generate_before_content hook.
		 *
		 * @since 0.1
		 *
		 * @hooked generate_featured_page_header_inside_single - 10
		 */
		do_action( 'generate_before_content' );
		?>

		<header class="entry-header">
			<?php
			/**
			 * generate_before_entry_title hook.
			 *
			 * @since 0.1
			 */
			do_action( 'generate_before_entry_title' );

			if ( generate_show_title() ) {
				the_title( '<h1 class="entry-title" itemprop="headline">', '</h1>' );
			}

			/**
			 * generate_after_entry_title hook.
			 *
			 * @since 0.1
			 *
			 * @hooked generate_post_meta - 10
			 */
			do_action( 'generate_after_entry_title' );
			?>
		</header><!-- .entry-header -->

		<?php
		/**
		 * generate_after_entry_header hook.
		 *
		 * @since 0.1
		 *
		 * @hooked generate_post_image - 10
		 */
		do_action( 'generate_after_entry_header' );
		?>

		<div class="entry-content" itemprop="text">
			<?php
			the_content();

			// if we have a Marketplace URL, let's display that widget
			// (skipped when it's the same link as the catalog block's product button)
			$meta = get_post_meta(get_the_id(), 'guru_fields', true);
			$product_url = get_post_meta(get_the_id(), 'product_url', true);
			if (is_array($meta) && !empty($meta['steamURL']) && $meta['steamURL'] !== $product_url) {
				
				// echo "We have Meta Data!";
				// echo "The Marketpalce URL is $meta[steamURL] :-)<br>";

				// add DAZ Affililate code to URL
				$dazlink = esc_url( guru_marketplace_link( $meta['steamURL'] ) );

				// content goes here
				
				// embed source image (only cached previews exist - generation stopped in 2022)
				$imageURL = guru_pull_image($meta['steamURL'], get_the_ID());
				if ($imageURL) {
					echo ('<a href="' . $dazlink . '" target="_blank">
					<img src="' . esc_url($imageURL) . '"></a>');
				}

				// print a link to the Marketplace Listing:
				echo ('<ul><li>');
				echo ('<a href="' . $dazlink . '" target="_blank">Product Link</a></li>');

				// let users regenerate the image
				// echo ('<li><a href="#">Regenerate Preview</a></li>');

				// echo ('<li>Post ID: ' . get_the_ID() . '</li>');
				// echo ('<li>Image URL: ' . $imageURL . '</li>');
				echo ('</ul>');
			}

			// catalog data: product button, stats, contact sheets, folder structure
			if ('assets' === get_post_type()) {
				get_template_part('template-parts/asset-catalog');
			}

			wp_link_pages( array(
				'before' => '<div class="page-links">' . __( 'Pages:', 'generatepress' ),
				'after'  => '</div>',
			) );
			?>
		</div><!-- .entry-content -->

		<?php
		/**
		 * generate_after_entry_content hook.
		 *
		 * @since 0.1
		 *
		 * @hooked generate_footer_meta - 10
		 */
		do_action( 'generate_after_entry_content' );

		/**
		 * generate_after_content hook.
		 *
		 * @since 0.1
		 */
		do_action( 'generate_after_content' );
		?>
	</div><!-- .inside-article -->
</article><!-- #post-## -->
