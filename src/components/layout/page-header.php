<?php
/**
 * Page Main Header
 *
 * This template expects a $data array
 */

use function Ejo\Templating\load_components;

// This component expects the following data
$data['components']     = [ 'page/breadcrumbs', 'page/title' ];
$data['custom-classes'] = [];

// Let plugins of themes filter this components data
$data = apply_filters( 'ejo/base/template/layout/page-header', $data );

// If no components: Exit
if ( ! $data['components'] ) { return; }

// Process the data
$data['custom-classes'] = implode(" ", $data['custom-classes'] );

?>
<header class="page-header <?= $data['custom-classes'] ?>">
	<div class="page-header__inner">

		<?php load_components( $data['components'] ); ?>

	</div>
</header>