<?php
/**
 * Content Title
 */
use function Ejo\Tmpl\get_page_title;

?>
<<?= $tag ?> <?= $classes ?><?= $attr ?>><?= get_page_title(); ?></<?= $tag ?>>

<<?= $tag ?> <?= $classes ?><?= $attr ?>><?= render_components(...); ?></<?= $tag ?>>