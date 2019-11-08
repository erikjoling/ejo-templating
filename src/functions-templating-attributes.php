<?php 
/** 
 * Attribute stuff
 */

namespace Ejo\Tmpl;

// ================================================
// General functions for attributes
// ================================================

/**
 * Outputs an escaped string of attributes for use in HTML.
 *
 * Example ['lang' => 'nl'] becomes lang="nl"
 *
 * @param array
 * @return void
 */
function display_attr( $attr ) {

	echo render_attr( $attr );
}

/** 
 * Render attributes
 *
 * Example ['lang' => 'nl'] becomes lang="nl"
 *
 * @param array
 * @return string
 */
function render_attr( $attr ) {
	$html = '';

	foreach ( $attr as $name => $value ) {

		$esc_value = '';

		// If the value is a link `href`, use `esc_url()`.
		if ( $value !== false && 'href' === $name ) {
			$esc_value = esc_url( $value );

		} elseif ( $value !== false ) {
			$esc_value = esc_attr( $value );
		}

		$html .= false !== $value ? sprintf( ' %s="%s"', esc_html( $name ), $esc_value ) : esc_html( " {$name}" );
	}

	return trim( $html );
}

// ================================================
// <HTML> attributes
// ================================================

/** 
 * Display HTML attributes
 */
function display_html_attr() {
	echo render_html_attr();
}

/** 
 * Render HTML attributes
 */
function render_html_attr() {
	return render_attr( get_html_attr() );
}
