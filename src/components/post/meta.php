<?php
/**
 * Post Meta
 */
use function Ejo\Tmpl\load_components;

// This component expects the following data
$data['components']     = $data['components'] ?? [ 'post/meta-author', 'post/meta-date', 'post/meta-categories' ];
$data['class-modifier'] = $data['class-modifier']  ?? '';

// Make data filterable
$data = apply_filters( 'ejo/base/template/post/meta', $data );

// Setup classes
$modified_class = ( $data['class-modifier'] )  ? 'post-meta--' . $data['class-modifier'] : '';

?>
<div class="post-meta <?= $modified_class ?>">

	<?php load_components( $data['components'] ); ?>

</div>