(function($){
    "use strict";
    jQuery(document).ready(function($){	

        var screen_1 = $('#register-screen-1');
        var screen_2 = $('#register-screen-2');
    
        $('#next-register-btn').on('click', function(e){
            e.preventDefault();
            var currnt = $(this);
            var form = currnt.parents('form');
            const id_type  = $("input[name=id_type]:checked").val();
            const authorid = $("#id").val();
            if( id_type === undefined ) {
                alert('اختار نوع التسجيل اولا');
                return;
            }
            // الدخول عن طريق نفاذ
            if( id_type === '1' ||  id_type === '2') {
                if(  authorid== null ||  authorid == ""  ) {
                    alert('ادخل رقم الهوية');
                    return;
                }
                nafathApi( authorid, id_type );
            }
        });
      
        var timerExpired = false; // Flag to track timer status
        var downloadTimer;
        var timeleft = 60;
        var show_error;
        var timer;
        function updateTimer(show_error, timeleft) {
            // alert(show_error);
            downloadTimer = setInterval(function() {
              if (timeleft <= 0) {
                clearInterval(downloadTimer);
                $('#time-model').fadeOut(1000, function() {
                  $(this).hide();
                });
                clearTimeout(timer);
                document.getElementById("timer").innerHTML = "0";
          
                // Timer expired, stop fetchdata and show a message
                console.log(show_error);
                if( show_error ) {
                    timerExpired = true;
                    showTimerExpiredMessage();
                }
              } else {
                document.getElementById("timer").innerHTML = timeleft;
              }
              timeleft -= 1;
            }, 1000);
        }
    
            var $messages = $('#register-error'); 
            var xhr; // Declare xhr variable outside the function scope
            function nafathApi(authorid, aqar_author_type_id) {
                xhr = $.ajax({
                    type: 'post',
                    url: im_ajax.ajaxurl,
                    dataType: 'json',
                    data:{
                        action: 'nafathApi',
                        id: authorid,
                    },	
                    beforeSend: function() {
                        $('.sync__loader').show();
                        $('.rh_login_modal_messages').hide();
                    },
                    complete: function(){
                        $('.sync__loader').hide();
                    },
                    success: function( response ) {
                        if( response.success ) {
                            var transId	= response.transId;
                            $('#id-number > #nafathNumber').empty().html(response.number);
                            $('#time-model').fadeIn( 1000, function() {
                                $(this).show();
                            });
                            updateTimer(true , 60);
                            fetchdata(authorid, aqar_author_type_id, transId);
                        } else {
                            $('.rh_login_modal_messages').show();
                            $messages.empty().append('<div dir="rtl" class="alert alert-danger" role="alert"><i class="houzez-icon icon-check-circle-1 mr-1"></i>'+ response.message +'</div>');
                            return false;
                        }
                    },
                    error: function(xhr, status, error) {
                        var err = eval("(" + xhr.responseText + ")");
                        console.log(err.Message);
                    }
                });
            }
            
    
            function fetchdata(authorid, aqar_author_type_id, transId){
                var role = 'houzez_agent';
                xhr = $.ajax({
                        type: 'post',
                        url: im_ajax.ajaxurl,
                        dataType: 'json',
                        data:{
                            action: 'fetchdata',
                            authorid: authorid,
                            transId: transId
                        },
                        success: function(data){
                        // Perform operation on return value
                            if( data.success && data.status === 'REJECTED' ) {
                                
                                $('#time-model').hide(); // Stop the timer
                                clearInterval(downloadTimer);
                                clearTimeout(timer);
                                document.getElementById("timer").innerHTML = "0";
                                $('#register-error-time').hide();
                                $messages.empty().append('<div dir="rtl" class="alert alert-danger" role="alert"><i class="houzez-icon icon-check-circle-1 mr-1"></i>تم رفض الطلب من قبل المستخدم.</div>');
                                $('.rh_login_modal_messages').show();
                                
                                // setTimeout(function() {
                                // 	window.location.reload();  // Reload the page after a delay
                                //   }, 3000); // Delay of 2000 milliseconds (2 seconds)
                            }
                            else if( data.success && data.status === 'COMPLETED' ){

                                $('#time-model').fadeOut(1000, function() {
                                    $(this).hide();
                                });

                                clearInterval(downloadTimer);
                                clearTimeout(timer);
                                document.getElementById("timer").innerHTML = "0";
                                $('#register-error-time').hide();

                                screen_1.fadeOut( 200, function() {
                                    screen_1.hide();
                                });
                                screen_2.html(data.html);
                                
                                $("input[name=full_name]").val(data.arFullName);
                                $("input[name=first_name]").val(data.arFirst);
                                $("input[name=last_name]").val(data.arGrand);
                                $("input[name=role]").val(aqar_author_type_id);
                                $("input[name=id_number]").val(data.id);
    
                                
                                // updateTimer(false , 0);
                                clearInterval(downloadTimer);
                                clearTimeout(timer);
                                document.getElementById("timer").innerHTML = "0";
                            } else {
                                timer = setTimeout(function(){fetchdata(authorid, aqar_author_type_id, transId);}, 1000);
                            }
                        },
                        complete:function(data){
                            
                        }
                    });
            }	
    
              // Function to show timer expired message and stop fetchdata
              function showTimerExpiredMessage() {
                if (timerExpired) {
                  // Display your message here
                  $('#register-error').empty();
                  var $messages = $('#register-error-time'); 
                  $messages.empty().append('<div dir="rtl" class="alert alert-danger" role="alert"><i class="houzez-icon icon-check-circle-1 mr-1"></i>انتهت صلاحية الموقّت. حاول ثانية</div>');
                  $('.rh_login_modal_messages').show();
            
                  // Abort the ongoing AJAX request (fetchdata)
                  if (xhr && xhr.readyState !== 4) {
                    xhr.abort();
                  }
                }
              }
                
    });

    $(document)
    .on("focusin", ".user-info-form input", function(event){
        return activeEvent(event.target);
    })
    .on("focusout", ".user-info-form input", function(event){
        return activeEvent(event.target);
    });

    function activeEvent(element) {
        var active = "active";
      
        return $(element).toggleClass(active).siblings().toggleClass(active);
    };

     // Add event listener for change event on radio inputs
     $('.Radio-module_input').change(function() {
        // Remove 'Radio-module_checked' class from all labels
        $('.radioBtn').removeClass('Radio-module_checked');
        
        // Add 'Radio-module_checked' class to the selected label
        $(this).closest('label').addClass('Radio-module_checked');

        // Get the selected radio input value
        var selectedValue = $(this).val();
        
        // Check the selected value and update the label text accordingly
        if (selectedValue === '1') {
            $('#name-ipt').text('رقم الهوية الوطنية');
        } else if (selectedValue === '2') {
            $('#name-ipt').text(' رقم سجل المنشأة ');
        }
    });


    $(document).on('submit', 'form#im-register-form', function(event) {

        event.preventDefault();
        var currnt = $(this);
        houzez_register( currnt );

    });

    var houzez_register = function ( currnt ) {

        var $form = currnt;
        var $messages = $('#register-error');

        $.ajax({
            type: 'post',
            url: im_ajax.ajaxurl,
            dataType: 'json',
            data: $form.serialize(),
            beforeSend: function( ) {
                $('.sync__loader').show();
            },
            complete: function(){
                $('.sync__loader').hide();
            },
            success: function( response ) {
                if( response.success ) {
                    $messages.empty().append('<div class="alert alert-success" role="alert"><i class="houzez-icon icon-check-circle-1 mr-1"></i>'+ response.data.msg +'</div>');
                    $('.rh_login_modal_messages').show();
                    $('html, body').animate({
                        scrollTop: $("#register-error").offset().top - 100
                    }, 1000);
                } else {
                    $messages.empty().append('<div class="alert alert-danger" role="alert"><i class="houzez-icon icon-check-circle-1 mr-1"></i>'+ response.error +'</div>');
                    $('.rh_login_modal_messages').show();

                    $('html, body').animate({
                        scrollTop: $("#register-error").offset().top - 100
                    }, 1000);
                }

                $('.sync__loader').hide();

                // if(houzez_reCaptcha == 1) {
                // 	$form.find('.g-recaptcha-response').remove();
                // 	if( g_recaptha_version == 'v3' ) {
                // 		houzezReCaptchaLoad();
                // 	} else {
                // 		houzezReCaptchaReset();
                // 	}
                // }
                
            },
            error: function(xhr, status, error) {
                var err = eval("(" + xhr.responseText + ")");
                console.log(err.Message);
            }
        });
    }

}(jQuery));