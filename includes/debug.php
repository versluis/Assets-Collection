<?php
/************************************/
/*
/* DEBUG AIDES
/*
/************************************/             
// show which template part is being used (debug only)
function guru_which_template() {
	if ( is_super_admin() ) {
		global $template;
    print_r( "<strong>Currently using: $template</strong>" );
	}
}
add_action( 'wp_footer', 'guru_which_template' );
