<?php
/**
 * Block Area for Site Main
 */

use function Ejo\Tmpl\load_components;
use function Ejo\Tmpl\the_post;

// This component expects the following data
$data['components']     = $data['components'] ?? [ 'layout/page-header', 'layout/page-content', 'layout/page-footer', 'page/comments' ];
$data['custom-classes'] = [];

// Make data filterable
$data = apply_filters( 'ejo/base/template/layout/page', $data );

// If no components: Exit
if ( ! $data['components'] ) { return; }

// Process the data
$data['custom-classes'] = implode(" ", $data['custom-classes'] );

?>
<?php the_post(); ?>
<article class="page <?= $data['custom-classes'] ?>">
	<div class="page__inner">

		<?php load_components( $data['components'] ); ?>
	
	</div>
</article>