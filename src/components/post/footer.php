<?php
/**
 * Post Footer
 */
use function Ejo\Base\load_components;

// Which components to use and in which order
$data['components'] = apply_filters( 'ejo/base/template/post/footer', [ 'post/more-link', 'post/meta' ] );

?>
<footer class="post__footer">	

	<?php load_components( $data['components'] ); ?>

</footer>