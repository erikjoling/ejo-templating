<?php
/**
 * Post
 */

use function Ejo\Templating\load_components;

// Which components to use and in which order
$components = apply_filters( 'ejo/base/post/post-plural', [ 'post/header', 'post/excerpt', 'post/footer' ] );
?>

<article class="post post--plural">
	<div class="post__inner">

		<?php load_components( $components ); ?>

	</div>
</article>