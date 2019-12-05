<?php
/**
 * Templating functions.
 */

namespace Ejo\Templating;


add_action( 'wp', function() {

	/**
	 * Post stuff
	 */

	Composition::setup_component_defaults( 'the-post-loop', function( $component ) {
		return [
			'content' => [ __NAMESPACE__ . '\\render_post_loop' ]
		];
	});

	Composition::setup_component_defaults( 'plural-post', function( $component ) { 
		return [
			'container' => [ 'tag' => 'article' ],
			'content' => [ ['plural-post-header'], ['plural-post-main'], ['plural-post-footer'] ]
		]; 
	});

	Composition::setup_component_defaults( 'plural-post-header', function( $component ) { 
		return [
			'container' => [ 'tag' => 'header', 'bem_block' => false, 'bem_element' => 'header' ],
			'content' => [ ['title'], ['post-meta'] ]
		]; 
	});

	Composition::setup_component_defaults( 'plural-post-title', function( $component ) { 
		return [
			'container' => [ 'tag' => 'h3', 'bem_block' => false, 'bem_element' => 'title' ],
			'content' => [ __NAMESPACE__ . '\\render_title', true ]
		]; 
	});

	Composition::setup_component_defaults( 'plural-post-main', function( $component ) { 
		return [
			'container' => [ 'tag' => 'div', 'bem_block' => false, 'bem_element' => 'main' ],
			'content' => [ ['excerpt'] ]
		]; 
	});

	Composition::setup_component_defaults( 'plural-post-footer', function( $component ) { 
		return [
			'container' => [ 'tag' => 'footer', 'bem_block' => false, 'bem_element' => 'footer' ],
			'content' => [ ['read-more'] ]
		]; 
	});

	Composition::setup_component_defaults( 'excerpt', function( $component ) { 
		return [
			'content' => [ __NAMESPACE__ . '\\render_excerpt' ]
		]; 
	});

	Composition::setup_component_defaults( 'read-more', function( $component ) { 
		return [
			'content' => [ __NAMESPACE__ . '\\render_read_more' ]
		]; 
	});

	Composition::setup_component_defaults( 'post-meta', function( $component ) {
		return [
			'container' => [],
			'content' => [ ['post-author'], ['post-date'], ['post-categories'] ]
		]; 
	});

	Composition::setup_component_defaults( 'post-author', function( $component ) { 
		$bem_element = (Composition::get_parent() == 'post-meta') ? 'author' : true;

		return [
			'container' => [ 'tag' => 'div', 'bem_block' => false, 'bem_element' => $bem_element ],
			'content' => [ __NAMESPACE__ . '\\render_post_author' ]
		]; 
	});

	Composition::setup_component_defaults( 'post-date', function( $component ) { 
		$bem_element = (Composition::get_parent() == 'post-meta') ? 'date' : true;

		return [
			'container' => [ 'tag' => 'div', 'bem_block' => false, 'bem_element' => $bem_element ],
			'content' => [ __NAMESPACE__ . '\\render_post_date' ]
		]; 
	});

	Composition::setup_component_defaults( 'post-categories', function( $component ) { 
		$bem_element = (Composition::get_parent() == 'post-meta') ? 'categories' : true;

		return [
			'container' => [ 'tag' => 'div', 'bem_block' => false, 'bem_element' => $bem_element ],
			'content' => [ __NAMESPACE__ . '\\render_post_categories' ]
		]; 
	});

	Composition::setup_component_defaults( 'post-nav', function( $component ) { 
		return [
			'container' => [ 'tag' => 'nav' ],
			'content' => [ ['post-nav-link-previous'], ['post-nav-link-next'] ]
		];
	});

	Composition::setup_component_defaults( 'post-nav-link-previous', function( $component ) { 
		return [
			'content' => [ __NAMESPACE__ . '\\render_post_nav_link', 'previous' ]
		];
	});

	Composition::setup_component_defaults( 'post-nav-link-next', function( $component ) { 
		return [
			'content' => [ __NAMESPACE__ . '\\render_post_nav_link', 'next' ]
		];
	});

	/**
	 * Template Type: archive
	 */
	if ( is_template_type('archive') ) {

		Composition::before_render_component_remove_action( 'page', '\\the_post' );

		Composition::setup_component_defaults( 'page', function( $component ) {

			return $component;
		});

		Composition::setup_component_defaults( 'page-main', function( $component ) {

			// Composition::component_insert_after( $component['content'], 'the-post-loop', 'page-content' );

			return $component;
		});
	}

	/**
	 * Template Type: Term
	 */
	if ( is_template_type('term') ) {

		Composition::setup_component_defaults( 'page-title', function( $component ) {
			$component['content'] = [ '\\single_term_title', '', false ];

			return $component;
		});

		// Composition::setup_component_defaults( 'page-content', function( $component ) {
		// 	$component['content'] = [ '\\the_archive_description' ];

		// 	return $component;
		// });
	}

	/**
	 * Template: blog page
	 */
	if ( is_template('blog') ) {
		
		// Composition::setup_component_defaults( 'page-title', function( $component ) {
		// 	$component['content'] = [ '\\get_post_field', 'post_title', get_queried_object_id() ];

		// 	return $component;
		// });

		// Composition::setup_component_defaults( 'page-content', function( $component ) {
		// 	// $component['content'] = apply_filters( 'the_content', get_the_content( null, false, get_queried_object_id() ) );

		// 	return $component;
		// });
	}

	/**
	 * Template: Post
	 */
	if ( is_template('post') ) {

		Composition::setup_component_defaults( 'page-header', function( $component ) {

			Composition::component_insert_after( $component['content'], 'post-meta', 'page-title' );

			return $component;
		});	

		Composition::setup_component_defaults( 'page-footer', function( $component ) {

			Composition::component_insert_after( $component['content'], 'post-nav' );

			return $component;
		});	
	}


	/**
	 * Template: 404
	 */
	if ( is_template('404') ) {

		// Composition::setup_component_defaults( 'page-title', function( $component ) {
		// 	$component['content'] = [ __NAMESPACE__ . '\\render_404_title' ];

		// 	return $component;
		// });

		Composition::setup_component_defaults( 'page-content', function( $component ) {
			$component['content'] = [ __NAMESPACE__ . '\\render_404_content' ];

			return $component;
		});
	}

	/**
	 * Template: Search
	 */
	if ( is_template('search') ) {

		// Composition::setup_component_defaults( 'page-title', function( $component ) {
		// 	$component['content'] = [ __NAMESPACE__ . '\\render_search_title' ];

		// 	return $component;
		// });

		Composition::setup_component_defaults( 'page-content', function( $component ) {
			$component['content'] = [ __NAMESPACE__ . '\\render_search_content' ];

			return $component;
		});
	}

	/**
	 * Template: Blog no page
	 */
	if ( is_template('blog-no-page') ) {

		// Composition::setup_component_defaults( 'page-title', function( $component ) {
		// 	$component['content'] = 'Blog of Front';

		// 	return $component;
		// });
	}

	/**
	 * Template: Blog on Front
	 */
	if ( is_template('author') ) {

		Composition::setup_component_defaults( 'page-title', function( $component ) {
			$component['content'] = [ '\\get_the_author_meta', 'display_name', absint( get_query_var( 'author' ) ) ];

			return $component;
		});
	}
});

	
/** 
 * Test
 */ 
add_action( 'wp', function() {

	// Composition::setup_component_defaults( 'page-header', function( $component ) {

	// 	Composition::component_move_before( $component['content'], 'page-title', 'breadcrumbs' );

	// 	Composition::component_insert_after( $component['content'], 'page-title', 'breadcrumbs' );
	// 	Composition::component_insert_before( $component['content'], 'breadcrumbs', 'page-title' );
	// 	Composition::component_remove( $component['content'], 'page-title' );

	// 	return $component;
	// });	

}, 99);

