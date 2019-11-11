<?php
/**
 * Templating functions.
 */

namespace Ejo\Tmpl;

function render_site() {

	ob_start();

	?>
	<!doctype html>
	<html <?= display_html_attr(); ?>>

	<head>
		<?php wp_head(); ?>
	</head>

	<body class="<?php display_body_classes(); ?>">

		<?php echo render_component( 'site' ); ?>

		<?php wp_footer(); ?>
		
	</body>

	</html>
	<?php

	return ob_get_clean();
}

function render_site_branding() {

	ob_start();
	?>

	<div class="site-branding">
		<h1 class="site-branding__title">
			<a class="site-branding__link" href="<?= home_url() ?>" rel="home"><?= get_bloginfo( 'name', 'display' ) ?></a>
		</h1>
	</div>
	
	<?php
	return ob_get_clean();
}

function render_breadcrumbs() {

	ob_start();
	?>

	Breadcrumbs
	
	<?php
	return ob_get_clean();
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

function render_post_archive_loop() {
	$render = '';

	ob_start();
	
	while ( have_posts() ) {
		the_post();
		echo render_plural_post();
	}
	
	return ob_get_clean();
}

function render_plural_post() {

	ob_start();
	?>

	<article class="post post--plural">
		<div class="post__inner">
			
			<?php echo render_plural_post_header(); ?>
			<?php echo render_plural_post_content(); ?>
			<?php echo render_plural_post_footer(); ?>

		</div>
	</article>
	
	<?php
	return ob_get_clean();
}

function render_plural_post_header() {

	ob_start();

	?>
	<header class="post__header">
		<h3><a href="<?php the_permalink() ?>" class=""><?php the_title(); ?></a></h3>
	</header>
	<?php

	return ob_get_clean();
}

function render_plural_post_content() {
	ob_start();

	?>
	<div class="post__excerpt">
		<?php the_excerpt(); ?>
	</div>
	<?php

	return ob_get_clean();
}

function render_plural_post_footer() {
	ob_start();

	?>
	<footer class="post__footer">
		Meta
	</footer>
	<?php

	return ob_get_clean();
}

function render_author() {

	ob_start();
	?>

	Erik Joling
	
	<?php
	return ob_get_clean();
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
			<?php //display_svg('bars') ?>
		</svg>

		<svg class="icon site-nav-toggle__icon site-nav-toggle__icon--close" aria-hidden="true" focusable="false">
			<?php //display_svg('times') ?>
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
 */
function render_post_nav() {

	$previous_link = get_previous_post_link( '%link' );
	$next_link     = get_next_post_link( '%link' );

	$class_modifier = '';

	if ( ! $previous_link && ! $next_link ) {
		return;
	}

	if ( ! $previous_link ) {
		$class_modifier = 'post-nav--first-post';
	}
	elseif ( ! $next_link ) {
		$class_modifier = 'post-nav--last-post';	
	}

	ob_start();
	?>

	<nav class="post-nav <?= $class_modifier ?>" role="navigation">
		<h2 class="screen-reader-text"><?= __( 'Post navigation' ) ?></h2>

		<div class="post-nav__links">
			
			<?php if ( $previous_link ) : ?>

				<div class="post-nav__previous">
					<div class="post-nav__link-description"><?= __( 'Previous article:', 'ejo-base' ) ?></div>
					<?= $previous_link ?>
				</div>

			<?php endif ;?>

			<?php if ( $next_link ) : ?>

				<div class="post-nav__next">
					<div class="post-nav__link-description"><?= __( 'Next article:', 'ejo-base' ) ?></div>
					<?= $next_link ?>
				</div>

			<?php endif ;?>

		</div>
	</nav>

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
