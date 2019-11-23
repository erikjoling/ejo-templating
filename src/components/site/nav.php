<?php 

use function Ejo\Templating\get_nav_location;
use function Hybrid\Menu\display_name;

?>

<?php if ( has_nav_menu( get_nav_location() ) ) : ?>

	<nav class="site-nav">

		<h3 class="site-nav__title screen-reader-text">
			<?php display_name( get_nav_location() ) ?>
		</h3>

		<?php wp_nav_menu( [
			'theme_location' => get_nav_location(),
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