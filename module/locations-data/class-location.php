<?php 

class IMLOCATIONS {
    public function __construct() {
        /*-----------------------------------------------------------------------------------*/
        //  sa_cities.sql only run once if we need it .
        /*-----------------------------------------------------------------------------------*/
        if( isset($_GET['ag-state']) && $_GET['ag-state'] == 1 && is_admin()) {
            add_action( 'init', array($this , 'add_sa_provinces' ));
        }
        if( isset($_GET['ag-city']) && $_GET['ag-city'] == 1 && is_admin()) {
            add_action( 'init', array($this , 'add_sa_cities' ));  
        }
        if( isset($_GET['ag-area-1']) && $_GET['ag-area-1'] == 1 && is_admin()) {
            add_action( 'init', array($this , 'add_sa_area' ));
        }
        if( isset($_GET['ag-area-2']) && $_GET['ag-area-2'] == 1 && is_admin()) {
            add_action( 'init', array($this , 'add_sa_area' ));
        }if( isset($_GET['ag-area-3']) && $_GET['ag-area-3'] == 1 && is_admin()) {
            add_action( 'init', array($this , 'add_sa_area' ));
        }
    }

    public function add_sa_provinces(){
        include  IM_DIR . 'module/locations-data/sa-data/add-sa-provinces.php';
    }

    public function add_sa_cities(){
        include  IM_DIR . 'module/locations-data/sa-data/add_sa_cities.php';
    }

    public function add_sa_area(){
        if( isset($_GET['ag-area-1']) && $_GET['ag-area-1'] == 1 && is_admin() ) {
            $file = DISTRICTS1;
        }else if( isset($_GET['ag-area-2']) && $_GET['ag-area-2'] == 1 && is_admin() ) {
            $file = DISTRICTS2;
        }else if( isset($_GET['ag-area-3']) && $_GET['ag-area-3'] == 1 && is_admin() ) {
            $file = DISTRICTS3;
        }
        if( $file ) {
            include  IM_DIR . 'module/locations-data/sa-data/add_sa_area.php';
        }
    }
}
new IMLOCATIONS();