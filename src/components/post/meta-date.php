<?php
/**
 * Post Meta Date
 */

use function Ejo\Tmpl\render_svg;
use function Hybrid\Post\display_date;

// This component expects the following data
$data['svg']  = $data['svg']  ?? render_svg('clock');

// Make data filterable
$data = apply_filters( 'ejo/base/template/post/meta_author', $data );

// Setup 
$svg = ( $data['svg'] )  ? $data['svg']  : '';

?>
<div class="post-meta__date">
	<?= $svg ?>
	<span><?= display_date() ?></span>
</div>