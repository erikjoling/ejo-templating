<?php
use function Hybrid\Site\display_title as site_title;
use function Hybrid\Site\display_description as site_description;
?>

<div class="site-branding">
	<?php \the_custom_logo() ?>
	<?php site_title( array( 'class' => 'site-branding__title', 'link_class' => 'site-branding__link special-link' ) ) ?>
	<?php site_description( array( 'class' => 'site-branding__description' ) ) ?>
</div>