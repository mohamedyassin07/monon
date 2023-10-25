<?php 
use radiustheme\Classima\RDTheme;
use Rtcl\Helpers\Functions;
use Rtcl\Models\RtclCFGField;
use Rtcl\Helpers\Link;

class NHC {

    public function __construct() {
        /* ---------------------- init_actions && init_includes --------------------- */
        $this->init_actions();
        $this->init_includes();
        // PreDebug($this->get_cf_post_id( 'ارض' ) );
    }
    
    /**
     * init_actions
     *
     * @return void
     */
    public function init_actions() {
        /* ------------------ action for display aqar isvalid form ------------------ */
        if( get_option( '_aq_show_api' ) == 'yes' ) {
            add_action( 'im_before_add_listing_condition', array($this, 'aqar_isvalid'), 20, 1 );
        }
        
        /* ----------------------- Ajax add property function ----------------------- */
        add_action( 'wp_ajax_nopriv_aqar_isvalid_api', array( $this,'aqar_isvalid_api' ));
        add_action( 'wp_ajax_aqar_isvalid_api', array( $this,'aqar_isvalid_api' ));

        /* ----------------------- Ajax get property info function ----------------------- */
        add_action( 'wp_ajax_nopriv_get_ad_info', array( $this,'get_ad_info' ));
        add_action( 'wp_ajax_get_ad_info', array( $this,'get_ad_info' ));

        /* ----------------------- Ajax CreateADLicense function ----------------------- */
        add_action( 'wp_ajax_nopriv_CreateADLicense', array( $this,'CreateADLicense' ));
        add_action( 'wp_ajax_CreateADLicense', array( $this,'CreateADLicense' ));

    }
    
    /**
     * init_includes
     *
     * @return void
     */
    public function init_includes() { }    
    
    /**
     * aqar_isvalid
     *
     * @param  mixed $userID
     * @return void
     */
    public function aqar_isvalid($userID)
    {
        $cancel_link = home_url('/');
        if( !is_user_logged_in() ) {
            $cancel_link = home_url('/');  
        }
        $allowed_html = array(
            'i' => array(
                'class' => array()
            ),
            'strong' => array(),
            'a' => array(
                'href' => array(),
                'title' => array(),
                'target' => array()
            )
        );
    
        $userID    = get_current_user_id();
        $id_number = get_the_author_meta( '_im_type_id' , $userID );
        $type_id   = get_the_author_meta( '_im_user_type' , $userID );
        $formTitle = get_option( '_form_head', 'اكمل البينات المطلوبة للتأكد من معلومات الاعلان' );
        if( empty($formTitle) ) {
            $formTitle = 'اكمل البينات المطلوبة للتأكد من معلومات الاعلان' ;
        }
        $dashboard_add_listing = esc_url( RDTheme::$options['header_btn_url'] );
        $rega_listing = add_query_arg('add-rega-listing', 'yes', $dashboard_add_listing); 
        $licensing_by_aqargate = add_query_arg('licensing-by-aqargate', 'yes', $dashboard_add_listing);
        
        if( isset($_GET['add-rega-listing']) && $_GET['add-rega-listing'] === 'yes' ) {
            include(IM_DIR . 'functions/nhc-form-fields/add-ad.php');   
        }
    
        else if( isset($_GET['licensing-by-aqargate']) && $_GET['licensing-by-aqargate'] === 'yes' ) {
            include(IM_DIR . 'functions/nhc-form-fields/create-ad.php');   
        } 
        else {
        ?>
        <div class="addPropert-wrap">
            <div class="addProperty_container">
                <h2 class="text-center">إضافة اعلان جديد</h2>
                <a href="<?php echo $rega_listing; ?>" class="addProperty_card" >
                    <img src="<?php echo trailingslashit( get_stylesheet_directory_uri() ) .'assets/img/refresh-data.png'; ?>" class="" loading="lazy" width="25" height="25">
                    <div class="addProperty_line"></div>
                    <div>
                        <p>اضافة اعلان مرخص</p>
                        <p>لدي رقم ترخيص الاعلان</p>
                    </div>
                </a>
                <a href="#" class="addProperty_card">
                    <img src="<?php echo trailingslashit( get_stylesheet_directory_uri() ) .'assets/img/add.png'; ?>" class="" loading="lazy" width="25" height="25">
                    <div class="addProperty_line"></div>
                    <div>
                        <p>اضافة اعلان (مع إصدار ترخيص)</p>
                        <p>إصدار الترخيص سيكون من خلال منصة  منون</p>
                    </div>
                </a>
            </div>
        </div>
        <?php
        }
    }

        
    /**
     * aqar_isvalid_api
     *
     * @return void
     */
    public function aqar_isvalid_api()
    {
        $nonce = isset($_POST['aqar_isvalid_api']) ? $_POST['aqar_isvalid_api'] : '';
        if ( ! wp_verify_nonce( $nonce, 'aqar_isvalid_api' ) ) {
            $ajax_response = array( 'success' => false , 'reason' => esc_html__( 'Security check failed!', 'houzez' ) );
            echo wp_send_json( $ajax_response );
            wp_die();
        }

        $adLicenseNumber = isset($_POST['adLicenseNumber']) ? $_POST['adLicenseNumber'] : '';
        $advertiserId    = isset($_POST['advertiserId']) ? $_POST['advertiserId'] : '';
        $idType          = isset($_POST['idType']) ? $_POST['idType'] : 1 ;

        if ( empty( $adLicenseNumber ) ) {
            $ajax_response = array( 'success' => false , 'reason' => esc_html__( 'رقم ترخيص الإعلان  مطلوب', 'houzez' ) );
            echo wp_send_json( $ajax_response );
            wp_die();
        }

        // if ( empty( $advertiserId ) ) {
        //     $ajax_response = array( 'success' => false , 'reason' => esc_html__( ' رقم هوية المعلن  مطلوب', 'houzez' ) );
        //     echo wp_send_json( $ajax_response );
        //     wp_die();
        // }

        require_once ( IM_DIR . 'module/nhc/class-rega-module.php' );

        $RegaMoudle = new RegaMoudle();

        $response = $RegaMoudle->AdvertisementValidator($adLicenseNumber);
        $response = json_decode( $response );

        if( $response->Body->result->isValid == true ){
            
            // في حالة رقم العلن موجود بالفعل اظهار رسالة بذلك
            $repeat_prop = get_option( '_repeat_prop' );
            if( $repeat_prop != 'yes' ) {
                $deedNumber = $response->Body->result->advertisement->deedNumber;
                $search_deedNumber_property = $this->search_deedNumber_property($deedNumber);
    
                if( $search_deedNumber_property ) {
                    $ajax_response = array( 'success' => false , 'reason' => 'رقم الاعلان موجود بالفعل !' );
                    echo wp_send_json( $ajax_response );
                    wp_die(); 
                }
                
            }

            // اضافة الاعلان
            $property_id = $this->add_property_advertisement($response->Body->result->advertisement);
            if( $property_id > 0 ) {
                // $submit_link   = houzez_dashboard_add_listing();
                $edit_link     = Link::get_listing_edit_page_link( $property_id );
                $ajax_response = array( 
                    'success' => true,
                    'reason'  => 'تم نشر الاعلان بنجاح -> ' . $property_id . ' سوف يتم اعادة توجيهك الي الاعلان ... ',
                    'redir'   => $edit_link
                );
                echo wp_send_json( $ajax_response );
                wp_die();
            } else {
                $ajax_response = array( 'success' => false , 'reason' => 'لم يتم اضافة الاعلان !');
                echo wp_send_json( $ajax_response );
                wp_die();
            }
            
        }else{
            $ajax_response = array( 
                'success' => false , 
                'reason' => 'هنالك مشكلة في الاتصال مع هيئة العقار'
            );
            echo wp_send_json( $ajax_response );
            wp_die();
        }

        
    }

