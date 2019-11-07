<?php

// Get archive description
$archive_description = get_the_archive_description();

?>

<?php if ( $archive_description && ! is_paged() ) : ?>

	<div class="archive-description">
		<?= $archive_description; ?>
	</div>

<?php endif ;?>