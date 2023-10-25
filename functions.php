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


define( 'IMDEBUG', true );


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

/* -------------------------------------------------------------------------- */
/*                       theme hook & function                                */
/* -------------------------------------------------------------------------- */
require_once ( IM_DIR . 'functions/theme-hook.php' );
require_once ( IM_DIR . 'functions/nafath-log.php' );

