<?php
/**
 * Post Meta Categories
 */

use function Ejo\Base\render_svg;
use function Hybrid\Post\display_terms;

// This component expects the following data
$data['svg']  = $data['svg']  ?? render_svg('bookmark');

// Make data filterable
$data = apply_filters( 'ejo/base/template/post/meta_author', $data );

// Setup 
$svg = ( $data['svg'] )  ? $data['svg']  : '';

?>
<div class="post-meta__categories">
	<?= $svg ?>
	<span><?= display_terms() ?></span>
</div>