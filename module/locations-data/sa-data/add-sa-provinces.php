<?php
   
    $property_provinces = csv_to_array(REGIONS);
    $terms = get_terms( array(
        'taxonomy'   => rtcl()->location,
        'hide_empty' => false,
    ) );
    // 1- insert province to property term .
    foreach ( $property_provinces as $province ) {
        foreach($terms as $term){
            $province_Id = get_option( '_im_property_state_'.$term->term_id, true );
            if( isset($province_Id['REGION_ID']) ){
                continue;
            } 
        }
            // prr($mdaKey);wp_die();
            $nameAr     = $province['REGIONNAME_AR'];
            $provinc_id = $province['REGION_ID'];
            $slug       = $nameAr.'-'.$provinc_id;

            $inserted_term =  wp_insert_term($nameAr, rtcl()->location, [
                    'slug' => $slug,
            ]);
            
            if (is_wp_error($inserted_term)) {
                $new_term_id = $inserted_term->error_data['term_exists'];
            } else {
                $new_term_id = $inserted_term['term_id'];
                update_term_meta( $new_term_id, 'term_from_file', 'NEW' );
                update_term_meta( $new_term_id, 'REGION_ID', $provinc_id );
            }
            // var_dump($new_term_id);wp_die();
            $im_meta['parent_country'] = 'saudi-arabia';
            $im_meta['REGION_ID'] = $provinc_id;
    
            update_option('_im_property_state_'.$new_term_id, $im_meta);
    }   