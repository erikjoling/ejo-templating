<?php
use function Ejo\Templating\display_svg;
?>

<button class="site-nav-toggle" data-toggle="offcanvas-panel" aria-expanded="false">
	<span class="screen-reader-text"><?= __('Open menu', 'ejo-base'); ?></span>

	<svg class="icon site-nav-toggle__icon site-nav-toggle__icon--open" aria-hidden="true" focusable="false">
		<?php display_svg('bars') ?>
	</svg>

	<svg class="icon site-nav-toggle__icon site-nav-toggle__icon--close" aria-hidden="true" focusable="false">
		<?php display_svg('times') ?>
	</svg>

	<span class="site-nav-toggle__text"><?= __('Menu', 'ejo-base'); ?></span>

</button>