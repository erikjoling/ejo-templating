<?php 
/**
 * Template conditionals
 */

function is_main_loop() {
	return ( \get_the_ID() == \get_queried_object_id() );
}

function is_plural_page() {
	return is_home() || is_archive() || is_search();
}

function is_singular_page() {
	return is_singular();
}

function is_blog_page() {

	$is_blog_page = false;

	if ( get_queried_object_id() ) {
		$is_blog_page = get_queried_object_id() == get_blog_page();
	}

	return $is_blog_page;
}

function is_home_and_front_page() {
	return \is_home() && ! \get_option('page_on_front') && ! is_blog_page();
}