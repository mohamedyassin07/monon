<?php
// Step 2: Define a class for your custom list table
if(!class_exists('WP_List_Table')){
    require_once( ABSPATH . 'wp-admin/includes/screen.php' );
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
class Custom_List_Table extends WP_List_Table {
    // Define necessary properties and methods for your custom list table
    // Refer to the WP_List_Table documentation for more details:
    // https://developer.wordpress.org/reference/classes/wp_list_table/
    
    public function __construct() {
        parent::__construct( array(
            'singular' => 'item',
            'plural'   => 'items',
            'ajax'     => false
        ) );
    }
    
    public function column_default( $item, $column_name ) {
        // Implement logic to display the columns for your table
        switch ( $column_name ) {
            case 'id':
            case 'transId':
            case 'cardId':
                return '<strong>' . $item[ $column_name ] . '</strong>';
            case 'status':
                $status = $item[ $column_name ];
                $bg_color = '';
    
                if ( $status === 'COMPLETED' ) {
                    $bg_color = '#8BC34A';
                    $color = '#fff';

                } elseif ( $status === 'PENDING' ) {
                    $bg_color = '#ffc107';
                    $color = '#fff';
                }elseif( $status === 'REJECTED' ) {
                    $bg_color = '#E91E63';
                    $color = '#fff';
                }
    
                return sprintf(
                    '<span style="border-radius: 4px;background-color: %s; color: %s; padding: 1px 5px 2px ;">%s</span>',
                    $bg_color,
                    $color,
                    $status
                );
            case 'date_created':
                $date_created = strtotime( $item[ $column_name ] );
                return '<strong>' . date( 'Y-m-d H:i:s', $date_created ) . '</strong>';
            default:
                return '<strong>' . print_r( $item, true ) . '</strong>'; // Fallback output for unknown columns
        }
    }
    
    public function column_cb( $item ) {
        // Implement logic for the checkbox column
        return sprintf(
            '<input type="checkbox" name="bulk-delete[]" value="%s" />',
            $item['id']
        );
    }
    
    public function get_columns() {
        // Define the columns for your table
        $columns = array(
            'cb'      => '<input type="checkbox" />',
            'id'      => '<strong>ID</strong>',
            'cardId'  => '<strong>cardId</strong>',
            'transId' => '<strong>transId</strong>',
            'status'  => '<strong>status</strong>',
            'date_created' => '<strong>التاريخ</strong>',

        );
        
        return $columns;
    }
    
    public function get_data() {
        // Retrieve data for your table from the custom table in the database
        global $wpdb;
        $table_name = $wpdb->prefix . 'nafath_callback';
        $data = $wpdb->get_results( "SELECT * FROM $table_name", ARRAY_A );
        
        return $data;
    }
    
    public function prepare_items() {
        global $wpdb;
        // Prepare the items and pagination for the table
        $data = $this->get_data();
        
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = array();

         
        
        $this->_column_headers = array( $columns, $hidden, $sortable );
        $this->process_bulk_action();


        $per_page = 20; // Number of items to display per page
        $current_page = $this->get_pagenum();
        $total_items = count( $data );

        // Apply search filter
        $search = ( isset( $_REQUEST['s'] ) ) ? sanitize_text_field( $_REQUEST['s'] ) : '';
        $query = "SELECT * FROM {$wpdb->prefix}nafath_callback";
        if ( ! empty( $search ) ) {
            $query .= $wpdb->prepare( " WHERE cardId LIKE %s", '%' . $search . '%' );
        }
        
       // Execute the SQL query with the search filter
        $results = $wpdb->get_results( $query, ARRAY_A );

        $total_items = count( $results );

        $this->set_pagination_args( array(
            'total_items' => $total_items,
            'per_page'    => $per_page
        ) );

        // Slice the results based on the current page and items per page
        $this->items = array_slice( $results, ( ( $current_page - 1 ) * $per_page ), $per_page );

    }

    /**
     * Get sortable columns
     * @return array
     */
    function get_sortable_columns(){
        $s_columns = array (
            'transId' => [ 'transId', true], 
            'status'  => [ 'status', true],
        );
        return $s_columns;
    }

    // Add bulk actions
    public function get_bulk_actions() {
        $actions = array(
            'delete' => 'Delete'
        );
        return $actions;
    }

    // Process bulk actions
    public function process_bulk_action() {
        if ( 'delete' === $this->current_action() ) {
            $item_ids = isset( $_REQUEST['bulk-delete'] ) ? $_REQUEST['bulk-delete'] : array();

            if ( is_array( $item_ids ) && ! empty( $item_ids ) ) {
                global $wpdb;
                $table_name = $wpdb->prefix . 'nafath_callback';

                foreach ( $item_ids as $item_id ) {
                    $wpdb->delete( $table_name, array( 'id' => $item_id ), array( '%d' ) );
                }
            }
        }
    }

    // Add search box beside bulk actions
    public function extra_tablenav( $which ) {
        if ( $which === 'top' ) {
            $search_value = isset( $_REQUEST['s'] ) ? $_REQUEST['s'] : '';
            ?>
            <div class="alignleft actions">
                <label class="screen-reader-text" for="custom-table-search"><?php _e( 'Search' ); ?>:</label>
                <input type="search" id="custom-table-search" name="s" value="<?php echo esc_attr( $search_value ); ?>" />
                <?php submit_button( __( 'Search' ), 'button', false, false, array( 'id' => 'search-submit' ) ); ?>
            </div>
            <?php
        }
        // if ( $which === 'top' || $which === 'bottom' ) {
        //     $this->bulk_actions( $which );
        // }
    }
}

// Step 4: Create a function to display your custom admin page

function display_custom_admin_page() {
    $list_table = new Custom_List_Table();
    $list_table->prepare_items();
    
    ?>
    <div class="wrap">
        <h1><?php esc_html_e( 'Nafath Process', 'aqar-gate' ); ?></h1>
        
        <!-- Display your custom table here -->
        <form method="post">
            <?php $list_table->display(); ?>
        </form>
    </div>
    <?php
}

// Step 5: Hook into the WordPress admin menu to add your custom page
function add_custom_admin_page() {
    add_menu_page(
        'Nafath Process',
        'Nafath Process',
        'manage_options',
        'nafath-process-page',
        'display_custom_admin_page',
        'dashicons-welcome-write-blog',
        10
    );
}

add_action( 'admin_menu', 'add_custom_admin_page' );