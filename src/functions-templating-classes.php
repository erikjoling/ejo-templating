<?php 
/** 
 * Classes stuff
 */

namespace Ejo\Tmpl;

// ================================================
// General functions for classes
// ================================================

/**
 * Outputs an escaped string of classes for use in HTML.
 *
 * Example ['class-1', 'class-2'] becomes "class-1 class-2"
 *
 * @param array
 * @return void
 */
function display_classes( $classes ) {

	echo render_classes( $classes );
}

/** 
 * Render classes
 *
 * Example ['class-1', 'class-2'] becomes "class-1 class-2"
 *
 * @param array
 * @return string
 */
function render_classes( $classes ) {
	$html = '';

	foreach ( $classes as $class ) {

		$esc_class = esc_html( $class );

		$html .= " $esc_class";
	}

	return trim( $html );
}

// ================================================
// <Body> classes
// ================================================

/** 
 * Display Body classes
 */
function display_body_classes() {
	echo render_body_classes();
}

/** 
 * Render Body classes
 */
function render_body_classes() {
	return render_classes( \get_body_class() );
}
