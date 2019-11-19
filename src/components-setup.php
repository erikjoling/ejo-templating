<?php
/**
 * Templating functions.
 */

namespace Ejo\Tmpl;


add_action( 'wp', function() {

	/*
	Ik interesseer me niet in het eerst registreren en daarna renderen. 
	Het is prima om tijdens het renderen alles op te bouwen. 
	Zolang het maar BEM proof is. En flexibel qua aanpasbaarheid.
	*/

	/*
	With the current setup the content can only be an array of components 
	or a string which needs to be outputted.
	*/

	/*
	Small problem with BEM: it looks like the BEM element inherits the wrong
	BEM block. See plural-post-title-link
	*/
	
	Composition::setup_component( 'site', function( $component ) { 
		return [
			'element' => [ 'tag' => 'div', 'inner_wrap' => true ],
			'content' => [ 'site-header', 'site-main', 'site-footer' ]
		]; 
	});

	Composition::setup_component( 'site-header', function( $component ) { 
		return [ 
			'element' => [ 'tag' => 'header', 'inner_wrap' => true, 'force_display' => true ],
			'content' => [ 'site-branding', 'site-nav-toggle', 'site-nav' ]
		]; 
	});

	Composition::setup_component( 'site-main', function( $component ) { 
		return [ 
			'element' => [ 'tag' => 'main', 'inner_wrap' => true, 'force_display' => true ],
			'content' => [ 'page' ]
		]; 
	});

	Composition::setup_component( 'site-footer', function( $component ) { 
		return [ 
			'element' => [ 'tag' => 'footer', 'inner_wrap' => true, 'force_display' => true ],
			'content' => []
		]; 
	});

	Composition::setup_component( 'page', function( $component ) { 
		return [ 
			'element' => [ 'tag' => 'article', 'inner_wrap' => true, 'force_display' => true ],
			'content' => [ 'the-post', 'page-header', 'page-main', 'page-footer' ]
		]; 
	});

	Composition::setup_component( 'page-header', function( $component ) { 
		return [ 
			'element' => [ 'tag' => 'header', 'inner_wrap' => true, 'force_display' => true ],
			'content' => [ 'breadcrumbs', 'page-title' ]
		]; 
	});

	Composition::setup_component( 'page-main', function( $component ) { 
		return [ 
			'element' => [ 'tag' => 'div', 'inner_wrap' => true, 'force_display' => true ],
			'content' => [ 'page-content' ]
		]; 
	});

	Composition::setup_component( 'page-footer', function( $component ) { 
		return [ 
			'element' => [ 'tag' => 'footer', 'inner_wrap' => true ],
			'content' => []
		]; 
	});

	# Small components

	Composition::setup_component( 'page-title', function( $component ) { 
		return [ 
			'element' => [ 'tag' => 'h1', 'bem_element' => 'title' ],
			'content' => get_page_title()
		]; 
	});

	Composition::setup_component( 'site-branding', function( $component ) { 
		return [
			'content' => render_site_branding() 
		]; 
	});

	Composition::setup_component( 'site-nav-toggle', function( $component ) { 
		return [
			'content' => render_site_nav_toggle() 
		]; 
	});

	Composition::setup_component( 'site-nav', function( $component ) { 
		return [
			'content' => render_site_nav() 
		]; 
	});

	Composition::setup_component( 'page-content', function( $component ) { 
		return [
			'content' => render_page_content() 
		]; 
	});

	Composition::setup_component( 'breadcrumbs', function( $component ) { 
		return [
			'content' => render_breadcrumbs() 
		]; 
	});

	Composition::setup_component( 'the-post-loop', function( $component ) { 
		return [
			'content' => render_post_archive_loop() 
		]; 
	});

	Composition::setup_component( 'plural-post', function( $component ) { 
		return [
			'element' => [ 'tag' => 'article' ],
			'content' => [ 'plural-post-header', 'plural-post-main', 'plural-post-footer' ]
		]; 
	});

	Composition::setup_component( 'plural-post-header', function( $component ) { 
		return [
			'element' => [ 'tag' => 'header', 'bem_block' => false, 'bem_element' => 'header' ],
			'content' => [ 'plural-post-title' ]
		]; 
	});

	Composition::setup_component( 'plural-post-title', function( $component ) { 
		return [
			'element' => [ 'tag' => 'h3', 'bem_block' => 'title', 'bem_element' => true ],
			'content' => [ 'plural-post-title-link' ]
		// 'content' => render_plural_post_title() 
		]; 
	});

	Composition::setup_component( 'plural-post-title-link', function( $component ) { 
		return [
			'element' => [ 'tag' => 'a', 'bem_block' => false, 'bem_element' => 'link', 'attr' => [ 'href' => get_the_permalink() ] ],
			'content' => get_the_title()
		]; 
	});

	Composition::setup_component( 'plural-post-main', function( $component ) { 
		return [
			'element' => [ 'tag' => 'div', 'bem_block' => false, 'bem_element' => 'main' ],
			'content' => render_plural_post_content() 
		]; 
	});

	Composition::setup_component( 'plural-post-footer', function( $component ) { 
		return [
			'element' => [ 'tag' => 'footer', 'bem_block' => false, 'bem_element' => 'footer' ],
		// 'content' => render_post_meta() 
		]; 
	});


	/**
	 * Specials
	 */
	Composition::setup_component( 'the-post', '\\the_post' );


	/**
	 * Template
	 */
	if (is_plural_page()) {

		Composition::setup_component( 'page', function( $component ) {

			Composition::component_remove( $component['content'], 'the-post' );

			return $component;
		});

		Composition::setup_component( 'page-main', function( $component ) {


			Composition::component_append( $component['content'], 'the-post-loop' );

			return $component;
		});

		// Composition::setup_component( 'page-header', function( $component ) {

		// 	Composition::component_append( $component['content'], 'page-content' );

		// 	return $component;
		// });
	}

	if ( is_singular_page('post') ) {

		Composition::setup_component( 'page-header', function( $component ) {

			Composition::component_append( $component['content'], 'post-meta', 'page-title' );

			return $component;
		});	
	}
});

	
/** 
 * Test
 */ 
add_action( 'wp', function() {

	log('hopelijk is dit als eerst...');
}, 99);

