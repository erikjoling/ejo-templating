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
	global $ejo_component_bem_blocks;

	/**
	 * Setup parent add start of rendering
	 */
	if ( empty( $ejo_component_parents ) ) {
		$ejo_component_parents = [];
	}

	/**
	 * Setup bem block add start of rendering
	 */
	if ( empty( $ejo_component_bem_blocks ) ) {
		$ejo_component_bem_blocks = [];
	}

	/**
	 * Prevent a component to be it's own parent
	 */ 
	if ( has_component_parent($name) ) {
		log("Fout: $name is eigen ouder");
		return '';
	}

	// Process component data
	$name    = esc_html( $name );
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
			'is_bem_block'  => false,
			'bem_element' => false,
		];

		// Merge/replace defaults with the component
		$element = array_replace_recursive( $element_defaults, $element );

		// Sanitize
		$element['tag']           = esc_html( $element['tag'] );
		$element['extra_classes'] = (array) $element['extra_classes'];
		$element['attributes']    = render_attr( $element['attributes'] );
		$element['inner_wrap']    = !! $element['inner_wrap'];
		$element['force_display'] = !! $element['force_display'];
		$element['bem_block']     = $name;
		$element['is_bem_block']  = !! $element['is_bem_block'];
		$element['bem_element']   = $element['bem_element'];

		// Setup classes
		$element['classes'] = [$element['bem_block']];
		if ( $element['bem_element'] && has_component_bem_block_parent() ) {
			$element['classes'][] = get_current_component_bem_block_parent() . "__{$element['bem_element']}";
		}
		$element['classes'] += $element['extra_classes'];
		$element['classes'] = render_classes($element['classes']);

		log($name);
		log($element);
		log(get_component_bem_blocks());
		log('----------------------------');
	}


	// Setup content render
	$content_render = '';

	if ( is_string($content) ) {

		$content_render .= $content;
	}
	elseif ( is_array($content) ) {

		// Add parent before loading components
		add_current_component_as_parent($name);

		if ( $element['is_bem_block'] ) {
			add_current_component_as_bem_block($name);
		}

		foreach ( $content as $inner_component ) {
			$content_render .= render_component( $inner_component );
		}

		// Remove bem block
		remove_current_component_as_bem_block($name);

		// Remove parent
		remove_current_component_as_parent($name);
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

		// Setup inner wrap render format
		$render_format_inner_wrap = ( $element['inner_wrap'] ) ? sprintf( '<div class="%s">%%s</div>', "{$element['bem_block']}__inner" ) : '%s'; 

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

function has_component_parent( $parent ) {
	return in_array($parent, get_component_parents());
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

function get_component_bem_blocks() {
	global $ejo_component_bem_blocks;

	return $ejo_component_bem_blocks;
}


function has_component_bem_block_parent() {
	return ! empty(get_component_bem_blocks());
}

function get_current_component_bem_block_parent() {
	$bem_blocks = get_component_bem_blocks();

	return end($bem_blocks); reset($bem_blocks);
}


function add_current_component_as_bem_block( $name ) {
	global $ejo_component_bem_blocks;

	$ejo_component_bem_blocks[] = $name;
	// ksort($ejo_component_bem_blocks);
}

function remove_current_component_as_bem_block( $name ) {
	global $ejo_component_bem_blocks;

	$ejo_component_bem_blocks = remove_value_from_array( $ejo_component_bem_blocks, $name );
	// ksort($ejo_component_bem_blocks);
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