<?php
/**
 * Templating functions.
 */

namespace Ejo\Templating;

/**
 * Get primary navigation location
 */
function get_nav_location() {
	return 'primary';
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


function get_menu_name( $location ) {
	$locations = get_nav_menu_locations();

	$menu = isset( $locations[ $location ] ) ? wp_get_nav_menu_object( $locations[ $location ] ) : '';

	return $menu ? $menu->name : '';
}

function get_page_title() {
	$title = false;

	$template = get_template();
	$template_type = get_template_type();

	if ($template_type == 'single') {
		$title = get_the_title();
	}
	elseif ($template_type == 'term') {
		$title = single_term_title('', false);
	}
	elseif ($template == 'blog') {
		$title = get_post_field('post_title', get_queried_object_id());
	}
	elseif ($template == '404') {
		$title = render_404_title();
	}
	elseif ($template == 'search') {
		$title = sprintf( esc_html__( 'Search results for: %s', 'ejo-base' ), get_search_query() );
	}

	return $title;
}

function get_page_content() {
	$content = false;

	$template = get_template();
	$template_type = get_template_type();

	if ($template_type == 'single') {
		$content = get_the_content();
	}
	elseif ($template_type == 'term') {
		$content = term_description();
	}
	elseif ($template == 'blog') {
		$content = get_post_field('post_content', get_queried_object_id());
	}
	elseif ($template == '404') {
		$content = render_404_content();
	}
	elseif ($template == 'search') {
		$content = sprintf( esc_html__( 'Search results for: %s', 'ejo-base' ), get_search_query() );
	}

	return $content;
}