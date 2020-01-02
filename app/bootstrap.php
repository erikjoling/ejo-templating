<?php
/**
 * Initialize cleanup related functionality
 */

namespace Ejo\Templating;

// Do not load if it's already loaded
if ( defined('EJO_TEMPLATING_IS_LOADED') ) {
	return;
}

require_once __DIR__ . '/functions-debugging.php';

require_once __DIR__ . '/../src/class-composition.php';
require_once __DIR__ . '/../src/functions-helpers.php';
require_once __DIR__ . '/../src/functions-filters.php';
require_once __DIR__ . '/../src/functions-helpers.php';
require_once __DIR__ . '/../src/functions-conditionals.php';
require_once __DIR__ . '/../src/functions-template.php';

add_action( 'wp', function() {
	require_once __DIR__ . '/../src/components/default.php';
	require_once __DIR__ . '/../src/components/post.php';
});

// Set check
define('EJO_TEMPLATING_IS_LOADED', true);