<?php 
/**
 * Template conditionals
 */

namespace Ejo\Templating;

// function is_main_loop() {
// 	return ( \get_the_ID() == \get_queried_object_id() );
// }

function get_template() {
	$template = 'unknown';

	/**
	 * Page related
	 */
	if (\is_singular('page')) {
		$template = 'page';
	}

	/**
	 * Post related
	 */
	elseif (\is_singular('post')) {
		$template = 'post';
	}
	elseif ( \get_queried_object_id() && ( \get_queried_object_id() == get_option('page_for_posts') ) ) {
		$template = 'blog';
	}
	elseif ( \is_home() && ! \get_option('page_on_front') ) {
		$template = 'blog-no-page';
	}
	elseif ( \is_category() ) {
		$template = 'category';
	}
	elseif ( \is_tag() ) {
		$template = 'tag';
	}
	elseif ( \is_date() ) {
		$template = 'date';
	}
	elseif ( \is_author() ) {
		$template = 'author';
	}

	/**
	 * Special Pages
	 */
	elseif ( \is_404() ) {
		$template = '404';
	}
	elseif ( \is_search() ) {
		$template = 'search';
	}

	return apply_filters( 'ejo/templating/get_template', $template );
}

function get_template_type() {
	$template_type = 'unknown';

	if ( \is_singular() ) {
		$template_type = 'single';
	}

	if ( \is_archive() || is_template('blog') || is_template('search') ) {
		$template_type = 'archive';
	}

	if ( \is_category() || \is_tag() || \is_tax() ) {
		$template_type = 'term';
	}

	if ( \is_post_type_archive() ) {
		$template_type = 'post-type-archive';
	}

	return apply_filters( 'ejo/templating/get_template_type', $template_type );
}

function is_template( $name ) {
	return get_template() == $name;
}

function is_template_type( $name ) {
	return get_template_type() == $name;
}


// function is_singular_page( $post_type = null ) {
// 	return \is_singular( $post_type );
// }

// function is_plural_page( $post_type = null ) {

// 	if ( $post_type == 'post') {
// 		return \is_home() || \is_archive();
// 	}

// 	return \is_search() || \is_home() || \is_archive();
// }

// function is_term_page() {
// 	return \is_category() || \is_tag() || \is_tax();
// }

// function is_special_page( $type = null ) {
// 	$special_page = false;

// 	if ( ! $type ) {
// 		$special_page = false;
// 	}
// 	elseif ( $type == 'blog' ) {
// 		if ( \get_queried_object_id() ) {
// 			$special_page = ( \get_queried_object_id() == get_option('page_for_posts') );
// 		}
// 	}
// 	elseif ( $type == 'blog-on-front' ) {
// 		$special_page = \is_home() && ! \get_option('page_on_front');
// 	}	
// 	elseif ( $type == '404' ) {
// 		$special_page = \is_404();
// 	} 
// 	elseif ( $type == 'search' ) {
// 		$special_page = \is_search();
// 	}
// 	elseif ( $type == 'author' ) {
// 		$special_page = \is_author();
// 	}

// 	return $special_page;
// }

