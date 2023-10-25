<style>
    svg#loader-1{
        height: auto !important;
    }
</style>
<div class="addPropert-wrap">
    <div class="addProperty-header">
        <img src="<?php echo trailingslashit( get_stylesheet_directory_uri() ) .'assets/img/refresh-data.png'; ?>" class="" loading="lazy" width="50" height="50">
        <h2>اضافة اعلان مرخص</h2>
    </div>
    <div id="form1" class="tab-content">
        <form autocomplete="off" id="submit_aqar_isvalid_form" name="new_post" method="post" action="#"
            enctype="multipart/form-data" class="add-frontend-property">
            <div class="w-100 text-center mb-5">
                <h2><?php echo $formTitle; ?></h2>
            </div>
            <hr>
            <div id="errors-messages" class="validate-errors alert alert-danger im-hidden" role="alert">
                <strong id="messages"></strong>
            </div>
            <div id="success-messages" class="validate-success alert alert-success im-hidden" role="alert">
                <strong id="messages"></strong>
            </div>
            <div class="addPropert-wrap mobile"> 
                <div class="addProperty_container_ad">
                    <div class="addProperty_card mobile">
                        <button id="get_ad_info" type="submit" class="btn im-submit-js btn-success">
                            <span class="btn-loader im-loader-js"></span>تأكيد المعلومات
                        </button>
                        <div class="addProperty_line"></div>
                        <div class="form-group col-md-8 col-sm-12">
                            <label for="adLicenseNumber"> * رقم ترخيص الاعلان</label>
                            <input class="form-control" id="adLicenseNumber" required name="adLicenseNumber" value=""
                                placeholder="" type="text">
                        </div>
                    </div>
                </div>
            </div>

            <div id="property-info" class="w-100" style="display: none;">
                <div class="w-100 validate-success alert alert-success" id="property-info-text"></div>
            </div>
            <?php wp_nonce_field('aqar_isvalid_api', 'aqar_isvalid_api'); ?>
            <!-- <input type="hidden" name="action" value="aqar_isvalid_api"/> -->
            <div class="d-flex justify-content-between add-new-listing-bottom-nav-wrap mt-5">
                <a href="<?php echo esc_url($cancel_link ); ?>" class="btn-cancel btn btn-primary-outlined">
                    <?php echo esc_html__('Cancel', 'im'); ?>
                </a>
                <button id="check_aqar_isvalid" type="submit" class="btn im-submit-js btn-primary im-hidden">
                    <?php get_template_part('template-parts/loader'); ?>
                    <?php echo esc_html__('Submit Property', 'im'); ?>
                </button>
            </div>
            <?php get_template_part('module/loader'); ?>
        </form>
        <script>
        jQuery(document).ready(function($) {

            $('#check_aqar_isvalid').prop('disabled', true);
            $('#check_aqar_isvalid').on('click', function(e) {
                e.preventDefault();
                var currnt = $(this);
                var form = currnt.parents('form');
                // introduce the validation rules to the form! 
                aqar_isvalid(currnt);
            });

            $('#get_ad_info').on('click', function(e) {
                e.preventDefault();
                var currnt = $(this);
                var form = currnt.parents('form');
                // introduce the validation rules to the form! 
                get_ad_info(currnt);
            });

            var aqar_isvalid = function(currnt) {
                var $Message = $('#errors-messages');
                var $success_messages = $('#success-messages');
                var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";
                var formValue = new FormData(document.getElementById('submit_aqar_isvalid_form'));
                formValue.append("action", "aqar_isvalid_api");
                $.ajax({
                    type: 'post',
                    url: ajaxurl,
                    dataType: 'json',
                    cache: false,
                    processData: false,
                    contentType: false,
                    data: formValue,
                    beforeSend: function() {
                        form_valid($('#submit_aqar_isvalid_form'));
                        $('.sync__loader').show();
                        $Message.addClass('im-hidden');
                        $success_messages.addClass('im-hidden');
                    },
                    complete: function() {
                        $('.sync__loader').hide();
                    },
                    success: function(response) {
                        if (response.success) {
                            $success_messages.removeClass('im-hidden');
                            $success_messages.find('#messages').empty().append(response.reason);
                            window.setTimeout(function() {
                                window.location = response.redir;
                            }, 1000);
                        } else {
                            $Message.removeClass('im-hidden');
                            $Message.find('#messages').empty().append(response.reason);
                        }
                        currnt.find('.im-loader-js').removeClass('loader-show');
                    },
                    error: function(xhr, status, error) {
                        var err = eval("(" + xhr.responseText + ")");
                        console.log(err.Message);
                    }
                });

            } // end aqar_isvalid

            var get_ad_info = function(currnt) {
                var $Message = $('#errors-messages');
                var $success_messages = $('#success-messages');
                var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";
                var formData = new FormData(document.getElementById('submit_aqar_isvalid_form'));
                formData.append("action", "get_ad_info");

                $.ajax({
                    type: 'post',
                    url: ajaxurl,
                    dataType: 'json',
                    cache: false,
                    processData: false,
                    contentType: false,
                    data: formData,
                    beforeSend: function() {
                        form_valid($('#submit_aqar_isvalid_form'));
                        $('.sync__loader').show();
                        $Message.addClass('im-hidden');
                        $success_messages.addClass('im-hidden');
                        $("#property-info").hide();
                    },
                    complete: function() {
                        $('.sync__loader').hide();
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#check_aqar_isvalid').removeClass('im-hidden');
                            $('#check_aqar_isvalid').prop('disabled', false);
                            $("#property-info").show();
                            $('html, body').animate({
                                scrollTop: $("#property-info-text").offset().top
                            }, 1000);
                            $('#property-info-text').empty().append(response.reason);
                        } else {
                            $Message.removeClass('im-hidden');
                            $Message.find('#messages').empty().append(response.reason);
                            document.getElementById('property-info-text').value = response
                                .reason;
                        }
                        currnt.find('.im-loader-js').removeClass('loader-show');
                    },
                    error: function(xhr, status, error) {
                        var err = eval("(" + xhr.responseText + ")");
                        console.log(err.Message);
                    }
                });

            } // get_ad_info
        });

        let form_valid = function(form) {
            form.validate({
                rules: {
                    adLicenseNumber: {
                        required: true,
                        minlength: 4,
                    }
                },
                messages: {
                    adLicenseNumber: {
                        required: " *  رقم ترخيص الاعلان مطلوب",
                        minlength: "يجب ألا يقل رقم ترخيص الإعلان عن 5 أرقام",
                    }
                }
            });
        }
        </script>
        
    </div>
</div>