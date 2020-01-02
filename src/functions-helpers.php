<?php
/**
 * Helpers
 */

namespace Ejo\Templating;


/** 
 * Render classes
 *
 * Example ['class-1', 'class-2'] becomes "class-1 class-2"
 *
 * @param array
 * @return string
 */
function render_classes( $classes ) {
	$html = '';

	foreach ( $classes as $class ) {

		$esc_class = esc_html( $class );

		$html .= " $esc_class";
	}

	return trim( $html );
}

/** 
 * Render attributes
 *
 * Example ['lang' => 'nl'] becomes lang="nl"
 *
 * @param 	array
 * @return 	string
 */
function render_attr( $attr ) {
	$html = '';

	foreach ( $attr as $name => $value ) {

		$esc_value = '';

		// If the value is a link `href`, use `esc_url()`.
		if ( $value !== false && 'href' === $name ) {
			$esc_value = esc_url( $value );

		} elseif ( $value !== false ) {
			$esc_value = esc_attr( $value );
		}

		$html .= false !== $value ? sprintf( ' %s="%s"', esc_html( $name ), $esc_value ) : esc_html( " {$name}" );
	}

	return trim( $html );
}

/**
 * Get the index of a value in an array
 *
 * @param $array An array to search
 * @param $target A value to find
 *
 * @return integer or false
 */
function array_get_index_by_value( array $array, $target ) {
	return array_search( $target, $array );
}

/**
 * Get the index of a key in an array
 *
 * @param $array An array to search
 * @param $target A key to find
 *
 * @return integer or false
 */
function array_get_index_by_key( array $array, $target ) {
	return array_search($target, array_keys($array), true);
}

/**
 * Inserts a new value before the value in the array.
 *
 * @param $array An array to insert in to.
 * @param $offset The offset to insert at.
 * @param $insert A value or value-pair to insert.
 *
 * @return The array with a value inserted
 */
function array_insert( array $array, $offset, $insert ) {

	// First make sure insert is an array
	$insert = (is_array($insert)) ? $insert : [$insert];

	// Return the array with inserted value
	return array_merge( array_slice( $array, 0, $offset ), $insert, array_slice( $array, $offset ) );
}

function array_remove_value( array $array, $value ) {

	if (($key = array_search($value, $array)) !== false) {
	    unset($array[$key]);
	}

	return $array;
}

/**
 * Render the callback
 *
 * @param Array with callback function in which the first entry is a
 * 		  callback (string, array) and the other entries are .. arguments
 * 
 * @return string Render
 */
function render_callback( $callback ) {

	$render = '';

	// Make sure callback is an array, because things more consistent.
	$callback = ( is_string($callback) ) ? [ $callback ] : $callback;

	// First part of callback is the callback functions, other entries are parameters
	$function = array_shift($callback);
	$params   = $callback;

	if ( $params) {
		$render .= call_user_func_array( $function, $params );
	}
	else {
		$render .= call_user_func( $function );
	}

	return $render;
}

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

	if (\is_singular()) {
		$title = \get_the_title();
	}
	elseif (is_blog_page()) {
		$title = \get_post_field('post_title', \get_queried_object_id());
	}
	// elseif (\is_tax()) {
	// 	$title = \single_term_title('', false);
	// }
	elseif (\is_archive()) {
		$title = \get_the_archive_title();
	}
	elseif (\is_404()) {
		$title = render_404_title();
	}
	elseif (\is_search()) {
		$title = sprintf( esc_html__( 'Search results for: %s', 'ejo-base' ), get_search_query() );
	}

	return apply_filters( 'ejo/templating/get_page_title', $title );
}

function get_page_content() {
	$content = false;

	if (\is_singular()) {
		$content = \get_the_content();
	}
	elseif (is_blog_page()) {
		$content = \get_post_field('post_content', \get_queried_object_id());
	}
	elseif (\is_tax() || \is_tag() || \is_category()) {
		$content = \term_description();
	}
	elseif (\is_archive()) {
		$content = \get_the_archive_description();		
	}
	elseif (\is_404()) {
		$content = render_404_content();
	}
	elseif (\is_search()) {
		$content = sprintf( esc_html__( 'Search results for: %s', 'ejo-base' ), get_search_query() );
	}

	return apply_filters( 'ejo/templating/get_page_content', $content );
}

// function get_page_title() {
// 	$title = false;

// 	$template = get_template();
// 	$template_type = get_template_type();

// 	if ($template_type == 'singular') {
// 		$title = get_the_title();
// 	}
// 	elseif ($template_type == 'term') {
// 		$title = single_term_title('', false);
// 	}
// 	elseif ($template == 'blog') {
// 		$title = get_post_field('post_title', get_queried_object_id());
// 	}
// 	elseif ($template == '404') {
// 		$title = render_404_title();
// 	}
// 	elseif ($template == 'search') {
// 		$title = sprintf( esc_html__( 'Search results for: %s', 'ejo-base' ), get_search_query() );
// 	}

// 	return $title;
// }

// function get_page_content() {
// 	$content = false;

// 	$template = get_template();
// 	$template_type = get_template_type();

// 	if ($template_type == 'singular') {
// 		$content = get_the_content();
// 	}
// 	elseif ($template_type == 'term') {
// 		$content = term_description();
// 	}
// 	elseif ($template == 'blog') {
// 		$content = get_post_field('post_content', get_queried_object_id());
// 	}
// 	elseif ($template == '404') {
// 		$content = render_404_content();
// 	}
// 	elseif ($template == 'search') {
// 		$content = sprintf( esc_html__( 'Search results for: %s', 'ejo-base' ), get_search_query() );
// 	}

// 	return $content;
// }