<?php
/**
 * Composition Class
 */

namespace Ejo\Templating;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Composition Class
 *
 * Class Type: Static
 */
final class Composition {

	/**
     * Component ancestors
     *
     * @var array
     */
    private static $ancestors = [];

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

		log( 'component: ' . $name );
		log( 'parent: ' . static::get_parent() );
		log( '' );

		// Let other scripts setup or manipulate the component
		//
		// Note: sometimes the filter only runs a function without returning a value
		//		 For example: the-post --> the_post();
		$component = apply_filters( "ejo/composition/component/{$name}", [], $name );

		// Process component parts
		$element = $component['element'] ?? false;
		$content = $component['content'] ?? false;

		// Setup render
		$render = '';
		
		// Only do stuff if $element and $content are not false
		if ( $element || $content ) {

			// Setup element
			$element = static::setup_component_element($element, $name);

			// Component should be a parent if it is a BEM block
			$parent = static::get_bem_block( $element['bem_block'], $element['name'] );

			// Only add current component as parent if it's defined as a BEM-block
			if ( $parent ) {
				static::add_parent($parent);
			}

			// Setup render
			$render = static::render_component_content( $content );

			// Only remove current component as parent if it's defined as a BEM-block
			if ( $parent ) {
				static::remove_parent($parent);
			}

			// If we have a render or display is forced, wrap element around render
			if ( $element && ( $render || $element['force_display'] ) ) {		
				$render = static::render_component_wrapped( $element, $render );
			}
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
				'attr'    		=> [],
				'inner_wrap'    => false,
				'force_display' => false,
				'bem_block'     => true,
				'bem_element'   => false,
			] );

			// Sanitize
			$element['name']          = esc_html( $element['name'] );
			$element['tag']           = esc_html( $element['tag'] );
			$element['extra_classes'] = (array) $element['extra_classes'];
			$element['attr']    	  = (array) $element['attr'];
			$element['inner_wrap']    = !! $element['inner_wrap'];
			$element['force_display'] = !! $element['force_display'];
		}

		return $element;
	}

	private static function render_component_content( $content ) {

		// Setup render
		$render = '';

		if ( is_string($content) ) {

			$render .= $content;
		}
		elseif ( is_array($content) ) {

			foreach ( $content as $inner_component ) {
				$render .= static::render_component( $inner_component );
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
				render_attr($element['attr']), 
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
		$classes = render_classes($classes);

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

			$bem_block_parent = static::get_parent();

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

	private static function get_ancestors() {
		return static::$ancestors ?? [];
	}

	private static function set_ancestors( $ancestors ) {
		if ( is_array($ancestors) ) {
			static::$ancestors = $ancestors;
		}
	}

	public static function has_ancestor( $name ) {
		$ancestors = static::get_ancestors();

		return ( ($key = array_search($name, $ancestors)) !== false );
	}

	public static function get_parent() {
		$ancestors = static::get_ancestors();

		return end($ancestors); reset($ancestors);
	}

	private static function add_parent( $name ) {
		$ancestors = static::get_ancestors();

		// Add parent
		$ancestors[] = $name;

		// Set ancestors
		static::set_ancestors($ancestors);
	}

	private static function remove_parent( $name ) {
		$ancestors = static::get_ancestors();

		// Remove parent
		$ancestors = remove_value_from_array($ancestors, $name);

		// Set ancestors
		static::set_ancestors($ancestors);
	}


	public static function component_add_before( &$components, $component_id, $target_component_id = null ) {
		$components = ( is_array($components) ) ? $components : [];

		if ($target_component_id) {
			$components = array_insert_before($components, $target_component_id, $component_id);
		}
		else {
			array_unshift($components, $component_id);
		}
	}

	public static function component_add_after( &$components, $component_id, $target_component_id = null ) {
		$components = ( is_array($components) ) ? $components : [];

		if ($target_component_id) {
			$components = array_insert_after($components, $target_component_id, $component_id);
		}
		else {
			array_push($components, $component_id);
		}
	}

	public static function component_move_before( &$components, $component_id, $target_component_id = null ) {
		$components = ( is_array($components) ) ? $components : [];

		// Find component
		$index = array_search( $component_id, $components );

		// Only move component if it is found
		if ($index !== false) {

			// First remove component
			static::component_remove($components, $component_id);

			// Then add component
			static::component_add_before($components, $component_id, $target_component_id);
		}
	}

	public static function component_move_after( &$components, $component_id, $target_component_id = null ) {
		$components = ( is_array($components) ) ? $components : [];

		// Find component
		$index = array_search( $component_id, $components );

		// Only move component if it is found
		if ($index !== false) {

			// First remove component
			static::component_remove($components, $component_id);

			// Then add component
			static::component_add_after($components, $component_id, $target_component_id);
		}
	}

	public static function component_remove( &$components, $component_id ) {
		$components = remove_value_from_array($components, $component_id);
	}

	/**
	 * Setup Component
	 *
	 * We are using 
	 *
	 * @param string Name
	 * @param array Component (element, content)
	 */
	public static function setup_component( $name, $component ) {

		add_filter( "ejo/composition/component/{$name}", $component, 10, 2 );
		
		// Passing an array as closure prevents directly calling a function when
		// setting up the component. When using a anonymous function it only
		// gets called when the filter is due

		// // If array then pass the component as a Closure
		// if ( is_array($component) ) {

		// 	add_filter( "ejo/composition/component/{$name}", function() use ($component) {
		// 		return $component;
		// 	});
		// }

		// // Else, we assume the component is a function
		// else {
		// 	add_filter( "ejo/composition/component/{$name}", $component, 10, 2 );
		// }

	}
}




