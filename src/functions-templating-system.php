<?php
/**
 * Templating functions.
 */

namespace Ejo\Tmpl;

function start_engine() {

	register_component( 'site',  		['tag' => 'div'] );
	register_component( 'site-header',  ['tag' => 'header'] );
	register_component( 'site-main',    ['tag' => 'main'] );
	register_component( 'site-footer',  ['tag' => 'footer'] );
	register_component( 'page',         ['tag' => 'article'] );
	register_component( 'page-header',  ['tag' => 'header'] );
	register_component( 'page-content', ['tag' => 'div'] );
	register_component( 'page-footer',  ['tag' => 'footer'] );

	add_to_component( 'site', [ 'site-header', 'site-main', 'site-footer' ] );
	add_to_component( 'site-main', [ 'page' ] );
	add_to_component( 'page', [ 'page-header', 'page-content', 'page-footer' ] );

	require_once( 'template-index.php' );
}


function register_component( $name, $args = [] ) {
	global $ejo_components;

	$ejo_components[$name]['args'] = $args;
	$ejo_components[$name]['components'] = [];
}


function add_to_component( $name, $components ) {
	global $ejo_components;

	$ejo_components[$name]['components'] = $components;
}

function get_component_args( $name ) {
	global $ejo_components;

	return $ejo_components[$name]['args'] ?? [];
}

function get_component_components( $name ) {
	global $ejo_components;

	return $ejo_components[$name]['components'] ?? [];
}

// function render_components( $name ) {
// 	global $ejo_components;

// 	$components = 

// 	foreach ( as $component) {
// 		# code...
// 	}
// }

function render_component( $name ) {

	echo $name;

	$default_args = [
		'tag'           => 'div',
		'inner_wrap'    => true,
		'extra_classes' => [],
		'attributes'    => [],
	];

	// Setup component
	$args = array_merge( $default_args, get_component_args($name) );
	$args = apply_filters( "ejo/tmpl/{$name}/args", $args );

	// Process component
	$name       = esc_html( $name );
	$tag        = esc_html( $args['tag'] );
	$classes    = trim( $name . ' ' . render_classes($args['extra_classes']) );
	$attributes = render_attr( $args['attributes'] );
	$inner_wrap	= !! $args['inner_wrap'];

	// Setup render
	// $format_inner_wrap = ( $inner_wrap ) ? sprintf( '<div class="%s">%%s</div>', "{$name}__inner" ) : '%s'; 
	// $format = sprintf( '<%1$s class="%2$s"%3$s>%4$s</%1$s>', $tag, $classes, $attributes, $format_inner_wrap );

	$render = '';

	$render .= "<{$tag} class=\"{$classes}\"{$attributes}>";
	$render .= ( $inner_wrap ) ? "<div class=\"{$name}__inner\">" : ''; 

	foreach ( get_component_components($name) as $component) {
		$render .= render_component( $component );
		// $content .= $component;
	}

	$render .= ( $inner_wrap ) ? "</div>" : '';
	$render .= "</{$tag}>";

	echo $render;
}



// /**
//  * Load Templates based on hierarchy (for use inside theme)
//  *
//  * It relies on the Hybrid View template loader and hierarchy
//  */
// function load_template( $data = null ) {
// 	\Hybrid\View\display( 'templates', \Hybrid\Template\hierarchy(), $data );
// }

// /**
//  * Load component
//  */
// function load_component( $component, $data = [] ) {

// 	// Load component
// 	require( get_file_path( 'lib/templating/components/' . $component . '.php' ) );
// }

// /**
//  * Load components
//  */
// function load_components( $components ) {
// 	if ( is_array($components) ) {

// 		foreach ($components as $component) {

// 			load_component( $component, [] );
		
// 		}
// 	}
// }

// function remove_component( $components, $name ) {
// 	if (($key = array_search($name, $components)) !== false) {
// 	    unset($components[$key]);
// 	}
// 	return $components;
// }
