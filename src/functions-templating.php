<?php
/**
 * Templating functions.
 */

namespace Ejo\Base;

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
