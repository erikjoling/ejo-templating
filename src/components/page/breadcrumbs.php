<?php 

Hybrid\Breadcrumbs\Trail::display([
	'labels'          => [],
	'post_taxonomy'   => [],
	'show_on_front'   => false,
	'show_trail_end'  => true,
	'network'         => false,
	'before'          => '',
	'after'           => '',
	'container_tag'   => 'nav',
	'title_tag'       => 'h2',
	'list_tag'        => 'ul',
	'item_tag'        => 'li',
	'container_class' => 'breadcrumbs',
	'title_class'     => 'breadcrumbs__title',
	'list_class'      => 'breadcrumbs__trail',
	'item_class'      => 'breadcrumbs__crumb'
]);
