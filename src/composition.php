<?php
/**
 * Composition Class
 */

namespace Ejo;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Composition Class
 *
 * Class Type: Static
 */
final class Composition {

	/**
     * Component parents
     *
     * @var array
     */
    private static $parents = [];

    /**
     * Constructor method.
     *
     * @return void
     */
    private function __construct() {}

    /**
	 * Display site
	 *
	 * @return 	void
	 */
	public static function display() {

		echo static::render_component( 'site' );
	}

	/**
	 * Render site
	 *
	 * @param 	String name
	 * @return 	String compontent
	 */
	public static function render() {

		return static::render_component( 'site' );
	}

	/**
	 * Display component
	 *
	 * @param 	String name
	 * @return 	void
	 */
	public static function display_component( $name ) {

		echo static::render_component($name);
	}

	/**
	 * Render component
	 *
	 * @param String name
	 * @return String component
	 */
	public static function render_component( $name ) {

		// Sanitize the name
		$name = esc_html( $name );

		// Setup component
		$component = apply_filters( "ejo/composition/component/{$name}", [], $name );

		// Process component parts
		$element = $component['element'] ?? false;
		$content = $component['content'] ?? false;

		// Setup element
		$element = static::setup_component_element($element, $name);

		// Component should be a parent if it is a BEM block
		$is_parent = !! $element['bem_block'];

		// Setup render
		$render = static::render_component_content( $content, $is_parent, $name );

		// If we have a render or display is forced, wrap element around render
		if ( $element && ( $render || $element['force_display'] ) ) {		
			$render = static::render_component_wrapped( $element, $render );
		}

		return $render;
	}

	private static function setup_component_element( $element, $name ) {

		// If element is specified process it
		if ( is_array($element) ) {

			// Merge/replace defaults with the component
			$element = wp_parse_args( $element, [
				'name'          => $name,
				'tag'           => 'div',
				'extra_classes' => [],
				'attributes'    => [],
				'inner_wrap'    => false,
				'force_display' => false,
				'bem_block'     => true,
				'bem_element'   => false,
			] );

			// Sanitize
			$element['name']          = esc_html( $element['name'] );
			$element['tag']           = esc_html( $element['tag'] );
			$element['extra_classes'] = (array) $element['extra_classes'];
			$element['attributes']    = (array) $element['attributes'];
			$element['inner_wrap']    = !! $element['inner_wrap'];
			$element['force_display'] = !! $element['force_display'];
		}

		return $element;
	}

	private static function render_component_content( $content, $is_parent, $name ) {
		// Setup render
		$render = '';

		if ( is_string($content) ) {

			$render .= $content;
		}
		elseif ( is_array($content) ) {

			// Only add current component as parent if it's defined as a BEM-block
			if ( $is_parent ) {
				static::add_parent($name);
			}

			foreach ( $content as $inner_component ) {
				$render .= static::render_component( $inner_component );
			}

			// Only remove current component as parent if it's defined as a BEM-block
			if ( $is_parent ) {
				static::remove_parent($name);
			}
		}

		return $render;
	}

	private static function render_component_wrapped( $element, $render ) {

		// Setup render
		$render_format = '%s';

		// Start rendering the element which wraps around the content
		if ($element) {

			$render_format_inner_wrap = '%s';

			if ( $element['inner_wrap']	) {

				$bem_block = static::get_bem_block( $element['bem_block'], $element['name'] );

				// Decide the classname of 'inner' based on whether it's a BEM-block
				$inner_class = ( $bem_block ) ? "{$bem_block}__inner" : 'inner';

				// Setup inner wrap render format
				$render_format_inner_wrap = sprintf( '<div class="%s">%%s</div>', $inner_class );
			}


			// Setup render format
			$render_format = sprintf( 
				'<%1$s class="%2$s"%3$s>%4$s</%1$s>', 
				$element['tag'], 
				static::render_element_classes($element), 
				static::render_attr($element['attributes']), 
				$render_format_inner_wrap 
			);
		}

		return sprintf( $render_format, $render );
	}

	private static function render_element_classes( $element ) {

		$classes = [];

		$bem_block   = static::get_bem_block( $element['bem_block'], $element['name'] );
		$bem_element = static::get_bem_element( $element['bem_element'], $bem_block, $element['name'] );

		if ($bem_block) {
			$classes[] = $bem_block;
		}

		if ($bem_element) {
			$classes[] = $bem_element;
		}

		$classes += $element['extra_classes'];
		$classes = static::render_classes($classes);

		return $classes;
	}


	private static function get_bem_block( $bem_block, $name ) {

		$_bem_block = false;

		if ( is_string($bem_block) && $bem_block != '' ) {
			$_bem_block = $bem_block;
		}
		elseif ( $bem_block === true ) {
			$_bem_block = $name;
		}

		return $_bem_block;
	}

	private static function get_bem_element( $bem_element, $bem_block, $name ) {

		$_bem_element = false;

		// Only do stuff with BEM element if it has a BEM block parent
		if ( $bem_element ) {

			$bem_block_parent = static::get_current_parent();

			if ($bem_block_parent) {

				// If bem_element is set to true automatically set bem_block as bem_element
				if ( $bem_element === true ) {
					$bem_element = $bem_block ?? $name;
				}

				// Add BEM element as class
				$_bem_element = "{$bem_block_parent}__{$bem_element}";
			}
		}

		return $_bem_element;
	}

	private static function get_parents() {
		return static::$parents ?? [];
	}

	private static function set_parents( $parents ) {
		if ( is_array($parents) ) {
			static::$parents = $parents;
		}
	}

	private static function is_parent( $name ) {
		$parents = static::get_parents();

		return ( ($key = array_search($name, $parents)) !== false );
	}

	private static function get_current_parent() {
		$parents = static::get_parents();

		return end($parents); reset($parents);
	}

	private static function add_parent( $name ) {
		$parents = static::get_parents();

		// Add parent
		$parents[] = $name;

		// Set parents
		static::set_parents($parents);
	}

	private static function remove_parent( $name ) {
		$parents = static::get_parents();

		if (($key = array_search($name, $parents)) !== false) {
		    unset($parents[$key]);

		    // Set parents
			static::set_parents($parents);		
		}
	}

	/** 
	 * Render classes
	 *
	 * Example ['class-1', 'class-2'] becomes "class-1 class-2"
	 *
	 * @param array
	 * @return string
	 */
	public static function render_classes( $classes ) {
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
	public static function render_attr( $attr ) {
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
}

