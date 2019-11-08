<?php
/**
 * Post Loop
 *
 * This template expects a $data parameter which holds the
 * components to show, order-specific
 */

use function Ejo\Tmpl\load_component;

// This component expects the following data
$data['components']     = [ 'post/post-plural' ];
$data['custom-classes'] = [];

// Let plugins of themes filter this components data
$data = apply_filters( 'ejo/base/template/post/loop', $data );

// If no components: Exit
if ( ! $data['components'] ) { return; }

// Process the data
$data['custom-classes'] = implode(" ", $data['custom-classes'] );

// // This component expects the following data
// $data['align']  = $data['align']  ?? 'wide';
// $data['layout'] = $data['layout'] ?? 'grid';

// // Make data filterable
// $data = apply_filters( 'ejo/base/template/post/loop', $data );

// // Setup classes
// $align_class  = ( $data['align'] )  ? 'align' . $data['align']    : '';
// $layout_class = ( $data['layout'] ) ? 'layout-' . $data['layout'] : '';
?>
<div class="post-loop <?= $data['custom-classes'] ?>">
	<div class="post-loop__inner">

	<?php while ( have_posts() ) : the_post(); ?>

		<?php load_component( 'post/post-plural', $data ) ?>

	<?php endwhile ?>
	
	</div>
</div>