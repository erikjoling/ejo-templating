<?php
/**
 * Helpers
 */

namespace Ejo\Templating;


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

/**
 * Get the index of a value in an array
 *
 * @param $array An array to search
 * @param $target A value to find
 *
 * @return integer or false
 */
function array_get_index_by_value( array $array, $target ) {
	return array_search( $target, $array );
}

/**
 * Get the index of a key in an array
 *
 * @param $array An array to search
 * @param $target A key to find
 *
 * @return integer or false
 */
function array_get_index_by_key( array $array, $target ) {
	return array_search($target, array_keys($array), true);
}

/**
 * Inserts a new value before the value in the array.
 *
 * @param $array An array to insert in to.
 * @param $offset The offset to insert at.
 * @param $insert A value or value-pair to insert.
 *
 * @return The array with a value inserted
 */
function array_insert( array $array, $offset, $insert ) {

	// First make sure insert is an array
	$insert = (is_array($insert)) ? $insert : [$insert];

	// Return the array with inserted value
	return array_merge( array_slice( $array, 0, $offset ), $insert, array_slice( $array, $offset ) );
}

function array_remove_value( array $array, $value ) {

	if (($key = array_search($value, $array)) !== false) {
	    unset($array[$key]);
	}

	return $array;
}
