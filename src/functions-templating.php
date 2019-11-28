<?php
/**
 * Templating functions.
 */

namespace Ejo\Templating;


/** 
 * Render HTML attributes
 */
function render_html_attr() {
	return render_attr( get_html_attr() );
}


/** 
 * Render Body classes
 */
function render_body_classes() {
	return render_classes( \get_body_class() );
}

/** 
 * Render Site
 */
function render_site() {

	\ob_start();

	?>
	<!doctype html>
	<html <?= render_html_attr(); ?>>

	<head>
		<?php \wp_head(); ?>
	</head>

	<body class="<?= render_body_classes(); ?>">

		<?php Composition::display(); ?>

		<?php \wp_footer(); ?>
		
	</body>

	</html>
	<?php

	return \ob_get_clean();
}

function render_site_branding_link() {
	return '<a class="site-branding__link" href="'. home_url() .'" rel="home">'. get_bloginfo( 'name', 'display' ) .'</a>';
}

function render_breadcrumbs() {

	return 'Breadcrumbs';
}


function render_page_title() {

	ob_start();
	?>

	<h1 class="page-title"><?= get_page_title() ?></h1>
	
	<?php
	return ob_get_clean();
}

function render_page_content() {

	return get_page_content();
}

function render_post_loop( $args = null ) {
	ob_start();

	if ( $args ) {
		$custom_query = new \WP_Query( $args ); 
			
		while ( $custom_query->have_posts() ) { 
			$custom_query->the_post(); 

			echo Composition::render_component('plural-post');

			wp_reset_postdata(); 
		}

	}
	else {
		while ( have_posts() ) { 
			the_post();

			echo Composition::render_component('plural-post');
		}
	}

	
	return ob_get_clean();
}

function render_plural_post_title() {

	ob_start();

	?>
	<a href="<?php the_permalink() ?>" class=""><?php the_title(); ?></a>
	<?php

	return ob_get_clean();
}

function render_plural_post_content() {
	ob_start();

	?>
	<?php the_excerpt(); ?>
	<?php

	return ob_get_clean();
}

function render_post_author() {
	$svg 	= apply_filters('ejo/templating/svg/author', '');
	$author = get_the_author();

	return "{$svg}<span>{$author}</span>";
}

function render_post_date() {
	$svg  = apply_filters('ejo/templating/svg/date', '');
	$date = get_the_date();

	return "{$svg}<span>{$date}</span>";
}

function render_post_categories() {
	$svg  		= apply_filters('ejo/templating/svg/categories', '');
	$categories = get_the_term_list( get_the_ID(), 'category', '', ', ', '' );
	
	return "{$svg}<span>{$categories}</span>";
}

function render_recent_posts( $args ) {

	// return Composition::render_component( 'recent-posts', [
	// 	'content' => [ 
	// 		'recent-posts-header', 
	// 		[ 'name' => 'recent-posts-main', 'content' => render_post_loop( $args )	],
	// 	]
	// ] );
}


/**
 * Render the footer line
 */
function render_site_credits() {

	return sprintf( __( 'Website by %s & %s', 'ejo-base' ), 
		'<a href="https://www.ejoweb.nl/" target="_blank" title="Ejoweb">Erik</a>',
		'<a href="https://www.woutervanderzee.nl/" target="_blank" title="Wouter van der Zee">Wouter</a>' 
	);
}

/**
 * Render the footer line
 */
function render_site_info() {

	return get_bloginfo( 'name', 'display' ) . ' © ' . date('Y');
}

/**
 * Render the footer line
 */
function render_site_nav_toggle() {
	ob_start();
	?>

	<button class="site-nav-toggle" aria-expanded="false">
		<span class="screen-reader-text"><?= __('Open menu', 'ejo-base'); ?></span>

		<svg class="icon site-nav-toggle__icon site-nav-toggle__icon--open" aria-hidden="true" focusable="false">
			<?php echo apply_filters('ejo/templating/svg/site-nav-toggle-open', ''); ?>
		</svg>

		<svg class="icon site-nav-toggle__icon site-nav-toggle__icon--close" aria-hidden="true" focusable="false">
			<?php echo apply_filters('ejo/templating/svg/site-nav-toggle-close', ''); ?>
		</svg>

		<span class="site-nav-toggle__text"><?= __('Menu', 'ejo-base'); ?></span>

	</button>

	<?php 
	$render = ob_get_clean();

   	return $render;
}


/**
 * Render site nav
 */
function render_site_nav() {
	$menu_location = apply_filters( 'ejo/tmpl/site_nav_location', 'site-nav' );

	ob_start();
	?>

	<?php if ( has_nav_menu( $menu_location ) ) : ?>

		<nav class="site-nav">

			<h3 class="site-nav__title screen-reader-text">
				<?php get_menu_name( $menu_location ) ?>
			</h3>

			<?php wp_nav_menu( [
				'theme_location' => $menu_location,
				'container'      => '',
				'menu_id'        => '',
				'menu_class'     => 'site-nav__items',
				'items_wrap'     => '<ul class="%2$s">%3$s</ul>',
				'item_spacing'   => 'discard'
			] ) ?>

		</nav>

	<?php else : ?>
		
		<nav class="site-nav site-nav--no-menu">

			<ul class="site-nav__items">
				<li class="site-nav__item"><span class="site-nav__link"><?= __('Menu not set yet', 'ejo-base') ?></span></li>
			</ul>

		</nav>

	<?php endif ?>

	<?php 
	$render = ob_get_clean();

   	return $render;
}

/**
 * Render post nav
 *
 * @param string $type (next or previous)
 * @return string
 */
function render_post_nav_link( $type = '' ) {

	$function = "get_{$type}_post_link";

	if (is_callable($function)) {
		$link = $function( '%link' );
	}

	$bem_block = Composition::get_parent();

	ob_start();
	?>
			
	<?php if ( $link ) : ?>

		<div class="<?= $bem_block ?>__link <?= $bem_block ?>__link--<?= $type ?>">
			<div class="<?= $bem_block ?>__link-description"><?= __( ucfirst($type) .' article:', 'ejo-base' ) ?></div>
			<?= $link ?>
		</div>

	<?php endif ;?>

	<?php 
	$render = ob_get_clean();

   	return $render;
}


/**
 * Render post nav
 */
function render_posts_nav() {

	// Hybrid\Pagination\display( 'posts', [
	// 	'prev_next' => false,
	// 	'title_text' => __( 'Page:', 'ejo-base' ),
	// 	'title_class' => 'pagination__title',
	// ] );

	ob_start();
	?>

	<nav><h2>posts navigation</h2></nav>

	<?php 
	$render = ob_get_clean();

   	return $render;
}

function render_404_title() {

	if (method_exists('\Ejo\Pack\Page404', 'render_title')) {
		$render = \Ejo\Pack\Page404::render_title();
	}

	return $render ?? __('Page not Found', 'theme-erik');
}

function render_404_content() {

	if (method_exists('\Ejo\Pack\Page404', 'render_content')) {
		$render = \Ejo\Pack\Page404::render_content();
	}

	return $render ?? apply_filters( 'the_content', __('Oops', 'theme-erik') );
}

function render_search_title() {

	return sprintf( esc_html__( 'Search results for: %s', 'ejo-base' ), get_search_query() );
}

function render_search_content() {

	return '';
}