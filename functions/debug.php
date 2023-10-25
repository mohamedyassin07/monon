<?php 
/**
 * @package i-monon
 * @author  Mohamed Yassin
 * @link    https://xbees.net/
 * @version 1.1.0
 *============================================================================*/

/* ------------------------------- show error ------------------------------- */
if( IMSHOWERROR ) {
    @ini_set('display_errors', 1);
    @ini_set('display_startup_errors', 1);
    @error_reporting(1);
}

/* ------------------------------- print debug ------------------------------ */
function PreDebug( $echo = '' )  {
    echo '<pre dir="ltr">';
        print_r( $echo );
    echo '</pre>';
}