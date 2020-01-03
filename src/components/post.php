<?php
/**
 * Templating functions.
 */

namespace Ejo\Templating;


/**
 * Post stuff
 */

Composition::component_data( 'post-archive-loop', function( $component ) {
	$component['container'] = [ 'tag' => 'section', 'inner_wrap' => true ];
	$component['content'] = [ __NAMESPACE__ . '\\render_post_loop' ];

	return $component;
});

Composition::component_data( 'plural-post', function( $component ) { 
	$component['container'] = [ 'tag' => 'article' ];
	$component['content']   = [ ['plural-post-header'], ['plural-post-main'], ['plural-post-footer'] ]; 

	return $component;
});

Composition::component_data( 'plural-post-header', function( $component ) { 
	$component['container'] = [ 'tag' => 'header', 'bem_block' => false, 'bem_element' => 'header' ];
	$component['content']   = [ ['plural-post-title'], ['plural-post-meta'] ]; 

	return $component;
});

Composition::component_data( 'plural-post-title', function( $component ) { 
	$component['container'] = [ 'tag' => 'h3', 'bem_block' => false, 'bem_element' => 'title' ];
	$component['content']   = [ __NAMESPACE__ . '\\wrap_in_link', 'get_the_title' ]; 

	return $component;
});

Composition::component_data( 'plural-post-main', function( $component ) { 
	$component['container'] = [ 'tag' => 'div', 'bem_block' => false, 'bem_element' => 'main' ];
	$component['content']   = [ ['excerpt'] ]; 

	return $component;
});

Composition::component_data( 'plural-post-footer', function( $component ) { 
	$component['container'] = [ 'tag' => 'footer', 'bem_block' => false, 'bem_element' => 'footer' ];
	$component['content']   = [ ['read-more'] ]; 

	return $component;
});

Composition::component_data( 'plural-post-meta', function( $component ) {
	$component['container'] = [ 'bem_block' => 'post-meta', 'bem_element' => false ];
	$component['content']   = [ ['meta-author'], ['meta-date'], ['meta-categories'] ];

	return $component;
});

Composition::component_data( 'excerpt', function( $component ) {
	$component['container'] = false;
	$component['content']   = [ __NAMESPACE__ . '\\render_excerpt' ];

	return $component;
});

Composition::component_data( 'read-more', function( $component ) { 
	$component['content'] = [ __NAMESPACE__ . '\\render_read_more' ];

	return $component;
});


Composition::component_data( 'post-meta', function( $component ) {
	$component['container'] = [ 'bem_block' => 'post-meta', 'bem_element' => 'meta' ];
	$component['content']   = [ ['meta-author'], ['meta-date'], ['meta-categories'] ];

	return $component;
});

Composition::component_data( 'meta-author', function( $component ) { 
	$component['container'] = [ 'bem_block' => false, 'bem_element' => 'author' ];
	$component['content']   = [ __NAMESPACE__ . '\\render_post_author' ]; 

	return $component;
});

Composition::component_data( 'meta-date', function( $component ) { 
	$component['container'] = [ 'bem_block' => false, 'bem_element' => 'date' ];
	$component['content']   = [ __NAMESPACE__ . '\\render_post_date' ]; 

	return $component;
});

Composition::component_data( 'meta-categories', function( $component ) { 
	$component['container'] = [ 'bem_block' => false, 'bem_element' => 'categories' ];
	$component['content']   = [ __NAMESPACE__ . '\\render_post_categories' ]; 

	return $component;
});

Composition::component_data( 'posts-nav', function( $component ) { 
	$component['container'] = [ 'tag' => 'nav' ];
	$component['content']   = [ '\\get_the_posts_pagination', ['mid_size' => 2 ] ];

	return $component;
});

Composition::component_data( 'post-nav', function( $component ) { 
	$component['container'] = [ 'tag' => 'nav' ];
	$component['content']   = [ ['post-nav-link-previous'], ['post-nav-link-next'] ];

	return $component;
});

Composition::component_data( 'post-nav-link-previous', function( $component ) { 
	$component['content'] = [ __NAMESPACE__ . '\\render_post_nav_link', 'previous' ];

	return $component;
});

Composition::component_data( 'post-nav-link-next', function( $component ) { 
	$component['content'] = [ __NAMESPACE__ . '\\render_post_nav_link', 'next' ];

	return $component;
});

/**
 * Template Type: archive
 */
if ( is_archive() || is_home() || is_blog_page() ) {

	Composition::not_do_action( 'page', '\\the_post' );

	Composition::component_data( 'page-main', function( $component ) {

		Composition::component_insert_after( $component['content'], ['post-archive-loop'], ['page-content'] );
		Composition::component_insert_after( $component['content'], ['posts-nav'], ['post-archive-loop'] );

		return $component;
	});	
}

/**
 * Template: Post
 */
if ( is_singular( 'post' ) ) {

	Composition::component_data( 'page-header', function( $component ) {

		Composition::component_insert_after( $component['content'], ['post-meta'], ['page-title'] );

		return $component;
	});	

	Composition::component_data( 'page-footer', function( $component ) {

		Composition::component_insert_after( $component['content'], ['post-nav'] );

		return $component;
	});	
}