    public function get_ad_info()
    {
        $nonce = isset($_POST['aqar_isvalid_api']) ? $_POST['aqar_isvalid_api'] : '';
        if ( ! wp_verify_nonce( $nonce, 'aqar_isvalid_api' ) ) {
            $ajax_response = array( 'success' => false , 'reason' => esc_html__( 'Security check failed!', 'houzez' ) );
            echo wp_send_json( $ajax_response );
            wp_die();
        }

        $adLicenseNumber = isset($_POST['adLicenseNumber']) ? $_POST['adLicenseNumber'] : '';
        $advertiserId    = isset($_POST['advertiserId']) ? $_POST['advertiserId'] : '';
        $idType          = isset($_POST['idType']) ? $_POST['idType'] : 1 ;


        if ( empty( $adLicenseNumber ) ) {
            $ajax_response = array( 'success' => false , 'reason' => esc_html__( 'رقم ترخيص الإعلان  مطلوب', 'houzez' ) );
            echo wp_send_json( $ajax_response );
            wp_die();
        }


        require_once ( IM_DIR . 'module/nhc/class-rega-module.php' );

        $RegaMoudle = new RegaMoudle();

        $response = $RegaMoudle->AdvertisementValidator( $adLicenseNumber );
        // var_export($response);
        $response = json_decode( $response );
        
        if( $response->Header->Status->Code != 200  ) {  
            $msg = 'هنالك مشكلة في الاتصال مع هيئة العقار';
            if( isset($response->Body->error->message) ) {
                $msg = $response->Body->error->message;
            } 
            $ajax_response = array( 'success' => false , 'reason' => $msg );
            echo wp_send_json( $ajax_response );
            wp_die();
        }else{
            $translate = [
                'advertiserId' => 'المعلن',
                'adLicenseNumber' => 'رقم الترخيص',
                'deedNumber' => 'رقم الاعلان',
                'advertiserName' => 'اسم المعلن',
                'phoneNumber' => 'رقم التليفون',
                'brokerageAndMarketingLicenseNumber' => 'الوساطة والتسويق',
                'isConstrained' => 'مقيد',
                'isPawned' => 'مرهونة',
                'streetWidth' => 'عرض الشارع',
                'propertyArea' => 'منطقة',
                'propertyPrice' => 'أسعار العقارات',
                'numberOfRooms' => 'عدد الغرف',
                'propertyType' => 'نوع الملكية',
                'propertyAge' => 'العمر',
                'advertisementType' => 'نوع الإعلان',
                'location' => 'موقع',
                'region' => 'منطقة',
                'city' => 'مدينة',
                'district' => 'الحي',
                'buildingNumber' => 'رقم المبنى',
                'longitude' => 'خط الطول',
                'latitude' => 'خط العرض',
                "regionCode" => "كود المنطقة",
                "cityCode" => "كود المدينة",
                "districtCode" => "كود الحي",
                "street" => "الشارع",
                "postalCode" => "الرمز البريدي",
                "buildingNumber" => "رقم المبني",
                "additionalNumber" => "رقم اضافي",
                'propertyFace' => 'واجهة العقار',
                'planNumber' => 'رقم المخطط',
                'obligationsOnTheProperty' => 'الالتزامات على الممتلكات',
                'guaranteesAndTheirDuration' => 'الضمانات والمدة الزمنية',
                'theBordersAndLengthsOfTheProperty' => 'حدود الملكية وأطوالها',
                'complianceWithTheSaudiBuildingCode' => 'الامتثال لكود البناء السعودي',
                'propertyUsages' => 'استخدام العقار',
                'propertyUtilities' => 'خدمات العقار',
                'channels' => 'قنوات الاعلان ',
                'creationDate' => 'تاريخ إنشاء ترخيص الاعلان ',
                'endDate' => 'تاريخ انتهاء ترخيص الاعلان',
                'qrCodeUrl' => 'رابط رمز الاستجابة السريع',
            ];
            $data = [];
            if( isset($response->Body->result->advertisement) ) {

                // في حالة رقم العلن موجود بالفعل اظهار رسالة بذلك
                $repeat_prop = get_option( '_repeat_prop' );
                if( $repeat_prop != 'yes' ) {
                    $deedNumber = $response->Body->result->advertisement->deedNumber;
                    $search_deedNumber_property = $this->search_deedNumber_property($deedNumber);
        
                    if( $search_deedNumber_property ) {
                        $ajax_response = array( 'success' => false , 'reason' => 'رقم الاعلان موجود بالفعل !' );
                        echo wp_send_json( $ajax_response );
                        wp_die(); 
                    }    
                }
                 
              
                foreach( $response->Body->result->advertisement as $key => $advertisement ) {
                    if( $key == 'location' 
                      ){
                        foreach ($advertisement as $k => $v) {
                            $data[]= $translate[$k] . ' : '  . $v . '<br>';
                        }
                    }else if (
                        $key == 'propertyUsages' ||
                        $key == 'propertyUtilities' ||
                        $key == 'channels'
                    ){ 
                        $data[]= $translate[$key] . ' : '  . $advertisement[0] . '<br>';
                    }else{
                        $data[]= $translate[$key] . ' : '  . $advertisement . '<br>';
                    }
                }
            }
            $ajax_response = array( 'success' => true , 'reason' => $data );
            echo wp_send_json( $ajax_response );
            wp_die();
        }
    }

