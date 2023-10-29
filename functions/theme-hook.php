<?php 

/* -------------------------------------------------------------------------- */
/*                             im_enqueue_scripts                             */
/* -------------------------------------------------------------------------- */
add_action('wp_enqueue_scripts', 'im_enqueue_scripts');
if( ! function_exists('im_enqueue_scripts') ){
    function im_enqueue_scripts()
    {
        wp_enqueue_script( 'i_monon', trailingslashit( get_stylesheet_directory_uri() )  .'/assets/js/i-monon.js', array(), '', true );
        $userID = get_current_user_id();
        $ajax_object = array(
            'ajaxurl' => admin_url( 'admin-ajax.php'),
            'userID'  => $userID,          
        );
        wp_localize_script( 'i_monon', 'im_ajax', $ajax_object );  
    }
}
/* -------------------------------------------------------------------------- */
/*                            filter myacount link                            */
/* -------------------------------------------------------------------------- */
add_filter( 'rtcl_get_account_endpoint_url', 'im_filter_myacount_link', 10, 2 );
function im_filter_myacount_link($url , $endpoint)  
{
    if( is_user_logged_in() ) 
    {
        return $url;
    } else if( ! empty( get_option('_register_page|||0|id') )  && 'registration' === $endpoint ){
        $page = (int) get_option('_register_page|||0|id');
        $url  = get_permalink( $page );
    }
    return $url;
}

/* -------------------------------------------------------------------------- */
/*                                  nafathApi                                 */
/* -------------------------------------------------------------------------- */
add_action( 'wp_ajax_nafathApi', 'nafathApi' );
add_action( 'wp_ajax_nopriv_nafathApi', 'nafathApi' );
function nafathApi() {
    $id = isset($_POST['id']) ? $_POST['id'] : '';

    if( empty($id) ) {
        wp_send_json( array('success' => false, 'error'=> 'رقم الهوية مطلوب') );
        wp_die();
    }

    if( ! is_user_logged_in() ){       
        $nath_id = get_users('meta_value=' . $id );
    
        $message = 'رقم الهوية مسجل مسبقا في الموقع , يمكنك تسجيل الدخول';
        if( is_array( $nath_id ) && count( $nath_id ) > 0 ) {
            wp_send_json( array('success' => false, 'message' => $message ) );
            wp_die();
        }
    }

    require_once ( IM_DIR . 'module/nafath/class-nafath.php' );

    $NafathMoudle = new NafathMoudle();

    $response = $NafathMoudle->login( $id );
    
    
    /**----------------Test--------------------- */
        // $trans = 'c6c5085d-13e7-4408-ad11-2afa44fe2e49';
        // $rand  = '44';
        // wp_send_json( array('success' => true, 'number' => $rand, 'transId' => $trans ) );
        // wp_die();
    /**----------------------------------------- */


    if( isset( $response->random ) ) {
        $data['userInfo'] = [];
        $data['response'] = $response;
        $data['transId']  = $response->transId;
        $data['cardId']   = $id;
        $data['status']   = 'PENDING';
       
        $NafathDB = new NafathDB();
       
        $NafathDB->update_nafath_callback($data);

        wp_send_json( array('success' => true, 'number' => $response->random , 'transId' => $response->transId ) );
        wp_die();
    }else{
        wp_send_json( array('success' => false, 'message' => isset($response->message) ? $response->message : 'هناك خطأ ! حاول مرة اخري' ) );
        wp_die(); 
    }
} 

