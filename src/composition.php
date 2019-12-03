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
     * Component
     *
     * @var array
     */
    private static $component = [];

    /**
     * Constructor method.
     *
     * @return void
     */
    private function __construct() {}

    /**
	 * Get Component
	 *
	 * @return array Component
	 */
	public static function get_component() {
		return static::$component;
	}

	/**
	 * Set Component
	 *
	 * @return 	void
	 */
	public static function set_component( $component ) {
		static::$component = $component;
	}

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

		/* ========================= */
		/* Setup Component Data
		/* ========================= */
		
		// If component is array with only one record, then setup empty component with name
		$component = ( is_array($component) && count($component) == 1 && isset($component[0]) ) ? [ 'name' => $component[0] ] : $component;

		// We don't like empty component names
		if ( empty($component['name']) ) {
			return '';
		}

		// Setup component defaults
		$component_defaults = apply_filters( "ejo/composition/component_defaults/{$name}", [] );

		// If component is empty we set it to default false structure
		if ( empty($component_defaults) ) {
			$component_defaults = [ 'container' => false, 'content' => false ];
		}

		// Merge the component with the defaults
		$component = array_replace_recursive($component_defaults, $component);

		// Give themes and plugins opportunity to override passed component
		$component = apply_filters( "ejo/composition/component/{$name}", $component );

		// Process component parts
		$component['name']    = $component['name'] ?? false;
		$component['container'] = $component['container'] ?? false;
		$component['content'] = $component['content'] ?? [];

		/* ========================= */
		/* Start rendering
		/* ========================= */

		// Setup render
		$render = '';

		// Only do stuff if $container and $content are not expicitely set as false
		if ( ! ($component['container'] === false && $component['content'] === false) ) {

			// Add action in case we need to do some action in stead of filtering (like the_post())
			do_action( "ejo/composition/before_render_component/{$component['name']}" );

			// Setup element
			$component = static::setup_component_element($component);

			// Component should be a parent if it is a BEM block
			$parent = static::get_bem_block( $container['bem_block'], $container['name'] );

			// Only add current component as parent if it's defined as a BEM-block
			if ( $parent ) {
				static::add_parent($parent);
			}

			/**
			 * Content could be a string, in which case we render it.
			 * And the content could be an array, in which case we assume
			 * it holds one or more components and we render them.
			 */
			if ( is_string($content) ) {
				$render .= $content;
			}
			elseif ( is_array($content) && ! empty($content) ) {

				// If the content part is a callback (string function, or array with class and function)
				if ( is_string($content[0]) || ( is_array($content[0]) && isset($content[0][1]) ) ) {

					$render .= \Ejo\Templating\render_callback($content);
				}
				else {
					foreach ( $content as $content_part ) {
						$render .= static::render_component( $content_part );
					}
				}
			}

		return $render;

			// Only remove current component as parent if it's defined as a BEM-block
			if ( $parent ) {
				static::remove_parent($parent);
			}

			// If we have a render or display is forced, wrap element around render
			if ( $container && ( $render || $container['force_display'] ) ) {		
				$render = static::render_component_wrapped( $container, $render );
			}
		}

		return $render;
	}


	private static function setup_component_element( $component ) {

		$container = $component['container'];

		// If element is specified process it
		if ( is_array($container) ) {

			// Merge/replace defaults with the component
			$container = wp_parse_args( $container, [
				'name'          => $name,
				'tag'           => 'div',
				'extra_classes' => [],
				'attr'    		=> [],
				'inner_wrap'    => false,
				'force_display' => false,
				'bem_block'     => true,
				'bem_element'   => false,
			] );


			// Process

			// Set bem_block to false if element is specified as bem_element and bem_block is not a string
			$container['bem_block'] = ( $container['bem_element'] && !is_string($container['bem_block']) ) ? false : $container['bem_block'];
			$container['tag']           = esc_html( $container['tag'] );
			$container['extra_classes'] = (array) $container['extra_classes'];
			$container['attr']    	  = (array) $container['attr'];
			$container['inner_wrap']    = !! $container['inner_wrap'];
			$container['force_display'] = !! $container['force_display'];

			$component['container'] = $container;
		}

		return $component;
	}

	/**
	 * Render the contents of a component
	 *
	 * @param 	string $content (output) | array $content (components)
	 * @return 	string $render
	 */ 
	private static function render_component_content( $content ) {


	}

	/**
	 * Wrap the render in an element
	 *
	 * @param 	array $container
	 * @param 	string $render
	 * @return 	string 
	 */ 
	private static function render_component_wrapped( $container, $render ) {

		// Setup render
		$render_format = '%s';

		// Start rendering the element which wraps around the content
		if ($container) {

			$render_format_inner_wrap = '%s';

			if ( $container['inner_wrap']	) {

				$bem_block = static::get_bem_block( $container['bem_block'], $container['name'] );

				// Decide the classname of 'inner' based on whether it's a BEM-block
				$inner_class = ( $bem_block ) ? "{$bem_block}__inner" : 'inner';

				// Setup inner wrap render format
				$render_format_inner_wrap = sprintf( '<div class="%s">%%s</div>', $inner_class );
			}


			// Setup render format
			$render_format = sprintf( 
				'<%1$s class="%2$s"%3$s>%4$s</%1$s>', 
				$container['tag'], 
				static::render_element_classes($container), 
				render_attr($container['attr']), 
				$render_format_inner_wrap 
			);
		}

		return sprintf( $render_format, $render );
	}

	/**
	 * Render the classes of the element
	 *
	 * @param 	array $container
	 * @return 	string classes
	 */ 
	private static function render_element_classes( $container ) {

		$classes = [];

		$bem_block   = static::get_bem_block( $container['bem_block'], $container['name'] );
		$bem_element = static::get_bem_element( $container['bem_element'], $bem_block, $container['name'] );

		if ($bem_block) {
			$classes[] = $bem_block;
		}

		if ($bem_element) {
			$classes[] = $bem_element;
		}

		$classes = array_merge($classes, $container['extra_classes']);
		$classes = render_classes($classes);

		return $classes;
	}

	/** 
	 * Process the block name of BEM
	 */
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