    public function add_property_advertisement($data)
    {
        $prop_id  = '';
        $userID = get_current_user_id();
        $new_listing_status = Functions::get_option_item( 'rtcl_moderation_settings', 'new_listing_status', 'pending' );
        $new_property = [];
        $new_property['post_type'] = rtcl()->post_type;
        $new_property['post_author'] = $userID;
        $new_property['post_status'] = 'draft';

        $title  = $data->propertyType .' ';
        $title .= $data->advertisementType . ' ';
        $title .= $data->location->region . ' ';

        // Title (the post_title could be empty if you use post_name instead);
        $new_property['post_title'] = $title;
        
        $prop_id = $success = wp_insert_post( $new_property );

        if ( $success && $prop_id && ( $listing = rtcl()->factory->get_listing( $prop_id ) ) ) {
            update_post_meta( $prop_id, 'featured', 0 );
            update_post_meta( $prop_id, '_views', 0 );
            $current_user_id = get_current_user_id();
            $ads             = absint( get_user_meta( $current_user_id, '_rtcl_ads', true ) );
            update_user_meta( $current_user_id, '_rtcl_ads', $ads + 1 );
            if ( 'publish' === $new_listing_status ) {
                Functions::add_default_expiry_date( $prop_id );
            }
        }

        $meta = [];
        if ( Functions::is_enable_terms_conditions()  ) {
            $meta['rtcl_agree'] = 1;
        }
        // Add price post meta
        if( isset( $data->propertyPrice ) ) {
            $meta['price'] = Functions::format_decimal( $data->propertyPrice );
        }

        $adress = 'المملكة العربية السعودية';
        // Address
        if( isset( $data->location->region ) || isset( $data->location->city ) ) {
            $country = 'المملكة العربية السعودية';
            $adress = $country . ', ' .$data->location->region .', '.$data->location->city . ', ' . $data->location->district;            
        }

        if ( "geo" === Functions::location_type() ) {
            if ( isset( $data->location ) ) {
                $meta['_rtcl_geo_address'] = Functions::sanitize( $adress );
            }
        } else {
            if ( isset( $data->location->postalCode ) ) {
                $meta['zipcode'] = Functions::sanitize( $data->location->postalCode );
            }
            if ( isset( $data->location->region ) || isset( $data->location->city ) ) {
                $meta['address'] = Functions::sanitize( $adress, 'textarea' );
            }
        }


        // lat & long
        if( ( isset($data->location->latitude) && !empty($data->location->latitude) ) && (  isset($data->location->longitude) && !empty($data->location->latitude)  ) ) {
               
            $lat = $data->location->latitude;
            $lng = $data->location->longitude;

            /* ---------------------- get lat & lon from openstreet --------------------- */
            $search_adress = $this->search_adress($adress);
            if( !empty($search_adress) && isset($search_adress[0]) ) {
                $lat = $search_adress[0]->lat;
                $lng = $search_adress[0]->lon;
            }
            /* ---------------------- get lat & lon from openstreet --------------------- */

            $streetView = '';
            $lat_lng = $lat.','.$lng;
            $meta['latitude'] = Functions::sanitize( $lat );
            $meta['longitude'] = Functions::sanitize( $lng );
        }
        
        if( isset( $data->advertisementType ) && ( $data->advertisementType != '' ) ) {
            $listing_type = '';
            foreach ( Functions::get_listing_types() as $key => $value) {
                if( $value ===  $data->advertisementType ) {
                    $listing_type = $key;
                }
            }
            $meta['ad_type'] = $listing_type;
        }

        /* meta data */
        if ( ! empty( $meta ) && $prop_id ) {
            foreach ( $meta as $key => $value ) {
                update_post_meta( $prop_id, $key, $value );
            }
        }


        // Custom Meta field
        $custom_filds_post = $this->get_cf_post_id( $data->propertyType );

        if( !empty( $custom_filds_post ) ) { 
            foreach ( $custom_filds_post as $field_data ) {
                
                if( $field_data['slug'] === 'adtype') {
                    $field_id = (int) $field_data['ID'];
                    $value = isset($data->advertisementType) ? $data->advertisementType : '';
                    if ( $field = rtcl()->factory->get_custom_field( $field_id ) ) {
                        $field->saveSanitizedValue( $prop_id, $value );
                    }
                }

                if( $field_data['slug'] === 'streetwidth') {
                    $field_id = (int) $field_data['ID'];
                    $value = isset($data->streetWidth) ? $data->streetWidth : '';
                    if ( $field = rtcl()->factory->get_custom_field( $field_id ) ) {
                        $field->saveSanitizedValue( $prop_id, $value );
                    }
                }

                if( $field_data['slug'] === 'propertyarea') {
                    $field_id = (int) $field_data['ID'];
                    $value = isset($data->propertyArea) ? $data->propertyArea : '';
                    if ( $field = rtcl()->factory->get_custom_field( $field_id ) ) {
                        $field->saveSanitizedValue( $prop_id, $value );
                    }
                }

                if( $field_data['slug'] === 'propertyface') {
                    $field_id = (int) $field_data['ID'];
                    $value = isset($data->propertyFace) ? $data->propertyFace : '';
                    if ( $field = rtcl()->factory->get_custom_field( $field_id ) ) {
                        $field->saveSanitizedValue( $prop_id, $value );
                    }
                }

                if( $field_data['slug'] === 'plannumber') {
                    $field_id = (int) $field_data['ID'];
                    $value = isset($data->planNumber) ? $data->planNumber : '';
                    if ( $field = rtcl()->factory->get_custom_field( $field_id ) ) {
                        $field->saveSanitizedValue( $prop_id, $value );
                    }
                }

                if( $field_data['slug'] === 'thebordersandlengthsoftheproperty') {
                    $field_id = (int) $field_data['ID'];
                    $value = isset($data->theBordersAndLengthsOfTheProperty) ? $data->theBordersAndLengthsOfTheProperty : '';
                    if ( $field = rtcl()->factory->get_custom_field( $field_id ) ) {
                        $field->saveSanitizedValue( $prop_id, $value );
                    }
                }

                if( $field_data['slug'] === 'propertyutilities') {
                    $field_id = (int) $field_data['ID'];
                    $value = !empty($data->propertyUtilities) ? $data->propertyUtilities[0] : '';
                    if ( $field = rtcl()->factory->get_custom_field( $field_id ) ) {
                        $field->saveSanitizedValue( $prop_id, $value );
                    }
                }

                if( $field_data['slug'] === 'obligationsontheproperty') {
                    $field_id = (int) $field_data['ID'];
                    $value = isset($data->obligationsOnTheProperty) ? $data->obligationsOnTheProperty : '';
                    if ( $field = rtcl()->factory->get_custom_field( $field_id ) ) {
                        $field->saveSanitizedValue( $prop_id, $value );
                    }
                }

                if( $field_data['slug'] === 'propertyusages') {
                    $field_id = (int) $field_data['ID'];
                    $value = !empty($data->propertyUsages) ? $data->propertyUsages[0] : '';
                    if ( $field = rtcl()->factory->get_custom_field( $field_id ) ) {
                        $field->saveSanitizedValue( $prop_id, $value );
                    }
                }
            }
        }

        update_post_meta( $prop_id, 'advertiserId', $data->advertiserId );
        update_post_meta( $prop_id, 'adLicenseNumber', $data->adLicenseNumber );
        update_post_meta( $prop_id, 'deedNumber', $data->deedNumber );
        update_post_meta( $prop_id, 'TitleDeed', $data->deedNumber ); 
 
        return $prop_id;
    }

