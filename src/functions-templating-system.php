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

function load_template() {
	require_once( 'template-index.php' );
}

function register_component( $name, $component = [] ) {
	global $ejo_components;

	$ejo_components[$name] = $component;
}

function get_components() {
	global $ejo_components;

	return $ejo_components ?? [];
}

function get_component( $name ) {
	global $ejo_components;

	return $ejo_components[$name] ?? [];
}

function get_component_element( $name ) {
	return get_component($name)['element'] ?? [];
}

function get_component_content( $name ) {
	return get_component($name)['content'] ?? [];
}

function get_component_parents( $name ) {
	return get_component($name)['parents'] ?? [];
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
function render_component( $name, $parents = [] ) {

	if ( ! is_registered_component( $name ) ) {
		return;
	}
	
	$component_defaults = [
		'name'    => $name,
		'element' => [
			'tag'           => false,
			'extra_classes' => [],
			'attributes'    => [],
			'inner_wrap'    => false,
			'force_display' => false,
		],
		'content' => [],
		'parents' => []
	];

	// Get component
	$component = get_component($name);
	
	// Add parents to component
	$component['parents'] = $parents;

	// Merge/replace defaults with the component
	$component = array_replace_recursive( $component_defaults, $component );

	// Allow component to be filterable
	$component = apply_filters( "ejo/tmpl/{$name}", $component );
	// $component['element'] = apply_filters( "ejo/tmpl/{$name}/element", $component['element'], $component );
	// $component['content'] = apply_filters( "ejo/tmpl/{$name}/content", $component['content'], $component );

	// log($component);

	// Process component data
	$name                = esc_html( $name );
	$content             = $component['content'];
	$parents             = $component['parents'];

	if ($component['element']) {
		$element = [
			'tag'           => esc_html( $component['element']['tag'] ),
			'classes'       => trim( $name . ' ' . render_classes($component['element']['extra_classes']) ),
			'attributes'    => render_attr( $component['element']['attributes'] ),
			'inner_wrap'    => !! $component['element']['inner_wrap'],
			'force_display' => !! $component['element']['force_display'],
		];
	}
	else {
		$element = false;
	}

	// log("Component `$name`. Below its ancestory...");
	// log($parents);
	// log('Inner Components:');
	// log($data['contents']);


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

	$_content = '';

	foreach ( $content as $content_component ) {

		$_parents = $parents;
		$_parents[] = $name;

		if ( is_registered_component( $content_component ) ) {
			$_content .= render_component( $content_component, $_parents );
		}
		else {

			// Setup function with or without namespace
			if ( '\\' === substr( $content_component, 0, 1 ) ) {
				$function = $content_component;
			}
			else {
				$function = __NAMESPACE__ . '\\' . $content_component;
			}

			if (function_exists($function)) {
				$_content .= $function();
			}
		}

		// elseif ( 'fn:' === substr( $content, 0, 3 ) ) {
		// 	$function = __NAMESPACE__ . '\\' . substr($content, 3, strlen($content));

		// 	// log($function);
		// 	$_content .= $function();
		// }
	}

	if ( ! $_content && ! $element['force_display'] ) {
		return '';
	}
	else {
		return sprintf( $render_format, $_content );
	}
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