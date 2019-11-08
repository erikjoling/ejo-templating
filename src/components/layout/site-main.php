<?php
/**
 * Component for Page
 */

use function Ejo\Tmpl\load_components;

// This component expects the following data
$data['components']     = $data['components'] ?? [ 'layout/page', 'layout/site-sidebar' ];
$data['custom-classes'] = [];

// Make data filterable
$data = apply_filters( 'ejo/base/template/layout/site-main', $data );

// If no components: Exit
if ( ! $data['components'] ) { return; }

// Process the data
$data['custom-classes'] = implode(" ", $data['custom-classes'] );

?>
<main class="site-main <?= $data['custom-classes'] ?>" id="site-main">
	<div class="site-main__inner">

		<?php load_components( $data['components'] ); ?>

	</div>
</main>