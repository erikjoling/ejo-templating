<?php 

global $post;

$label = 'pwbox-' . ( empty($post->ID) ? rand() : $post->ID );

?>

<form action="<?= esc_url( site_url( 'wp-login.php?action=postpass', 'login_post' ) ) ?>" class="post-password-form" method="post">
	<div class="row row--full row--margin">
		
		<div class="cell cell--fixed">
			<div class="post-password-form__icon icon"><?php Ejo\Templating\display_svg( 'lock-alt' ); ?></div>
		</div>
		<div class="post-password-form__inner">
			<p><?= __( 'This content is password protected. To view it please enter your password below:' ) ?></p>
			<div class="post-password-form__field">
				<label for="<?= $label ?>">
					<span class="screen-reader-text"><?= __( 'Password:' ) ?></span>
					<input name="post_password" id="' . $label . '" type="password" size="20" placeholder="<?= __( 'Password' ) ?>" />
				</label>
				<input type="submit" name="Submit" value="<?= esc_attr__( 'Unlock' ) ?>" />
			</div>
		</div>

	</div>
</form>