/* -------------------------------------------------------------------------- */
/*                                  fetchdata                                 */
/* -------------------------------------------------------------------------- */
if( ! function_exists('fetchdata') ) {
function fetchdata()
{

    $id = isset($_POST['authorid']) ? $_POST['authorid'] : '';
    $transId = isset($_POST['transId']) ? $_POST['transId'] : '';

    if( empty($id) ) {
        wp_send_json( array('success' => false, 'message' => '' ) );
        wp_die(); 
    }

    $NafathDB = new NafathDB();

    $data = [
        'transId'  => $transId,
        'cardId'   => $id,
        'userInfo' => '',
    ];

    $get_status = $NafathDB->get_status($data);
    
    if( $get_status ){
        $get_data = $NafathDB->get_nafath_data($data);
        if( isset($get_data['status']) && $get_data['status'] === 'REJECTED' ) {
            wp_send_json( array(
                'success'  => $get_status,
                'msg'      => $get_data['msg'],
                'status'   => $get_data['status']
            ) );
            wp_die();
        }
            wp_send_json( array(
                'success'    => $get_status,
                'id'         => $id,
                'arFullName' => $get_data['arFullName'],
                'arFirst'    => $get_data['arFirst'],
                'arGrand'    => $get_data['arGrand'],
                'arTwoNames' => $get_data['arTwoNames'],
                'status'     => $get_data['status'],
                'html'       => im_register_form(),
            ) );
        wp_die();

    }else{
        wp_send_json( array('success' => false, 'message' => 'لم يتم اكنمال الربط' ) );
        wp_die();
    }
}
add_action( 'wp_ajax_fetchdata', 'fetchdata' );
add_action( 'wp_ajax_nopriv_fetchdata', 'fetchdata' );
}
/* -------------------------------------------------------------------------- */
/*                             im_urlsafeB64Decode                            */
/* -------------------------------------------------------------------------- */
function im_urlsafeB64Decode($input)
{
    $remainder = strlen($input) % 4;
    if ($remainder) {
        $padlen = 4 - $remainder;
        $input .= str_repeat('=', $padlen);
    }
    return base64_decode(strtr($input, '-_', '+/'));
}

/* -------------------------------------------------------------------------- */
/*                                Register form                               */
/* -------------------------------------------------------------------------- */
function im_register_form (){

    $html = '<form class="user-info-form" id="im-register-form" type="post">
        <input type="hidden" id="first_name" name="first_name" value="">
        <input type="hidden" id="last_name" name="last_name" value="">
        <input type="hidden" id="role" name="role" value="">
        <input type="hidden" id="transId" name="transId" value="">
        <div class="register-form row">
            <div class="form-group col-md-12 mb-3 col-xs-12">
                <div class="form-group-field username-field">
                    <input class="form-control" name="full_name" type="text"
                        placeholder="' . __('full Name','im') . '" readonly />
                </div><!-- input-group -->
            </div><!-- form-group -->
            <div class="col-sm-6 col-xs-12">
                <div class="form-group">
                    <label>' . __('رقم الهوية','im') . '</label>
                    <input type="text" name="id_number" value="" class="form-control"
                        placeholder="' . __('يرجي ادخال رقم الهوية','im') . '" readonly>
                </div>
            </div>
    
            <div class="form-group col-sm-6 col-xs-12 mb-3">
                <div class="form-group">
                    <label for="username">' . __('Username','im') . '</label>
                    <input class="form-control" name="username" type="text"
                        placeholder="' .  __('Username','im') . '" />
                </div><!-- input-group -->
            </div><!-- form-group -->
    
    
            <div class="form-group col-sm-6 col-xs-12 mb-3">
                <div class="form-group">
                    <label for="useremail">' . __('Email','im') . '</label>
                    <input class="form-control" name="useremail" type="email"
                        placeholder="' . __('Email','im') . '" />
                </div><!-- input-group -->
            </div><!-- form-group --> ';
            $html .= '<div class="form-group col-sm-6 col-xs-12">
                <div class="form-group">
                    <label for="phone_number">' . __('Phone','im') . '</label>
                    <input class="form-control" name="phone_number" type="number"
                        placeholder="' . __('Phone','im') . '" />
                </div><!-- input-group -->
            </div><!-- form-group --> ';
             
    
            $html .= '<div class="form-group col-sm-6 col-xs-12">
                <div class="form-group">
                    <label for="register_pass">' . __('Password','im') . '</label>
                    <input class="form-control" name="register_pass" placeholder="' . __('Password','im') . '"
                        type="password" />
                </div><!-- input-group -->
            </div><!-- form-group -->
            <div class="form-group col-sm-6 col-xs-12">
                <div class="form-group">
                    <label for="register_pass_retype"> ' . __('Retype Password','im') . '</label>
                    <input class="form-control" name="register_pass_retype"
                        placeholder="' . __('Retype Password','im') . '" type="password" />
                </div><!-- input-group -->
            </div><!-- form-group --> ';
             
            $html .= '<div class="col-sm-6 col-xs-12">
                <div class="form-group">
                    <label
                        for="brokerage_license_number">' . __('رقم رخصة ( فال )','im') . '</label>
                    <input type="text" name="brokerage_license_number" value="" class="form-control"
                        placeholder="">
                </div>
            </div>
            <div class="col-sm-6 col-xs-12">
                <div class="form-group">
                    <label for="license_expiration_date">' . __('انتهاء الرخصة','im') . '</label>
                    <input type="date" name="license_expiration_date" value="" class="form-control" placeholder="">
                </div>
            </div>
        </div><!-- login-form-wrap -->
    
        <div class="form-tools">
            <label class="control control--checkbox">
                <input name="term_condition" type="checkbox">
                ' . sprintf( __( 'I agree with your <a target="_blank" href="%s">Terms & Conditions</a>', 'im' ), 
                get_permalink(get_option('login_terms_condition') )) . '
                <span class="control__indicator"></span>
            </label>
        </div><!-- form-tools --> ';
    
        $html .= '<input type="hidden" name="action" value="im_registration" id="register_action">
                <button id="im-register" type="submit" class="btn btn-primary btn-full-width mt-5">
                '. __('Register', 'im') .'
                </button>
            </form>';
        return $html;   
        
    }

