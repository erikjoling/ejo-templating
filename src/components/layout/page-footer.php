<?php
/**
 * Block Area for Site Content Footer
 */

use function Ejo\Base\load_components;

// This component expects the following data
$data['components']     = $data['components'] ?? [];
$data['custom-classes'] = [];

// Make data filterable
$data = apply_filters( 'ejo/base/template/layout/page-footer', $data );

// If no components: Exit
if ( ! $data['components'] ) { return; }

// Process the data
$data['custom-classes'] = implode(" ", $data['custom-classes'] );

?>
<footer class="page-footer <?= $data['custom-classes'] ?>">
	<div class="page-footer__inner">

		<?php load_components( $data['components'] ); ?>

	</div>
</footer>