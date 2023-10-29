<?php
/**
 * @package i-monon
 * @author  Mohamed Yassin
 * @link    https://xbees.net/
 * @version 1.1.0
 *============================================================================*/


/* -------------------------------------------------------------------------- */
/*                                Constants                                   */
/* -------------------------------------------------------------------------- */
define('IM_DIR', __DIR__.'/');


define( 'IMDEBUG', false );


if( ! defined( 'IMSHOWERROR' ) ) {
    define( 'IMSHOWERROR', false );
}

/* -------------------------------------------------------------------------- */
/*                                  debug mod                                 */
/* -------------------------------------------------------------------------- */
if( IMDEBUG == true ) {
    require_once ( IM_DIR . 'functions/debug.php' );   
}

/* -------------------------------------------------------------------------- */
/*                               Enqueue Scripts                              */
/* -------------------------------------------------------------------------- */
add_action( 'wp_enqueue_scripts', 'classima_child_styles', 18 );
function classima_child_styles() {
	wp_enqueue_style( 'classipost-style', get_stylesheet_uri() );
}

/* -------------------------------------------------------------------------- */
/*                                    Languages                               */
/* -------------------------------------------------------------------------- */
add_action( 'after_setup_theme', 'classima_child_theme_setup' );
function classima_child_theme_setup() {
    load_theme_textdomain( 'classima', get_stylesheet_directory() . '/languages' );
    load_child_theme_textdomain( 'im', get_stylesheet_directory() . '/languages' );
}

/* -------------------------------------------------------------------------- */
/*                                   Module                                   */
/* -------------------------------------------------------------------------- */
require_once ( IM_DIR . 'module/nafath/class-nafath-db.php' );
require_once ( IM_DIR . 'module/nhc/class-nhc.php' );
require_once ( IM_DIR . 'module/carbon-field/class-carbon-field.php' );
require_once ( IM_DIR . 'module/metaboxes/metaboxes.php' );
require_once ( IM_DIR . 'module/metaboxes/class-custom-column.php' );



/* -------------------------------------------------------------------------- */
/*                       theme hook & function                                */
/* -------------------------------------------------------------------------- */
require_once ( IM_DIR . 'functions/theme-hook.php' );
require_once ( IM_DIR . 'functions/nafath-log.php' );


/* -------------------------------------------------------------------------- */
/*                             LOCATIONS                                      */
/* -------------------------------------------------------------------------- */
require_once ( IM_DIR . 'module/locations-data/class-location.php' );

$Regions     = IM_DIR . 'module/locations-data/Regions.csv';
$Cities      = IM_DIR . 'module/locations-data/Cities.csv';
$Districts_1 = IM_DIR . 'module/locations-data/Districts-1.csv';
$Districts_2 = IM_DIR . 'module/locations-data/Districts-2.csv';
$Districts_3 = IM_DIR . 'module/locations-data/Districts-3.csv';


define('REGIONS',   $Regions);
define('CITIES',    $Cities);
define('DISTRICTS1', $Districts_1);
define('DISTRICTS2', $Districts_2);
define('DISTRICTS3', $Districts_3);