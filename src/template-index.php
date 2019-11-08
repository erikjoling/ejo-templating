<?php 

use function Ejo\Tmpl\display_html_attr;
use function Ejo\Tmpl\display_body_classes;
// use function Ejo\Tmpl\load_component;


?>
<!doctype html>
<html <?= display_html_attr(); ?>>

<head>
	<?php wp_head(); ?>
</head>

<body class="<?php display_body_classes(); ?>">

	<h1>Template index</h1>

	<?php // load_component( 'layout/site' ); ?>

	<?php 

	echo Ejo\Tmpl\render_component( 'site' ); 

	?>

	<?php wp_footer(); ?>
	
</body>

</html>