<?php
/**
 * Footer Line
 */

use function Ejo\Base\render_site_info;
use function Ejo\Base\render_site_credits;
?>

<div class="site-meta"><?= render_site_info(); ?> | <?= render_site_credits(); ?></div>
