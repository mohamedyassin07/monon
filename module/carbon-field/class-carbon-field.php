<?php

use Carbon_Fields\Container;
use Carbon_Fields\Field;

class IM_CARBON_FIELD
{
    public function __construct(){
        add_action( 'after_setup_theme', array ( $this , 'crb_load' ) );
        add_action( 'carbon_fields_register_fields', array( $this , 'settings_panel' ) );
        // add_action( 'carbon_fields_register_fields', array( $this , 'tax_select' ) );
        // add_action( 'carbon_fields_theme_options_container_saved', [ $this, 'fields_steps' ] );
    }

    public function crb_load() {
        include_once ( IM_DIR.'libs/cf/vendor/autoload.php' );
        \Carbon_Fields\Carbon_Fields::boot();
    }

    public function settings_panel() {

        Container::make( 'theme_options','ag_settings', __( 'I-Monon Settings' ) )
        
        ->add_tab(
            'اعدادت الهيئة العامة للعقار',
            array(
                Field::make( 'checkbox', 'aq_show_api', 'Enable Api' ),
                Field::make( 'text', 'client_id', __( 'XIBM Client Id', 'im' ) ),
                Field::make( 'text', 'client_secret', __( 'XIBM Client Secret', 'im' ) ),
                Field::make( 'text', 'form_head', __( 'عنوان نموذج اضافة الاعلان', 'im' ) )
                ->set_attribute( 'placeholder', 'اكمل البينات المطلوبة للتأكد من معلومات الاعلان' ),
                Field::make( 'checkbox', 'repeat_prop', 'السماح بتكرار الاعلان' ),
                Field::make( 'checkbox', 'sandbox', 'Enable sandbox' ),
                Field::make( 'checkbox', 'dummy', 'Enable dummy data' ),
                // Field::make( 'rich_text', 'add_propery_info', __( 'تعليمات نشر اعلان وترخيص' ) ),
               
            )
        ) 
        
        // ->add_tab( __( 'Property Export' ), array(
        //     Field::make( 'html', 'crb_information_text' )
        //     ->set_html( $export_btn )
        // ) ) 

        ->add_tab( __( 'اعدادت نفاذ' ), array(
            Field::make( 'text', 'nafath_apikey', __( 'Nafath Apikey', 'im' ) ),
            Field::make( 'checkbox', 'nafath_sandbox', 'Enable sandbox' ),
            Field::make( 'association', 'register_page', __( 'Register Page' ) )
            ->set_types( array(
                array(
                    'type'      => 'post',
                    'post_type' => 'page',
                )
            )
         )->set_max( 1 ),
        ) );
        // ->add_tab( __( 'اضافة داتا المناطق/الاحياء/المدن' ), array(
        //     Field::make( 'html', 'crb_information_text_0' )
        //     ->set_html( __( 'الاضافة تكون بالترتيب المناطق اولا ثم المدن ثم الاحياء', 'im' )  ),
        //     Field::make( 'html', 'crb_information_text_2' )
        //     ->set_html( $state_btn ),
        //     Field::make( 'html', 'crb_information_text_3')
        //     ->set_html( $city_btn ),
        //     Field::make( 'html', 'crb_information_text_4')
        //     ->set_html( $area_btn_1 ),
        //     Field::make( 'html', 'crb_information_text_5')
        //     ->set_html( $area_btn_2 ),
        //     Field::make( 'html', 'crb_information_text_6')
        //     ->set_html( $area_btn_3 )
        // ) )

        // ->add_tab( __( 'Twilio Settings' ), array(
        //     Field::make( 'text', 'twilio-account-sid' ),
        //     Field::make( 'text', 'twilio-auth-token' ),
        //     Field::make( 'text', 'twilio-sender-number' ),
        //     Field::make( 'textarea', 'r-sms-txt' )
        //     ->set_attribute( 'placeholder', __("[otp] is your One Time Verification(OTP) to confirm your phone no at AqarGate.",'aqargate') ),

        // ) );


             // Display container on Book Category taxonomy
            
            Container::make( 'term_meta', __( 'Icon Font' ) )
            ->where( 'term_taxonomy', '=', 'property_status' )
            ->add_fields( array( 
                Field::make( 'icon', 'property_type_icon', __( 'Property Icon', 'crb' ) ),
            ) );

            Container::make( 'term_meta', __( 'Icon Font' ) )
             ->where( 'term_taxonomy', '=', 'property_feature' )
             ->add_fields( array( 
                 Field::make( 'icon', 'property_type_icon', __( 'Property Icon', 'crb' ) ),
            ) );

            Container::make( 'term_meta', __( 'Icon Font' ) )
             ->where( 'term_taxonomy', '=', 'property_label' )
             ->add_fields( array( 
                 Field::make( 'icon', 'property_type_icon', __( 'Property Icon', 'crb' ) ),
            ) );

    }

   
    
    /**
     * ag_tax_select
     *
     * @return void
     */
    public function ag_tax_select(){
        Container::make( 'term_meta', __( 'Select Fileds To Show' ) )
        ->where( 'term_taxonomy', '=', 'property_type' )
        ->add_fields( array(
        Field::make( 'multiselect', 'crb_available_fields', __( 'Select Fileds To Show for web' ) )
        ->add_options( $this->ag_fields_array() )
        
        ) );

    }
    
    
    /**
     * ag_fields_array
     *
     * @return void
     */
    public function ag_fields_array(){
        //get Fields
        $fields_builder = array();
        $adp_details_fields = isset(get_option('houzez_options')['adp_details_fields']) ? get_option('houzez_options')['adp_details_fields'] : '' ;
        if( is_array( $adp_details_fields ) ){
            $fields_builder = $adp_details_fields['enabled'];
            unset($fields_builder['placebo']);
        }
        return $fields_builder;
    }
    
    /**
     * prop_multi_step_fileds
     *
     * @return void
     */
    public function prop_multi_step_fileds(){      
        $main_fields  = ag_get_property_fields();
        // $extra_fields = ag_get_property_fields_extra();
        $extra_fields = [];
        // $all_fields   = array_merge($main_fields , (array)$extra_fields);
        $all_fields   = $main_fields;
        $prop_multi_step_fileds = [];
        if( !empty( $all_fields ) && is_array( $all_fields ) ) {
            foreach ($all_fields as $field) {
                $field_key = $field['field_id'];
                $prop_multi_step_fileds[$field_key] = $field['label'];
            }
        }
        return $prop_multi_step_fileds;     
    }
}

new IM_CARBON_FIELD();
