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
 * Setting up component
 */
function setup_component( $name ) {

	// Sanitize component data
	$name = esc_html( $name );

	$component = apply_filters( "ejo/tmpl/component/{$name}", [], $name );

	return [
		'name'    => $name,
		'element' => (isset($component['element'])) ? setup_component_element($component['element']) : false,
		'content' => (isset($component['content'])) ? setup_component_content($component['content']) : false
	];
}

function setup_component_element( $element ) {

	// If element is specified process it
	if ( is_array($element) ) {

		// Merge/replace defaults with the component
		$element = wp_parse_args( $element, [
			'tag'           => 'div',
			'extra_classes' => [],
			'attributes'    => [],
			'inner_wrap'    => false,
			'force_display' => false,
			'bem_block'     => true,
			'bem_element'   => false,
		] );

		// Sanitize
		$element['tag']           = esc_html( $element['tag'] );
		$element['extra_classes'] = (array) $element['extra_classes'];
		$element['attributes']    = (array) $element['attributes'];
		$element['inner_wrap']    = !! $element['inner_wrap'];
		$element['force_display'] = !! $element['force_display'];
	}

	return $element;
}

function setup_component_content( $content ) {

	$_content = '';

	// Function
	if ( is_string($content) ) {
		$_content = $content;
	}
	// Array of content
	elseif ( is_array($content) ) {

		$_content = [];
		foreach ( $content as $subcomponent ) {
			$_content[] = setup_component( $subcomponent );
		}
	}

	return $_content;
}

/**
 * Rendering
 */
function render_component( $component ) {

	// Sanitize component data
	$name = esc_html( $component['name'] );

	// // Prevent a component to be a child itself (infinite loop)
	// if ( is_component_child($name) ) {
	// 	return '';
	// }

	// Setup element
	$element = $component['element'];
	$content = $component['content'];

	// Setup render
	$render = '';

	if ( is_string($content) ) {

		$render .= call_user_func( $content );
	}
	elseif ( is_array($content) ) {

		// Only add current component as parent if it's defined as a BEM-block
		if ( $element['bem_block'] ) {
			add_current_component_as_parent($name);
		}

		foreach ( $content as $inner_component ) {
			$render .= render_component( $inner_component );
		}

		// Only remove current component as parent if it's defined as a BEM-block
		if ( $element['bem_block'] ) {
			remove_current_component_as_parent($name);
		}
	}

	// If we have a render or display is forced, wrap element around render
	if ( $render || $element['force_display'] ) {		
		$render = sprintf( render_element_format( $element, $name ), $render );
	}

	return $render;
}

function render_element_format( $element, $name ) {

	// Setup render
	$render_format = '%s';

	// Start rendering the element which wraps around the content
	if ($element) {

		$render_format_inner_wrap = '%s';

		if ( $element['inner_wrap']	) {

			$bem_block = get_bem_block( $element['bem_block'], $name );

			// Decide the classname of 'inner' based on whether it's a BEM-block
			$inner_class = ( $bem_block ) ? "{$bem_block}__inner" : 'inner';

			// Setup inner wrap render format
			$render_format_inner_wrap = sprintf( '<div class="%s">%%s</div>', $inner_class );
		}


		// Setup render format
		$render_format = sprintf( 
			'<%1$s class="%2$s"%3$s>%4$s</%1$s>', 
			$element['tag'], 
			render_element_classes($element, $name), 
			render_attr($element['attributes']), 
			$render_format_inner_wrap 
		);
	}

	return $render_format;
}

function render_element_classes( $element, $name ) {

	$classes = [];

	$bem_block = get_bem_block( $element['bem_block'], $name );
	$bem_element = get_bem_element( $element['bem_element'], $bem_block, $name );

	if ($bem_block) {
		$classes[] = $bem_block;
	}

	if ($bem_element) {
		$classes[] = $bem_element;
	}

	$classes += $element['extra_classes'];
	$classes = render_classes($classes);

	return $classes;
}

function get_bem_block( $bem_block, $name ) {

	$_bem_block = false;

	if ( is_string($bem_block) && $bem_block != '' ) {
		$_bem_block = $bem_block;
	}
	elseif ( $bem_block === true ) {
		$_bem_block = $name;
	}

	return $_bem_block;
}

function get_bem_element( $bem_element, $bem_block, $name ) {

	$_bem_element = false;

	// Only do stuff with BEM element if it has a BEM block parent
	if ( $bem_element ) {

		$bem_block_parent = get_current_component_parent();

		if ($bem_block_parent) {

			// If bem_element is set to true automatically set bem_block as bem_element
			if ( $bem_element === true ) {
				$bem_element = $bem_block ?? $name;
			}

			// Add BEM element as class
			$_bem_element = "{$bem_block_parent}__{$bem_element}";
		}
	}

	return $_bem_element;
}


function get_components() {
	global $ejo_tmpl_components;

	return $ejo_tmpl_components ?? [];
}

function set_components($components) {
	global $ejo_tmpl_components;

	$ejo_tmpl_components = $components ?? [];
}

/**
 * Get component parents
 */
function get_component_parents() {
	global $ejo_tmpl_component_parents;

	// Make sure it's always an array
	if (! is_array($ejo_tmpl_component_parents)) {
		$ejo_tmpl_component_parents = [];
	}

	return $ejo_tmpl_component_parents;
}

function is_component_child( $name ) {
	return in_array($name, get_component_parents());
}

function get_current_component_parent() {
	$parents = get_component_parents();

	return end($parents); reset($parents);
}

function add_current_component_as_parent( $name ) {
	$parents = get_component_parents();
	$parents[] = $name;

	$GLOBALS['ejo_tmpl_component_parents'] = $parents;
	// ksort($ejo_tmpl_component_parents);
}

function remove_current_component_as_parent( $name ) {
	$parents = get_component_parents();	
	$parents = remove_value_from_array( $parents, $name );

	$GLOBALS['ejo_tmpl_component_parents'] = $parents;
	// ksort($ejo_tmpl_component_parents);
}


function component_prepend( &$components, $value, $prepend_before = null ) {
	$components = ( is_array($components) ) ? $components : [];

	if ($prepend_before) {
		$components = array_insert_before($components, $prepend_before, $value);
	}
	else {
		array_unshift($components, $value);
	}
}

function component_append( &$components, $value, $prepend_after = null ) {
	$components = ( is_array($components) ) ? $components : [];

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
