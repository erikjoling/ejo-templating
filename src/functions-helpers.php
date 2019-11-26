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

// function find_array_value( array $array, $lookup_value ) {
// 	$index = array_search( $lookup_value, $array );
	
// 	return false === $index ? 0 : $index;
// }

/**
 * Inserts a new value before the value in the array.
 *
 * @param $array An array to insert in to.
 * @param $lookup_value The value to insert before.
 * @param $insert_value A value or value-pair to insert.
 *
 * @return The array with a value inserted
 */
function array_insert_before_value( array $array, $lookup_value, $insert_value ) {

	// First make sure insert_value is an array
	$insert_value = (is_array($insert_value)) ? $insert_value : [$insert_value];

	// Find index
	$index = array_search( $lookup_value, $array );

	// If not found an index, then set offset to 0
	$offset = ($index !== false) ? $index : 0;

	// Return the array with inserted value
	return array_merge( array_slice( $array, 0, $offset ), $insert_value, array_slice( $array, $offset ) );
}

/**
 * Inserts a new value after the value in the array.
 *
 * @param $array An array to insert in to.
 * @param $lookup_value The value to insert after.
 * @param $insert_value A value or value-pair to insert.
 *
 * @return The array with a value inserted
 */
function array_insert_after_value( array $array, $lookup_value, $insert_value ) {

	// First make sure insert_value is an array
	$insert_value = (is_array($insert_value)) ? $insert_value : [$insert_value];

	// Find index
	$index = array_search( $lookup_value, $array );

	// Set offset after index or at end
	$offset = ($index !== false) ? $index + 1 : count( $array );

	// Return the array with inserted value
	return array_merge( array_slice( $array, 0, $offset ), $insert_value, array_slice( $array, $offset ) );
}

/**
 * Inserts a new value before a key in the array.
 *
 * @param $array An array to insert in to.
 * @param $key The key to insert before.
 * @param $insert_value A value or value-pair to insert.
 *
 * @return The array with a value inserted
 */
function array_insert_before_key( array $array, $key, $insert_value ) {

	log($key);
	log($insert_value);

	// First make sure insert_value is an array
	$insert_value = (is_array($insert_value)) ? $insert_value : [$insert_value];
	log($insert_value);

	// Get key index;
	$index = array_search($key, array_keys($array), true);

	// Set offset as index or at start
    $offset = ($index !== false) ? $index : 0;

    log( "offset $offset" );

	// Return the array with inserted value
	$array = array_slice( $array, 0, $offset ) + $insert_value + array_slice( $array, $offset );

    log( $array );

    return $array;

}

/**
 * Inserts a new value after a key in the array.
 *
 * @param $array An array to insert in to.
 * @param $key The key to insert after.
 * @param $insert_value A value or value-pair to insert.
 *
 * @return The array with a value inserted
 */
function array_insert_after_key( array $array, $key, $insert_value ) {

	// First make sure insert_value is an array
	$insert_value = (is_array($insert_value)) ? $insert_value : [$insert_value];

	// Get key index
	$index = array_search($key, array_keys($array), true);

	// Set offset after index or at end
    $offset = ($index !== false) ? $index + 1 : count( $array );

	// Return the array with inserted value
	return array_merge( array_slice( $array, 0, $offset ), $insert_value, array_slice( $array, $offset ) );
}

function array_remove_value( array $array, $value ) {

	if (($key = array_search($value, $array)) !== false) {
	    unset($array[$key]);
	}

	return $array;
}
