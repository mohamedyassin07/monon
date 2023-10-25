<?php 

class NafathDB {

    public function __construct() {
        add_action( 'init', [$this, 'nafath_callback_table']);
    }

    public function nafath_callback_table()
    {
        global $wpdb;
   
        // Let's not break the site with exception messages
        $wpdb->hide_errors();
        
        if ( ! function_exists( 'dbDelta' ) ) {
            require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        }
        
        $collate = '';
        
        if ( $wpdb->has_cap( 'collation' ) ) {
            $collate = $wpdb->get_charset_collate();
        }

        $table_name = $wpdb->prefix.'nafath_callback';
        $query = $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $table_name ) );

        if ( ! $wpdb->get_var( $query ) == $table_name ) {
            $sql = "CREATE TABLE $table_name (
                id INTEGER NOT NULL AUTO_INCREMENT,
                transId TEXT NOT NULL,
                cardId TEXT NOT NULL,
                userInfo longtext NOT NULL,
                response longtext NOT NULL,
                status TEXT NOT NULL,
                PRIMARY KEY (id)
                ) $charset_collate;";
            dbDelta( $sql );  
        }   
        
        $table_name = $wpdb->prefix . 'nafath_callback';
        $column_name = 'date_created';

        // Check if the column exists
        $column_exists = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM information_schema.columns WHERE table_name = %s AND column_name = %s",
                $table_name,
                $column_name
            )
        );

        if (!$column_exists) {
            // Add the column if it doesn't exist
            $wpdb->query(
                $wpdb->prepare(
                    "ALTER TABLE $table_name ADD COLUMN $column_name DATETIME DEFAULT CURRENT_TIMESTAMP"
                )
            );
        }
    }

    public function update_nafath_callback( $data = array() )
    {
        $transId  = isset($data['transId']) ? $data['transId'] : '';
        $cardId   = isset($data['cardId']) ? $data['cardId'] : '';
        $userInfo = isset($data['userInfo']) ? $data['userInfo'] : [];
        $status   = isset($data['status']) ? $data['status'] : '';


        global $wpdb;
        $table_name  = $wpdb->prefix."nafath_callback";

        $nafath_callback_id = $wpdb->get_results( "SELECT id FROM `{$table_name}` WHERE `transId` = '{$transId}' AND `cardId` = '{$cardId}'");
        
        $nafath_data = [
            'transId'  => $transId,
            'cardId'   => $cardId,
            'userInfo' => maybe_serialize( $userInfo ) ,
            'response' => maybe_serialize( $data ),
            'status'   => $status
        ];
         
        //If nothing found to update, it will try and create the record.
        if ( $nafath_callback_id === false || $nafath_callback_id < 1 || empty($nafath_callback_id) ) {
            $dataTB = $wpdb->insert($table_name, $nafath_data);
        }
        else{
            $where  = [ 'id' => $nafath_callback_id[0]->id ];
            $dataTB = $wpdb->update( $table_name, $nafath_data, $where );
        }

        return $dataTB;

    }

    public function get_status( $data = array() )
    {
        $transId  = isset($data['transId']) ? $data['transId'] : '';
        $cardId   = isset($data['cardId']) ? $data['cardId'] : '';
        $userInfo = isset($data['userInfo']) ? $data['userInfo'] : [];

        global $wpdb;
        $table_name  = $wpdb->prefix."nafath_callback";

        $nafath_callback = $wpdb->get_results( "SELECT * FROM `{$table_name}` WHERE `transId` = '{$transId}' AND `cardId` = '{$cardId}'");
 
        if( $nafath_callback ){
            $stauts = $nafath_callback[0]->status;
            if( $stauts === 'COMPLETED' || $stauts === 'REJECTED' ) {
                return true;
            }else{
                return false;
            }
        }
        return false;
    }

    public function get_nafath_data( $data = array() )
    {
        $transId  = isset($data['transId']) ? $data['transId'] : '';
        $cardId   = isset($data['cardId']) ? $data['cardId'] : '';
        $userInfo = isset($data['userInfo']) ? $data['userInfo'] : [];

        $nafath_callback = [];

        global $wpdb;
        $table_name  = $wpdb->prefix."nafath_callback";

        $nafath_callback = $wpdb->get_results( "SELECT * FROM `{$table_name}` WHERE `transId` = '{$transId}' AND `cardId` = '{$cardId}'");
        $data = [];
        if( !empty( $nafath_callback) && $nafath_callback[0]->status === 'REJECTED' ) {
            $data = [
                'status' => 'REJECTED',
                'msg'    => 'تم رفض الطلب من قبل المستخدم.'
            ];
        }
        else if( !empty( $nafath_callback ) ) {
            $userInfo = unserialize($nafath_callback[0]->userInfo); 
            $data = [
                'arFullName' => $userInfo->arFullName,
                'arFirst' => $userInfo->arFirst,
                'arGrand' => $userInfo->arFamily,
                'arTwoNames' => $userInfo->arTwoNames,
                'status' => $nafath_callback[0]->status,
            ];
        }
        return $data;
    }

}
new NafathDB();