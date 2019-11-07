<?php

$previous_link = get_previous_post_link( '%link' );
$next_link     = get_next_post_link( '%link' );

$class_modifier = '';

if ( ! $previous_link && ! $next_link ) {
	return;
}

if ( ! $previous_link ) {
	$class_modifier = 'post-nav--first-post';
}
elseif ( ! $next_link ) {
	$class_modifier = 'post-nav--last-post';	
}

?>
<nav class="post-nav <?= $class_modifier ?>" role="navigation">
	<h2 class="screen-reader-text"><?= __( 'Posts navigation' ) ?></h2>

	<div class="post-nav__links">
		
		<?php if ( $previous_link ) : ?>

			<div class="post-nav__previous">
				<div class="post-nav__link-description"><?= __( 'Previous article:', 'ejo-base' ) ?></div>
				<?= $previous_link ?>
			</div>

		<?php endif ;?>

		<?php if ( $next_link ) : ?>

			<div class="post-nav__next">
				<div class="post-nav__link-description"><?= __( 'Next article:', 'ejo-base' ) ?></div>
				<?= $next_link ?>
			</div>

		<?php endif ;?>

	</div>
</nav>