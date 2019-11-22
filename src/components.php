<?php
/**
 * Templating functions.
 *
 * Deceprecated!
 */

namespace Ejo\Tmpl;

function setup_site_component( $component ) {
	return [
		'element' => [ 'tag' => 'div', 'inner_wrap' => true ],
		'content' => [ 'site-header', 'site-main', 'site-footer' ]
	]; 
}

function setup_component_site( $component ) { 
	return [
		'element' => [ 'tag' => 'div', 'inner_wrap' => true ],
		'content' => [ 'site-header', 'site-main', 'site-footer' ]
	]; 
}

function setup_component_site_header( $component ) { 
	return [ 
		'element' => [ 'tag' => 'header', 'inner_wrap' => true, 'force_display' => true ],
		'content' => [ 'site-branding', 'site-nav-toggle', 'site-nav' ]
	]; 
}

function setup_component_site_main( $component ) { 
	return [ 
		'element' => [ 'tag' => 'main', 'inner_wrap' => true, 'force_display' => true ],
		'content' => [ 'page' ]
	]; 
}

function setup_component_site_footer( $component ) { 
	return [ 
		'element' => [ 'tag' => 'footer', 'inner_wrap' => true, 'force_display' => true ],
		'content' => []
	]; 
}

function setup_component_page( $component ) { 
	return [ 
		'element' => [ 'tag' => 'article', 'inner_wrap' => true, 'force_display' => true ],
		'content' => [ 'the-post', 'page-header', 'page-main', 'page-footer' ]
	]; 
}

function setup_component_page_header( $component ) { 
	return [ 
		'element' => [ 'tag' => 'header', 'inner_wrap' => true, 'force_display' => true ],
		'content' => [ 'breadcrumbs', 'page-title' ]
	]; 
}

function setup_component_page_main( $component ) { 
	return [ 
		'element' => [ 'tag' => 'div', 'inner_wrap' => true, 'force_display' => true ],
		'content' => [ 'page-content' ]
	]; 
}

function setup_component_page_footer( $component ) { 
	return [ 
		'element' => [ 'tag' => 'footer', 'inner_wrap' => true ],
		'content' => []
	]; 
}

# Small components

function setup_component_page_title( $component ) { 
	return [ 
		'element' => [ 'tag' => 'h1', 'bem_element' => 'title' ],
		'content' => get_page_title()
	]; 
}

function setup_component_site_branding( $component ) { 
	return [
		'content' => render_site_branding() 
	]; 
}

function setup_component_site_nav_toggle( $component ) { 
	return [
		'content' => render_site_nav_toggle() 
	]; 
}

function setup_component_site_nav( $component ) { 
	return [
		'content' => render_site_nav() 
	]; 
}

function setup_component_page_content( $component ) { 
	return [
		'content' => render_page_content() 
	]; 
}

function setup_component_breadcrumbs( $component ) { 
	return [
		'content' => render_breadcrumbs() 
	]; 
}

function setup_component_the_post_loop( $component ) { 
	return [
		'content' => render_post_archive_loop() 
	]; 
}

function setup_component_plural_post( $component ) { 
	return [
		'element' => [ 'tag' => 'article' ],
		'content' => [ 'plural-post-header', 'plural-post-main', 'plural-post-footer' ]
	]; 
}

function setup_component_plural_post_header( $component ) { 
	return [
		'element' => [ 'tag' => 'header', 'bem_block' => false, 'bem_element' => 'header' ],
		'content' => [ 'plural-post-title' ]
	]; 
}

function setup_component_plural_post_title( $component ) { 
	return [
		'element' => [ 'tag' => 'h3', 'bem_block' => 'title', 'bem_element' => true ],
		'content' => [ 'plural-post-title-link' ]
	// 'content' => render_plural_post_title() 
	]; 
}

function setup_component_plural_post_title_link( $component ) { 
	return [
		'element' => [ 'tag' => 'a', 'bem_block' => false, 'bem_element' => 'link', 'attr' => [ 'href' => get_the_permalink() ] ],
		'content' => get_the_title()
	]; 
}

function setup_component_plural_post_main( $component ) { 
	return [
		'element' => [ 'tag' => 'div', 'bem_block' => false, 'bem_element' => 'main' ],
		'content' => render_plural_post_content() 
	]; 
}

function setup_component_plural_post_footer( $component ) { 
	return [
		'element' => [ 'tag' => 'footer', 'bem_block' => false, 'bem_element' => 'footer' ],
	// 'content' => render_post_meta() 
	]; 
}


/**
 * Specials
 */
function setup_component_the_post( $component ) {
	return '\\the_post';
}



	function setup_component__page__plural_page( $component ) {

		Composition::component_remove( $component['content'], 'the-post' );

		return $component;
	}

	function setup_component__page_main__plural_page( $component ) {


		Composition::component_append( $component['content'], 'the-post-loop' );

		return $component;
	}

	// function setup_component__page_headern__plural_page( $component ) {

	// 	Composition::component_append( $component['content'], 'page-content' );

	// 	return $component;
	// };

if ( is_singular_page('post') ) {

	function setup_component__page_header__singular_post( $component ) {

		Composition::component_append( $component['content'], 'post-meta', 'page-title' );

		return $component;
	}
}

