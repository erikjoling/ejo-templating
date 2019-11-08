<?php
/**
 * Templating functions.
 */

namespace Ejo\Tmpl;


function render_site_branding() {

	ob_start();
	?>

	<div class="site-branding">
		<h1 class="site-branding__title">
			<a class="site-branding__link" href="<?= home_url() ?>" rel="home"><?= get_bloginfo( 'name', 'display' ) ?></a>
		</h1>
	</div>
	
	<?php
	$render = ob_get_contents();
   	ob_end_clean();

	return $render;
}

function render_page_title() {

	ob_start();
	?>

	<h1 class="page-title"><?= get_the_title() ?></h1>
	
	<?php
	$render = ob_get_contents();
   	ob_end_clean();

	return $render;
}

function render_page_content() {

	ob_start();
	the_content();
	$render = ob_get_contents();
   	ob_end_clean();

	return $render;
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
