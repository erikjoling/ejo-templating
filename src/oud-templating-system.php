<?php
/**
 * Templating functions.
 */

namespace Ejo\Templating;

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

	// Sanitize component data
	$name = esc_html( $name );

	// Prevent a component to be a child itself (infinite loop)
	if ( is_component_child($name) ) {
		return '';
	}

	// Allow filters
	$element = apply_filters( "ejo/tmpl/{$name}/element", false );
	$content = apply_filters( "ejo/tmpl/{$name}/content", null );

	// If element is specified process it
	if ( is_array($element) ) {
		$element_defaults = [
			'tag'           => 'div',
			'extra_classes' => [],
			'attributes'    => [],
			'inner_wrap'    => false,
			'force_display' => false,
			'bem_block'     => $name,
			'bem_element'   => false,
		];

		// Merge/replace defaults with the component
		$element = array_replace( $element_defaults, $element );

		// Sanitize
		$element['tag']           = esc_html( $element['tag'] );
		$element['extra_classes'] = (array) $element['extra_classes'];
		$element['attributes']    = render_attr( $element['attributes'] );
		$element['inner_wrap']    = !! $element['inner_wrap'];
		$element['force_display'] = !! $element['force_display'];

		// Setup classes
		$element['classes'] = [];

		if ($element['bem_block']) {
			$element['classes'][] = $element['bem_block'];
		}

		// Only do stuff with BEM element if it has a BEM block parent
		if ( $element['bem_element'] ) {

			$bem_element = $element['bem_element'];

			// If bem_element is set to true automatically set bem_block as bem_element
			if ( $element['bem_element'] === true ) {
				$bem_element = $element['bem_block'] ? $element['bem_block'] : $name;
			}

			// Add BEM element as class
			$element['classes'][] = get_current_component_parent() . "__{$bem_element}";
		}

		$element['classes'] += $element['extra_classes'];
		$element['classes'] = render_classes($element['classes']);

		// log($name);
		// log($element);
		// log(get_component_parents());
		// log('----------------------------');
	}


	// Setup content render
	$content_render = '';

	if ( is_string($content) ) {

		$content_render .= $content;
	}
	elseif ( is_array($content) ) {


		// Only add current component as parent if it's defined as a BEM-block
		if ( $element['bem_block'] ) {
			add_current_component_as_parent($name);
		}

		foreach ( $content as $inner_component ) {
			$content_render .= render_component( $inner_component );
		}

		// Only remove current component as parent if it's defined as a BEM-block
		if ( $element['bem_block'] ) {
			remove_current_component_as_parent($name);
		}
	}

	if ( ! $content_render && ! $element['force_display'] ) {
		return '';
	}
	else {
		return sprintf( render_element_format( $element, $name ), $content_render );
	}
}

function render_element_format( $element ) {

	// Setup render
	$render_format = '%s';

	// Start rendering the element which wraps around the content
	if ($element) {

		$render_format_inner_wrap = '%s';

		if ( $element['inner_wrap']	) {

			// Decide the classname of 'inner' based on whether it's a BEM-block
			$inner_class = ( $element['bem_block'] ) ? "{$element['bem_block']}__inner" : 'inner';

			// Setup inner wrap render format
			$render_format_inner_wrap = sprintf( '<div class="%s">%%s</div>', $inner_class );
		}


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
	global $ejo_tmpl_component_parents;

	$ejo_tmpl_component_parents[] = $name;
	// ksort($ejo_tmpl_component_parents);
}

function remove_current_component_as_parent( $name ) {
	global $ejo_tmpl_component_parents;

	$ejo_tmpl_component_parents = remove_value_from_array( $ejo_tmpl_component_parents, $name );
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