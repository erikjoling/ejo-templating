<?php
namespace Ejo\Tmpl;

add_filter( 'nav_menu_css_class',         __NAMESPACE__ . '\nav_menu_css_class',         5, 2 );
add_filter( 'nav_menu_item_id',           __NAMESPACE__ . '\nav_menu_item_id',           5    );
add_filter( 'nav_menu_submenu_css_class', __NAMESPACE__ . '\nav_menu_submenu_css_class', 5    );
add_filter( 'nav_menu_link_attributes',   __NAMESPACE__ . '\nav_menu_link_attributes',   5    );

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