<?php
/**
 * Templating functions.
 */

namespace Ejo\Templating;


/**
 * Post stuff
 */

Composition::setup_component_defaults( 'post-archive-loop', function( $component ) {
	$component['container'] = [ 'tag' => 'section', 'inner_wrap' => true ];
	$component['content'] = [ __NAMESPACE__ . '\\render_post_loop' ];

	return $component;
});

Composition::setup_component_defaults( 'plural-post', function( $component ) { 
	$component['container'] = [ 'tag' => 'article' ];
	$component['content']   = [ ['plural-post-header'], ['plural-post-main'], ['plural-post-footer'] ]; 

	return $component;
});

Composition::setup_component_defaults( 'plural-post-header', function( $component ) { 
	$component['container'] = [ 'tag' => 'header', 'bem_block' => false, 'bem_element' => 'header' ];
	$component['content']   = [ ['plural-post-title'], ['plural-post-meta'] ]; 

	return $component;
});

Composition::setup_component_defaults( 'plural-post-title', function( $component ) { 
	$component['container'] = [ 'tag' => 'h3', 'bem_block' => false, 'bem_element' => 'title' ];
	$component['content']   = [ __NAMESPACE__ . '\\wrap_in_link', 'get_the_title' ]; 

	return $component;
});

Composition::setup_component_defaults( 'plural-post-main', function( $component ) { 
	$component['container'] = [ 'tag' => 'div', 'bem_block' => false, 'bem_element' => 'main' ];
	$component['content']   = [ ['excerpt'] ]; 

	return $component;
});

Composition::setup_component_defaults( 'plural-post-footer', function( $component ) { 
	$component['container'] = [ 'tag' => 'footer', 'bem_block' => false, 'bem_element' => 'footer' ];
	$component['content']   = [ ['read-more'] ]; 

	return $component;
});

Composition::setup_component_defaults( 'plural-post-meta', function( $component ) {
	$component['container'] = [ 'bem_block' => 'post-meta', 'bem_element' => false ];
	$component['content']   = [ ['meta-author'], ['meta-date'], ['meta-categories'] ];

	return $component;
});

Composition::setup_component_defaults( 'excerpt', function( $component ) {
	$component['container'] = false;
	$component['content']   = [ __NAMESPACE__ . '\\render_excerpt' ];

	return $component;
});

Composition::setup_component_defaults( 'read-more', function( $component ) { 
	$component['content'] = [ __NAMESPACE__ . '\\render_read_more' ];

	return $component;
});


Composition::setup_component_defaults( 'post-meta', function( $component ) {
	$component['container'] = [ 'bem_block' => 'post-meta', 'bem_element' => 'meta' ];
	$component['content']   = [ ['meta-author'], ['meta-date'], ['meta-categories'] ];

	return $component;
});

Composition::setup_component_defaults( 'meta-author', function( $component ) { 
	$component['container'] = [ 'bem_block' => false, 'bem_element' => 'author' ];
	$component['content']   = [ __NAMESPACE__ . '\\render_post_author' ]; 

	return $component;
});

Composition::setup_component_defaults( 'meta-date', function( $component ) { 
	$component['container'] = [ 'bem_block' => false, 'bem_element' => 'date' ];
	$component['content']   = [ __NAMESPACE__ . '\\render_post_date' ]; 

	return $component;
});

Composition::setup_component_defaults( 'meta-categories', function( $component ) { 
	$component['container'] = [ 'bem_block' => false, 'bem_element' => 'categories' ];
	$component['content']   = [ __NAMESPACE__ . '\\render_post_categories' ]; 

	return $component;
});

Composition::setup_component_defaults( 'posts-nav', function( $component ) { 
	$component['container'] = [ 'tag' => 'nav' ];
	$component['content']   = [ '\\get_the_posts_pagination', ['mid_size' => 2 ] ];

	return $component;
});

Composition::setup_component_defaults( 'post-nav', function( $component ) { 
	$component['container'] = [ 'tag' => 'nav' ];
	$component['content']   = [ ['post-nav-link-previous'], ['post-nav-link-next'] ];

	return $component;
});

Composition::setup_component_defaults( 'post-nav-link-previous', function( $component ) { 
	$component['content'] = [ __NAMESPACE__ . '\\render_post_nav_link', 'previous' ];

	return $component;
});

Composition::setup_component_defaults( 'post-nav-link-next', function( $component ) { 
	$component['content'] = [ __NAMESPACE__ . '\\render_post_nav_link', 'next' ];

	return $component;
});

/**
 * Template Type: archive
 */
if ( is_archive() || is_home() || is_blog_page() ) {

	Composition::not_do_before_render_component( 'page', '\\the_post' );

	Composition::setup_component_defaults( 'page-main', function( $component ) {

		Composition::component_insert_after( $component['content'], ['post-archive-loop'], ['page-content'] );
		Composition::component_insert_after( $component['content'], ['posts-nav'], ['post-archive-loop'] );

		return $component;
	});	
}

/**
 * Template: Post
 */
if ( is_singular( 'post' ) ) {

	Composition::setup_component_defaults( 'page-header', function( $component ) {

		Composition::component_insert_after( $component['content'], ['post-meta'], ['page-title'] );

		return $component;
	});	

	Composition::setup_component_defaults( 'page-footer', function( $component ) {

		Composition::component_insert_after( $component['content'], ['post-nav'] );

		return $component;
	});	
}