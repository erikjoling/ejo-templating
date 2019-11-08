<?php
/**
 * Templating functions.
 */

namespace Ejo\Tmpl;


add_filter( 'body_class', __NAMESPACE__ . '\body_class_filter', 10, 2 );

/**
 * Returns the body classes.
 *
 * @return string
 */
function body_class_filter( $classes, $class ) {

	$classes = [];

	/**
	 * Templating
	 */

	// Archives
	if ( is_plural_page() ) {
		$classes[] = 'template-archive';
		$classes[] = 'template-archive--' . get_post_type();
	} 

	elseif ( is_singular_page() ) {
		
		$classes[] = 'template-singular';
		$classes[] = 'template-singular--' . get_post_type();

		// Checks for custom template.
		$template = str_replace(
			[ 'template-', 'tmpl-' ],
			'',
			basename( get_page_template_slug(), '.php' )
		);

		if ($template) {
			$classes[] = "template-{$template}";
		} 
	}

	// WP admin bar.
	if ( \is_admin_bar_showing() ) {
		$classes[] = 'has-admin-bar';
	}

	return array_map( 'esc_attr', array_unique( array_merge( $classes, (array) $class ) ) );
}