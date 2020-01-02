<?php 
/**
 * Template conditionals
 */

namespace Ejo\Templating;

/**
 * Try to simplify WordPress templates
 *
 * 
 */
function get_template() {
	$template = 'unknown';

	/**
	 * If singular the template is the post type (post, page, attachment, custom)
	 */
	if (\is_singular()) {
		$template = \get_post_type();
	}

	elseif ( \get_queried_object_id() && ( \get_queried_object_id() == get_option('page_for_posts') ) ) {
		$template = 'post-archive';
	}
	elseif ( \is_home() && ! \get_option('page_on_front') ) {
		$template = 'blog-no-page';
	}
	elseif ( \is_post_type_archive() ) {
		$template = \get_post_type() . '-archive';
	}
	elseif ( \is_category() ) {
		$template = 'category';
	}
	elseif ( \is_tag() ) {
		$template = 'tag';
	}
	elseif ( \is_tax() ) {
		$template = \get_queried_object()->term_id;
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
		$template_type = 'singular';
	}

	elseif ( \is_archive() || is_template('post-archive') || is_template('blog-no-page') || \is_post_type_archive() || is_template('search') ) {
		$template_type = 'archive';
	}

	return apply_filters( 'ejo/templating/get_template_type', $template_type );
}

function is_template( $name ) {
	return get_template() == $name;
}

function is_template_type( $name ) {
	return get_template_type() == $name;
}

// function is_main_loop() {
// 	return ( \get_the_ID() == \get_queried_object_id() );
// }

function is_blog_page() {
	return \get_queried_object_id() && ( \get_queried_object_id() == get_option('page_for_posts') );
}

function is_blog_home() {
	return \is_home() && ! \get_option('page_on_front');
}