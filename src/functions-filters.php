<?php
namespace Ejo\Templating;

add_filter( 'body_class', __NAMESPACE__ . '\body_class_filter', 10, 2 );

add_filter( 'nav_menu_css_class',         __NAMESPACE__ . '\nav_menu_css_class',         5, 2 );
add_filter( 'nav_menu_item_id',           __NAMESPACE__ . '\nav_menu_item_id',           5    );
add_filter( 'nav_menu_submenu_css_class', __NAMESPACE__ . '\nav_menu_submenu_css_class', 5    );
add_filter( 'nav_menu_link_attributes',   __NAMESPACE__ . '\nav_menu_link_attributes',   5    );

add_filter( 'excerpt_more',   __NAMESPACE__ . '\edit_excerpt_link' );
add_filter( 'excerpt_length', __NAMESPACE__ . '\excerpt_length' );

/**
 * Returns the body classes.
 *
 * @return string
 */
function body_class_filter( $classes, $class ) {

	$classes = [];

	// Template Type
	$classes[] = 'is-template-type-' . get_template_type();

	// Template
	$classes[] = 'is-template-' . get_template();
	
	// Checks for custom template.
	$template = str_replace(
		[ 'template-', 'tmpl-' ],
		'',
		basename( get_page_template_slug(), '.php' )
	);

	if ($template) {
		$classes[] = "is-template-{$template}";
	} 

	/**
	 * Other
	 */	

	if ( \is_admin_bar_showing() ) {
		$classes[] = 'has-admin-bar';
	}

	return array_map( 'esc_attr', array_unique( array_merge( $classes, (array) $class ) ) );
}

/**
 * Simplifies the nav menu class system.
 *
 * @param  array   $classes
 * @param  object  $item
 * @return array
 */
function nav_menu_css_class( $classes, $item ) {

	$_classes = [ 'site-nav__item' ];

	// On 404 pages don't add current, ancestor relation in menu
	// Because 404 pages think they belong to the blog index page...
	if ( ! is_404() ) {

		foreach ( [ 'item', 'parent', 'ancestor' ] as $type ) {

			if ( \in_array( "current-menu-{$type}", $classes ) || \in_array( "current_page_{$type}", $classes ) ) {

				$_classes[] = 'item' === $type ? 'site-nav__item--current' : "site-nav__item--{$type}";
			}
		}

		// If the menu item is a post type archive and we're viewing a single
		// post of that post type, the menu item should be an ancestor.
		if ( 'post_type_archive' === $item->type && \is_singular( $item->object ) && ! \in_array( 'menu__item--ancestor', $_classes ) ) {
			$_classes[] = 'site-nav__item--ancestor';
		}
	}

	// Add a class if the menu item has children.
	if ( \in_array( 'menu-item-has-children', $classes ) ) {
		$_classes[] = 'has-children';
	}

	// Add custom user-added classes if we have any.
	$custom = get_post_meta( $item->ID, '_menu_item_classes', true );

	if ( $custom ) {
		$_classes = array_merge( $_classes, (array) $custom );
	}

	return $_classes;
}


/**
 * Simplifies the nav menu class system.
 *
 * @param string $item_id
 * @return string
 */
function nav_menu_item_id( $item_id ) {
	$item_id = '';

	return $item_id;
}

/**
 * Adds a custom class to the nav menu link.
 *
 * @param  array   $attr;
 * @return array
 */
function nav_menu_link_attributes( $attr ) {

	$attr['class'] = 'site-nav__link';

	return $attr;
}

/**
 * Adds a custom class to the submenus in nav menus.
 *
 * @param  array   $classes
 * @return array
 */
function nav_menu_submenu_css_class( $classes ) {

	$classes = [ 'site-nav__sub-menu' ];

	return $classes;
}


/**
 * EXCERPT
 */

function edit_excerpt_link( $more ) {
	$bem_block = Composition::get_parent();

    return "&nbsp;<span class=\"{$bem_block}__excerpt-delimiter\">...<span>";
} 

function excerpt_length( $length ) {
	return 32;
}