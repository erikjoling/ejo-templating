<?php
/**
 * Templating functions.
 */

namespace Ejo\Tmpl;

/**
 * Get primary navigation location
 */
function get_nav_location() {
	return 'primary';
}



/**
 * Only setup the post on singular pages
 */
function the_post() {
	if ( is_singular() ) {
		\the_post();
	}
}

function get_blog_page() {
	return get_option('page_for_posts');
}

/** 
 * Get HTML attributes
 */
function get_html_attr() {
	$attr = [];

	$parts = wp_kses_hair( get_language_attributes(), [ 'http', 'https' ] );

	if ( $parts ) {

		foreach ( $parts as $part ) {

			$attr[ $part['name'] ] = $part['value'];
		}
	}

	return $attr;
}
