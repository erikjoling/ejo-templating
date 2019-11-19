<?php
/**
 * Helpers
 */

namespace Ejo\Tmpl;


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

/** 
 * Render attributes
 *
 * Example ['lang' => 'nl'] becomes lang="nl"
 *
 * @param 	array
 * @return 	string
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