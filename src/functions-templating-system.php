<?php
/**
 * Templating functions.
 */

namespace Ejo\Tmpl;

function start_engine() {

	register_component( 'site',  		['tag' => 'div', 'inner_wrap' => true] );
	register_component( 'site-header',  ['tag' => 'header', 'inner_wrap' => true] );
	register_component( 'site-main',    ['tag' => 'main', 'inner_wrap' => true] );
	register_component( 'site-footer',  ['tag' => 'footer', 'inner_wrap' => true] );
	register_component( 'page',         ['tag' => 'article', 'inner_wrap' => true] );
	register_component( 'page-header',  ['tag' => 'header', 'inner_wrap' => true] );
	register_component( 'page-content', ['tag' => 'div', 'inner_wrap' => true] );
	register_component( 'page-footer',  ['tag' => 'footer', 'inner_wrap' => true] );

	// add_to_component( 'site', [ 'site-header' ] );
	add_to_component( 'site', [ 'site-header', 'site-main', 'site-footer' ] );
	add_to_component( 'site-main', [ 'page' ] );
	add_to_component( 'page', [ 'fn:the_post', 'page-header', 'page-content', 'page-footer' ] );
	add_to_component( 'site-header', [ 'fn:render_site_branding' ] );
	add_to_component( 'page-header', [ 'fn:render_page_title' ] );
	add_to_component( 'page-content', [ 'fn:render_page_content' ] );

	log(get_components());
}

function load_template() {
	require_once( 'template-index.php' );	
}


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
function render_component( $name ) {

	if ( ! is_registered_component( $name ) ) {
		return;
	}

	$defaults = [
		'tag'              => 'div',
		'inner_wrap'       => false,
		'extra_classes'    => [],
		'attributes'       => [],
		'inner_components' => [],
	];

	// Setup component
	$data = array_merge( $defaults, get_component($name) );
	$data = apply_filters( "ejo/tmpl/{$name}", $data );

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

			log($function);
			$inner_content .= $function();
		}
		else {
			$inner_content .= render_component( $inner_component );
		}

	}

	return sprintf( $format, $inner_content );
}