/* -------------------------------------------------------------------------- */
/*                             update user acount                             */
/* -------------------------------------------------------------------------- */
add_action( 'rtcl_update_user_account', 'im_update_user_account', 10, 2 );
function im_update_user_account($user_id, $data)  {
    if( ! empty($data)  && ! empty( $user_id )) {

        if( isset($data['full_name'])  && !empty( $data['full_name'] ) ) {
            update_user_meta( $user_id, 'display_name', $data['full_name'] );
        }
        if( isset($data['type-id'])  && !empty( $data['type-id'] ) ) {
            update_user_meta( $user_id, '_im_type_id', $data['type-id'] );
        }
        if( isset($data['FAL-license-number'])  && !empty( $data['FAL-license-number'] ) ) {
            update_user_meta( $user_id, '_im_FAL_license_number', $data['FAL-license-number'] );
        }
        if( isset($data['FAL-license-number-end'])  && !empty( $data['FAL-license-number-end'] ) ) {
            update_user_meta( $user_id, '_im_FAL_license_number_end', $data['FAL-license-number-end'] );
        }
        if( isset($data['user-type'])  && !empty( $data['user-type'] ) ) {
            update_user_meta( $user_id, '_im_user_type', $data['user-type'] );
        }
        if( isset($data['company'])  && !empty( $data['company'] ) ) {
            update_user_meta( $user_id, '_im_company', $data['company'] );
        }

    }
}

/* -------------------------------------------------------------------------- */
/*                         filter my account endpoint                         */
/* -------------------------------------------------------------------------- */
add_filter( 'rtcl_my_account_endpoint', 'im_my_account_endpoint' );
function im_my_account_endpoint($endpoint) {
    
    return $endpoint;
}

/* -------------------------------------------------------------------------- */
/*                             custom registration                            */
/* -------------------------------------------------------------------------- */
add_action( 'wp_ajax_im_registration', 'im_registration' );
add_action( 'wp_ajax_nopriv_im_registration', 'im_registration' );
/**
 * im_registration
 * 
 * @var first_name                  @var last_name
 * @var role                        @var transId
 * @var full_name                   @var id_number
 * @var username                    @var useremail
 * @var phone_number                @var register_pass
 * @var register_pass_retype        @var brokerage_license_number
 * @var license_expiration_date     @var action/im_registration
 * 
 * @return user_id
 */