    public static function get_cf_post_id( $propertyType )
    {
        $sup_category = [
            'Land' => 'ارض',
            'Role' => 'دور',
            'Apartment' => 'شقة',
            'villa' => 'فيلا',
            'SmallApartment' => 'شقَّة صغيرة (استوديو)',
            'Bedrooms' => 'غرفة',
            'Break' => 'استراحة',
            'Complex' => 'مجمع',
            'Tower' => 'برج',
            'Exhibition' => 'معرض',
            'Office' => 'مكتب',
            'Storehouse' => 'مستودع',
            'Booth' => 'كشك',
            'Cinema' => 'سينما',
            'Hotel' => 'فندق',
            'CarParking' => 'مواقف سيارات',
            'Workshop' => 'ورشة',
            'ATM' => 'صراف',
            'Factory' => 'مصنع',
            'School' => 'مدرسة',
            'HospitalHealthCenter' => 'مستشفى، مركز صحي',
            'ElectricityStation' => 'محطة كهرباء',
            'TelecomTower' => 'برج اتصالات',
            'Station' => 'محطة',
            'Farm' => 'مزرعة',
            'Architecture' => 'عمارة',
        ];
        
        // Search for the key based on the form value
        $key = array_search($propertyType, $sup_category);
        $parent_title = '';
        
        if ($key !== false) {
            $parent_title = $key;
        }

        if( empty($parent_title) ) {
            return array();
        }
        
        // Get parent post ID based on the title
        $parent_posts_query = new WP_Query(array(
            'post_type' => 'rtcl_cfg',
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'title' => $parent_title
        ));

        $parent_post_id = 0;

        if ($parent_posts_query->have_posts()) {
            $parent_post = $parent_posts_query->posts[0];
            $parent_post_id = $parent_post->ID;
            // Restore the global post data
            wp_reset_postdata();
        } 

        $fields = Functions::get_all_cf_fields_by_cfg_id($parent_post_id);
        $f = [];
        if (!empty($fields)) {
            foreach ($fields as $field) {
                $RtclCFGField = new RtclCFGField($field->ID);
                $f[] = [
                    'ID'   => $field->ID,
                    'slug' => $RtclCFGField->getSlug(),
                ];
                
            }
        }

        return $f;
    }
    
    /**
     * search_deedNumber_property
     *
     * @param  mixed $deedNumber
     * @return void
     */
    public function search_deedNumber_property($deedNumber)
    {
        global $wpdb;
        if( empty($deedNumber) ) {
            return false;
        }
        
        $hasDeedNumber = $wpdb->get_results("select * from $wpdb->postmeta where meta_key='deedNumber' and meta_value='$deedNumber'");

        if ( count($hasDeedNumber) == 0 ) {
            return false;
        }

        return true;

    }


