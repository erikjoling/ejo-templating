<?php
/**
 * Templating functions.
 */

namespace Ejo\Tmpl;

function start_engine() {

	/**
	 * Setup the system after not before `wp` hook because 
	 * we need conditional tags to be available
	 */
	// add_action( 'wp', __NAMESPACE__ . '\setup_templates' );
	add_action( 'wp', function() {

		register_component( 'site',  		['tag' => 'div', 'inner_wrap' => true] );
		register_component( 'site-header',  ['tag' => 'header', 'inner_wrap' => true] );
		register_component( 'site-main',    ['tag' => 'main', 'inner_wrap' => true] );
		register_component( 'site-footer',  ['tag' => 'footer', 'inner_wrap' => true] );
		register_component( 'page',         ['tag' => 'article', 'inner_wrap' => true] );
		register_component( 'page-header',  ['tag' => 'header', 'inner_wrap' => true] );
		register_component( 'page-content', ['tag' => 'div', 'inner_wrap' => true] );
		register_component( 'page-footer',  ['tag' => 'footer', 'inner_wrap' => true] );

		register_component( 'site-branding',  ['tag' => 'div'] );

		// add_to_component( 'site', [ 'site-header' ] );
		add_to_component( 'site', [ 'site-header', 'site-main', 'site-footer' ] );
		add_to_component( 'site-main', [ 'page' ] );
		add_to_component( 'site-header', [ 'site-branding', 'fn:render_site_nav_toggle', 'fn:render_site_nav' ] );
		add_to_component( 'site-footer', [] );

		add_to_component( 'site-branding', function() {
			
		} );

		add_filter( 'ejo/tmpl/site-header', function($data) {

			if ($data['parent'] == 'site') {
				$data['inner_components'] = [ 'site-branding', 'fn:render_site_nav_toggle', 'fn:render_site_nav' ];
			}

			return $data;
		});

		if ( is_singular_page() ) {		
			log('is_singular_page');
			add_to_component( 'page', [ 'fn:the_post', 'page-header', 'page-content', 'page-footer' ] );
			add_to_component( 'page-header', [ 'fn:render_page_title' ] );
			add_to_component( 'page-content', [ 'fn:render_page_content' ] );
		}

		if ( is_singular_page('post') ) {
			log('is_singular_page');
			add_to_component( 'page-footer', [ 'fn:render_post_nav' ] );
		}

		if ( is_plural_page('post') ) {
			log('is_plural_page');
			add_to_component( 'page', [ 'page-header', 'page-content', 'page-footer' ] );
			add_to_component( 'page-header', [ 'fn:render_page_title' ] );
			add_to_component( 'page-content', [ 'fn:render_page_content' ] );
			add_to_component( 'page-footer', [ 'fn:render_posts_nav' ] );
		}
	} );


	// log(get_components());


}

function load_template() {
	require_once( 'template-index.php' );	
}

// function setup_templates() {

// 	// add_filter( 'ejo/tmpl/site', function($data) {
// 	// 	$data['inner_components'] = [ 'site-header', 'site-main', 'site-footer' ];

// 	// 	return $data;
// 	// });

// 	// add_filter( 'ejo/tmpl/site-header', function($data) {
// 	// 	$data['inner_components'] = [ 'fn:render_site_branding', 'fn:render_site_nav_toggle', 'fn:render_site_nav' ];

// 	// 	return $data;
// 	// });

// 	// // add_to_component( 'site', [ 'site-header' ] );
// 	// add_to_component( 'site', [ 'site-header', 'site-main', 'site-footer' ] );
// 	// add_to_component( 'site-main', [ 'page' ] );
// 	// add_to_component( 'page', [ 'fn:the_post', 'page-header', 'page-content', 'page-footer' ] );
// 	// add_to_component( 'site-header', [ 'fn:render_site_branding', 'fn:render_site_nav_toggle', 'fn:render_site_nav' ] );
// 	// add_to_component( 'page-header', [ 'fn:render_page_title' ] );
// 	// add_to_component( 'page-content', [ 'fn:render_page_content' ] );

// 	// add_to_component( 'page-footer', [ 'fn:render_post_nav' ] );
// }


function register_component( $name, $component = [] ) {
	global $ejo_components;

	$ejo_components[$name] = $component;
}

function add_to_component( $name, $components ) {
	global $ejo_components;

	$ejo_components[$name]['inner_components'] = $components;
}

function get_components() {
	global $ejo_components;

	return $ejo_components ?? [];
}

function get_component( $name ) {
	global $ejo_components;

	return $ejo_components[$name] ?? [];
}

/**
 * Check if component is registered
 */
function is_registered_component( $name ) {
	global $ejo_components;

	return isset($ejo_components[$name]);
}

/**
 * Render component
 *
 * Note: A component should be registered first
 */
function render_component( $name, $parent = null ) {

	if ( ! is_registered_component( $name ) ) {
		return;
	}

	$defaults = [
		'tag'              => 'div',
		'inner_wrap'       => false,
		'extra_classes'    => [],
		'attributes'       => [],
		'inner_components' => [],
		'parent'		   => $parent,
	];

	// Setup component
	$data = array_merge( $defaults, get_component($name) );
	$data = apply_filters( "ejo/tmpl/{$name}", $data );

	// log("Component `$name`");
	// log("Parent `{$data['parent']}`");
	// log('Inner Components:');
	// log($data['inner_components']);

	// Process component
	$name             = esc_html( $name );
	$tag              = esc_html( $data['tag'] );
	$classes          = trim( $name . ' ' . render_classes($data['extra_classes']) );
	$attributes       = render_attr( $data['attributes'] );
	$inner_wrap	      = !! $data['inner_wrap'];
	$inner_components = $data['inner_components'];

	// Setup render
	$format_inner_wrap = ( $inner_wrap ) ? sprintf( '<div class="%s">%%s</div>', "{$name}__inner" ) : '%s'; 
	$format = sprintf( '<%1$s class="%2$s"%3$s>%4$s</%1$s>', $tag, $classes, $attributes, $format_inner_wrap );

	$inner_content = '';

	foreach ( $inner_components as $inner_component ) {

		if ( 'fn:' === substr( $inner_component, 0, 3 ) ) {
			$function = __NAMESPACE__ . '\\' . substr($inner_component, 3, strlen($inner_component));

			// log($function);
			$inner_content .= $function();
		}
		else {
			$inner_content .= render_component( $inner_component, $name );
		}

	}

	return sprintf( $format, $inner_content );
}
