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

		// return static::render_component( 'site' );
		return static::render_component( ['site'] );
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

		$component = static::setup_component_data($component);

		// Add action in case we need to do some action in stead of filtering (like the_post())
		do_action( "ejo/composition/before_render_component/{$component['name']}" );

		$render = static::setup_component_render($component);

		return $render;

	}

	/**
	 * Setup Component
	 *
	 * @param array [ 'component-name' ] or array [ name, container, content ]
	 *
	 * @return array fully setup component [ name, container, content ]
	 */
	private static function setup_component_data( $component ) {

		// If component is array with only one record, then setup empty component with name
		$component = ( is_array($component) && count($component) == 1 && isset($component[0]) ) ? [ 'name' => $component[0] ] : $component;

		// We don't like empty component names
		if ( empty($component['name']) ) {
			return '';
		}

		// The name is untouchable
		$name = esc_html($component['name']);

		// Setup component defaults
		$component_defaults = apply_filters( "ejo/composition/setup_component_defaults/{$name}", [
			'name'      => $name,
			'container' => [],
			'content'	=> [],
		] );

		// Make sure filtered component is how we like it
		$component_defaults = [
			'name'      => $name,
			'container' => $component_defaults['container'] ?? [],
			'content'	=> $component_defaults['content'] ?? [],
		];

		// Merge the component with the defaults
		$component = array_replace_recursive($component_defaults, $component);

		// Give themes and plugins opportunity to override passed component
		$component = apply_filters( "ejo/composition/setup_component/{$name}", $component );

		// Make sure filtered component is how we like it
		$component = [
			'name'      => $name,
			'container' => $component['container'] ?? [],
			'content'	=> $component['content'] ?? [],
		];

		if ( is_array($component['container']) || $component['container'] === true ) {

			// Convert true containers to empty array
			$component['container'] = ( ! is_array($component['container']) ) ? [] : $component['container'];

			// Merge/replace defaults with the component
			$component['container'] = wp_parse_args( $component['container'], [
				'tag'           => 'div',
				'extra_classes' => [],
				'attr'    		=> [],
				'inner_wrap'    => false,
				'force_display' => false,
				'bem_block'     => true,
				'bem_element'   => false,
			] );

			// Process

			// // Set bem_block to false if element is specified as bem_element and bem_block is not a string
			// if ( $component['container']['bem_element'] && !is_string($component['container']['bem_block']) ) {
			// 	$component['container']['bem_block'] =  false;
			// }

			// Others
			$component['container']['tag']           = esc_html( $component['container']['tag'] );
			$component['container']['extra_classes'] = (array) $component['container']['extra_classes'];
			$component['container']['attr']    	     = (array) $component['container']['attr'];
			$component['container']['inner_wrap']    = !! $component['container']['inner_wrap'];
			$component['container']['force_display'] = !! $component['container']['force_display'];
		}
		else {
			$component['container'] = false;
		}

		return $component;
	}

	/**
	 * Setup Component Render
	 *
	 * @param array [ 'component-name' ] or array [ name, container, content ]
	 *
	 * @return array fully setup component [ name, container, content ]
	 */
	private static function setup_component_render( $component ) {

		// Setup render
		$render = '';

		$name 		   = $component['name'];
		$container     = $component['container'];
		$force_display = $container['force_display'] ?? false;
		$content       = $component['content'];

		// Only render when it makes sense
		if ( $content || $force_display ) {

			// Add parent			
			static::add_parent($component);

			/**
			 * Content could be a string, in which case we render it.
			 * And the content could be an array, in which case we assume
			 * it holds one or more components or a callback
			 */
			if ( ! $content ) {
				// Do nothing...
			}
			elseif ( is_string($content) ) {
				$render .= $content;
			}
			elseif ( is_array($content) ) {

				// If the content part is a callback (string function, or array with class and function)
				if ( is_string($content[0]) || ( is_array($content[0]) && isset($content[0][1]) ) ) {

					$render .= render_callback($content);
				}

				// Render the inner components
				else {					
					foreach ( $content as $inner_component ) {
						$render .= static::render_component( $inner_component );
					}
				}
			}

			// Remove component from parents
			static::remove_parent($component);

			// If we have a render or display is forced, wrap container around render
			if ( ( $container && $render ) || $force_display ) {

				// First render the inner wrap around the render
				if ( $container['inner_wrap']	) {

					$bem_block = static::get_bem_block( $component );

					// Decide the classname of 'inner' based on whether it's a BEM-block
					$inner_class = ( $bem_block ) ? "{$bem_block}__inner" : 'inner';

					// Render
					$render = sprintf( '<div class="%s">%s</div>', $inner_class, $render);
				}

				$classes    = render_classes( static::get_classes($component) );
				$attributes = render_attr( $container['attr'] );
				$attributes = ($attributes) ? " $attributes" : '';
				$tag 		= $container['tag'];

				// Setup render format
				$render = sprintf( '<%1$s class="%2$s"%3$s>%4$s</%1$s>', $tag, $classes, $attributes, $render );
			}
		}

		return $render;
	}

	/**
	 * Render the classes of the element
	 *
	 * @param 	array $component
	 * @return 	array classes
	 */ 
	private static function get_bem_classes( $component ) {

		$classes = [];

		$bem_block   = static::get_bem_block( $component );
		$bem_element = static::get_bem_element( $component );

		if ($bem_block) {
			$classes[] = $bem_block;
		}

		if ($bem_element) {
			$classes[] = $bem_element;
		}

		return $classes;
	}

	/**
	 * Get the classes of the component container
	 *
	 * @param 	array $component
	 * @return 	array classes
	 */ 
	private static function get_classes( $component ) {

		$classes = static::get_bem_classes($component);
		$classes = array_merge($classes, $component['container']['extra_classes']);

		return $classes;
	}

	/** 
	 * Process the block name of BEM
	 */
	private static function get_bem_block( $component ) {

		$bem_block = false;

		if ( is_string($component['container']['bem_block']) && $component['container']['bem_block'] != '' ) {
			$bem_block = $component['container']['bem_block'];
		}
		elseif ( $component['container']['bem_block'] === true ) {
			$bem_block = $component['name'];
		}

		return $bem_block;
	}

	private static function get_bem_element( $component ) {

		$bem_element = false;

		// Only do stuff with BEM element if it has a BEM block parent
		if ( $component['container']['bem_element'] ) {

			$parent_bem_block = static::get_parent();

			if ($parent_bem_block) {


				// If bem_element is set to true automatically set bem_block as bem_element
				if ( $component['container']['bem_element'] === true ) {
					$component['container']['bem_element'] = static::get_bem_block($component) ?? $component['name'];
				}

				// Add BEM element as class
				$bem_element = "{$parent_bem_block}__" . $component['container']['bem_element'];
			}
		}

		return $bem_element;
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

	public static function has_parent( $name ) {
		return (static::get_parent() == $name);
	}

	private static function add_parent( $component ) {

		// Component should be a parent if it is a BEM block
		$name = static::get_bem_block( $component );

		// Only add current component as parent if it's defined as a BEM-block
		if ($name) {
			$ancestors   = static::get_ancestors();
			$ancestors[] = $name;

			// Set ancestors
			static::set_ancestors($ancestors);
		}

	}

	private static function remove_parent( $component ) {

		// Component should be a parent if it is a BEM block
		$name = static::get_bem_block( $component );

		// Only add current component as parent if it's defined as a BEM-block
		if ($name) {
			$ancestors = static::get_ancestors();
			$ancestors = array_remove_value($ancestors, $name);

			// Set ancestors
			static::set_ancestors($ancestors);
		}
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

		// Fill the index gaps
		$components = array_values($components);
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

		add_filter( "ejo/composition/setup_component_defaults/{$name}", $component, 10, 2 );
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

		add_filter( "ejo/composition/setup_component/{$name}", $component, 10, 2 );
	}

	/**
	 * Easily hook into action hook
	 *
	 * @param string Name
	 * @param string or function
	 */
	public static function do_before_render_component( $name, $function ) {

		add_action( "ejo/composition/before_render_component/{$name}", $function );
	}

	/**
	 * Easily remove action
	 *
	 * @param string Name
	 * @param string or function
	 */
	public static function not_do_before_render_component( $name, $function ) {

		if ( is_string($function) || is_array($function) ) {
			remove_action( "ejo/composition/before_render_component/{$name}", $function );
		}
	}
}




