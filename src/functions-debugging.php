<?php
/**
 * Debug functions.
 */

namespace Ejo\Tmpl;

/**
 * Log data to wp-content/debug.log
 *
 * It doesn't matter if WP_DEBUG is true because I also want to be able
 * to log on production environment (which has WP_DEBUG disabled)
 */
function log( $data )  {
    if ( is_array( $data ) || is_object( $data ) ) {
        error_log( print_r( $data, true ) );
    } else {
        error_log( $data );
    }
}
