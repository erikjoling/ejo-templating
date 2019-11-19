<?php
/**
 * Templating functions.
 */

namespace Ejo\Tmpl;


add_action( 'wp', function() {

	/**
	 * Everything without a template conditional is considered to part of a singular page template
	 */

	render_component( 'site', function($data) {

	}, []);
		[ 
		'element' => [
			'tag' => 'div', 
			'inner_wrap' => true, 
		],
		'content' => [
			'site-header', 
			// 'site-main', 
			// 'site-footer'
		] 
	] );

	/*
	Ik interesseer me niet in het eerst registreren en daarna renderen. 
	Het is prima om tijdens het renderen alles op te bouwen. 
	Zolang het maar BEM proof is. En flexibel qua aanpasbaarheid.
	*/

	// register_component( 'site-header', [ 
	// 	'element' => [
	// 		'tag' => 'div', 
	// 		'inner_wrap' => true, 
	// 	],
	// 	'content' => [
	// 		'site-branding', 
	// 		// 'site-nav-toggle', 
	// 		// 'site-nav'
	// 	] 
	// ] );

	// register_component( 'site-header', function( $component ) {
	// 	'element' => [
	// 		'tag' => 'div', 
	// 		'inner_wrap' => true, 
	// 	],
	// 	'content' => [
	// 		'site-branding', 
	// 		// 'site-nav-toggle', 
	// 		// 'site-nav'
	// 	] 
	// ] );

	// register_component( 'site-branding', [ 
	// 	'content' => 'render_site_branding'
	// ] );

	/**
	 * First setting up the ELEMENT of each component
	 */

	add_filter( 'ejo/tmpl/component/site', function( $component ) {
		return [ 
			'element' => [ 'tag' => 'div', 'inner_wrap' => true ],
			'content' => [ 'site-header', 'site-main' ]
		];
	});

	add_filter( 'ejo/tmpl/component/site-header', function( $component ) {
		return [ 
			'element' => [ 'tag' => 'header', 'inner_wrap' => true, 'force_display' => true ],
			'content' => [ 'site-branding', 'site-nav-toggle', 'site-nav' ]
		];
	});

	add_filter( 'ejo/tmpl/component/site-main', function( $component ) {
		return [ 
			'element' => [ 'tag' => 'main', 'inner_wrap' => true, 'force_display' => true ],
			'content' => [ 'page' ]
		];
	});

	add_filter( 'ejo/tmpl/component/site-footer', function( $component ) {
		return [ 
			'element' => [ 'tag' => 'footer', 'inner_wrap' => true, 'force_display' => true ],
			'content' => []
		];
	});

	add_filter( 'ejo/tmpl/component/page', function( $component ) {
		return [ 
			'element' => [ 'tag' => 'article', 'inner_wrap' => true, 'force_display' => true ],
			'content' => [ 'the-post', 'page-header', 'page-main', 'page-footer' ]
		];
	});

	add_filter( 'ejo/tmpl/component/page-header', function( $component ) {
		return [ 
			'element' => [ 'tag' => 'header', 'inner_wrap' => true, 'force_display' => true ],
			'content' => [ 'breadcrumbs', 'page-title' ]
		];
	});

	add_filter( 'ejo/tmpl/component/page-main', function( $component ) {
		return [ 
			'element' => [ 'tag' => 'div', 'inner_wrap' => true, 'force_display' => true ],
			'content' => [ 'page-content' ]
		];
	});

	add_filter( 'ejo/tmpl/component/page-footer', function( $component ) {
		return [ 
			'element' => [ 'tag' => 'footer', 'inner_wrap' => true ],
			'content' => []
		];
	});


	# Small components

	add_filter( 'ejo/tmpl/component/site-nav-toggle', function( $components ) {
		return [ 
			'content' => __NAMESPACE__ . '\render_site_nav_toggle'
		];
	});

	add_filter( 'ejo/tmpl/component/site-nav', function( $components ) {
		return [ 
			'content' => __NAMESPACE__ . '\render_site_nav'
		];
	});

	add_filter( 'ejo/tmpl/component/page-title', function( $components ) {
		return [ 
			'element' => [ 'tag' => 'h1', 'bem_element' => 'title' ],
			'content' => __NAMESPACE__ . '\get_page_title'
		];
	});

	add_filter( 'ejo/tmpl/component/page-content', function( $components ) {
		return [ 
			'content' => __NAMESPACE__ . '\render_page_content'
		];
	});

	add_filter( 'ejo/tmpl/component/breadcrumbs', function( $components ) {
		return [ 
			'content' => __NAMESPACE__ . '\render_breadcrumbs'
		];
	});

	add_filter( 'ejo/tmpl/component/the-post', function( $components ) {
		return [ 
			'content' => '\the_post'
		];
	});

	add_filter( 'ejo/tmpl/component/the-post-loop', function( $components ) {
		return [ 
			'content' => __NAMESPACE__ . '\render_post_archive_loop'
		];
	});

	// add_filter( 'ejo/tmpl/the-post-loop/content', __NAMESPACE__ . '\render_post_archive_loop' );
	// add_filter( 'ejo/tmpl/plural-post-header/content', __NAMESPACE__ . '\render_plural_post_title' );
	// add_filter( 'ejo/tmpl/plural-post-content/content', __NAMESPACE__ . '\render_plural_post_content' );
	// // add_filter( 'ejo/tmpl/plural-post-footer/content', __NAMESPACE__ . '\render_post_meta' );

	add_filter( 'ejo/tmpl/component/site-branding', function( $component ) {
		return [ 
			'content' => __NAMESPACE__ . '\\render_site_branding'
		];
	});





	# Plural Post

	add_filter( 'ejo/tmpl/plural-post/element', function( $element ) {
		return [ 'tag' => 'article' ];
	});

	add_filter( 'ejo/tmpl/plural-post-header/element', function( $element ) {
		return [ 'tag' => 'header', 'bem_block' => false, 'bem_element' => 'header' ];
	});

	add_filter( 'ejo/tmpl/plural-post-main/element', function( $element ) {
		return [ 'tag' => 'div', 'bem_block' => false, 'bem_element' => 'main' ];
	});

	add_filter( 'ejo/tmpl/plural-post-footer/element', function( $element ) {
		return [ 'tag' => 'footer', 'bem_block' => false, 'bem_element' => 'footer' ];
	});

	// Hoe omgaan met link?
	// add_filter( 'ejo/tmpl/plural-post-title/element', function( $element ) {
	// 	return [ 'tag' => 'h3', 'bem_block' => false, 'bem_element' => 'title' ];
	// });

	# Post Meta

	add_filter( 'ejo/tmpl/post-meta/element', function( $element ) {
		return [ 'tag' => 'div', 'inner_wrap' => true ];
	});

	add_filter( 'ejo/tmpl/author/element', function( $element ) {
		return [ 'tag' => 'span', 'bem_element' => true ];
	});


	/**
	 * Second setting up the CONTENT of each component
	 */

	# Big site components

	add_filter( 'ejo/tmpl/plural-post/content', function( $content ) {
		return [ 'plural-post-header', 'plural-post-main', 'plural-post-footer' ];
	});

	add_filter( 'ejo/tmpl/plural-post-main/content', function( $content ) {
		return [ 'plural-post-content' ];
	});

	# Small components

	add_filter( 'ejo/tmpl/post-meta/content', function( $content ) {
		return [ 'author' ];
	});

	/**
	 * Map Components to functions 
	 */
	add_filter( 'ejo/tmpl/site-branding/content', function($content) { return __NAMESPACE__ . '\render_site_branding'; } );
	// add_filter( 'ejo/tmpl/site-nav-toggle/content', __NAMESPACE__ . '\render_site_nav_toggle' );
	// add_filter( 'ejo/tmpl/site-nav/content', __NAMESPACE__ . '\render_site_nav' );
	// add_filter( 'ejo/tmpl/page-title/content', __NAMESPACE__ . '\get_page_title' );
	// add_filter( 'ejo/tmpl/page-content/content', __NAMESPACE__ . '\render_page_content' );
	// add_filter( 'ejo/tmpl/breadcrumbs/content', __NAMESPACE__ . '\render_breadcrumbs' );
	// add_filter( 'ejo/tmpl/the-post/content', '\the_post' );
	// add_filter( 'ejo/tmpl/the-post-loop/content', __NAMESPACE__ . '\render_post_archive_loop' );
	// add_filter( 'ejo/tmpl/plural-post-header/content', __NAMESPACE__ . '\render_plural_post_title' );
	// add_filter( 'ejo/tmpl/plural-post-content/content', __NAMESPACE__ . '\render_plural_post_content' );
	// // add_filter( 'ejo/tmpl/plural-post-footer/content', __NAMESPACE__ . '\render_post_meta' );

	// // Post Meta
	// add_filter( 'ejo/tmpl/author/content', __NAMESPACE__ . '\render_author' );
	// add_filter( 'ejo/tmpl/date/content', __NAMESPACE__ . '\render_date' );
	// add_filter( 'ejo/tmpl/categories/content', __NAMESPACE__ . '\render_categories' );

	/**
	 * Archive setup (plural page)
	 */

	if (is_plural_page()) {

		add_filter( 'ejo/tmpl/component/page', function( $component ) {

			component_remove( $component['content'], 'the-post' );

			return $component;
		});

		add_filter( 'ejo/tmpl/component/page-main', function( $component ) {

			component_append( $component['content'], 'the-post-loop' );

			return $component;
		});

		add_filter( 'ejo/tmpl/page-header/content', function( $content ) {

			// component_append( $content, 'page-content' );

			return $content;
		});
	}

	if ( is_singular_page('post') ) {

		add_filter( 'ejo/tmpl/page-header/content', function( $content ) {

			component_append( $content, 'post-meta', 'page-title' );

			return $content;
		});	
	}
});

	
/** 
 * Test
 */ 
add_action( 'wp', function() {
	$components = get_components();

	// Register site as first component
	$components[] = setup_component('site');

	set_components($components);

	// log($components);
});