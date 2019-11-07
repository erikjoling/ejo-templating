<?php
/**
 * Post More Link
 */
use function Ejo\Base\load_components;

?>
<a href="<?php the_permalink() ?>" class="read-more"><?= __('Read more', 'ejo-base'); ?></a>