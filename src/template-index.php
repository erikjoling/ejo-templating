<?php 

use function Ejo\Tmpl\display_html_attr;
use function Ejo\Tmpl\display_body_classes;
use function Ejo\Tmpl\render_component;

?>
<!doctype html>
<html <?= display_html_attr(); ?>>

<head>
	<?php wp_head(); ?>
</head>

<body class="<?php display_body_classes(); ?>">

	<?php echo render_component( 'site' ); ?>

	<?php wp_footer(); ?>
	
</body>

</html>