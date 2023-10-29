<?php
    $property_area = csv_to_array($file);
    // 3- insert province to property tax .
        $property_cities = get_terms( array(
            'taxonomy' => rtcl()->location,
            'hide_empty' => false,
        ) );
        $parent_city = 0;
        foreach ( $property_area as $mdaKey ) {
            // prr($mdaKey);wp_die();
            $cityId             = $mdaKey['CITY_ID'];
            $nameAr             = $mdaKey['DISTRICTNAME_AR'];
            $_id                = $mdaKey['DISTRICT_ID'];
            $property_area_slug = $nameAr.'-'.$_id;


            foreach( $property_cities as $_term ){
                $slug = $_term->slug;
                $city_Id = get_option( '_im_property_city_'.$_term->term_id, true ); 
                if( isset($city_Id['CITY_ID']) && $cityId == $city_Id['CITY_ID'] ){
                    $im_meta['parent_city'] = $slug;
                    $parent_city            = $_term->term_id;
                } else {
                    continue;
                }
            }

            $inserted_term =  wp_insert_term($nameAr, rtcl()->location, [
                'slug'   => $property_area_slug,
                'parent' => $parent_city,
            ]);

            if (is_wp_error($inserted_term)) {
                $new_term_id = $inserted_term->error_data['term_exists'];
            } else {
                $new_term_id = $inserted_term['term_id'];
                update_term_meta( $new_term_id, 'term_from_file', 'NEW' );
                update_term_meta( $new_term_id, 'DISTRICT_ID', $_id );

            }
            // var_dump($new_term_id);wp_die();
            $im_meta['DISTRICT_ID'] = $_id;
            
            update_option( '_im_property_area_'.$new_term_id, $im_meta );
        }    