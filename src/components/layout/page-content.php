<?php
/**
 * Block Area for Site Content Main
 */

use function Ejo\Tmpl\load_components;

// This component expects the following data
$data['components']     = $data['components'] ?? [ 'page/content' ];
$data['custom-classes'] = [];

// Make data filterable
$data = apply_filters( 'ejo/base/template/layout/page-content', $data );

// If no components: Exit
if ( ! $data['components'] ) { return; }

// Process the data
$data['custom-classes'] = implode(" ", $data['custom-classes'] );

?>
<div class="page-content <?= $data['custom-classes'] ?>">
	<div class="page-content__inner">

		<?php load_components( $data['components'] ); ?>

	</div>
</div>