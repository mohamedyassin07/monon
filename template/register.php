<?php
/**
 * Template Name: Register Template
 * 
 * @author  Mohamed Yassin
 * @since   1.0
 * @version 1.0
 */
use Rtcl\Helpers\Functions;
use Rtcl\Helpers\Link;

get_header(); 
?>
<div id="primary" class="content-area">
    <div class="container">
        <div class="row">
            <div class="col-md-8 offset-md-2 offset-sm-0">
                <!-- Register Area -->
                <div class="register-box">
                    <!-- Register User Info Area -->
                    <div class="register-user-info-box">

                        <!-- User Info Title Area -->
                        <div class="user-info-title-box text-center">
                            <h1>إنشاء حساب</h1>
                            <p>
                                إنشاء حساب جديد من خلال النفاذ الوطني
                            </p>
                            <p class="login-link"><?php esc_html_e( 'Already have an account? Please login', 'classified-listing' ); ?>
                                <a href="<?php echo esc_url( Link::get_my_account_page_link() ); ?>"><?php esc_html_e( 'Here', 'classified-listing' ); ?></a>
                            </p>
                        </div>
                        <!-- User Info Title Area End -->

                        <!-- User Info Form Area -->
                        <div class="user-info-box">

                            <div class="rh_login_modal_messages rh_login_message_show">
                                <p id="register-message" class="rh_modal__msg"></p>
                                <p id="register-error" class="rh_modal__msg"></p>
                                <p id="register-error-time" class="rh_modal__msg"></p>
                            </div>
                            <div id="register-screen-2">
                                <?php 
                                if( isset( $_GET['debug-im'] ) ) {
                                    echo im_register_form();
                                 }
                                ?>
                            </div>
                            <div id="register-screen-1">
                                <form class="user-info-form" action="#">
    
                                    <div class="form-group">
                                        <div class="radioBtnWrapper">
                                            <label class="radioBtn Radio-module_checked">
                                                <div>
                                                    <span class="Radio-module_button">
                                                        <input type="radio" class="Radio-module_input" name="id_type" value="1" checked="">
                                                    </span>
                                                    <span>هوية وطنية</span>
                                                </div>
                                            </label>
                                            <label class="radioBtn">
                                                <div>
                                                    <span class="Radio-module_button">
                                                        <input type="radio" class="Radio-module_input" name="id_type" value="2">
                                                    </span>
                                                    <span>منشأة</span>
                                                </div>
                                            </label>
                                        </div>
                                        <label id="name-ipt" for="id">رقم الهوية الوطنية</label>
                                        <input type="number" id="id" class="id" maxlength="10" minlength="10">
                                    </div>

                    
                                    <button id="next-register-btn" type="submit" class="btn btn-primary btn-full-width">
                                        <?php esc_html_e('Next','im');?>
                                    </button>
                                </form>
                                <div id="time-model" class="time-model" style="display: none;">
                                    <div class="warp">
                                        <div id="id-number">
                                            <span>رقم التاكيد</span>
                                            <span id="nafathNumber">00</span>
                                        </div>
                                        <div id="timer">60</div>
                                    </div>
                                </div> 
                            </div>
                            <?php get_template_part('module/loader'); ?>
                        </div>
                        <!-- User Info Form Area End -->

                    </div>
                    <!-- Register User Info Area End -->

                </div>
                <!-- Register Area End -->
            </div>
        </div>
    </div>
</div>
<?php get_footer(); ?>