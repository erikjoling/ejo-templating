<?php
/**
 * Templating functions.
 */

namespace Ejo\Templating;


add_action( 'wp', function() {

	/**
	 * Site and Page stuff
	 */
	Composition::setup_component_defaults( 'site', function( $component ) { 
		return [
			'container' => [ 'tag' => 'div', 'force_display' => true  ],
			'content' => [ ['site-header'], ['site-main'], ['site-footer'] ]
		];
	});

	Composition::setup_component_defaults( 'site-header', function( $component ) { 
		return [
			'container' => [ 'tag' => 'header', 'force_display' => true ],
			'content' => [ ['site-branding'], ['site-nav-toggle'], ['site-nav'] ]
		];
	});

	Composition::setup_component_defaults( 'site-main', function( $component ) { 
		return [
			'container' => [ 'tag' => 'main', 'force_display' => true ],
			'content' => [ ['page'] ]
		];
	});

	Composition::setup_component_defaults( 'site-footer', function( $component ) { 
		return [
			'container' => [ 'tag' => 'footer', 'force_display' => true ],
		];
	});

	Composition::setup_component_defaults( 'page', function( $component ) { 
		return [
			'container' => [ 'tag' => 'article', 'force_display' => true ],
			'content' => [ ['page-header'], ['page-main'], ['page-footer'] ]
		];
	});

	Composition::setup_component_defaults( 'page-header', function( $component ) { 
		return [
			'container' => [ 'tag' => 'header', 'force_display' => true ],
			'content' => [ ['breadcrumbs'], ['page-title'] ]
		];
	});

	Composition::setup_component_defaults( 'page-main', function( $component ) { 
		return [
			'container' => [ 'tag' => 'div', 'force_display' => true ],
			'content' => [ ['page-title'], ['content'] ]
		];
	});

	Composition::setup_component_defaults( 'page-footer', function( $component ) { 
		return [
			'container' => [ 'tag' => 'footer' ],
		];
	});


	Composition::setup_component_defaults( 'page-title', function( $component ) {

		$component['container'] = [ 'tag' => 'h2', 'bem_block' => true, 'bem_element' => 'title' ];
		$component['content'] = [ '\\get_the_title' ];

		if( Composition::has_parent('page-header') ) {
			$component['container']['tag'] = 'h1';
		}
		
		return $component;
	});


	Composition::setup_component_defaults( 'site-branding', function( $component ) {
		return [
			'container' => false,
			'content' => [ __NAMESPACE__ . '\\render_site_branding' ]
		];
	});

	Composition::setup_component_defaults( 'site-nav-toggle', function( $component ) {
		return [
			'container' => false,
			'content' => [ __NAMESPACE__ . '\\render_site_nav_toggle' ]
		];
	});

	Composition::setup_component_defaults( 'site-nav', function( $component ) { 
		return [
			'container' => false,
			'content' => [ __NAMESPACE__ . '\\render_site_nav' ]
		];
	});

	Composition::setup_component_defaults( 'content', function( $component ) {
		return [
			'content' => [ __NAMESPACE__ . '\\render_content' ]
		];
	});

	Composition::setup_component_defaults( 'breadcrumbs', function( $component ) { 
		return [
			'content' => [ __NAMESPACE__ . '\\render_breadcrumbs' ]
		];
	});

	// Before render component...
	Composition::before_render_component( 'page', '\\the_post' );

	
	/**
	 * Template: Post
	 */
	if ( is_template('post') ) {

		Composition::setup_component_defaults( 'page-header', function( $component ) {

			Composition::component_insert_after( $component['content'], 'post-meta', 'page-title' );

			return $component;
		});	

		Composition::setup_component_defaults( 'page-footer', function( $component ) {

			Composition::component_insert_after( $component['content'], 'post-nav' );

			return $component;
		});	
	}


	/**
	 * Template: 404
	 */
	if ( is_template('404') ) {

		// Composition::setup_component_defaults( 'page-title', function( $component ) {
		// 	$component['content'] = [ __NAMESPACE__ . '\\render_404_title' ];

		// 	return $component;
		// });

		Composition::setup_component_defaults( 'page-content', function( $component ) {
			$component['content'] = [ __NAMESPACE__ . '\\render_404_content' ];

			return $component;
		});
	}

	/**
	 * Template: Search
	 */
	if ( is_template('search') ) {

		// Composition::setup_component_defaults( 'page-title', function( $component ) {
		// 	$component['content'] = [ __NAMESPACE__ . '\\render_search_title' ];

		// 	return $component;
		// });

		Composition::setup_component_defaults( 'page-content', function( $component ) {
			$component['content'] = [ __NAMESPACE__ . '\\render_search_content' ];

			return $component;
		});
	}

	/**
	 * Template: Blog no page
	 */
	if ( is_template('blog-no-page') ) {

		// Composition::setup_component_defaults( 'page-title', function( $component ) {
		// 	$component['content'] = 'Blog of Front';

		// 	return $component;
		// });
	}

	/**
	 * Template: Blog on Front
	 */
	if ( is_template('author') ) {

		Composition::setup_component_defaults( 'page-title', function( $component ) {
			$component['content'] = [ '\\get_the_author_meta', 'display_name', absint( get_query_var( 'author' ) ) ];

			return $component;
		});
	}
});

	
/** 
 * Test
 */ 
add_action( 'wp', function() {

	// Composition::setup_component_defaults( 'page-header', function( $component ) {

	// 	Composition::component_move_before( $component['content'], 'page-title', 'breadcrumbs' );

	// 	Composition::component_insert_after( $component['content'], 'page-title', 'breadcrumbs' );
	// 	Composition::component_insert_before( $component['content'], 'breadcrumbs', 'page-title' );
	// 	Composition::component_remove( $component['content'], 'page-title' );

	// 	return $component;
	// });	

}, 99);

