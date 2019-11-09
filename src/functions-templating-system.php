<?php
/**
 * Templating functions.
 */

namespace Ejo\Tmpl;

function start_engine() {
	setup_components();
}

function setup_components() {
	require_once( 'components.php' );
}

/**
 * Render component
 *
 * Note: A component should be registered first
 */
function render_component( $name ) {

	global $ejo_component_parents;

	/**
	 * Setup parent add start of rendering
	 */
	if ( empty( $ejo_component_parents ) ) {
		$ejo_component_parents = [];
	}
	
	$component = apply_filters( "ejo/tmpl/{$name}", [] );

	// Process component data
	$name    = esc_html( $name );
	$element = $component['element'] ?? false;
	$content = $component['content'] ?? null;

	// If element is specified process it
	if ( is_array($element) ) {
		$element_defaults = [
			'tag'           => 'div',
			'extra_classes' => [],
			'attributes'    => [],
			'inner_wrap'    => false,
			'force_display' => false,
		];

		// Merge/replace defaults with the component
		$element = array_replace_recursive( $element_defaults, $element );

		// Sanitize
		$element['tag']           = esc_html( $element['tag'] );
		$element['classes']       = trim( $name . ' ' . render_classes($element['extra_classes']) );
		$element['attributes']    = render_attr( $element['attributes'] );
		$element['inner_wrap']    = !! $element['inner_wrap'];
		$element['force_display'] = !! $element['force_display'];
	}

	/**
	 * This is for component-functions to directly render content
	 */
	$content = apply_filters( "ejo/tmpl/{$name}/content", $content );

	// Setup content render
	$content_render = '';

	if ( is_string($content) ) {

		$content_render .= $content;
	}
	elseif ( is_array($content) ) {

		// Add parent before loading components
		add_current_component_as_parent($name);

		foreach ( $content as $inner_component ) {
			$content_render .= render_component( $inner_component );
		}

		// Remove parent before loading components
		remove_current_component_as_parent($name);
	}

	if ( ! $content_render && ! $element['force_display'] ) {
		return '';
	}
	else {
		return sprintf( render_element_format( $element, $name ), $content_render );
	}
}

function render_element_format( $element, $name ) {

	// Setup render
	$render_format = '%s';

	// Start rendering the element which wraps around the content
	if ($element) {

		// Setup inner wrap render format
		$render_format_inner_wrap = ( $element['inner_wrap'] ) ? sprintf( '<div class="%s">%%s</div>', "{$name}__inner" ) : '%s'; 

		// Setup render format
		$render_format = sprintf( 
			'<%1$s class="%2$s"%3$s>%4$s</%1$s>', 
			$element['tag'], 
			$element['classes'], 
			$element['attributes'], 
			$render_format_inner_wrap 
		);
	}

	return $render_format;
}

function get_component_parents() {
	global $ejo_component_parents;

	return $ejo_component_parents;
}

function add_current_component_as_parent( $name ) {
	global $ejo_component_parents;

	$ejo_component_parents[] = $name;
	// ksort($ejo_component_parents);
}

function remove_current_component_as_parent( $name ) {
	global $ejo_component_parents;

	$ejo_component_parents = remove_value_from_array( $ejo_component_parents, $name );
	// ksort($ejo_component_parents);
}

function component_prepend( &$components, $value, $prepend_before = null ) {
	if ($prepend_before) {
		$components = array_insert_before($components, $prepend_before, $value);
	}
	else {
		array_unshift($components, $value);
	}
}

function component_append( &$components, $value, $prepend_after = null ) {
	if ($prepend_after) {
		$components = array_insert_after($components, $prepend_after, $value);
	}
	else {
		array_push($components, $value);
	}
}

function component_remove( &$components, $lookup_value ) {
	$components = remove_value_from_array($components, $lookup_value);
}

function array_insert_before( array $array, $lookup_value, $insert_value ) {

	$index = array_search( $lookup_value, $array );
	$index = false === $index ? 0 : $index;
	return array_merge( array_slice( $array, 0, $index ), [$insert_value], array_slice( $array, $index ) );
}

function array_insert_after( array $array, $lookup_value, $insert_value ) {

	$index = array_search( $lookup_value, $array );
	$index = false === $index ? count( $array ) : $index + 1;
	return array_merge( array_slice( $array, 0, $index ), [$insert_value], array_slice( $array, $index ) );
}

function remove_value_from_array( array $array, $value ) {

	if (($key = array_search($value, $array)) !== false) {
	    unset($array[$key]);
	}

	return $array;
}