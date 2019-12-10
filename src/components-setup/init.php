<?php
/**
 * Templating functions.
 */

namespace Ejo\Templating;


add_action( 'wp', function() {
	require_once __DIR__ . '/default.php';
	require_once __DIR__ . '/blog.php';
	// require_once __DIR__ . '/search.php';
	// require_once __DIR__ . '/404.php';
});

