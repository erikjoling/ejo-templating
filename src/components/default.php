<?php
/**
 * Templating functions.
 */

namespace Ejo\Templating;


/**
 * Site and Page stuff
 */
Composition::component_data( 'html', function( $component ) { 
	$component['container'] = [ 'tag' => 'html', 'force_display' => true, 'bem_block' => false, 'attr' => get_html_attr() ];
	$component['content']   = [ ['head'], ['body'] ];

	return $component;
});

Composition::component_data( 'head', function( $component ) { 
	$component['container'] = [ 'tag' => 'head', 'force_display' => true, 'bem_block' => false ];
	$component['content']   = [ __NAMESPACE__ . '\\render_wp_head' ];

	return $component;
});

Composition::component_data( 'body', function( $component ) { 
	$component['container'] = [ 'tag' => 'body', 'force_display' => true, 'bem_block' => 'site', 'class' => \get_body_class() ];
	$component['content']   = [ ['site-header'], ['site-main'], ['site-footer'], ['site-footer-scripts'] ];

	return $component;
});

// Composition::component_data( 'site', function( $component ) { 
// 	$component['container'] = [ 'tag' => 'div', 'force_display' => true  ];
// 	$component['content']   = [ ['site-header'], ['site-main'], ['site-footer'] ];

// 	return $component;
// });

Composition::component_data( 'site-header', function( $component ) { 
	$component['container'] = [ 'tag' => 'header', 'force_display' => true ];
	$component['content'] 	= [ ['site-branding'], ['site-nav-toggle'], ['site-nav'] ];

	return $component;
});

Composition::component_data( 'site-main', function( $component ) { 
	$component['container'] = [ 'tag' => 'main', 'force_display' => true ];
	$component['content'] 	= [ ['page'] ];

	return $component;
});

Composition::component_data( 'site-footer', function( $component ) { 
	$component['container'] = [ 'tag' => 'footer', 'force_display' => true ];
	// $component['content'] 	= [];

	return $component;
});

Composition::component_data( 'site-footer-scripts', function( $component ) { 
	$component['container'] = false;
	$component['content'] 	= [ __NAMESPACE__ . '\\render_wp_footer' ];;

	return $component;
});

Composition::component_data( 'page', function( $component ) { 
	$component['container'] = [ 'tag' => 'article', 'force_display' => true ];
	$component['content'] 	= [ ['page-header'], ['page-main'], ['page-footer'] ];

	return $component;
});

Composition::component_data( 'page-header', function( $component ) { 
	$component['container'] = [ 'tag' => 'header', 'force_display' => true ];
	$component['content'] 	= [ ['breadcrumbs'], ['page-title'] ];

	return $component;
});

Composition::component_data( 'page-main', function( $component ) { 
	$component['container'] = [ 'tag' => 'div', 'force_display' => true ];
	$component['content'] 	= [ ['page-content'] ];

	return $component;
});

Composition::component_data( 'page-footer', function( $component ) { 
	$component['container'] = [ 'tag' => 'footer' ];

	return $component;
});

Composition::component_data( 'site-branding', function( $component ) {
	$component['container'] = false;
	$component['content'] 	= [ __NAMESPACE__ . '\\render_site_branding' ];

	return $component;
});

Composition::component_data( 'site-nav-toggle', function( $component ) {
	$component['container'] = false;
	$component['content'] 	= [ __NAMESPACE__ . '\\render_site_nav_toggle' ];

	return $component;
});

Composition::component_data( 'site-nav', function( $component ) { 
	$component['container'] = false;
	$component['content'] 	= [ __NAMESPACE__ . '\\render_site_nav' ];

	return $component;
});

Composition::component_data( 'breadcrumbs', function( $component ) { 
	$component['content'] = [ __NAMESPACE__ . '\\render_breadcrumbs' ];
	
	return $component;
});

Composition::component_data( 'page-title', function( $component ) {

	$component['container'] = [ 'tag' => 'h1', 'bem_block' => true, 'bem_element' => 'title' ];
	$component['content'] 	= [ __NAMESPACE__ . '\\get_page_title' ];

	if( ! Composition::has_parent('page-header') ) {
		$component['container']['tag'] = 'h2';
	}
	
	return $component;
});

Composition::component_data( 'page-content', function( $component ) {
	// $component['container'] = [ 'bem_block' => true, 'bem_element' => 'content' ];
	$component['container'] = false;
	$component['content']   = [ __NAMESPACE__ . '\\render_content' ];
	
	return $component;
});

// Before render component...
Composition::do_action( 'page', '\\the_post' );
