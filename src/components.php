<?php
/**
 * Templating functions.
 */

namespace Ejo\Tmpl;


add_action( 'wp', function() {

	/**
	 * Default setup (singular page)
	 */

	/**
	 * Elements
	 */

	add_filter( 'ejo/tmpl/site/element', function( $element ) {
		return [ 'tag' => 'div', 'inner_wrap' => true ];
	});

	add_filter( 'ejo/tmpl/site-header/element', function( $element ) {
		return [ 'tag' => 'header', 'inner_wrap' => true, 'force_display' => true ];
	});

	add_filter( 'ejo/tmpl/site-main/element', function( $element ) {
		return [ 'tag' => 'main', 'inner_wrap' => true, 'force_display' => true ];
	});

	add_filter( 'ejo/tmpl/site-footer/element', function( $element ) {
		return [ 'tag' => 'footer', 'inner_wrap' => true, 'force_display' => true ];
	});

	add_filter( 'ejo/tmpl/page/element', function( $element ) {
		return [ 'tag' => 'article', 'inner_wrap' => true, 'force_display' => true ];
	});

	add_filter( 'ejo/tmpl/page-header/element', function( $element ) {
		return [ 'tag' => 'header', 'inner_wrap' => true, 'force_display' => true ];
	});

	add_filter( 'ejo/tmpl/page-main/element', function( $element ) {
		return [ 'tag' => 'div', 'inner_wrap' => true, 'force_display' => true ];
	});

	add_filter( 'ejo/tmpl/page-footer/element', function( $element ) {
		return [ 'tag' => 'footer', 'inner_wrap' => true ];
	});


	/**
	 * Content
	 */

	add_filter( 'ejo/tmpl/site/content', function( $content ) {
		return [ 'site-header', 'site-main', 'site-footer' ];
	});

	add_filter( 'ejo/tmpl/site-header/content', function( $content ) {
		return [ 'site-branding', 'site-nav-toggle', 'site-nav' ];
	});

	add_filter( 'ejo/tmpl/site-main/content', function( $content ) {
		return [ 'page' ];
	});

	add_filter( 'ejo/tmpl/page/content', function( $content ) {
		return [ 'the-post', 'page-header', 'page-main', 'page-footer' ];
	});

	add_filter( 'ejo/tmpl/page-header/content', function( $content ) {
		return [ 'breadcrumbs', 'page-title' ];
	});

	add_filter( 'ejo/tmpl/page-main/content', function( $content ) {
		return [ 'page-content' ];
	});

	/**
	 * Map Components to functions 
	 */
	add_filter( 'ejo/tmpl/site-branding/content', __NAMESPACE__ . '\render_site_branding' );
	add_filter( 'ejo/tmpl/site-nav-toggle/content', __NAMESPACE__ . '\render_site_nav_toggle' );
	add_filter( 'ejo/tmpl/site-nav/content', __NAMESPACE__ . '\render_site_nav' );
	add_filter( 'ejo/tmpl/page-title/content', __NAMESPACE__ . '\render_page_title' );
	add_filter( 'ejo/tmpl/page-content/content', __NAMESPACE__ . '\render_page_content' );
	add_filter( 'ejo/tmpl/breadcrumbs/content', __NAMESPACE__ . '\render_breadcrumbs' );
	add_filter( 'ejo/tmpl/the-post/content', '\the_post' );
	add_filter( 'ejo/tmpl/the-post-loop/content', __NAMESPACE__ . '\render_post_archive_loop' );

	/**
	 * Archive setup (plural page)
	 */

	if (is_plural_page()) {

		add_filter( 'ejo/tmpl/page/content', function( $content ) {

			component_remove( $content, 'the-post' );

			return $content;
		});

		add_filter( 'ejo/tmpl/page-main/content', function( $content ) {

			component_append( $content, 'the-post-loop' );

			return $content;
		});

		add_filter( 'ejo/tmpl/page-header/content', function( $content ) {

			// component_append( $content, 'page-content' );

			return $content;
		});
	}
});

	
/** 
 * Test
 */ 
add_action( 'wp', function() {
	// add_filter( 'ejo/tmpl/site/element', function( $element ) {
	// 	$element['extra_classes'][] = 'has-background-black';

	// 	return $element;
	// });

	add_filter( 'ejo/tmpl/page/content', function( $content ) {
		component_append( $content, 'page-header' );
		component_append( $content, 'page-main' );
		component_append( $content, 'page-footer' );

		return $content;
	});

	add_filter( 'ejo/tmpl/page-main/content', function( $content ) {
		component_prepend( $content, 'page-header' );
		// component_append( $content, 'page-content' );
		// component_append( $content, 'page-footer' );

		return $content;
	});

	// add_filter( 'ejo/tmpl/page-footer/content', function( $content ) {
	// 	component_append( $content, 'breadcrumbs' );

	// 	return $content;
	// });


	// add_filter( 'ejo/tmpl/page-header/content', function( $content ) {
	// 	component_remove( $content, 'breadcrumbs' );

	// 	return $content;
	// });

	// add_filter( 'ejo/tmpl/breadcrumbs/content', '__return_false' );
});