    public function CreateADLicense()
    {
        $dataArray = [];
        $dataArray["platformOwnerId"] = !empty( get_option( 'platformOwnerId' ) ) ? get_option( 'platformOwnerId' ) : "7013399329";
        $dataArray["platformId"] = "08db9cad-376b-450a-80c8-b5c8163f52b0";
        
        if( empty($_POST['theAdThrough']) ){
            $ajax_response = array( 'success' => false , 'reason' => 'الحقل: الاعلان من خلال مطلوب' );
            echo wp_send_json( $ajax_response );
            wp_die();
        }

        $dataArray["theAdThrough"] = $_POST['theAdThrough'];

        global $current_user;
        $userID       = get_current_user_id();
        $id_number    = get_the_author_meta( 'aqar_author_id_number' , $userID );
        $display_name = get_the_author_meta( 'aqar_display_name' , $userID );
        if( empty($display_name) ) {
            $display_name = $current_user->display_name;
        }
        
        if( houzez_is_agency() ) {
            $user_agency_id = get_user_meta( $userID, 'fave_author_agency_id', true );

            if( !empty( $user_agency_id ) ) {
                $display_name = get_the_title($user_agency_id);
            }else if( !empty( get_the_author_meta( 'fave_author_company' , $userID ) ) ) {
                $display_name = get_the_author_meta( 'fave_author_company' , $userID );
            }
            $tax_number = get_the_author_meta( 'fave_author_tax_no' , $userID );

            $dataArray["advertiserId"] = !empty($tax_number) ? $tax_number : $id_number;
            $dataArray["advertiserName"] = $display_name;
        } else {
            $dataArray["advertiserId"] = $id_number;
            $dataArray["advertiserName"] = $display_name;
        }

        
        $dataArray["userIdNumber"] = $id_number;
        $dataArray["fullName"]     = $display_name;
    
    
        if( isset( $_POST['attorneyCode'] )  && !empty( $_POST['attorneyCode'] ) ) {
            $dataArray["attorneyCode"] = (int)$_POST['attorneyCode'];
        }
        if( isset( $_POST['attorneyFirstId'] )  && !empty( $_POST['attorneyFirstId'] ) ) {
            $dataArray["attorneyFirstId"] = (int)$_POST['attorneyFirstId'];
        }
        if( isset( $_POST['attorneySecondId'] )  && !empty( $_POST['attorneySecondId'] ) ) {
            $dataArray["attorneySecondId"] = (int)$_POST['attorneySecondId'];
        }

        if( isset( $_POST['brokerageContractNumber'] )  && !empty( $_POST['brokerageContractNumber'] ) ) {
            $dataArray["brokerageContractNumber"] = $_POST['brokerageContractNumber'];
        }

        /* --------------------------------- for testing -------------------------------- */
        $dataArray["advertiserId"] = '7013399329';
        $dataArray["attorneyCode"] = 40985145; // رقم الوكالة
        $dataArray["attorneyFirstId"] = 1034758670; // رقم هوية الموكل
        $dataArray["attorneySecondId"] = 1034758704; // رقم هوية الوكيل
        /* ------------------------------- عقد الوساطة ------------------------------ */
        $dataArray["brokerageContractNumber"] = "6200000027"; // رقم عقد الوساطة
        /* ----------------------------------- end ---------------------------------- */ 


        $dataArray["streetWidth"] = !empty( $_POST['streetWidth'] ) ? (int)$_POST['streetWidth'] : 1 ;
        $dataArray["propertyArea"] = isset( $_POST['propertyArea'] ) ? (int)$_POST['propertyArea'] : '' ;
        $dataArray["propertyPrice"] = isset( $_POST['propertyPrice'] ) ? (int)$_POST['propertyPrice'] : '' ;
        $dataArray["numberOfRooms"] = isset( $_POST['numberOfRooms'] ) ? (int)$_POST['numberOfRooms'] : 0 ;
        $dataArray["propertyType"] = isset( $_POST['propertyType'] ) ? $_POST['propertyType'] : '';
        $dataArray["propertyAge"] = isset( $_POST['propertyAge'] ) ? $_POST['propertyAge'] : '';
        $dataArray["advertisementType"] = isset( $_POST['advertisementType'] ) ? $_POST['advertisementType'] : '';
        $dataArray["propertyFace"] = isset( $_POST['propertyFace'] ) ? $_POST['propertyFace'] : '';
        $dataArray["planNumber"] = isset( $_POST['planNumber'] ) ? $_POST['planNumber'] : '';
        $dataArray["obligationsOnTheProperty"] = isset( $_POST['obligationsOnTheProperty'] ) ? $_POST['obligationsOnTheProperty'] : '';
        $dataArray["guaranteesAndTheirDuration"] = isset( $_POST['guaranteesAndTheirDuration'] ) ? $_POST['guaranteesAndTheirDuration'] : '';
        $dataArray["theBordersAndLengthsOfTheProperty"] = isset( $_POST['theBordersAndLengthsOfTheProperty'] ) ? $_POST['theBordersAndLengthsOfTheProperty'] : '';

        $dataArray["complianceWithTheSaudiBuildingCode"] = (isset( $_POST['complianceWithTheSaudiBuildingCode'] ) && $_POST['complianceWithTheSaudiBuildingCode'] === 'yes' ) ? true : false;
        
        $dataArray["adType"] = isset( $_POST['adType'] ) ? $_POST['adType'] : '';

        $dataArray["channels"] = ["licensedPlatform"];

        $dataArray["propertyUtilities"] = isset( $_POST['propertyUtilities'] ) ? $_POST['propertyUtilities'] : [];
        $dataArray["propertyUsages"] = isset( $_POST['propertyUsages'] ) ? $_POST['propertyUsages'] : [];

        $dataArray["issueDate"] = date('Y-m-d');
        $dataArray["endDate"] = date('Y-m-d', strtotime(date('Y-m-d') . ' +29 days'));

        $dataArray["location"]["region"] = $this->locations_number_name($_POST["location"]["region"]);
        $dataArray["location"]["RegionCode"] = $this->locations_number_name($_POST["location"]["region"], true);
        $dataArray["location"]["city"] = $this->locations_number_name($_POST["location"]["city"]);
        $dataArray["location"]["cityCode"] = $this->locations_number_name($_POST["location"]["city"], true);
        $dataArray["location"]["district"] = $this->locations_number_name($_POST["location"]["district"]);
        $dataArray["location"]["DistrictCode"] = $this->locations_number_name($_POST["location"]["district"], true);
        $dataArray["location"]["street"] = $_POST["location"]["street"];
        $dataArray["location"]["postalCode"] = (int)$_POST["location"]["postalCode"];
        $dataArray["location"]["buildingNumber"] = (int)$_POST["location"]["buildingNumber"];
        $dataArray["location"]["additionalNumber"] = (int)$_POST["location"]["additionalNumber"];
        $dataArray["location"]["longitude"] = $_POST["location"]["longitude"];
        $dataArray["location"]["latitude"] = $_POST["location"]["latitude"];

        $adPhotos = [];
        if( isset( $_POST['propperty_image_ids'] ) && !empty( $_POST['propperty_image_ids'] ) ) {
           $adPhotos = $this->get_images_ids( $_POST['propperty_image_ids'] );
        }
        $dataArray["adPhotos"] = $adPhotos;

 
       require_once ( IM_DIR . 'module/nhc/class-rega-module.php' );
        $RegaMoudle = new RegaMoudle();

        $response = $RegaMoudle->CreateADLicense( $dataArray );

        $response = json_decode($response);

        $reason = 'Error Test';
        if( isset( $response->Header->Status->Code ) && $response->Header->Status->Code != 200 ) {

            if( isset( $response->Header->Status->Description ) && $response->Header->Status->Code != 400 ) {
                $reason = $response->Header->Status->Description;
            }else if( isset( $response->Body->error->details ) || isset($response->Body->error->message) ) {
                $reason = $response->Body->error->message . ' -- ' .$response->Body->error->details . '<br>';
            }
        } else {
            if( isset($response->Body->result->adObject) ) {
               $data = $response->Body->result->adObject;
               $property_id = $this->add_property_CreateADLincense($data);
               if( $property_id > 0 ) {
                    // Property Images
                    if( isset( $_POST['propperty_image_ids'] ) ) {
                        if (!empty($_POST['propperty_image_ids']) && is_array($_POST['propperty_image_ids'])) {
                            $property_image_ids = array();
                            foreach ($_POST['propperty_image_ids'] as $prop_img_id ) {
                                $property_image_ids[] = intval( $prop_img_id );
                                add_post_meta($property_id, 'fave_property_images', $prop_img_id);
                            }

                            // featured image
                            if( isset( $_POST['featured_image_id'] ) ) {
                                $featured_image_id = intval( $_POST['featured_image_id'] );
                                if( in_array( $featured_image_id, $property_image_ids ) ) {
                                    update_post_meta( $property_id, '_thumbnail_id', $featured_image_id );

                                    /* if video url is provided but there is no video image then use featured image as video image */
                                    if ( empty( $property_video_image ) && !empty( $_POST['prop_video_url'] ) ) {
                                        update_post_meta( $property_id, 'fave_video_image', $featured_image_id );
                                    }
                                }
                            } elseif ( ! empty ( $property_image_ids ) ) {
                                update_post_meta( $property_id, '_thumbnail_id', $property_image_ids[0] );

                                /* if video url is provided but there is no video image then use featured image as video image */
                                if ( empty( $property_video_image ) && !empty( $_POST['prop_video_url'] ) ) {
                                    update_post_meta( $property_id, 'fave_video_image', $property_image_ids[0] );
                                }
                            }
                        }
                    }
                   
                    $edit_link     = Link::get_listing_edit_page_link( $property_id );
                    $ajax_response = array( 
                        'success' => true,
                        'reason'  => 'تم نشر الاعلان بنجاح -> ' . $property_id . ' سوف يتم اعادة توجيهك الي الاعلان ... ',
                        'redir'   => $edit_link
                    );
                    echo wp_send_json( $ajax_response );
                    wp_die();
               } else {
                    $ajax_response = array( 'success' => false , 'reason' => 'لم يتم اضافة العقار للاسباب الاتية : ' . $property_id );
                    echo wp_send_json( $ajax_response );
                    wp_die();
               }
            }
        }
        // var_export($dataArray);

  
        $ajax_response = array( 'success' => false , 'reason' => $reason );
        echo wp_send_json( $ajax_response );
        wp_die();
    }

