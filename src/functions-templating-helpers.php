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
 * Only setup the post on singular pages
 */
// function the_post() {
// 	if ( is_singular() ) {
// 		\the_post();
// 	}
// }

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


function get_menu_name( $location ) {
	$locations = get_nav_menu_locations();

	$menu = isset( $locations[ $location ] ) ? wp_get_nav_menu_object( $locations[ $location ] ) : '';

	return $menu ? $menu->name : '';
}


function get_page_type() {

	$page_type = '';

	if ( is_home_and_front_page() ) {
		$page_type = 'home_front';

	} elseif ( is_home() ) {
		$page_type = 'blog';

	} elseif ( is_singular() ) {
		$page_type = 'singular';

	} elseif ( is_category() || is_tag() || is_tax() ) {
		$page_type = 'term';

	} elseif ( is_post_type_archive() ) {
		$page_type = 'post_type_archive';

	} elseif ( is_author() ) {
		$page_type = 'author';

	} elseif ( is_date() ) {
		$page_type = 'date';

	} elseif ( is_search() ) {
		$page_type = 'search';

	} elseif ( is_404() ) {
		$page_type = '404';
	}

	return $page_type;
}

function get_page_title() {

	$page_type = get_page_type();

	if ($page_type == 'singular') { 
		return single_post_title( '', false );
	}

	if ($page_type == 'blog') { 
		return get_post_field( 'post_title', get_queried_object_id() );
	}

	if ($page_type == 'home_front') { 
		return get_bloginfo( 'name', 'display' ); 
	}

	if ($page_type == 'search') { 
		return sprintf(	esc_html__( 'Search results for: %s', 'ejo-base' ), get_search_query() );
	}

	if ($page_type == '404') { 
		return Page404::render_title();
	}

	if ($page_type == 'term') { 
		return single_term_title( '', false );
	}

	if ($page_type == 'post_type_archive') { 
		return post_type_archive_title( '', false );
	}

	if ($page_type == 'author') { 
		return get_the_author_meta( 'display_name', absint( get_query_var( 'author' ) ) );
	}

	return esc_html__( 'Could not find title', 'ejo-base' );
}

function get_page_content() {

	$page_type = get_page_type();

	if ($page_type == 'singular') { 
		return apply_filters( 'the_content', get_the_content() );
	}

	if ($page_type == 'blog') { 
		return apply_filters( 'the_content', get_the_content( null, false, get_queried_object_id() ) );
	}

	if ($page_type == 'home_front') { 
		return '';
	}

	if ($page_type == 'search') { 
		return '';
	}

	if ($page_type == '404') { 
		return Page404::render_content();
	}

	if ($page_type == 'term') { 
		return apply_filters( 'the_content', get_the_archive_description() );
	}

	if ($page_type == 'post_type_archive') { 
		return '';
	}

	if ($page_type == 'author') { 
		return apply_filters( 'the_content', get_the_author_meta( 'description', absint( get_query_var( 'author' ) ) ) );
	}

	return esc_html__( 'Could not find content', 'ejo-base' );
}

function get_page_image_id() {

	$page_type = get_page_type();

	if ($page_type == 'singular') { 
		return get_post_thumbnail_id();
	}

	if ($page_type == 'blog') { 
		return get_post_thumbnail_id( get_queried_object_id() );
	}

	if ($page_type == 'home_front') { 
		return false;
	}

	if ($page_type == 'search') { 
		return false;
	}

	if ($page_type == '404') { 
		return get_post_thumbnail_id( Page404::get_post_id() );
	}

	if ($page_type == 'term') { 
		return \get_term_meta( \get_queried_object_id(), 'image', true );
	}

	if ($page_type == 'post_type_archive') { 
		return false;
	}

	if ($page_type == 'author') { 
		return false;
	}

	return false;
}

function get_page_image_url( $size ) {
	$image_url = false;
	$image_id = get_page_image_id();

	if ($image_id) {
		$image_url = \wp_get_attachment_image_url( $image_id, $size );
	}

	return $image_url;
}
