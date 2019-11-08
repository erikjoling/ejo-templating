<?php
/**
 * Page Footer
 */
use function Ejo\Tmpl\load_components;

// This component expects the following data
$data['components']     = $data['components'] ?? [ 'site/footer-blocks', 'site/meta' ];
$data['custom-classes'] = [];

// Make data filterable
$data = apply_filters( 'ejo/base/template/layout/site-footer', $data );

// If no components: Exit
if ( ! $data['components'] ) { return; }

// Process the data
$data['custom-classes'] = implode(" ", $data['custom-classes'] );

?>
<footer class="site-footer <?= $data['custom-classes'] ?>">
	<div class="site-footer__inner">

		<?php load_components( $data['components'] ); ?>

	</div>
</footer>