    public function add_property_CreateADLincense($data)
    {
        $prop_id  = '';
        $userID = get_current_user_id();
        $listings_admin_approved = houzez_option('listings_admin_approved');
        $edit_listings_admin_approved = houzez_option('edit_listings_admin_approved');
        $enable_paid_submission = houzez_option('enable_paid_submission');
        $new_property = [];
        $new_property['post_type'] = 'property';
        $new_property['post_author'] = $userID;

        // Title (the post_title could be empty if you use post_name instead);
        $title = $data->aqarType .' ';
        $title .= $data->advertisementType . ' ';
        $title .= $data->location->region . ' ';

        $new_property['post_title'] = $title;
        
        // $new_property['post_name'] = isset($data->deedNumber) ? 'property-' . $data->deedNumber : 'new-property';
 
        if( houzez_is_admin() ) {
            $new_property['post_status'] = 'publish';
        } else {
            if( $listings_admin_approved != 'yes' && ( $enable_paid_submission == 'no' || $enable_paid_submission == 'free_paid_listing' || $enable_paid_submission == 'membership' ) ) {
                if( $user_submit_has_no_membership == 'yes' ) {
                    $new_property['post_status'] = 'draft';
                } else {
                    $new_property['post_status'] = 'publish';
                }
            } else {
                if( $user_submit_has_no_membership == 'yes' && $enable_paid_submission = 'membership' ) {
                    $new_property['post_status'] = 'draft';
                } else {
                    $new_property['post_status'] = 'pending';
                }
            }
        }


            /*
            * Filter submission arguments before insert into database.
            */
            
            $new_property = apply_filters( 'houzez_before_submit_property', $new_property );

            add_filter( 'wp_insert_post_empty_content', '__return_false' );

            $prop_id = wp_insert_post( $new_property );

            if( $prop_id > 0 ) {
                $submitted_successfully = true;
                if( $enable_paid_submission == 'membership'){ // update package status
                    houzez_update_package_listings( $userID );
                }
            }
  
            // Add price post meta
            if( isset( $data->propertyPrice ) ) {
                update_post_meta( $prop_id, 'fave_property_price', $data->propertyPrice  );
            }

            // Add property type
            if( isset( $data->aqarType ) && ( $data->aqarType != '' ) ) {
                $type = $data->aqarType;
                wp_set_object_terms( $prop_id, $type, 'property_type' );
            } else {
                wp_set_object_terms( $prop_id, '', 'property_type' );
            }

            // Add property status
            if( isset( $data->advertisementType ) && ( $data->advertisementType != '' ) ) {
                $prop_status = $data->advertisementType;
                wp_set_object_terms( $prop_id, $prop_status, 'property_status' );
            } else {
                wp_set_object_terms( $prop_id, '', 'property_status' );
            }

            // Postal Code
            if( isset( $data->location->postalCode ) ) {
                update_post_meta( $prop_id, 'fave_property_zip', $data->location->postalCode );
            }

            

            if( isset($data->streetWidth) ) {
                update_post_meta( $prop_id, 'fave_d8b9d8b1d8b6-d8a7d984d8b4d8a7d8b1d8b9', $data->streetWidth );
            }

            //property-age

            if( isset( $data->propertyAge ) ) {
                update_post_meta( $prop_id, 'fave_property_year', $data->propertyAge );

            }
            
            if( isset($data->numberOfRooms) ) {
                update_post_meta( $prop_id, 'fave_property_rooms', $data->numberOfRooms );
            }

            $state_id = [];
            // Add property state
            if( isset( $data->location->region ) ) {
                $property_state = $data->location->region;
                $state_id = wp_set_object_terms( $prop_id, $property_state, 'property_state' );
            }

            $city_id = [];
            // Add property city
            if( isset( $data->location->city ) ) {
                $property_city = $data->location->city;
                $city_id = wp_set_object_terms( $prop_id, $property_city, 'property_city' );
                $term_object = get_term( $state_id[0] );
                $parent_state = $term_object->slug;
                $houzez_meta = array();
                $houzez_meta['parent_state'] = $parent_state;
                if( !empty( $city_id) && !empty($houzez_meta['parent_state'])  ) {
                    update_option('_houzez_property_city_' . $city_id[0], $houzez_meta);
                }
            }
  

            $area_id = [];
            // Add property area
            if( isset( $data->location->district ) ) {
                $property_area = sanitize_text_field( $data->location->district );
                $area_id = wp_set_object_terms( $prop_id, $property_area, 'property_area' );
                $term_object = get_term( $city_id[0] );
                $parent_city = $term_object->slug;
                $houzez_meta = array();
                $houzez_meta['parent_city'] = $parent_city;
                if( !empty( $area_id) && !empty($houzez_meta['parent_city'])  ) {
                    update_option('_houzez_property_area_' . $area_id[0], $houzez_meta);
                }
            }


            //prop_size 
            if( isset( $data->propertyArea ) ) {
                update_post_meta( $prop_id, 'fave_property_size', $data->propertyArea );
            }

            if( isset( $data->planNumber ) ){
                update_post_meta( $prop_id, 'fave_d8b1d982d985-d8a7d984d985d8aed8b7d8b7', $data->planNumber );
            }
             
            // سعر متر البيع
            //d8b3d8b9d8b1-d985d8aad8b1-d8a7d984d8a8d98ad8b9
            if( isset( $data->propertyPrice ) ){
                update_post_meta( $prop_id, 'fave_d8b3d8b9d8b1-d985d8aad8b1-d8a7d984d8a8d98ad8b9', $data->propertyPrice );
            }
            
            // obligationsOnTheProperty
            if( isset( $data->obligationsOnTheProperty ) ){
                update_post_meta( $prop_id, 'fave_d8a7d984d8add982d988d982-d988d8a7d984d8a7d984d8aad8b2d8a7d985d8a7d8aa-d8b9d984d989-d8a7d984d8b9d982d8a7d8b1-d8a7d984d8bad98ad8b1-d985', $data->obligationsOnTheProperty );
            }
            
            $adress = 'المملكة العربية السعودية';
            // Address
            if( isset( $data->location->region ) || isset( $data->location->city ) ) {
                $country = 'المملكة العربية السعودية';
                $adress = $country . ', ' .$data->location->region .', '.$data->location->city . ', ' . $data->location->district;            
                update_post_meta( $prop_id, 'fave_property_map_address', $adress );
                update_post_meta( $prop_id, 'fave_property_address', $adress );
            }

            // lat & long
            if( ( isset($data->location->latitude) && !empty($data->location->latitude) ) && (  isset($data->location->longitude) && !empty($data->location->latitude)  ) ) {
               
                $lat = $data->location->latitude;
                $lng = $data->location->longitude;

                /* ---------------------- get lat & lon from openstreet --------------------- */
                $search_adress = $this->search_adress($adress);
                if( !empty($search_adress) && isset($search_adress[0]) ) {
                    $lat = $search_adress[0]->lat;
                    $lng = $search_adress[0]->lon;
                }
                /* ---------------------- get lat & lon from openstreet --------------------- */

                $streetView = '';
                $lat_lng = $lat.','.$lng;


                update_post_meta( $prop_id, 'houzez_geolocation_lat', $lat );
                update_post_meta( $prop_id, 'houzez_geolocation_long', $lng );
                update_post_meta( $prop_id, 'fave_property_location', $lat_lng );
                update_post_meta( $prop_id, 'fave_property_map', '1' );
                update_post_meta( $prop_id, 'fave_property_map_street_view', $streetView );
            }

            // Land Area Size
            if( isset( $data->propertyArea ) ) {
                update_post_meta( $prop_id, 'fave_property_land', $data->propertyArea );
            }
            
            if( isset($data->propertyFace) ) {
                update_post_meta( $prop_id, 'fave_d988d8a7d8acd987d8a9-d8a7d984d8b9d982d8a7d8b1', $data->propertyFace );
            }
            
            

            update_post_meta( $prop_id, 'advertiserId', $data->advertiserId );
            update_post_meta( $prop_id, 'number', $data->number );
            update_post_meta( $prop_id, 'fave_d8b1d982d985-d8a7d984d8aad981d988d98ad8b6', $data->number );
            update_post_meta( $prop_id, 'deedNumber', $data->deedNumber );
            update_post_meta( $prop_id, 'fave_d8b1d982d985-d8b9d982d8af-d8a7d984d988d8b3d8a7d8b7d8a9-d8a7d984d8b9d982d8a7d8b1d98ad8a9', $data->contractNumber ); 
 
            /*---------------------------------------------------------------------------------*
            * Save expiration meta 
            *----------------------------------------------------------------------------------*/
            update_post_meta( $prop_id, 'creationDate', $data->issueDate );
            update_post_meta( $prop_id, 'endDate', $data->endDate );
            update_post_meta( $prop_id, 'houzez_manual_expire', 1 );

            $options = array();
            $timestamp = get_gmt_from_date($data->endDate,'U');

            // Schedule/Update Expiration
            $options['id'] = $prop_id;

            _houzezScheduleExpiratorEvent( $prop_id, $timestamp, $options );
            /*---------------------------------------------------------------------------------*
            * End expiration meta
            *----------------------------------------------------------------------------------*/
 
            if( isset( $data->obligationsOnTheProperty ) ) {
                update_post_meta( $prop_id, 'fave_d8a7d984d8a7d984d8aad8b2d8a7d985d8a7d8aa-d8a7d984d8a3d8aed8b1d989-d8b9d984d989-d8a7d984d8b9d982d8a7d8b1', $data->obligationsOnTheProperty  );
            }
            if( isset( $data->guaranteesAndTheirDuration ) ) {
                update_post_meta( $prop_id, 'fave_d8a7d984d8b6d985d8a7d986d8a7d8aa-d988d985d8afd8aad987d8a7', $data->guaranteesAndTheirDuration );
            }
            if( isset( $data->theBordersAndLengthsOfTheProperty ) ) {
                update_post_meta( $prop_id, 'fave_d8add8afd988d8af-d988d8a3d8b7d988d8a7d984-d8a7d984d8b9d982d8a7d8b1', $data->theBordersAndLengthsOfTheProperty );
            } 
            if( isset( $data->complianceWithTheSaudiBuildingCode ) ) {
                update_post_meta( $prop_id, 'fave_d985d8b7d8a7d8a8d982d8a9-d983d988d8af-d8a7d984d8a8d986d8a7d8a1-d8a7d984d8b3d8b9d988d8afd98a', $data->complianceWithTheSaudiBuildingCode );
            }
            if( isset( $data->complianceWithTheSaudiBuildingCode ) ) {
                update_post_meta( $prop_id, 'fave_d985d8b7d8a7d8a8d982d8a9-d983d988d8af-d8a7d984d8a8d986d8a7d8a1-d8a7d984d8b3d8b9d988d8afd98a', $data->complianceWithTheSaudiBuildingCode );
            }
            if( isset( $data->propertyUtilities ) ) {
                if( is_array($data->propertyUtilities) ) {
                    foreach( $data->propertyUtilities as $propertyUtiliti ) {
                        update_post_meta( $prop_id, 'fave_d8aed8afd985d8a7d8aa-d8a7d984d8b9d982d8a7d8b1', $propertyUtiliti );
                    }
                }
            }
            if( isset( $data->channels ) ) {
                if( is_array($data->channels) ) {
                    foreach( $data->channels as $channel ) {
                        update_post_meta( $prop_id, 'fave_d982d986d988d8a7d8aa-d8a7d984d8a5d8b9d984d8a7d986', $channel );
                    }
                }
            }

             //prop_labels
             if( isset( $data->propertyUsages ) ) {
                if( is_array($data->propertyUsages) ) {
                    foreach( $data->propertyUsages as $propertyUsage ) {
                        wp_set_object_terms( $prop_id, $propertyUsage, 'property_label' );
                    }
                }
            }
            

        return $prop_id;
    }

