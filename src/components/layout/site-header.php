<?php
/**
 * Page Header
 */
use function Ejo\Templating\load_components;

$data['components']     = [ 'site/skip-to-content', 'site/branding', 'site/nav-toggle', 'site/nav'  ];
$data['custom-classes'] = [];

// Make data filterable
$data = apply_filters( 'ejo/base/template/layout/site-header', $data );

// If no components: don't output template
if ( ! $data['components'] ) { return; }

// Process the data
$data['custom-classes'] = implode(" ", $data['custom-classes'] );

?>
<header class="site-header <?= $data['custom-classes'] ?>">
	<div class="site-header__inner">

		<?php load_components( $data['components'] ); ?>

	</div>
</header>