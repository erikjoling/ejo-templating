<?php
/**
 * Templating functions.
 */

namespace Ejo\Tmpl;


add_action( 'wp', function() {

	/**
	 * Default setup (singular page)
	 */

	add_filter( 'ejo/tmpl/site', function( $component ) {

		$component['element'] = [ 'tag' => 'div', 'inner_wrap' => true ];
		$component['content'] = [ 'site-header', 'site-main', 'site-footer' ];

		return $component;
	});

	add_filter( 'ejo/tmpl/site-header', function( $component ) {

		$component['element'] = [ 'tag' => 'header', 'inner_wrap' => true, 'force_display' => true ];
		$component['content'] = [ 'site-branding', 'site-nav-toggle', 'site-nav' ];

		return $component;
	});

	add_filter( 'ejo/tmpl/site-main', function( $component ) {

		$component['element'] = [ 'tag' => 'main', 'inner_wrap' => true, 'force_display' => true ];
		$component['content'] = [ 'page' ];

		return $component;
	});

	add_filter( 'ejo/tmpl/site-footer', function( $component ) {

		$component['element'] = [ 'tag' => 'footer', 'inner_wrap' => true, 'force_display' => true ];

		return $component;
	});

	add_filter( 'ejo/tmpl/page', function( $component ) {

		$component['element'] = [ 'tag' => 'article', 'inner_wrap' => true, 'force_display' => true ];
		$component['content'] = [ 'the-post', 'page-header', 'page-main', 'page-footer' ];

		return $component;
	});

	add_filter( 'ejo/tmpl/page-header', function( $component ) {

		$component['element'] = [ 'tag' => 'header', 'inner_wrap' => true, 'force_display' => true ];
		$component['content'] = [ 'breadcrumbs', 'page-title' ];

		return $component;
	});

	add_filter( 'ejo/tmpl/page-main', function( $component ) {

		$component['element'] = [ 'tag' => 'div', 'inner_wrap' => true, 'force_display' => true ];
		$component['content'] = [ 'page-content' ];

		return $component;
	});

	add_filter( 'ejo/tmpl/page-footer', function( $component ) {

		$component['element'] = [ 'tag' => 'footer', 'inner_wrap' => true ];

		return $component;
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

		add_filter( 'ejo/tmpl/page', function( $component ) {

			component_remove( $component['content'], 'the-post' );

			return $component;
		});

		add_filter( 'ejo/tmpl/page-main', function( $component ) {

			component_append( $component['content'], 'the-post-loop' );

			return $component;
		});

		add_filter( 'ejo/tmpl/page-header', function( $component ) {

			// component_append( $component['content'], 'page-content' );

			return $component;
		});

	}
});