    public function locations_number_name($str, $number = false)
    {
        if( empty( $str ) ) {
            return;
          }
          
          $input = $str;
          // Splitting the string using regular expressions
          $pattern = '/([^\d]+)(\d+)/u';
          preg_match($pattern, $input, $matches);
          
          if( empty($matches) ) {
            $stringPart = str_replace('-', ' ', $str);
            $numberPart = 0;
          }else{
            $stringPart = $matches[1]; // The string part "الحناكيه"
            $numberPart = $matches[2]; // The number part "306001"
            $stringPart = str_replace('-', ' ', $stringPart);
          }
          if( $number ) {
            return $numberPart;
          }
          return $stringPart;

    }

    public function get_images_ids($propperty_image_ids)
    {
        
       require_once ( IM_DIR . 'module/nhc/class-rega-module.php' );
        $RegaMoudle = new RegaMoudle();
        
        $images_url = [];

        foreach ( (array) $propperty_image_ids as $image_id ) {    
            $image_url = wp_get_attachment_image_url( $image_id, 'full' );
            if( empty( $image_url ) ) {
                continue;
            }
            $form_data = array(
                'file'=> new CURLFile( $image_url )
            );
            $response = $RegaMoudle->SendAttachment($form_data);
            $response = json_decode( $response );
            if( isset( $response->Body->result->fileId ) ) {
                $images_url[] = $response->Body->result->fileId ;
            }
        }

        return $images_url;

    }
    
    /**
     * search_adress
     *
     * @param  mixed $adress
     * @return void
     */
    public function search_adress($adress)
    {
        if ( empty( $adress ) ) {
            return;
        }

        $adress_search = urlencode($adress);

        $data = [];
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://nominatim.openstreetmap.org/search?email=admin@aqargate.com&format=json&q=' . $adress_search,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);

        $data = json_decode($response);

        return $data;

    }

}
new NHC();