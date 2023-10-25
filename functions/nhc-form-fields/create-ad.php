<div class="addPropert-wrap">
    <div class="addProperty-header">
        <img src="<?php echo trailingslashit( get_stylesheet_directory_uri() ) .'assets/img/add.png'; ?>" class=""
            loading="lazy" width="50" height="50">
        <h2>نشر وترخيص الإعلان</h2>
    </div>
    <div id="form2" class="tab-content">
        <form autocomplete="off" id="submit_createAd" name="new_post" method="post" action="#"
            enctype="multipart/form-data" class="create-frontend-property">
            <div class="row">
                <div class="col-md-12">
                    <div id="errors-messages_2" class="validate-errors alert alert-danger houzez-hidden" role="alert">
                        <strong id="messages"></strong>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div id="success-messages_2" class="validate-success alert alert-success houzez-hidden"
                        role="alert">
                        <strong id="messages"></strong>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
            </div>
            <div id="broker_2">
                <div class="row">
                    <div class="col-md-12">
                        <h4>عقد الوساطة</h4>
                    </div>
                    <div class="col-md-6 col-sm-12">
                        <div class="form-group mt-5">
                            <div class="d-flex justify-content-between">
                                <label>هل يوجد عقد وساطة؟</label>
                                <div class="form-control-wrap d-flex">
                                    <label class="control control--radio mr-3">
                                        <input type="radio" name="is-broker-contract"
                                            value="yes"><?php echo houzez_option('cl_yes', 'Yes '); ?>
                                        <span class="control__indicator"></span>
                                    </label>
                                    <label class="control control--radio">
                                        <input type="radio" name="is-broker-contract"
                                            value="no" checked><?php echo houzez_option('cl_no', 'No '); ?>
                                        <span class="control__indicator"></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="brokerage-Contract-Number" class="col-md-6" style="display: none;">
                        <div class="form-group">
                            <label for="brokerageContractNumber">
                                رقم عقد الوساطة العقارية
                            <span class="required-field">*</span> 
                            </label>
                            <input class="form-control" id="brokerageContractNumber" name="brokerageContractNumber"
                                value="" placeholder="" type="text" required>
                        </div>
                    </div>
                    <div id="create-brokerage-Contract-Number" class="col-md-12 col-sm-12" style="background: #fff;padding: 2rem;">
                        <?php echo apply_filters( 'the_content', get_option( '_add_propery_info' ) ); ?>
                    </div>
                </div>
            </div>
            <div id="all-info" class="aq_hide">
                <div class="row">
                    <div class="col-md-12">
                        <h4>بيانات المعلن</h4>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="theAdThrough"> نوع هوية المستخدم <span class="required-field">*</span> </label>
                            <select name="theAdThrough" id="theAdThrough" class="selectpicker labels-select-picker form-control" data-size="5" data-selected-text-format="count > 2" title="يرجى الاختيار" data-none-results-text="لا توجد أي نتائج مطابقة {0}" data-live-search="true" data-actions-box="true" data-select-all-text="أختر الكل" data-deselect-all-text="إلغاء الاختيار" data-count-selected-text="{0} أنوع الاستخدام" required>
                                <option value="OwnerOffice">مالك منشأة</option>
                                <option value="BrokerOffice">وسيط منشأة</option>
                                <option value="OwnerAgent">وكيل المالك </option>
                                <option value="OwnerIndividual">مالك فرد </option>
                                <option value="BrokerIndividual">وسيط فرد</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div id="broker" class="aq_hide" style="display: none;">
                    <div class="row">
                        <div class="col-md-12">
                            <h4>بيانات الوسيط</h4>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="userIdNumber">رقم هوية المستخدم</label>
                                <input class="form-control" id="userIdNumber" name="userIdNumber" value=""
                                    placeholder="" type="text">
                                <small class="form-text text-muted">في حالة الإعلان الذي تم إنشاؤه بواسطة الوسيط المؤسسة
                                    أو للمالك مؤسسة</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="fullName">اسم المستخدم</label>
                                <input class="form-control" id="fullName" name="fullName" value="" placeholder=""
                                    type="text">
                                <small class="form-text text-muted">في حالة الإعلان الذي تم إنشاؤه بواسطة الوسيط المؤسسة
                                    أو للمالك مؤسسة</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="agent" style="display: none;">
                    <div class="row">
                        <div class="col-md-12">
                            <h4>بيانات الوكالة</h4>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="attorneyCode">رقم الوكالة</label>
                                <input class="form-control" id="attorneyCode" name="attorneyCode" value=""
                                    placeholder="" type="text">
                                <small class="form-text text-muted">في حالة طلب ترخيص الإعلان من قبل ممثل المالك</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="attorneyFirstId">رقم هوية الموكل </label>
                                <input class="form-control" id="attorneyFirstId" name="attorneyFirstId" value=""
                                    placeholder="" type="text">
                                <small class="form-text text-muted">لا حاجة للتوكيل في حالة المالك الممثل هو نفس المالك
                                    الممثل الذي وافق على عقد الوساطة </small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="attorneySecondId">رقم هوية الوكيل</label>
                                <input class="form-control" id="attorneySecondId" name="attorneySecondId" value=""
                                    placeholder="" type="text">
                                <small class="form-text text-muted">لا حاجة للتوكيل في حالة المالك الممثل هو نفس المالك
                                    الممثل الذي وافق على عقد الوساطة </small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <h4>تفاصيل الاعلان</h4>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="propertyType">نوع العقار
                             <span class="required-field">*</span>
                            </label>
                            <?php echo propertyType_select(); ?>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="advertisementType">نوع الاعلان
                                <span class="required-field">*</span>
                            </label>
                            <?php echo advertisementType_select(); ?>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="propertyUsages">نوع الاستخدام
                            <span class="required-field">*</span>
                            </label>
                            <?php echo propertyUsages_select(); ?>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="adType">غرض الاعلان
                             <span class="required-field">*</span>
                            </label>
                            <select name="adType" id="adType" class="selectpicker labels-select-picker form-control">
                                <option value="sale">للبيع</option>
                                <option value="rent">للايجار</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="propertyPrice">السعر
                            <span class="required-field">*</span>
                            </label>
                            <input type="number" class="form-control" name="propertyPrice" id="propertyPrice" value=""
                                placeholder="" required>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div id="propertyFields" class="row">
                            <div class="col-md-4 aq-field">
                                <div class="form-group">
                                    <label for="streetWidth">عرض الشارع</label>
                                    <input type="text" class="form-control" name="streetWidth" id="streetWidth" value=""
                                        placeholder="">
                                </div>
                            </div>
                            <div class="col-md-4 aq-field">
                                <div class="form-group">
                                    <label for="propertyArea">مساحة العقار</label>
                                    <input type="text" class="form-control" name="propertyArea" id="propertyArea"
                                        value="" placeholder="">
                                </div>
                            </div>
                            <div class="col-md-4 aq-field">
                                <div class="form-group">
                                    <label for="numberOfRooms">عدد الغرف</label>
                                    <input type="text" class="form-control" name="numberOfRooms" id="numberOfRooms"
                                        value="" placeholder="">
                                </div>
                            </div>
                            <div class="col-md-4 aq-field">
                                <div class="form-group">
                                    <label for="propertyAge">عمر العقار</label>
                                    <select name="propertyAge" id="propertyAge" data-size="5"
                                        class="selectpicker labels-select-picker form-control"
                                        data-selected-text-format="count > 2" title="يرجى الاختيار"
                                        data-none-results-text="لا توجد أي نتائج مطابقة {0}" data-live-search="true"
                                        data-actions-box="true" data-select-all-text="أختر الكل"
                                        data-deselect-all-text="إلغاء الاختيار"
                                        data-count-selected-text="{0} أنوع الاستخدام">
                                        <option value="New">جديد</option>
                                        <option value="LessThanOneYear">اقل من سنة</option>
                                        <option value="Year">سنة</option>
                                        <option value="TwoYears">سنتين</option>
                                        <option value="ThreeYears">ثلاث سنوات</option>
                                        <option value="FourYears">اربع سنوات</option>
                                        <option value="FiveYears">خمس سنوات</option>
                                        <option value="SixYears">ست سنوات</option>
                                        <option value="SevenYears">سبع سنوات</option>
                                        <option value="EightYears">ثمان سنوات</option>
                                        <option value="NineYears">تسع سنوات</option>
                                        <option value="Decade">عشر سنوات</option>
                                        <option value="MoreThanTenYears">اكثر من عشر سنوات</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4 aq-field">
                                <div class="form-group">
                                    <label for="propertyFace">واجهة العقار</label>
                                    <select name="propertyFace" id="propertyFace" data-size="5"
                                        class="selectpicker labels-select-picker form-control"
                                        data-selected-text-format="count > 2" title="يرجى الاختيار"
                                        data-none-results-text="لا توجد أي نتائج مطابقة {0}" data-live-search="true"
                                        data-actions-box="true" data-select-all-text="أختر الكل"
                                        data-deselect-all-text="إلغاء الاختيار"
                                        data-count-selected-text="{0} أنوع الاستخدام">
                                        <option value="Eastern">شرقية</option>
                                        <option value="Western">غربية</option>
                                        <option value="North">شمالية</option>
                                        <option value="Southern">جنوبية</option>
                                        <option value="NorthEast">شمالية شرقية</option>
                                        <option value="SouthEast">جنوبية شرقية</option>
                                        <option value="SouthWestern">جنوبية غربية</option>
                                        <option value="NorthWest">شمالية غربية</option>
                                        <option value="ThreeStreets">ثلاثة شوارع</option>
                                        <option value="FourStreets">اربعة شوارع</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4 aq-field">
                                <div class="form-group">
                                    <label for="planNumber">رقم المخطط</label>
                                    <input type="text" class="form-control" name="planNumber" id="planNumber" value=""
                                        placeholder="">
                                </div>
                            </div>
                            <div class="col-md-4 aq-field">
                                <div class="form-group">
                                    <label for="theBordersAndLengthsOfTheProperty">حدود واطوال العقار</label>
                                    <input type="text" class="form-control" name="theBordersAndLengthsOfTheProperty"
                                        id="theBordersAndLengthsOfTheProperty" value="" placeholder="">
                                </div>
                            </div>
                            <div class="col-md-4 aq-field">
                                <div class="form-group">
                                    <label for="propertyUtilities">خدمات العقار</label>
                                    <select name="propertyUtilities[]" id="propertyUtilities"
                                        class="selectpicker labels-select-picker form-control"
                                        data-selected-text-format="count > 2" title="يرجى الاختيار"
                                        data-none-results-text="لا توجد أي نتائج مطابقة {0}" data-live-search="true"
                                        data-actions-box="true" data-select-all-text="أختر الكل"
                                        data-deselect-all-text="إلغاء الاختيار"
                                        data-count-selected-text="{0} خدمات العقار" multiple>
                                        <option value="Electricity">كهرباء</option>
                                        <option value="Waters">مياه</option>
                                        <option value="Sanitation">صرف صحي </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6 aq-field">
                                <div class="form-group">
                                    <label for="obligationsOnTheProperty">هل يوجد حقوق والتزامات؟</label>
                                    <input id="obligationsOnTheProperty" name="obligationsOnTheProperty" type="text"
                                        class="form-control" value=""
                                        placeholder="في حال عدم وجود اي قيود يرجى كتابة : لا يوجد">
                                </div>
                            </div>
                            <div class="col-md-6 aq-field">
                                <div class="form-group">
                                    <label for="guaranteesAndTheirDuration"> الضمانات ومدتها</label>
                                    <input id="guaranteesAndTheirDuration" name="guaranteesAndTheirDuration" type="text"
                                        class="form-control" value=""
                                        placeholder="في حال عدم وجود اي قيود يرجى كتابة : لا يوجد">
                                </div>
                            </div>
                            <div class="col-md-6 aq-field">
                                <div class="form-group mt-4">
                                    <div id="complianceWithTheSaudiBuildingCode" class="d-flex justify-content-between">
                                        <label>مطابقة كود البناء السعودي</label>
                                        <div class="form-control-wrap d-flex">
                                            <label class="control control--radio mr-3">
                                                <input type="radio" name="complianceWithTheSaudiBuildingCode"
                                                    value="yes"><?php echo houzez_option('cl_yes', 'Yes '); ?>
                                                <span class="control__indicator"></span>
                                            </label>
                                            <label class="control control--radio">
                                                <input type="radio" name="complianceWithTheSaudiBuildingCode"
                                                    value="no"><?php echo houzez_option('cl_no', 'No '); ?>
                                                <span class="control__indicator"></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <h4>الموقع</h4>
                    </div>
                    <?php include( AG_DIR . 'module/location.php'); ?>
                </div>
                <div class="row">

                    <div class="col-md-12">
                        <?php include( AG_DIR . 'module/nhc-form-fields/media.php'); ?>
                    </div>
                </div>
                <div class="d-flex justify-content-between add-new-listing-bottom-nav-wrap">
                    <a href="<?php echo $cancel_link; ?>" class="btn-cancel btn btn-primary-outlined">
                        إلغاء </a>
                    <button id="CreateADLicense" type="submit" class="btn houzez-submit-js btn-primary houzez-hidden">
                        <span class="btn-loader houzez-loader-js"></span>إنشاء ترخيص إعلان
                    </button>
                </div>
            </div>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
    <script>
    jQuery(document).ready(function($) {
        $('#theAdThrough').on('change', function() {
            var selectedDiv = $(this).val();
            if (selectedDiv === 'OwnerAgent') {
                $('#agent').fadeIn(500);
            } else {
                $('#agent').fadeOut(500);
            }

        });

        $("input[name=is-broker-contract]").change(function() {
            if ($(this).is(":checked")) {
                if ($(this).val() === "yes") {
                    $('#create-brokerage-Contract-Number').hide();
                    $('#brokerage-Contract-Number').fadeIn(1000);
                } else {
                    $('#brokerage-Contract-Number').hide();
                    $("#all-info").hide();
                    $('#create-brokerage-Contract-Number').fadeIn(1000);
                }
            }
        });

        $("#brokerageContractNumber").on("input", function() {
            if ($(this).val() !== "") {
                $("#all-info").fadeIn(1000);
            } else {
                $("#all-info").fadeOut(1000);
            }
        });

        // $('#CreateADLicense').on('click', function(e) {
        //     e.preventDefault();
        //     var currnt = $(this);
        //     CreateADLicense(currnt);
        // });

        $('#form2').validate({
            rules: {},
            messages: {},
            highlight: function ( element, errorClass, validClass ) {
                $( element ).addClass( "is-invalid" ).removeClass( "is-valid" );
                $( element ).parent('.bootstrap-select').addClass( "is-invalid" ).removeClass( "is-valid" );
                $( element ).parent('.control--checkbox').find('.control__indicator').addClass( "is-invalid" ).removeClass( "is-valid" );
            },
            unhighlight: function (element, errorClass, validClass) {
                $( element ).removeClass( "is-invalid" );
                $( element ).parent('.bootstrap-select').removeClass( "is-invalid" );
                $( element ).parent('.control--checkbox').find('.control__indicator').removeClass( "is-invalid" );
            },
            submitHandler: function(form) {
                // Form is valid, proceed with form submission
                var currnt = $('#CreateADLicense');
                CreateADLicense(currnt);
            }
        });

        var CreateADLicense = function(currnt) {
            var errors_messages = $('#errors-messages_2');
            var success_messages = $('#success-messages_2');
            var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";
            var formData = new FormData(document.getElementById('submit_createAd'));
            formData.append("action", "CreateADLicense");

            $.ajax({
                type: 'post',
                url: ajaxurl,
                dataType: 'json',
                cache: false,
                processData: false,
                contentType: false,
                data: formData,
                beforeSend: function() {
                    currnt.find('.houzez-loader-js').addClass('loader-show');
                    errors_messages.addClass('houzez-hidden');
                    success_messages.addClass('houzez-hidden');
                },
                complete: function() {
                    currnt.find('.houzez-loader-js').removeClass('loader-show');
                },
                success: function(response) {
                    if (response.success) {
                        success_messages.removeClass('houzez-hidden');
                        success_messages.find('#messages').empty().append(response.reason);
                        $('html, body').animate({
                            scrollTop: success_messages.offset().top - 20
                        }, 500);
                        window.setTimeout(function() {
                            window.location = response.redir;
                        }, 2000);
                    } else {
                        errors_messages.removeClass('houzez-hidden');
                        errors_messages.find('#messages').empty().append(response.reason);
                        $('html, body').animate({
                            scrollTop: errors_messages.offset().top - 20
                        }, 1000);

                    }
                    currnt.find('.houzez-loader-js').removeClass('loader-show');
                },
                error: function(xhr, status, error) {
                    var err = eval("(" + xhr.responseText + ")");
                    console.log(err.Message);
                }
            });

        } // get_ad_info

        var fieldsData = {
            All : ['streetWidth', 'propertyArea', 'numberOfRooms', 'propertyAge', 'propertyFace',
                'planNumber', 'theBordersAndLengthsOfTheProperty', 'propertyUtilities',
                'obligationsOnTheProperty', 'guaranteesAndTheirDuration',
                'complianceWithTheSaudiBuildingCode'
            ],
            Land: ['streetWidth', 'propertyArea', 'propertyFace', 'planNumber',
                'theBordersAndLengthsOfTheProperty', 'propertyUtilities', 'obligationsOnTheProperty'
            ],
            Role: ['propertyArea', 'numberOfRooms', 'propertyAge',
                'planNumber', 'theBordersAndLengthsOfTheProperty', 'propertyUtilities',
                'obligationsOnTheProperty', 'guaranteesAndTheirDuration',
                'complianceWithTheSaudiBuildingCode'
            ],
            Apartment: ['propertyArea', 'numberOfRooms', 'propertyAge','propertyFace',
                'planNumber', 'theBordersAndLengthsOfTheProperty', 'propertyUtilities',
                'obligationsOnTheProperty', 'guaranteesAndTheirDuration',
                'complianceWithTheSaudiBuildingCode'
            ],
            villa: ['streetWidth', 'propertyArea', 'numberOfRooms', 'propertyAge', 'propertyFace',
                'planNumber', 'theBordersAndLengthsOfTheProperty', 'propertyUtilities',
                'obligationsOnTheProperty', 'guaranteesAndTheirDuration',
                'complianceWithTheSaudiBuildingCode'
            ],
            SmallApartment: ['propertyArea', 'numberOfRooms', 'propertyAge',
                'planNumber', 'theBordersAndLengthsOfTheProperty', 'propertyUtilities',
                'obligationsOnTheProperty', 'guaranteesAndTheirDuration',
                'complianceWithTheSaudiBuildingCode'
            ],
            Bedrooms: ['propertyArea', 'propertyAge',
                 'theBordersAndLengthsOfTheProperty', 'propertyUtilities',
                'obligationsOnTheProperty', 'guaranteesAndTheirDuration',
                'complianceWithTheSaudiBuildingCode'
            ],
            Break: ['streetWidth', 'propertyArea', 'numberOfRooms', 'propertyAge', 'propertyFace',
                'planNumber', 'theBordersAndLengthsOfTheProperty', 'propertyUtilities',
                'obligationsOnTheProperty', 'guaranteesAndTheirDuration',
                'complianceWithTheSaudiBuildingCode'
            ],
            Complex: ['streetWidth', 'propertyArea', 'propertyAge', 'propertyFace',
                'planNumber', 'theBordersAndLengthsOfTheProperty', 'propertyUtilities',
                'obligationsOnTheProperty', 'guaranteesAndTheirDuration',
                'complianceWithTheSaudiBuildingCode'
            ],
            Tower: ['streetWidth', 'propertyArea', 'propertyAge', 'propertyFace',
                'planNumber', 'theBordersAndLengthsOfTheProperty', 'propertyUtilities',
                'obligationsOnTheProperty', 'guaranteesAndTheirDuration',
                'complianceWithTheSaudiBuildingCode'
            ],
            Exhibition: ['streetWidth', 'propertyArea', 'propertyAge', 'propertyFace',
                'planNumber', 'theBordersAndLengthsOfTheProperty', 'propertyUtilities',
                'obligationsOnTheProperty', 'guaranteesAndTheirDuration',
                'complianceWithTheSaudiBuildingCode'
            ],
            Office: ['streetWidth', 'propertyArea', 'numberOfRooms', 'propertyAge',
                'planNumber', 'theBordersAndLengthsOfTheProperty', 'propertyUtilities',
                'obligationsOnTheProperty',
            ],
            Storehouse: ['streetWidth', 'propertyArea', 'numberOfRooms', 'propertyAge',
                'planNumber', 'theBordersAndLengthsOfTheProperty', 'propertyUtilities',
                'obligationsOnTheProperty', 'guaranteesAndTheirDuration',
                'complianceWithTheSaudiBuildingCode'
            ],
            Booth: ['propertyArea', 'numberOfRooms', 'propertyAge',
                'theBordersAndLengthsOfTheProperty', 'propertyUtilities',
                'obligationsOnTheProperty'
            ],
            Cinema: ['streetWidth', 'propertyArea', 'propertyAge',
                'planNumber', 'theBordersAndLengthsOfTheProperty', 'propertyUtilities',
                'obligationsOnTheProperty', 'guaranteesAndTheirDuration',
                'complianceWithTheSaudiBuildingCode'
            ],
            Hotel: ['streetWidth', 'propertyArea', 'numberOfRooms', 'propertyAge', 'propertyFace',
                'planNumber', 'theBordersAndLengthsOfTheProperty', 'propertyUtilities',
                'obligationsOnTheProperty', 'guaranteesAndTheirDuration',
                'complianceWithTheSaudiBuildingCode'
            ],
            CarParking: ['propertyArea', 'numberOfRooms', 'propertyAge',
                'planNumber', 'theBordersAndLengthsOfTheProperty', 'propertyUtilities',
                'obligationsOnTheProperty', 'guaranteesAndTheirDuration',
                'complianceWithTheSaudiBuildingCode'
            ],
            Workshop: ['streetWidth', 'propertyArea', 'propertyAge', 'propertyFace',
                'planNumber', 'theBordersAndLengthsOfTheProperty', 'propertyUtilities',
                'obligationsOnTheProperty', 'guaranteesAndTheirDuration',
                'complianceWithTheSaudiBuildingCode'
            ],
            ATM: ['streetWidth', 'propertyArea', 'propertyAge',
                'planNumber', 'theBordersAndLengthsOfTheProperty', 'propertyUtilities',
                'obligationsOnTheProperty', 'guaranteesAndTheirDuration'
            ],
            Factory: ['streetWidth', 'propertyArea', 'propertyAge',
                'planNumber', 'theBordersAndLengthsOfTheProperty', 'propertyUtilities',
                'obligationsOnTheProperty', 'guaranteesAndTheirDuration',
                'complianceWithTheSaudiBuildingCode'
            ],
            School: ['streetWidth', 'propertyArea', 'numberOfRooms', 'propertyAge', 'propertyFace',
                'planNumber', 'theBordersAndLengthsOfTheProperty', 'propertyUtilities',
                'obligationsOnTheProperty', 'guaranteesAndTheirDuration',
                'complianceWithTheSaudiBuildingCode'
            ],
            HospitalHealthCenter: ['streetWidth', 'propertyArea', 'numberOfRooms', 'propertyAge',
                'propertyFace', 'planNumber', 'theBordersAndLengthsOfTheProperty', 'propertyUtilities',
                'obligationsOnTheProperty', 'guaranteesAndTheirDuration',
                'complianceWithTheSaudiBuildingCode'
            ],
            ElectricityStation: ['streetWidth', 'propertyArea',
                'propertyFace', 'planNumber', 'theBordersAndLengthsOfTheProperty', 'propertyUtilities',
                'obligationsOnTheProperty', 'guaranteesAndTheirDuration'
            ],
            TelecomTower: ['streetWidth', 'propertyArea',
                'propertyFace', 'planNumber', 'propertyUtilities',
                'obligationsOnTheProperty', 'guaranteesAndTheirDuration'
            ],
            Station: ['streetWidth', 'propertyArea', 'propertyAge', 'propertyFace',
                'planNumber', 'theBordersAndLengthsOfTheProperty', 'propertyUtilities',
                'obligationsOnTheProperty', 'guaranteesAndTheirDuration',
                
            ],
            Farm: ['streetWidth', 'propertyArea', 'numberOfRooms', 'propertyAge', 'propertyFace',
                'planNumber', 'theBordersAndLengthsOfTheProperty', 'propertyUtilities',
                'obligationsOnTheProperty', 'guaranteesAndTheirDuration'
            ],
            Architecture: ['streetWidth', 'propertyArea', 'numberOfRooms', 'propertyAge', 'propertyFace',
                'planNumber', 'theBordersAndLengthsOfTheProperty', 'propertyUtilities',
                'obligationsOnTheProperty', 'guaranteesAndTheirDuration'
            ],
        };

        $('#propertyType').change(function() {
            var selectedOption = $(this).val();
            var selectedFields = fieldsData[selectedOption];
            var allInput       = fieldsData['All'];

            if (allInput && allInput.length) {
                for (var i = 0; i < allInput.length; i++) {
                    $('#' + allInput[i]).val("");
                    $('#' + allInput[i]).removeAttr('required');
                    $('label[for="' + allInput[i] + '"] .required-field').remove();
                }
            }
            // Hide all fields initially
            $('#propertyFields > div').hide();
            

            // Show selected fields
            if (selectedFields && selectedFields.length) {
                for (var i = 0; i < selectedFields.length; i++) {
                    $('#' + selectedFields[i]).val("");
                    $('#' + selectedFields[i]).closest('.aq-field').fadeIn(500);
                    $('#' + selectedFields[i]).prop('required',true);
                    $('label[for="' + selectedFields[i] + '"]').append('<span class="required-field">*</span>');
                }
            }

            // Show property fields container if there are selected fields
            $('#propertyFields').toggle(selectedFields && selectedFields.length > 0);

        });

    });
    </script>
</div>