function im_registration() {

    $allowed_html = array();

    $usermane          = trim( sanitize_text_field( wp_kses( $_POST['username'], $allowed_html ) ));
    $email             = trim( sanitize_text_field( wp_kses( $_POST['useremail'], $allowed_html ) ));
    $term_condition    = isset( $_POST['term_condition'] ) ? wp_kses( $_POST['term_condition'], $allowed_html ) : "off";
    $enable_password   = true;

    $response = isset( $_POST["g-recaptcha-response"] ) ? $_POST["g-recaptcha-response"] : "";

    do_action('im_before_register');

    $user_roles = array ( 'houzez_agency', 'houzez_agent', 'houzez_buyer', 'houzez_seller', 'houzez_owner', 'houzez_manager' );

    $user_role = get_option( 'default_role' );

    // if( isset( $_POST['role'] ) && $_POST['role'] != '' && in_array( $_POST['role'], $user_roles ) ) {
    //     $user_role = isset( $_POST['role'] ) ? sanitize_text_field( wp_kses( $_POST['role'], $allowed_html ) ) : $user_role;
    // } else {
    //     $user_role = $user_role;
    // }


    $term_condition = ( $term_condition == 'on') ? true : false;

    if( !$term_condition ) {
        echo wp_send_json( array( 'success' => false, 'error' => esc_html__('You need to agree with terms & conditions.', 'im') ) );
        wp_die();
    }

    $firstname = isset( $_POST['first_name'] ) ? $_POST['first_name'] : '';
    $lastname = isset( $_POST['last_name'] ) ? $_POST['last_name'] : '';
 

    $phone_number = isset( $_POST['phone_number'] ) ? $_POST['phone_number'] : '';
    if( empty($phone_number)  ) {
        echo wp_send_json( array( 'success' => false, 'error' => esc_html__('Please enter your Phone number', 'im') ) );
        wp_die();
    }


    $brokerage_license_number = isset( $_POST['brokerage_license_number'] ) ? $_POST['brokerage_license_number'] : '';
    if( empty($brokerage_license_number) ) {
        echo wp_send_json( array( 'success' => false, 'error' => esc_html__('رقم رخصة الفال مطلوب', 'im') ) );
        wp_die();
    }
    $license_expiration_date = isset( $_POST['license_expiration_date'] ) ? $_POST['license_expiration_date'] : '';
    if( empty($license_expiration_date)  ) {
        echo wp_send_json( array( 'success' => false, 'error' => esc_html__('تاريخ انتهاء الرخصة مطلوب', 'im') ) );
        wp_die();
    }

    if( empty( $usermane ) ) {
        echo wp_send_json( array( 'success' => false, 'error' => esc_html__('The username field is empty.', 'im') ) );
        wp_die();
    }
    if( strlen( $usermane ) < 3 ) {
        echo wp_send_json( array( 'success' => false, 'error' => esc_html__('Minimum 3 characters required', 'im') ) );
        wp_die();
    }
    if (preg_match("/^[0-9A-Za-z_]+$/", $usermane) == 0) {
        echo wp_send_json( array( 'success' => false, 'error' => esc_html__('Invalid username (do not use special characters or spaces)!', 'im') ) );
        wp_die();
    }
    if( username_exists( $usermane ) ) {
        echo wp_send_json( array( 'success' => false, 'error' => esc_html__('This username is already registered.', 'im') ) );
        wp_die();
    }
    if( empty( $email ) ) {
        echo wp_send_json( array( 'success' => false, 'error' => esc_html__('The email field is empty.', 'im') ) );
        wp_die();
    }

    if( email_exists( $email ) ) {
        echo wp_send_json( array( 'success' => false, 'error' => esc_html__('This email address is already registered.', 'im') ) );
        wp_die();
    }

    if( !is_email( $email ) ) {
        echo wp_send_json( array( 'success' => false, 'error' => esc_html__('Invalid email address.', 'im') ) );
        wp_die();
    }
    

    if( $enable_password == 'yes' ){
        $user_pass         = trim( sanitize_text_field(wp_kses( $_POST['register_pass'] ,$allowed_html) ) );
        $user_pass_retype  = trim( sanitize_text_field(wp_kses( $_POST['register_pass_retype'] ,$allowed_html) ) );

        if ($user_pass == '' || $user_pass_retype == '' ) {
            echo wp_send_json( array( 'success' => false, 'error' => esc_html__('One of the password field is empty!', 'im') ) );
            wp_die();
        }

        if ($user_pass !== $user_pass_retype ){
            echo wp_send_json( array( 'success' => false, 'error' => esc_html__('Passwords do not match', 'im') ) );
            wp_die();
        }
    }


    // houzez_google_recaptcha_callback();

    if($enable_password == 'yes' ) {
        $user_password = $user_pass;
    } else {
        $user_password = wp_generate_password( $length=12, $include_standard_special_chars=false );
    }
    $user_id = wp_create_user( $usermane, $user_password, $email );

    if ( is_wp_error($user_id) ) {
        echo wp_send_json( array( 'success' => false, 'error' => $user_id ) );
        wp_die();
    } else {

        wp_update_user( array( 'ID' => $user_id, 'role' => $user_role ) );

        update_user_meta( $user_id, 'first_name', $firstname);
        update_user_meta( $user_id, 'last_name', $lastname);

        if ( !empty( $_POST['id_number'] ) ) {
            $id_number = sanitize_text_field( $_POST['id_number'] );
            update_user_meta( $user_id, 'aqar_author_id_number', $id_number );
        }

        if( !empty( $_POST['brokerage_license_number'] ) ){
            $brokerage_license_number = $_POST['brokerage_license_number'];
            update_user_meta( $user_id, 'brokerage_license_number', $brokerage_license_number );
        }
        
        if( !empty( $_POST['license_expiration_date'] ) ){
            $license_expiration_date = $_POST['license_expiration_date'];
            update_user_meta( $user_id, 'license_expiration_date', $license_expiration_date );
        }

        if ( !empty( $_POST['full_name'] ) ) {    
            wp_update_user( array (
                'ID' => $user_id, 
                'display_name' => $_POST['full_name'],
                'nickname'     => $_POST['full_name']
            ));
            update_user_meta( $user_id, 'display_name', $_POST['full_name'] );
            update_user_meta( $user_id, 'nickname', $_POST['full_name'] );
        }

        if ( !empty( $_POST['id_number'] ) ) { 
            update_user_meta( $user_id, '_im_type_id', $_POST['id_number'] );
        }
        if ( !empty( $_POST['brokerage_license_number'] ) ) { 
            update_user_meta( $user_id, '_im_FAL_license_number', $_POST['brokerage_license_number'] );
        }
        if ( !empty( $_POST['license_expiration_date'] ) ) { 
            update_user_meta( $user_id, '_im_FAL_license_number_end', $_POST['license_expiration_date'] );
        }
        if ( !empty( $_POST['role'] ) ) { 
            update_user_meta( $user_id, '_im_user_type', $_POST['role'] );
        }

        if ( !empty( $_POST['phone_number'] ) ) { 
            update_user_meta( $user_id, '_rtcl_phone', $_POST['phone_number'] );
         }

        update_user_meta( $user_id, '_im_nafath_account', 1 );

        do_action('im_after_register', $user_id);
        if( $enable_password =='yes' ) {
            echo wp_send_json( 
                array( 
                    'success' => true, 
                    'data'    => [
                        'msg' => esc_html__('Your account was created and you can login now!', 'im'),
                        'user_id' => $user_id,
                    ],
                ) );
        } else {
            echo wp_send_json( array( 'success' => true, 'error' => esc_html__('An email with the generated password was sent!', 'im') ) );
        }
    }
    wp_die();

} 

/* -------------------------------------------------------------------------- */
/*                                csv_to_array                                */
/* -------------------------------------------------------------------------- */
if( ! function_exists('csv_to_array') ){    
    /**
     * csv_to_array
     *
     * @param  mixed $file
     * @return void
     */
    function csv_to_array($file) {

        if (($handle = fopen($file, 'r')) === false) {
            die('Error opening file');
        }
        
        $headers = fgetcsv($handle, 10000, ',');
        $headers = preg_replace('/ ^[\pZ\p{Cc}\x{feff}]+|[\pZ\p{Cc}\x{feff}]+$/ux', '', $headers);
        $_data = array();
        
        while ($row = fgetcsv($handle, 10000, ',')) {
            $row = preg_replace('/ ^[\pZ\p{Cc}\x{feff}]+|[\pZ\p{Cc}\x{feff}]+$/ux', '', $row);
            if (count($row) == count($headers)) {
                $_data[] = array_combine($headers, $row);
            }else{
                $_data[] = array_merge($headers, $row);
            }
        }
        fclose($handle);
    
        return $_data;
      
      }
}