<?php
/**
 * Site component
 */
use function Ejo\Tmpl\load_components;

// This component expects the following data
$data['components']     = [ 'layout/site-header', 'layout/site-main', 'layout/site-footer' ];
$data['custom-classes'] = [];

// Make data filterable
$data = apply_filters( 'ejo/base/template/layout/site', $data );

// If no components: Exit
if ( ! $data['components'] ) { return; }

// Process the data
$data['custom-classes'] = implode(" ", $data['custom-classes'] );

?>
<div class="site <?= $data['custom-classes'] ?>">
	<div class="site__inner">

		<?php load_components( $data['components'] ); ?>

	</div>
</div>