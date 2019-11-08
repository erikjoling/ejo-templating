<?php
/**
 * Templating functions.
 */

namespace Ejo\Tmpl;

function start_engine() {
	require_once( 'template-index.php' );
}


function register_component( $component ) {
	global $ejo_tmpl_components;

	$component = [
		'name' => 'component-name',
		'tag' => 'div',
		'inner' => true,
		'extra_classes' => [],
		'attributes' => [],
	];


}







/**
 * Load Templates based on hierarchy (for use inside theme)
 *
 * It relies on the Hybrid View template loader and hierarchy
 */
function load_template( $data = null ) {
	\Hybrid\View\display( 'templates', \Hybrid\Template\hierarchy(), $data );
}

/**
 * Load component
 */
function load_component( $component, $data = [] ) {

	// Load component
	require( get_file_path( 'lib/templating/components/' . $component . '.php' ) );
}

/**
 * Load components
 */
function load_components( $components ) {
	if ( is_array($components) ) {

		foreach ($components as $component) {

			load_component( $component, [] );
		
		}
	}
}

function remove_component( $components, $name ) {
	if (($key = array_search($name, $components)) !== false) {
	    unset($components[$key]);
	}
	return $components;
}
