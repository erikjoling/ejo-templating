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

		echo static::render();
	}

	/**
	 * Render site
	 *
	 * @param 	String name
	 * @return 	String component
	 */
	public static function render() {

		$component = [
			'name'	  => 'site',
			// 'element' => [ 'inner_wrap' => true ],
			// 'content' => [ 'site-header', 'site-main', 'site-footer' ],
			// 'content' => 'hallo!',
			// 'element' => false,
		];

		// return static::render_component( 'site' );
		return static::render_component( $component );
	}

	/**
	 * Display component
	 *
	 * @param 	String name
	 * @return 	void
	 */
	public static function display_component( $component ) {

		echo static::render_component( $component );
	}

	/**
	 * Render component
	 *
	 * @param 	string $component (name), array $component ([name, element, content])
	 * @return 	String $render
	 */
	public static function render_component( $component ) {
		log($component);
		
		// If component is array with only one record, then setup empty component with name
		$component = ( is_array($component) && count($component) == 1 && isset($component[0]) ) ? [ 'name' => $component[0] ] : $component;

		log($component);

		// We don't like empty component names
		if ( empty($component['name']) ) {
			return '';
		}

		// Sanitize the name and store it. It should not be changed
		$name = $component['name'] = esc_html( $component['name'] );

		// Setup component data for rendering
		$component = static::setup_component_settings($name, $component);

		// Add action in case we need to do some action in stead of filtering (like the_post())
		do_action( "ejo/composition/after_setup_component/{$name}", $name );

		// Process component parts
		$element = $component['element'] ?? [];
		$content = $component['content'] ?? [];

		// Setup render
		$render = '';

		log($content);
		
		// Only do stuff if $element and $content are not expicitely set as false
		if ( $element !== false || $content !== false ) {

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

	private static function setup_component_settings( $name, $component ) {

		// Setup component defaults
		$component_defaults = apply_filters( "ejo/composition/component_defaults/{$name}", [] );

		// If component is empty we set it to default false structure
		if ( empty($component_defaults) ) {
			$component_defaults = [ 'element' => false, 'content' => false ];
		}

		// Merge the component with the defaults
		$component = array_replace_recursive($component_defaults, $component);

		// Give themes and plugins opportunity to override passed component
		return apply_filters( "ejo/composition/component/{$name}", $component );
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

	/**
	 * Render the contents of a component
	 *
	 * @param 	string $content (output) | array $content (components)
	 * @return 	string $render
	 */ 
	private static function render_component_content( $content ) {

		// Setup render
		$render = '';

		/**
		 * Content could be a string, in which case we render it.
		 * And the content could be an array, in which case we assume
		 * it holds one or more components and we render them.
		 */
		if ( is_string($content) ) {
			$render .= $content;
		}
		elseif ( is_array($content) ) {
		
			// If the content part is a callback (string function, or array with class and function)
			if ( is_string($content[0]) || ( is_array($content[0]) && isset($content[0][1]) ) ) {

				// First part of content_part is callback, other entries are parameters
				$callback = array_shift($content);
				$params   = $content;

				if ( $params) {
					$render .= call_user_func_array( $callback, $params );
				}
				else {
					$render .= call_user_func( $callback );
				}
			}
			else {
				foreach ( $content as $content_part ) {
					$render .= static::render_component( $content_part );
				}
			}
		}

		return $render;
	}

	/**
	 * Wrap the render in an element
	 *
	 * @param 	array $element
	 * @param 	string $render
	 * @return 	string 
	 */ 
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

	/**
	 * Render the classes of the element
	 *
	 * @param 	array $element
	 * @return 	string classes
	 */ 
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

		$classes = array_merge($classes, $element['extra_classes']);
		$classes = render_classes($classes);

		return $classes;
	}



	// private static function is_content_component( $content_part ) {

	// 	return (is_string($content_part) || isset($content_part['element']) || isset($content_part['content']));
	// }

	// private static function is_content_callback( $content_part ) {

	// 	return isset($content_part['callback']);
	// }

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
		$ancestors = array_remove_value($ancestors, $name);

		// Set ancestors
		static::set_ancestors($ancestors);
	}

	/**
	 * Add a component before an other component in the content
	 * If no component is found it will be placed at the start
	 *
	 * @param array $components
	 * @param string $component | array $component
	 * @param string $target_component_name
	 *
	 * @return void
	 */
	public static function component_insert_before( &$components, $component, $target_component_name = null ) {
		$components = ( is_array($components) ) ? $components : [];

		// Find index
		$index = static::get_component_index( $components, $target_component_name );

		// Set offset at index or at start
		$offset = ($index !== false) ? $index : 0;

		// Wrap inside array to insert it correctly
		$component = [$component];

		// Insert
		$components = array_insert($components, $offset, $component);
	}

	public static function component_insert_after( &$components, $component, $target_component_name = null ) {
		$components = ( is_array($components) ) ? $components : [];

		// Find index
		$index = static::get_component_index( $components, $target_component_name );

		// Set offset after index or at end
		$offset = ($index !== false) ? $index + 1 : count( $components );

		// Wrap inside array to insert it correctly
		$component = [$component];

		// Insert
		$components = array_insert($components, $offset, $component);
	}

	public static function component_move_before( &$components, $component, $target_component_name = null ) {
		$components = ( is_array($components) ) ? $components : [];

		// Find the index of the component
		$component_index = static::get_component_index( $components, $component );

		if ( $component_index !== false ) {

			// Get component
			$component = $components[$component_index];

			// First remove component
			static::component_remove($components, $component);

			// Then add component
			static::component_insert_before($components, $component, $target_component_name);
		}
	}

	public static function component_move_after( &$components, $component, $target_component_name = null ) {
		$components = ( is_array($components) ) ? $components : [];

		// Find the index of the component
		$component_index = static::get_component_index( $components, $component );

		if ( $component_index !== false ) {

			// Get component
			$component = $components[$component_index];

			// First remove component
			static::component_remove($components, $component);

			// Then add component
			static::component_insert_after($components, $component, $target_component_name);
		}
	}

	/**
	 * Remove a component from a component list
	 *
	 * @param array $components
	 * @param string $component or array $component
	 *
	 * @return integer or false
	 */
	public static function component_remove( &$components, $component ) {

		// Setup component name
		$component_name = ( isset($component['name']) ) ? $component['name'] : $component;

		// Find index
		$index = static::get_component_index( $components, $component_name );

		// Remove
		if ( $index !== false ) {
			unset($components[$index]);
		}
	}

	/**
	 * Get the index of component
	 *
	 * @param array $components
	 * @param string $component_name
	 *
	 * @return integer or false
	 */
	private static function get_component_index( array $components, $component_name ) {

		if ( ! $component_name ) {
			return false;
		}

		// First try to find if we can find component just by name
		$index = array_get_index_by_value( $components, $component_name );

		// If we can't find name, then check for component
		if ( $index === false ) {
			
			// Check the component name of each component
			foreach ($components as $_index => $component) {

				if ( isset($component['name']) && $component['name'] == $component_name ) {
					$index = $_index;
					break;
				}
			}
		}

		return $index;
	}

	/**
	 * Component defaults
	 *
	 * @param string Name
	 * @param array Component (element, content)
	 */
	public static function setup_component_defaults( $name, $component ) {

		add_filter( "ejo/composition/component_defaults/{$name}", $component, 10, 2 );
	}

	/**
	 * Overwrite Component
	 *
	 * We are using 
	 *
	 * @param string Name
	 * @param array Component (element, content)
	 */
	public static function setup_component( $name, $component ) {

		add_filter( "ejo/composition/component/{$name}", $component, 10, 2 );
	}

	/**
	 * Easily hook into action hook
	 *
	 * @param string Name
	 * @param string or function
	 */
	public static function after_setup_component( $name, $function ) {

		add_action( "ejo/composition/after_setup_component/{$name}", $function );
	}

	/**
	 * Easily remove action
	 *
	 * @param string Name
	 * @param string or function
	 */
	public static function after_setup_component_remove( $name, $function ) {

		if ( is_string($function) || is_array($function) ) {
			remove_action( "ejo/composition/after_setup_component/{$name}", $function );
		}
	}
}




