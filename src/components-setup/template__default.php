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
		$component['container'] = [ 'tag' => 'div', 'force_display' => true  ];
		$component['content']   = [ ['site-header'], ['site-main'], ['site-footer'] ];

		return $component;
	});

	Composition::setup_component_defaults( 'site-header', function( $component ) { 
		$component['container'] = [ 'tag' => 'header', 'force_display' => true ];
		$component['content'] 	= [ ['site-branding'], ['site-nav-toggle'], ['site-nav'] ];

		return $component;
	});

	Composition::setup_component_defaults( 'site-main', function( $component ) { 
		$component['container'] = [ 'tag' => 'main', 'force_display' => true ];
		$component['content'] 	= [ ['page'] ];

		return $component;
	});

	Composition::setup_component_defaults( 'site-footer', function( $component ) { 
		$component['container'] = [ 'tag' => 'footer', 'force_display' => true ];
		// $component['content'] 	= [];

		return $component;
	});

	Composition::setup_component_defaults( 'page', function( $component ) { 
		$component['container'] = [ 'tag' => 'article', 'force_display' => true ];
		$component['content'] 	= [ ['page-header'], ['page-main'], ['page-footer'] ];

		return $component;
	});

	Composition::setup_component_defaults( 'page-header', function( $component ) { 
		$component['container'] = [ 'tag' => 'header', 'force_display' => true ];
		$component['content'] 	= [ ['breadcrumbs'], ['page-title'] ];

		return $component;
	});

	Composition::setup_component_defaults( 'page-main', function( $component ) { 
		$component['container'] = [ 'tag' => 'div', 'force_display' => true ];
		$component['content'] 	= [ ['page-title'], ['content'] ];

		return $component;
	});

	Composition::setup_component_defaults( 'page-footer', function( $component ) { 
		$component['container'] = [ 'tag' => 'footer' ];

		return $component;
	});

	Composition::setup_component_defaults( 'page-title', function( $component ) {

		$component['container'] = [ 'tag' => 'h2', 'bem_block' => true, 'bem_element' => 'title' ];
		$component['content'] 	= [ '\\get_the_title' ];

		if( Composition::has_parent('page-header') ) {
			$component['container']['tag'] = 'h1';
		}
		
		return $component;
	});


	Composition::setup_component_defaults( 'site-branding', function( $component ) {
		$component['container'] = false;
		$component['content'] 	= [ __NAMESPACE__ . '\\render_site_branding' ];

		return $component;
	});

	Composition::setup_component_defaults( 'site-nav-toggle', function( $component ) {
		$component['container'] = false;
		$component['content'] 	= [ __NAMESPACE__ . '\\render_site_nav_toggle' ];

		return $component;
	});

	Composition::setup_component_defaults( 'site-nav', function( $component ) { 
		$component['container'] = false;
		$component['content'] 	= [ __NAMESPACE__ . '\\render_site_nav' ];

		return $component;
	});

	Composition::setup_component_defaults( 'content', function( $component ) {
		$component['content'] = [ __NAMESPACE__ . '\\render_content' ];
		
		return $component;
	});

	Composition::setup_component_defaults( 'breadcrumbs', function( $component ) { 
		$component['content'] = [ __NAMESPACE__ . '\\render_breadcrumbs' ];
		
		return $component;
	});

	// Before render component...
	Composition::before_render_component( 'page', '\\the_post' );

});

