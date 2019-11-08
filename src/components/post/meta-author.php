<?php
/**
 * Post Meta Author
 */

use function Ejo\Tmpl\render_svg;

// This component expects the following data
$data['svg']  = $data['svg']  ?? render_svg('user-circle');

// Make data filterable
$data = apply_filters( 'ejo/base/template/post/meta_author', $data );

// Setup 
$svg = ( $data['svg'] )  ? $data['svg']  : '';

?>
<div class="post-meta__author">
	<?= $svg ?>
	<span><?= get_the_author() ?></span>
</div>