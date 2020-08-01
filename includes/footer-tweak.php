<?php
// ********************************************************
// redefine footer content
// ********************************************************

// remove default implementation
remove_action( 'generate_credits', 'generate_add_footer_info' );

// define our own
if ( ! function_exists( 'generate_add_footer_info' ) ) {
	add_action( 'generate_credits', 'generate_add_footer_info' );
	/**
	 * Add the copyright to the footer
	 *
	 * @since 0.1
	 */
	function generate_add_footer_info() {
    $copyright = sprintf( '<span class="copyright">&copy; %1$s %2$s</span> &bull; %4$s <a href="%3$s" itemprop="url">%5$s</a> &bull; <a href="https://github.com/versluis/Games-Collection" target="_blank">Fork Me</a>',
			date( 'Y' ),
			'<a href="https://wpguru.tv">The WP Guru</a>',
			esc_url( 'https://generatepress.com' ),
			_x( 'Powered by', 'GeneratePress', 'generatepress' ),
			__( 'GeneratePress', 'generatepress' )
		);

		echo apply_filters( 'generate_copyright', $copyright ); // WPCS: XSS ok.
	}
}

// add Genre to Tag Archives, Status to Categories
add_filter( 'get_the_archive_title', function( $title ) {
  if ( is_tag() ) {
      $title = 'Genre: ' . $title;
  }

  if (is_category()) {
    $title = 'Status: ' . $title;
  }

  return $title;
}, 50 );
