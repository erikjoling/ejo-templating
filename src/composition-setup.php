<?php
/**
 * Templating functions.
 */

namespace Ejo\Tmpl;


add_action( 'wp', function() {

	/*
	Ik interesseer me niet in het eerst registreren en daarna renderen. 
	Het is prima om tijdens het renderen alles op te bouwen. 
	Zolang het maar BEM proof is. En flexibel qua aanpasbaarheid.
	*/

	add_filter( 'ejo/composition/component/site', function( $component ) {
		return [ 
			'element' => [ 'tag' => 'div', 'inner_wrap' => true ],
			'content' => [ 'site-header', 'site-main' ]
		];
	});

	add_filter( 'ejo/composition/component/site-header', function( $component ) {
		return [ 
			'element' => [ 'tag' => 'header', 'inner_wrap' => true, 'force_display' => true ],
			'content' => [ 'site-branding', 'site-nav-toggle', 'site-nav' ]
		];
	});

	add_filter( 'ejo/composition/component/site-main', function( $component ) {
		return [ 
			'element' => [ 'tag' => 'main', 'inner_wrap' => true, 'force_display' => true ],
			'content' => [ 'page' ]
		];
	});

	add_filter( 'ejo/composition/component/site-footer', function( $component ) {
		return [ 
			'element' => [ 'tag' => 'footer', 'inner_wrap' => true, 'force_display' => true ],
			'content' => []
		];
	});

	add_filter( 'ejo/composition/component/page', function( $component ) {
		return [ 
			'element' => [ 'tag' => 'article', 'inner_wrap' => true, 'force_display' => true ],
			'content' => [ 'the-post', 'page-header', 'page-main', 'page-footer' ]
		];
	});

	add_filter( 'ejo/composition/component/page-header', function( $component ) {
		return [ 
			'element' => [ 'tag' => 'header', 'inner_wrap' => true, 'force_display' => true ],
			'content' => [ 'breadcrumbs', 'page-title' ]
		];
	});

	add_filter( 'ejo/composition/component/page-main', function( $component ) {
		return [ 
			'element' => [ 'tag' => 'div', 'inner_wrap' => true, 'force_display' => true ],
			'content' => [ 'page-content' ]
		];
	});

	add_filter( 'ejo/composition/component/page-footer', function( $component ) {
		return [ 
			'element' => [ 'tag' => 'footer', 'inner_wrap' => true ],
			'content' => []
		];
	});


	# Small components

	add_filter( 'ejo/composition/component/site-nav-toggle', function( $components ) {
		return [ 
			'content' => __NAMESPACE__ . '\render_site_nav_toggle'
		];
	});

	add_filter( 'ejo/composition/component/site-nav', function( $components ) {
		return [ 
			'content' => __NAMESPACE__ . '\render_site_nav'
		];
	});

	add_filter( 'ejo/composition/component/page-title', function( $components ) {
		return [ 
			'element' => [ 'tag' => 'h1', 'bem_element' => 'title' ],
			'content' => __NAMESPACE__ . '\get_page_title'
		];
	});

	add_filter( 'ejo/composition/component/page-content', function( $components ) {
		return [ 
			'content' => __NAMESPACE__ . '\render_page_content'
		];
	});

	add_filter( 'ejo/composition/component/breadcrumbs', function( $components ) {
		return [ 
			'content' => __NAMESPACE__ . '\render_breadcrumbs'
		];
	});

	add_filter( 'ejo/composition/component/the-post', function( $components ) {
		return [ 
			'content' => '\the_post'
		];
	});

	add_filter( 'ejo/composition/component/the-post-loop', function( $components ) {
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
});

	
/** 
 * Test
 */ 
add_action( 'wp', function() {
	
});