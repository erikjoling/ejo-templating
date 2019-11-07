<?php 

use function Ejo\Base\load_components;

// This component expects the following data
$data['components']     = [ 'page/title' ];
$data['custom-classes'] = [];

// Let plugins of themes filter this components data
$data = apply_filters( 'ejo/base/template/page/comments', $data );

// If no components: Exit
if ( ! $data['components'] ) { return; }

// Process the data
$data['custom-classes'] = implode(" ", $data['custom-classes'] );

?>
<section class="page-comments <?= $data['custom-classes'] ?>">
	<div class="page-comments__inner">
		<h1><?= __( 'Comments', 'ejo-base') ?></h1>
	</div>
</section>