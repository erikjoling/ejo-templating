<?php
/**
 * Templating functions.
 */

namespace Ejo\Tmpl;

// log( array_replace_recursive(['element' => [ 'test' => 'test']], ['element' => false])  );
// log( array_replace_recursive(['element' => [ 'test' => 'test']], ['element' => [ 'test' => 'passed']])  );
// log( array_replace_recursive([], ['element' => [ 'test' => 'passed']])  );

// Site
register_component( 'site', [ 
	'element' => [ 'tag' => 'div', 'inner_wrap' => true ],
	'content' => [ 'site-header', 'site-main', 'site-footer' ],
] );

// Site header
register_component( 'site-header', [
	'element' => [ 'tag' => 'header', 'inner_wrap' => true ],
	'content' => [ 'render_site_branding', 'render_site_nav_toggle', 'render_site_nav' ],
] );

//
register_component( 'site-main', [
	'element' => [ 'tag' => 'main', 'inner_wrap' => true ],
	'content' => [ 'page' ],
] );

//
register_component( 'site-footer', [
	'element' => [ 'tag' => 'footer', 'inner_wrap' => true, 'force_display' => true ],
	'content' => [ '' ],
] );

//
register_component( 'page', [
	'element' => [ 'tag' => 'article', 'inner_wrap' => true ],
	'content' => [ '\the_post', 'page-header', 'page-content', 'page-footer' ],
] );

//
register_component( 'page-header', [
	'element' => [ 'tag' => 'header', 'inner_wrap' => true ],
	'content' => [ 'render_breadcrumbs', 'render_page_title' ],
] );

//
register_component( 'page-content', [
	'element' => [ 'tag' => 'div', 'inner_wrap' => true ],
	'content' => [ 'render_page_content' ],
] );

//
register_component( 'page-footer', [
	'element' => [ 'tag' => 'footer', 'inner_wrap' => true ],
	'content' => [],
] );

// register_component( 'the-post-loop',  ['element' => false, 'content' => [] ] );
// register_component( 'the-pagination', ['element' => false, 'content' => [] ] );

/**
 * Setup the system after not before `wp` hook because 
 * we need conditional tags to be available
 */
// add_action( 'wp', __NAMESPACE__ . '\setup_templates' );
add_action( 'wp', function() {


	if (is_plural_page()) {
		add_filter( 'ejo/tmpl/page', function( $component ) {

			$component['content'] = remove_value_from_array($component['content'], '\the_post');

			return $component;
		});
		add_filter( 'ejo/tmpl/page-content', function( $component ) {

			$component['content'] = ['render_post_archive_loop'];
			// $component['content'] = ['render_page_title'];

			return $component;
		});
	}

	// add_filter( 'ejo/tmpl/site-header', function( $component ) {

	// 	log("Component `{$component['name']}`. Below its ancestory...");
	// 	log($component['parents']);
	// 	log($component['content']);

	// 	$component['content'] = array_insert_before( $component['content'], 'render_site_nav', '\get_the_title' );

	// 	if (in_array('site', $component['parents'])) {
	// 		$component['content'] = array_insert_after( $component['content'], '\get_the_title', '\get_the_title' );
	// 	}

	// 	if (is_singular_page()) {
	// 		$component['content'] = array_insert_after( $component['content'], '\get_the_title', '\get_the_title' );
	// 	}

	// 	log($component['content']);

	// 	return $component;
	// } );
});

/*
Het ideaal is een variabele met daarin alle data. (Of in de code?)

[
	site => [
		element => [
			tag => ''
			extra_classes    => [],
			attributes       => [],
			inner_wrap       => false,
		],
		content => [], // Het mooiste zou zijn als deze ontzettend hookable is. 
					   // Zodat bij een kleine wijziging de content niet helemaal
					   // opnieuw hoeft worden opgezet.
		parents => [], // Deze wordt automatisch gevuld
	]
]
site:
content => [ site-header, site-main, site-footer ]

page:
content => [ the-post, page-header, page-content, page-footer ]

post-loop:
content => post_loop

We gaan uit van een default state.
*/
