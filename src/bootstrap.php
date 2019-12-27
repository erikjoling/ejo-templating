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
require_once __DIR__ . '/functions-helpers.php';
require_once __DIR__ . '/functions-filters.php';

require_once __DIR__ . '/composition.php';
require_once __DIR__ . '/components-setup/init.php';

require_once __DIR__ . '/functions-templating-helpers.php';
require_once __DIR__ . '/functions-templating-conditionals.php';
require_once __DIR__ . '/functions-templating.php';

// Set check
define('EJO_TEMPLATING_IS_LOADED', true);