<?php

class im_tax_column {
    public function __construct() {
        $taxonomies = [
            rtcl()->location,
            'property_city',
            'property_area',

        ];
        foreach ($taxonomies as $taxonomy) {
            add_filter( 'manage_' . $taxonomy . '_custom_column', [$this, 'taxonomy_rows'],15, 3 );
            add_filter( 'manage_edit-' . $taxonomy . '_columns',  [$this, 'taxonomy_columns'] );
        }
    }

    public function taxonomy_columns( $original_columns ) {
        $new_columns = $original_columns;
        array_splice( $new_columns, 3 );
        // $new_columns['termstatus'] = esc_html__( 'Status', 'im' );
        $new_columns['termid'] = esc_html__( 'ID', 'im' );
        return array_merge( $new_columns, $original_columns );
        }
        
    public function taxonomy_rows( $row, $column_name, $term_id ) {
        $meta       = get_term_meta( $term_id, 'term_from_file', true );
        $state_meta = !empty(get_term_meta( $term_id, 'REGION_ID', true )) ? get_term_meta( $term_id, 'REGION_ID', true ) : get_option('_houzez_property_state_'.$term_id);
        $city_meta  = !empty(get_term_meta( $term_id, 'CITY_ID', true )) ? get_term_meta( $term_id, 'CITY_ID', true ) : get_option('_houzez_property_city_'.$term_id);
        $area_meta  = !empty(get_term_meta( $term_id, 'DISTRICT_ID', true )) ? get_term_meta( $term_id, 'DISTRICT_ID', true ) : get_option('_houzez_property_area_'.$term_id);
   
        if ( 'termstatus' === $column_name ) {
             
            if (!empty($meta)) {
                echo $row . '<span style="color:green;font-weight: bold;">NEW</span>';
            } else {
                echo $row . '<span style="color:red;font-weight: bold;">OLD<?span>';
            }   
        }
        if( 'termid' === $column_name ) {
            if( isset($state_meta['REGION_ID']) && !empty($state_meta['REGION_ID'])){
                echo '<span style="font-weight: bold;">'.$state_meta['REGION_ID'].'</span>';
            }else{
                echo '<span style="font-weight: bold;">'.$state_meta.'</span>';
            }
            if( isset( $city_meta['CITY_ID'] ) && !empty($city_meta['CITY_ID'])){
                echo '<span style="font-weight: bold;">'.$city_meta['CITY_ID'].'</span>';
            }else{
                echo '<span style="font-weight: bold;">'.$city_meta.'</span>';

            }
            if( isset($area_meta['DISTRICT_ID']) && !empty($area_meta['DISTRICT_ID'])){
                echo '<span style="font-weight: bold;">'.$area_meta['DISTRICT_ID'].'</span>';
            }else{
                echo '<span style="font-weight: bold;">'.$area_meta.'</span>';
            }
        }
    }
}
new im_tax_column();