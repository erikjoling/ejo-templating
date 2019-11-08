<?php 
/**
 * Blog block
 */

namespace Ejo\Tmpl;

// This component expects the following data
$data['align']  = $data['align']  ?? 'wide';
$data['layout'] = $data['layout'] ?? 'grid';

// Make data filterable
$data = apply_filters( 'ejo/base/template/block/blog', $data );

// Setup classes
$align_class  = ( $data['align'] )  ? 'align' . $data['align']    : '';
$layout_class = ( $data['layout'] ) ? 'layout-' . $data['layout'] : '';

?>
<div class="wp-block-ejo-base-blog-block <?= $align_class ?>">
	<div class="wp-block-ejo-base-blog-block__inner <?= $layout_class ?>">

		<?php if ( module_is_active( 'blog' ) ) : ?>

			<?php $query_latest_posts = new \WP_Query( 'posts_per_page=3' ); ?>
			
			<?php while ( $query_latest_posts->have_posts() ) : $query_latest_posts->the_post(); ?>

				<?php load_component( 'post/plural-post' ); ?>

				<?php wp_reset_postdata(); ?>

			<?php endwhile; ?>

		<?php else : ?>

			<h2>Blog niet ingeschakeld</h2>

		<?php endif; ?>

	</div>
</div>