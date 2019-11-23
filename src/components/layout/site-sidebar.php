<?php 

use function Ejo\Templating\load_components;

// This component expects the following data
$data['components']     = [ 'site/branding' ];
$data['custom-classes'] = [];

// Let plugins of themes filter this components data
$data = apply_filters( 'ejo/base/template/layout/site-sidebar', $data );

// If no components: Exit
if ( ! $data['components'] ) { return; }

// Process the data
$data['custom-classes'] = implode(" ", $data['custom-classes'] );

?>
<aside class="site-sidebar <?= $data['custom-classes'] ?>">
	<div class="site-sidebar__inner">
		<h1><?= __( 'Site Sidebar', 'ejo-base') ?></h1>
	</div>
</aside>