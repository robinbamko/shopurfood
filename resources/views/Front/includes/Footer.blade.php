<style>
.newsletter-inner {
    display: inline-block; width: 100%;
}
.newsletter-email {
    float: left;
    font-size: 13px;
    font-weight: 300;
    border: 0;
    background: inherit;
    color: #fff;
    padding: 10px 12px;
    width: 71%;
    border-radius: 3px 0px 0px 3px;
    border: 1px #666 solid;
}
.subscribe {
    text-transform: uppercase;
    font-size: 13px;
    font-weight: 500;
    float: left;
    color: #fff;
    text-decoration: none;
    background: inherit;
    padding: 10px 25px;
    border: 1px #d93529 solid;
    border-radius: 3px 3px 3px 0px;
    margin-left: -1px;
    background: #d93529;
    letter-spacing: 1px;
}

@media only screen and (min-width: 992px) and (max-width: 1199px)
{
    .newsletter-email{width: 65%;}
}
@media only screen and (min-width: 768px) and (max-width: 991px)
{
    .subscribe{padding: 10px 8px;}
}
@media only screen and (min-width: 576px) and (max-width: 767px)
{
    .newsletter-email{width: 74%;}
}

</style>


<div class="footer">
    <div class="container">
        <div class="row">
            <div class="col-md-12 col-lg-12 footer1">
                <div class="row">
                    <div class="col-md-6 col-lg-6">
                        {{-- <div class="footer-logo">

                            @if(count($logo_settings_details) > 0)
                                @php
                                    foreach($logo_settings_details as $logo_set_val){ }
                                    $filename = public_path('images/logo/').$logo_set_val->favicon;
                                @endphp
                                @if(file_exists($filename))
                                    <a href="#"><img src="{{url('public/images/logo/'.$logo_set_val->favicon)}}" alt="Logo" class="img-responsive logo"></a>
                                @else
                                    <a href="#"><img src="{{url('public/images/noimage/'.$no_logo)}}" alt="Logo" class="img-responsive logo" width="50px"></a>
                                @endif
                            @else
                                <a href="#"><img src="{{url('public/images/noimage/'.$no_logo)}}" alt="Logo" class="img-responsive logo" width="50px"></a>
                        @endif
                        <!-- <img src="{{url('')}}/public/front/images/footer-logo.png"> -->
                        </div> --}}
                        <div class="footer-content">
                            @if($FOOTERDES != '')
                                <p>{{ $FOOTERDES }}</p>
                            @endif
                            <ul>
                                @if($SITEFACEBOOK!="")
                                    <li><a href="{{ $SITEFACEBOOK }}"><img src="{{url('')}}/public/front/images/facebook.png"></a></li>
                                @endif
                                @if($SITETWITTER!="")
                                    <li><a href="{{ $SITETWITTER }}"><img src="{{url('')}}/public/front/images/twitter.png"></a></li>
                                @endif
                                @if($SITEGOOGLE)
                                    <li><a href="{{ $SITEGOOGLE}}"><img src="{{url('')}}/public/front/images/google-plus.png"></a></li>
                                @endif
                                @if($SITELINKEDIN)
                                    <li><a href="{{ $SITELINKEDIN}}"><img src="{{url('')}}/public/front/images/linkedin.png"></a></li>
                                {{--<li><a href="{{ $SITELINKEDIN }}" target="new"><i class="fa fa-instagram tooltip-demo" aria-hidden="true" title="Linked In"></i></a></li>--}}
                            @endif



<!-- <li><a href=""><img src="{{url('')}}/public/front/images/pinterest.png"></a></li> -->
                            </ul>
                        </div>
						<!-- STARTING LANGUAGE CHANGE SECTION -->
						 @if(count($Active_language) > 0 )
                            @foreach($Active_language as $default)
                                @php $lang = $default->lang_code;  @endphp
                            @endforeach
                        @endif
                        
                        @if(count($Active_language) > 1 && $lang !='en')
                            <div class="language " id="language" style="width:75%" >
                                <select class="form-control" name="lang_select" id="lang_select" onchange="set_lang_code(this)">
                                    {{--<option>Select Language</option>--}}
                                    @foreach($Active_language as $lang)
                                        <option value="{{ $lang->lang_code }}"
                                        <?php if(Session::get('front_lang_code') == $lang->lang_code)
                                        { echo 'selected="selected"'; }else{    } ?> >
                                            {{ $lang->lang_name }}</option>

                                    @endforeach
                                </select>
                            </div>
                        @endif
						<!-- STARTING LANGUAGE CHANGE SECTION -->
						
                    </div>

                    <div class="col-md-6 col-lg-6 col-sm-12 footer-menu">
                        <!-- <h3>Information</h3> -->
                        <ul>
                            <li>
                                <a href="{{ url('/') }}">{{ (Lang::has(Session::get('front_lang_file').'.HOME')!= '') ?  trans(Session::get('front_lang_file').'.HOME'): trans($FRONT_LANGUAGE.'.HOME') }}</a>
                            </li>
                            <li>
                                <a href="{{ url('merchant_signup') }}">{{ (Lang::has(Session::get('front_lang_file').'.FRONT_MER_SIGNUP')!= '') ?  trans(Session::get('front_lang_file').'.FRONT_MER_SIGNUP'): trans($FRONT_LANGUAGE.'.FRONT_MER_SIGNUP') }}</a>
                                <a href="{{url('merchant-login')}}"> / {{ (Lang::has(Session::get('front_lang_file').'.FRONT_MER_LOGIN')!= '') ?  trans(Session::get('front_lang_file').'.FRONT_MER_LOGIN'): trans($FRONT_LANGUAGE.'.FRONT_MER_LOGIN') }}</a>
                            </li>
                            <li><a href="{{ url('faq') }}">{{ (Lang::has(Session::get('front_lang_file').'.FRONT_FAQ')!= '') ?  trans(Session::get('front_lang_file').'.FRONT_FAQ'): trans($FRONT_LANGUAGE.'.FRONT_FAQ') }}</a></li>
                            <li>
                                <a href="{{ url('delivery-person-signup') }}">{{ (Lang::has(Session::get('front_lang_file').'.FRONT_DEL_BOY_SIGNUP')!= '') ?  trans(Session::get('front_lang_file').'.FRONT_DEL_BOY_SIGNUP'): trans($FRONT_LANGUAGE.'.FRONT_DEL_BOY_SIGNUP') }}</a>
                                
                            </li>
                            @if((Session::get('front_lang_code'))== '' || (Session::get('front_lang_code'))== 'en')

                                @php $i = 1 ; @endphp
                                @if($i <=4 )
                                    @foreach($cms_details as $cms)
                                        @if($cms->page_title_en != '')
                                        <li><a href="{{url('cms/').'/'. $cms->id}}"> {{$cms->page_title_en}}</a></li>
                                        @endif
                                        @php $i++; @endphp
                                    @endforeach
                                @endif
                            @else
                                @php $cp_title = 'page_title_'.Session::get('front_lang_code'); @endphp
                                @php $i = 1 ; @endphp
                                @if($i <=4 )
                                    @foreach($cms_details as $cms)
                                    @if($cms->$cp_title != '' || $cms->$cp_title != null)
                                        <li><a href="{{url('cms/').'/'. $cms->id}}"> {{$cms->$cp_title}}</a></li>
                                        @endif
                                        @php $i++; @endphp

                                    @endforeach
                                @endif

                            @endif

						<li><a href="{{ url('contact-us') }}">{{ (Lang::has(Session::get('front_lang_file').'.FRONT_CONTACTUS')!= '') ?  trans(Session::get('front_lang_file').'.FRONT_CONTACTUS'): trans($FRONT_LANGUAGE.'.FRONT_CONTACTUS') }}</a></li>

                        <!-- <li><a href="">Press Releases</a></li>
								<li><a href="">Shop with Points</a></li>
								<li><a href="">More Branches</a></li> -->
                        </ul>
                    </div>

                   



                </div>

            </div>
					
					
        </div>
    </div>


<div class="container">

    <div class="row footer2">
		<div class="col-md-12 col-lg-12 ">
			<p>©<span>{{ $SITEFOOTERTEXT }}</span>. @lang(Session::get('front_lang_file').'.FRONT_ALL_RIGHTS')</p>
		</div>
		<!-- <div class="col-md-6 col-lg-5">   
			<p><span>@lang(Session::get('front_lang_file').'.JOIN_NEWSLETTER')</span></p>
			<div class="newsletter-inner">
				<input class="newsletter-email" id="sub_email" type="email" required="" name="email" placeholder=" @lang(Session::get('front_lang_file').'.ENTER_EMAIL_NEWSLETTER')">
				<button class="button subscribe" id="subscribe_submit" title="Subscribe"> @lang(Session::get('front_lang_file').'.FRONT_SUBSCRIBE') </button>
				<div class="mail-loader" style="display:none"> <img src="<?php echo url('')?>/public/images/loader.gif"></div>
			</div>
		</div> -->
    </div>
</div>

</div>

<!-- MODAL-->
<!-- Central Modal Large Info-->

@php
    $rest = search_restaurantdet_sample();@endphp
<?php /*
<div class="modal fade" id="centralModalLGInfoDemo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-lg modal-notify modal-info" role="document">
        <!--Content-->
        <div class="modal-content">
            <!--Header-->
            <div class="modal-header">
                <p class="heading lead">@lang(Session::get('front_lang_file').'.FRONT_SELECT_LOCATION')
                <!-- <span class="help-block">@lang(Session::get('front_lang_file').'.FRONT_ENTER_YOUR_LOCATION_HELP_TEXT')</span> -->
                </p>

                @if(Session::has('search_location') == 1)
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" class="white-text">&times;</span>
                    </button>
                @endif
            </div>

            <!--Body-->
            <div class="modal-body">

                <div class="text-center">
					<span class="help-block"><a href="javascript:set_my_currentLocation();"><i class="fa fa-map-marker" aria-hidden="true"></i>Use my current location</a></span>
                    <input class="form-control valid" id="footer_us3-address" required="" name="cus_address" type="text" value="{{(Session::has('search_location') == 1) ? Session::get('search_location') : 'Enter a location'}}" placeholder="Enter a location" autocomplete="off" aria-invalid="false">
					
                    <input class="form-control valid" id="footer_us3-lat" required="" readonly="" name="cus_lat" type="hidden" value="{{(Session::has('search_latitude') == 1) ? Session::get('search_latitude') : ''}}" aria-invalid="false">
                    <input class="form-control valid" id="footer_us3-lon" required="" readonly="" name="cus_long" type="hidden" value="{{(Session::has('search_longitude') == 1) ? Session::get('search_longitude') : ''}}" aria-invalid="false">
                </div>
				
                <div id="footer_us3" style="width: 100%; height: 400px;"></div>
            </div>

            <!--Footer-->

            <div class="modal-footer">
                <!--<a type="button" class="btn btn-info">Get it now
                    <i class="fa fa-diamond ml-1"></i>
                </a>-->
                @if(Session::has('search_location') == 1)
                    <button type="button" class="btn btn-info" data-dismiss="modal">@lang(Session::get('front_lang_file').'.FRONT_CLOSE')</button>
                @endif
            </div>

        </div>
        <!--/.Content-->
    </div>
</div> */ ?>
<!-- Central Modal Large Info-->
<!-- EOF MODAL -->
<style>
    .pac-container {
        z-index: 10000 !important;
    }
</style>

<!--<style>
	/* Always set the map height explicitly to define the size of the div
	* element that contains the map. */
	#map {
	height: 100%;
	}
	/* Optional: Makes the sample page fill the window. */


	#infowindow-content .title {
	font-weight: bold;
	}

	#infowindow-content {
	display: none;
	}

	#map #infowindow-content {
	display: inline;
	}

	.pac-card {
	margin: 10px 10px 0 0;
	border-radius: 2px 0 0 2px;
	box-sizing: border-box;
	-moz-box-sizing: border-box;
	outline: none;
	box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
	background-color: #fff;
	font-family: Roboto;
	}

	#pac-container {
	padding-bottom: 12px;
	margin-right: 12px;
	}

	.pac-controls {
	display: inline-block;
	padding: 5px 11px;
	}

	.pac-controls label {
	font-family: Roboto;
	font-size: 13px;
	font-weight: 300;
	}

	#pac-input {
	background-color: #fff;
	font-family: Roboto;
	font-size: 15px;
	font-weight: 300;
	margin-left: 12px;
	padding: 0 11px 0 13px;
	text-overflow: ellipsis;
	width: 400px;
	}

	#pac-input:focus {
	border-color: #4d90fe;
	}

	#title {
	color: #fff;
	background-color: #4d90fe;
	font-size: 25px;
	font-weight: 500;
	padding: 6px 12px;
	}
	#target {
	width: 345px;
	}
</style>-->

{!!$ANALYTICS_CODE!!}
<script src="{{url('')}}/public/front/js/jquery-2.2.0.min.js"></script>
<script src="{{url('')}}/public/front/js/bootstrap.min.js"></script>


<script src="{{url('')}}/public/front/js/slick.js"></script>
<script src="{{url('')}}/public/admin/assets/scripts/jquery.validate.js"></script>
<script src="{{url('')}}/public/admin/assets/scripts/jquery.dataTables.min.js"></script>
<script src="{{url('')}}/public/admin/assets/scripts/dataTables.bootstrap.min.js"></script>
<script src="{{url('')}}/public/admin/assets/scripts/dataTables.responsive.js"></script>
<script>

    /* ----------Select language start ----------*/
    function set_lang_code(e)
    {
        var lang_code = $('#lang_select').val();
        $.ajax({
            type : "POST",
            url:'{{ url("change_language")}}',
            data: {lang_code : lang_code},
            success : function(response)
            {
                if(response != 'fail')
                {
                    location.reload();
                }
                else
                {
                    alert('No response from Server');
                    return false;
                }
            }
        });
    }
   /* ----------Select language end ----------*/

    $(document).ready(function () {
        /*$(".header-input").click(function(e){
            e.preventDefault();
            e.stopPropagation();
            $(".address-btn").toggle();
            });
            $('body').click( function() {
            $('.address-btn').hide();
            });
        $('[data-toggle="tooltip"]').tooltip(); */
        $(".location-content").click(function () {
            $(".address-btn").toggle();
        });
		$('.tooltip-demo').tooltip({placement:'bottom'})



    });

		$(document).on('click touch', function (event) {
            if (!$(event.target).parents().addBack().is('.location-content')) {
                $('.address-btn').hide();
            }
        });
</script>
<script>

    $(document).ready(function () {
        $('.head-dropdown p').click(function (e) {
            e.preventDefault();
            e.stopPropagation();
            $('.head-dropdown-menu').toggle();

        });

        $('body').click(function () {
            $('.head-dropdown-menu').hide();
        });
    });
</script>


<script src="<?php echo URL::to('/'); ?>/public/js/intlTelInput.js"></script>
<script type="text/javascript">
    $("#cus_phone").intlTelInput({onlyCountries: ["{{$default_country_code}}"]});

</script>

@if($HIPPO_STATUS == 1)
    <!--<script src= "https://chat.fuguchat.com/js/widget.js"></script> -->
    <script src="{{url('').'/public/front/js/wid.js'}}"></script>
    @if(Session::has('customer_id') == 1)
        @php
            $cus_details = DB::table('gr_customer')->where('cus_id','=',Session::get('customer_id'))->first();
        @endphp
        <script>
            window.fuguUpdate({
                appSecretKey: "{{ $HIPPO_SECRET_KET }}",
                uniqueId: "{{ Session::get('customer_id') }}",
                email: "{{ $cus_details->cus_email }}",
                name: "{{ $cus_details->cus_fname }}",
                phone: "{{ $cus_details->cus_phone1 }}"
            });
        </script>
    @else
        <script>
            window.fuguInit({
                appSecretKey: "{{ $HIPPO_SECRET_KET }}"
            });
        </script>
    @endif
@endif

<script type="text/javascript">
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('.modal-toggle').click(function (e) {
        var tab = e.target.hash;
        if (tab == "#login") {
            $('li.btnOnPopup_login > a').addClass("show active");
            $(tab).addClass("active show");
            $('#signup').removeClass("active show");
            $('li.btnOnPopup_signup > a').removeClass("active show");
        }
        else if (tab == "#signup") {

            document.getElementById("headerSignupForm").reset();
            var element = document.getElementById('cus_phone');
            if (element.value == '') {
                $('#cus_phone').val('{{$default_dial}}');
            }
            $('#signup_error').hide();
            $('.login-btn').show();


            $('li.btnOnPopup_signup > a').addClass("show active");
            //$('li.btnOnPopup > a[href="#' +tab + '"]').addClass("show active");
            $(tab).addClass("active show");
            $('#login').removeClass("active show");
            $('li.btnOnPopup_login > a').removeClass("active show");
        }
    });
</script>
<script type="text/javascript">
    $(document).ready(function () {
        $(".signup").on("click", function () {
            $(".modal-body").addClass('signupActive');
            $(".modal-body").removeClass('loginActive');
        });
        $("a.login").on("click", function () {
            $(".modal-body").addClass('loginActive');
            $(".modal-body").removeClass('signupActive');
        });
    });
</script>
<script>
    $('#forget-pwd').click(function () {
        $('#myModal').css('display', 'none');
        $('div.modal-backdrop').remove();
    });
</script>

<script>
    function addImage(pk) {
        alert("addImage: " + pk);
    }

    $('#myModal .save').click(function (e) {
        e.preventDefault();
        addImage(5);
        $('#myModal').modal('hide');
        //$(this).tab('show')
        return false;
    })
</script>

<script type="text/javascript">
    $(function () {
        var element = document.getElementById('cus_phone');
        if (element.value == '') {
            $('#cus_phone').val('{{$default_dial}}');
        }

        $('.alert').delay(500).fadeIn('normal', function () {
            $(this).delay(3000).fadeOut('slow');
        });


    });
    $(document).ready(function () {
        @if(Session::has('search_location') != 1)
			$('#centralModalLGInfoDemo').modal({
				backdrop: 'static',
				keyboard: false
			})
        @endif
    });
</script>
@if(TWILIO_STATUS == 1)
    <script type="text/javascript">
        function signup() {
            var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
            var default_dial = <?php echo $default_dial; ?>;
            var cus_email = $('#cus_email').val();
            if ($('#cus_fname').val() == '') {
                $('#cus_fname_err').html("@lang(Session::get('front_lang_file').'.FRONT_EN_CUS_NAME')");
                return false;
            } else {
                $('#cus_fname_err').html('');
            }

            if ($('#cus_phone').val() == default_dial) {
                $('#cus_phone_err').html("@lang(Session::get('front_lang_file').'.FRONT_EN_PH_NUM')");
                return false;
            } else {
                $('#cus_phone_err').html("");
            }

            if ($('#cus_email').val() == '') {
                $('#cus_email_err').html("@lang(Session::get('front_lang_file').'.FRONT_ENTR_EMAIL')");
                return false;
            }
            else if ((reg.test(cus_email) == false)) {
                $('#cus_email_err').html("@lang(Session::get('front_lang_file').'.FRONT_EN_VA_MAIL')");
                return false;
            }
            else {
                $('#cus_email_err').html('');
            }

            if ($('#cus_pwd').val() == '') {
                $('#cus_pwd_err').html("@lang(Session::get('front_lang_file').'.FRONT_EN_PASS')");
                return false;
            } else {
                $('#cus_pwd_err').html('');
            }

            <?php if($PW_PROTECT == 1) { ?>

            if ($('.signup_pwd .invalid').length > 0) {
                $('#cus_pwd_err').html("@lang(Session::get('front_lang_file').'.FRONT_EN_VA_PASS')");
            }
            <?php } ?>

                <?php if( $SHOW_CAPTCHA == 1 ) { ?>

            if ($('#enter_captcha_cus').val() == '') {
                $('#captcha_err').html("@lang(Session::get('front_lang_file').'.FRONT_EN_CAPTCHA')").css({'color': 'red'});
                return false;
            }
            else if ($('#enter_captcha_cus').val().trim() != code) {
                $('#captcha_err').html("@lang(Session::get('front_lang_file').'.FRONT_INCORR_CAPTCHA')").css({'color': 'red'});
                return false;
            }
            else {
                $('#captcha_err').html('');
            }
            <?php } ?>
            if ($('#term').prop("checked") == false) {
                $('#term_err').html("@lang(Session::get('front_lang_file').'.FRONT_ACCEPT_TERMS')");
                return false;

            } else {
                $('#term_err').html('');

            }

            var cus_fname = $('#cus_fname').val();
            var cus_phone = $('#cus_phone').val();
            var cus_email = $('#cus_email').val();
            var cus_pwd = $('#cus_pwd').val();
			@if(Request::segment(2)!='' && Request::segment(1)=='refer-login')
				var referer_id = '{{Request::segment(2)}}';
			@else
				var referer_id = '';
			@endif

            $.ajax({
                type: 'POST',
                url: '{{ url('signup_with_otp') }}',
                data: {
                    'cus_fname': cus_fname,
                    'cus_phone': cus_phone,
                    'cus_email': cus_email,
                    'cus_pwd': cus_pwd,
                    'referer_id': referer_id
                },
                beforeSend: function () {
                    $('#normal_signup').hide();
                    $('#normal_loader').show();
                },
                success: function (res) {
                    $('#normal_loader').hide();
                    var obj = jQuery.parseJSON(res);
                    if (obj.msg == 'Success') {
                        $("#myModal").modal('hide');
                        $('#otpmyModal').modal({
                            show: true,
                            keyboard: false,
                            backdrop: 'static'
                        });
                        $("#otp_sec").html('<input type="hidden" id="current_otp" value="' + obj.otp + '">');

                    } else {
                        $('#signup_error').html(obj.msg).show();
                    }
                }
            });

        }
    </script>
@else
    <script type="text/javascript">
        function signup() {
            var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
            var default_dial = <?php echo $default_dial; ?>;
            var cus_email = $('#cus_email').val();

			if ($('#cus_fname').val() == '') {
                $('#cus_fname_err').html("@lang(Session::get('front_lang_file').'.FRONT_EN_CUS_NAME')");
                return false;
            } else {
                $('#cus_fname_err').html('');
            }
            if ($('#cus_phone').val() == default_dial) {
                $('#cus_phone_err').html("@lang(Session::get('front_lang_file').'.FRONT_EN_PH_NUM')");
                return false;
            } else {
                $('#cus_phone_err').html("");
            }

            if ($('#cus_email').val() == '') {
                $('#cus_email_err').html("@lang(Session::get('front_lang_file').'.FRONT_ENTR_EMAIL')");
                return false;
            }
            else if ((reg.test(cus_email) == false)) {
                $('#cus_email_err').html("@lang(Session::get('front_lang_file').'.FRONT_EN_VA_MAIL')");
                return false;
            }
            else {
                $('#cus_email_err').html('');
            }

            if ($('#cus_pwd').val() == '') {
                $('#cus_pwd_err').html("@lang(Session::get('front_lang_file').'.FRONT_EN_PASS')");
                return false;
            }
            else {
                $('#cus_pwd_err').html('');
            }
            <?php if($PW_PROTECT == 1) { ?>

            if ($('.invalid').length > 0) {
                $('#cus_pwd_err').html("@lang(Session::get('front_lang_file').'.FRONT_EN_VA_PASS')");
                return false;
            }
            else {
                $('#cus_pwd_err').html("");
            }
            <?php } ?>

           

            <?php if( $SHOW_CAPTCHA == 1 ) { ?>

            if ($('#enter_captcha_cus').val() == '') {
                $('#captcha_err').html("@lang(Session::get('front_lang_file').'.FRONT_EN_CAPTCHA')").css({'color': 'red'});
                return false;
            }
            else if ($('#enter_captcha_cus').val().trim() != code) {
                $('#captcha_err').html("@lang(Session::get('front_lang_file').'.FRONT_INCORR_CAPTCHA')").css({'color': 'red'});
                return false;
            }
            else {
                $('#captcha_err').html('');
            }
            <?php } ?>
			
			 if ($('#term').prop("checked") == false) {
                $('#term_err').html("@lang(Session::get('front_lang_file').'.FRONT_ACCEPT_TERMS')");
                return false;

            } else {
                $('#term_err').html('');

            }

            var cus_fname = $('#cus_fname').val();
            var cus_phone = $('#cus_phone').val();
            var cus_email = $('#cus_email').val();
            var cus_pwd = $('#cus_pwd').val();
            //var referer_id = '{{Request::segment(2)}}';
			@if(Request::segment(2)!='' && Request::segment(1)=='refer-login')
				var referer_id = '{{Request::segment(2)}}';
			@else
				var referer_id = '';
			@endif

            $.ajax({
                type: 'POST',
                url: '{{ url('signup') }}',
                data: {
                    'cus_fname': cus_fname,
                    'cus_phone': cus_phone,
                    'cus_email': cus_email,
                    'cus_pwd': cus_pwd,
                    'referer_id': referer_id
                },
                beforeSend: function () {
                    $('#normal_signup').hide();
                    $('#normal_loader').show();
                },
                success: function (res) {
                    $('#normal_loader').hide();
                    var obj = jQuery.parseJSON(res);
                    if (obj.msg == 'Success') {
                        location.reload();
                    } else {
                        $('#signup_error').html(obj.msg).show();
                    }
                }
            });

        }
    </script>
@endif
<script type="text/javascript">
    function validate_phone(gotId) {
        var defaultDial = '{{Config::get('config_default_dial')}}';
        var element = document.getElementById(gotId);
        if (element.value == '' || element.value.length < defaultDial.trim().length) {

            $('#' + gotId).val('{{$default_dial}}');

        }
        element.value = element.value.replace(/[^0-9 +]+/, '');
    }

    var wage = document.getElementById("email");
    wage.addEventListener("keydown", function (e) {
        if (e.keyCode === 13) {  //checks whether the pressed key is "Enter"
            chk_login()
        }
    });
    var pwd = document.getElementById("pwd");
    pwd.addEventListener("keydown", function (e) {
        if (e.keyCode === 13) {  //checks whether the pressed key is "Enter"
            chk_login()
        }
    });

    function chk_login() {
        var email = document.getElementById('email').value;
        var pwd = document.getElementById('pwd').value;
        if (email != '' && pwd != '') {
            $.ajax({
                type: 'POST',
                url: '<?php echo url('check_login'); ?>',
                data: {'mail': email, 'pwd': pwd},
                /*beforeSend: function()
                {
                    alert('ss');
                    //document.getElementById("submit_otp").blur();
                    //$('.login-btn').css('pointer-events','none');
                },*/
                success: function (res) {
                    if (res == 0) {
                        $('#login_err').html("@lang(Session::get('front_lang_file').'.ADMIN_INVALID_MAIL')");
                        return false;
                    }
                    else if (res == -1) {
                        $('#login_err').html("@lang(Session::get('front_lang_file').'.ADMIN_INVALID_PASS')");
                        return false;
                    }
                    else if (res == -4) {
                        $('#login_err').html("@lang(Session::get('front_lang_file').'.FRONT_PH_NOT_EXISTS')");
                        return false;
                    }
                    else if (res == -2) {
                        $('.myModal').modal('hide');
                        location.reload();
                    }
                    else if (res == 1) {
                        var url = "<?php echo Session::pull('from'); ?>";
                        window.location.href = url;
                    }
                },
                error: function (xhr) { // if error occured
                    $('#login_err').html(xhr.responseText);
                    return false;
                    //alert("Error occured.please try again\n"+xhr.statusText +'\n'+ xhr.responseText);
                    //$(placeholder).append(xhr.statusText + xhr.responseText);
                    //$(placeholder).removeClass('loading');
                }

            });
        }
    }
</script>

<script type="text/javascript">
    function otp_submit() {
        if ($('#otp').val() == '') {
            $('#otp_err').html('Please Enter OTP');
        }
        else {
            $('#otp_err').html('');
            var otp = $('#otp').val();
            var current_otp = $('#current_otp').val();
            $.ajax({
                type: 'POST',
                url: '{{ url('check_otp') }}',
                data: {'otp': otp, 'current_otp': current_otp},
                beforeSend: function () {
                    //document.getElementById("submit_otp").blur();
                    $('#submit_otp').css('pointer-events', 'none');
                },
                success: function (res) {
                    if (res == 'match') {
                        location.reload();
                    } else {
                        $('#otp_err').html('Wrong OTP');
                        $('#submit_otp').css('pointer-events', 'auto');
                    }
                }
            });

        }
    }

    function mobile_otp_submit() {
        if ($('#mobile_otp').val() == '') {
            $('#mobile_otp_err').html('Please Enter OTP');
        }
        else {
            $('#mobile_otp_err').html('');
            var otp = $('#mobile_otp').val();
            var current_otp = $('#mobile_current_otp').val();
            $.ajax({
                type: 'POST',
                url: '{{ url('mobile_check_otp') }}',
                data: {'otp': otp, 'current_otp': current_otp},
                beforeSend: function () {
                    //document.getElementById("submit_otp").blur();
                    $('#submit_otp').css('pointer-events', 'none');
                },
                success: function (res) {
                    if (res == 'match') {
                        $('#otpMobileChangemyModal').modal('hide');
                    } else {
                        alert('Wrong OTP');
                        $('#submit_otp').css('pointer-events', 'auto');
                        location.reload();
                    }
                }
            });

        }
    }
</script>
<script>

    $(document).ready(function () {


        $('.signupClass').change(function () {
            $('#normal_signup').show();
            $('#normal_loader').hide();
            $('#signup_error').hide();
        });
        <?php if($SHOW_CAPTCHA == 1)
        { ?>
        createCaptcha('cus');
        <?php } ?>
    });
    <?php if($PW_PROTECT == 1) { ?>
    var pw = document.getElementById('cus_pwd');
    var letter = document.getElementById("letter");
    var capital = document.getElementById("capital");
    var number = document.getElementById("number");
    var length = document.getElementById("length");


    pw.onkeyup = function () {

        // Validate lowercase letters
        var lowerCaseLetters = /[a-z]/g;
        if (pw.value.match(lowerCaseLetters)) {
            letter.classList.remove("invalid");
            letter.classList.add("valid");
        } else {
            letter.classList.remove("valid");
            letter.classList.add("invalid");
        }

        // Validate capital letters
        var upperCaseLetters = /[A-Z]/g;
        if (pw.value.match(upperCaseLetters)) {
            capital.classList.remove("invalid");
            capital.classList.add("valid");
        } else {
            capital.classList.remove("valid");
            capital.classList.add("invalid");
        }

        // Validate numbers
        var numbers = /[0-9]/g;
        var spl_chr = /[!@$%^&*()]/g;
        if (pw.value.match(numbers) && pw.value.match(spl_chr)) {
            //if(pw.value.match(numbers)) {
            number.classList.remove("invalid");
            number.classList.add("valid");
        } else {
            number.classList.remove("valid");
            number.classList.add("invalid");
        }

        // Validate length
        if (pw.value.length >= 6) {
            length.classList.remove("invalid");
            length.classList.add("valid");
        } else {
            length.classList.remove("valid");
            length.classList.add("invalid");
        }
    }
        <?php } ?>
    var code;

    function createCaptcha(type) {
        //clear the contents of captcha div first
        document.getElementById('captcha_' + type).innerHTML = "";
        var charsArray =
            "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ@!#$%^&*";
        var lengthOtp = 5;
        var captcha = [];
        for (var i = 0; i < lengthOtp; i++) {
            //below code will not allow Repetition of Characters
            var index = Math.floor(Math.random() * charsArray.length + 1); //get the next character from the array
            if (captcha.indexOf(charsArray[index]) == -1)
                captcha.push(charsArray[index]);
            else i--;
        }
        var canv = document.createElement("canvas");
        canv.id = "captcha";
        canv.width = 100;
        canv.height = 40;
        var ctx = canv.getContext("2d");
        ctx.font = "25px Akronim";
        ctx.strokeText(captcha.join(""), 0, 30);
        //storing captcha so that can validate you can save it somewhere else according to your specific requirements
        code = captcha.join("");
        document.getElementById("captcha_" + type).appendChild(canv); // adds the canvas to the body element
    }

</script>
<script type="text/javascript" src='https://maps.google.com/maps/api/js?sensor=false&libraries=places&key={{$MAP_KEY}}'></script>
<script src="{{url('')}}/public/js/bootbox.min.js"></script>

<script type="text/javascript">
    var map;
	var marker;
	function initialize_footer() {
		var ip_lat = {{ (Session::has('search_location')== '1') ?  Session::get('search_latitude'): $ip_latitude }};
		var ip_long = {{ (Session::has('search_location')== '1') ?  Session::get('search_longitude'): $ip_longitude }};

		var myLatlng = new google.maps.LatLng(ip_lat,ip_long);
		var mapOptions = {
							zoom				: 15,
							center				: myLatlng,
							disableDefaultUI	: true,
							panControl			: true,
							zoomControl			: true,
							mapTypeControl		: true,
							streetViewControl	: true,
							mapTypeId			: google.maps.MapTypeId.ROADMAP,
							fullscreenControl	: true
						};

		map = new google.maps.Map(document.getElementById('footer_us3'),mapOptions);
		var marker = new google.maps.Marker({
												position	: myLatlng,
												map			: map,
												draggable	: true,    
												@if(Session::has('search_location')== '1')
												@else
													visible : false,
												@endif
											}); 

		google.maps.event.addListener(marker, 'dragend', function(e) {
			var lat = this.getPosition().lat();
			var lng = this.getPosition().lng();
			$.ajax({
				type: 'post',
				url: "<?php echo url("save-location-insession"); ?>",
				data: {
						'latitude'	: lat,
						'longitude'	: lng,
						"location"	: $('#footer_us3-address').val()
						},
				success: function (response) { 
					if (response == 1) {
						//location.reload();
						location.href = '{{url('')}}';
					}
					else if (response == 2) {
						// alert(response);
						bootbox.confirm("Your cart will be emptied if you change your address. Are you sure you want to proceed?", function (result) {
							if (result == true) {
								$.ajax({
									type		: 'post',
									url			: "<?php echo url("save-location-insession-clearcart"); ?>",
									data: {
									'latitude'	: lat,
									'longitude'	: lng,
									"location"	: $('#footer_us3-address').val()
									},
									success: function (response) {
										location.href = '{{url('')}}';
									}
								});
							}
							else {
								location.reload();
							}
						});
					}
					else {
						location.reload();
					}
				}
			});
		});
		
		var input = document.getElementById('footer_us3-address');
		var autocomplete = new google.maps.places.Autocomplete(input);
		autocomplete.bindTo('bounds', map);
		
		google.maps.event.addListener(autocomplete, 'place_changed', function () {
			var place = autocomplete.getPlace();
			var lat = place.geometry.location.lat();
			var lng = place.geometry.location.lng();

			if (place.geometry.viewport) 
            {
				map.fitBounds(place.geometry.viewport);
				var myLatlng = place.geometry.location; 
				var latlng = new google.maps.LatLng(lat, lng);
				marker.setPosition(latlng);

				
				$.ajax({
					type: 'post',
					url: "<?php echo url("save-location-insession"); ?>",
					data: {
							'latitude'	: lat,
							'longitude'	: lng,
							"location"	: $('#footer_us3-address').val()
							},
					success: function (response) { 
						if (response == 1) {
							//location.reload();
							location.href = '{{url('')}}';
						}
						else if (response == 2) {
							// alert(response);
							bootbox.confirm("Your cart will be emptied if you change your address. Are you sure you want to proceed?", function (result) {
								if (result == true) {
									$.ajax({
										type		: 'post',
										url			: "<?php echo url("save-location-insession-clearcart"); ?>",
										data: {
										'latitude'	: lat,
										'longitude'	: lng,
										"location"	: $('#footer_us3-address').val()
										},
										success: function (response) {
											location.href = '{{url('')}}';
										}
									});
								}
								else {
									location.reload();
								}
							});
						}
						else {
							location.reload();
						}
					}
				});
			} 
            else 
            {
				map.setCenter(place.geometry.location); 
				map.setZoom(17);
			}
		});
	}

    google.maps.event.addDomListener(window, 'load', initialize_footer);
    </script>
<script>
	@if(Session::has('search_location')== '')
		setTimeout(function(){ $('#footer_us3-address').val(''); }, 2500);
	@endif
	function set_my_currentLocation(){
		$.ajax({
			type: 'post',
			url: "<?php echo url("save-location-insession-clearcart"); ?>",
			data: {
				'latitude': '{{$ip_latitude}}',
				'longitude': '{{$ip_longitude}}',
				"location": '{{$ip_location}}'
			},
			success: function (response) {
				location.href = '{{url('')}}';
			}
		});
	}
</script>
{{-- used for forgot password --}}
<script>
    function check_mail(event) {

        var text = document.getElementById('forgot_email').value;
        var out = true;
        $.ajax({
            'data': {'text': text},
            'type': 'post',
            'async': false,
            'url': '<?php echo url('check_user'); ?>',
            success: function (response) {
                if ($.trim(response) == "Invalid Email") {
                    $('#forgot_email').css('border', '1px solid red');
                    $('#error').html('@lang(Session::get('front_lang_file').'.FRONT_MAIL_NOT_EXIST')');
                    out = false;
                    event.preventDefault();
                }
                else if ($.trim(response) == "Invalid Phone") {
                    $('#forgot_email').css('border', '1px solid red');
                    $('#error').html('@lang(Session::get('front_lang_file').'.FRONT_INVALID_PHONE')');
                    out = false;
                    event.preventDefault();
                }
                else if ($.trim(response) == "Not Valid") {
                    $('#forgot_email').css('border', '1px solid red');
                    $('#error').html('@lang(Session::get('front_lang_file').'.FRONT_CONTAIN_CN_CODE')');
                    out = false;
                    event.preventDefault();
                }
                else if ($.trim(response) == "Invalid") {
                    $('#forgot_email').css('border', '1px solid red');
                    $('#error').html('@lang(Session::get('front_lang_file').'.FRONT_INVALID_FORMAT')');
                    out = false;
                    event.preventDefault();
                }
                else if ($.trim(response) == "Success") {
                    out = true;
                }
            }

        });
        //alert(out);
        return out;
        /*if(!out)
            {
            return out;
        }*/
    }

    function clear_data(id) {
        $('#' + id).val('');
        $('#error').html('');
        $('#forgot_email').css('border', '1px solid #495057');
    }

</script>
<script type="text/javascript">
    function addtowish(pro_id, pro_type) {
        //alert();
        //var wishlisturl = document.getElementById('wishlisturl').value;

        $.ajax({
            type: "get",
            url: "<?php echo url('addtowish'); ?>",
            data: {'pro_id': pro_id, 'pro_type': pro_type},
            success: function (response) {
                //alert(response); return false;
                if (response == 0) {

                    $(".add-to-wishlist").fadeIn('slow').delay(5000).fadeOut('slow');
                    //window.location=wishlisturl;
                    window.location.reload();

                } else {

                    //window.location=wishlisturl;
                }
            },
            error: function (xhr, status, error) {
                var err = eval("(" + xhr.responseText + ")");
                alert('@lang(Session::get('front_lang_file').'.FRONT_AJAX_ERROR')\n' + err.message);


            }
        });
    }


    function removeFromwish_ajax(ws_id) {
        $.ajax({
            type: "get",
            url: "<?php echo url('remove_wish_product'); ?>",
            data: {'ws_id': ws_id},
            beforeSend: function () {
            },
            success: function (response) {
                var result = response.split('`');
                if (result[0] == '0') {
                    $('#wishId_' + pro_id).attr('onclick', "addtowish_ajax('" + pro_id + "'," + pro_type + ",'remove')");
                    $('#wishId_' + pro_id).html('<i class="fa fa-heart"></i>');
                } else {
                    $('#wishId_' + pro_id).attr('onclick', "addtowish_ajax('" + pro_id + "'," + pro_type + ",'add')");
                    $('#wishId_' + pro_id).html('<i class="fa fa-heart-o"></i>');
                }
                $('#mySuxesMsg').html('<div class="alert alert-success text-center animated fadeIn"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><strong id="suxsMsg">' + result[1] + '</strong></div>')
                $('html, body').animate({
                    'scrollTop': '0'
                });
            },
            error: function (xhr, status, error) {
                var err = eval("(" + xhr.responseText + ")");
                alert('@lang(Session::get('front_lang_file').'.FRONT_AJAX_ERROR')\n' + err.message);
                window.location.reload();
            }
        });
    }
</script>
<script src="{{url('').'/public/front/js/wow.min.js'}}"></script>

<script>
    new WOW().init();

    wow = new WOW(
        {
            boxClass: 'wow',      // default
            animateClass: 'animated', // default
            offset: 0,          // default
            mobile: true,       // default
            live: true        // default
        }
    )
    wow.init();
</script>


<!--<script>
    if (jQuery(window).width() > 768) {
        $(window).scroll(function(){
            if ($(window).scrollTop() >95) {
                $('.header').addClass('fixed-header');
            }
            else {
                $('.header').removeClass('fixed-header');
            }
        });
    }
</script>-->

<script>
    $('.mobile-loc').click(function () {
        $('.location-sec').slideToggle();
    });

    // $('.mobile-menu').click(function () {
    //     $('.login-menu').slideToggle();
    // });
    // $('.mobile-menu').click(function () {
    //     $('body').toggleClass('menuactive');
    //     $('.login-menu').toggle();
    //     $('.login-menu').css('right','0');
    // });  

    $('.mobile-menu').click(function () {
        $('body').toggleClass('menuactive');
        //$('.login-menu').toggle();
        $('.login-menu').toggleClass('menuactive-div');
    });
	// $('#filter_toggle').click(function () {
 //        //$('body').toggleClass('menuactive');
 //        //$('.login-menu').toggle();
 //        //$('.filter-menu').toggleClass('menuactive-div');
 //    });

  $('#filter_toggle').click(function(){    
   $('.filter-menu').css('right','0px');
   });
  $('.filter-menu button').click(function(){   
   $('.filter-menu').css('right','-100%');
   });
    @if(Session::has('search_location') != 1)
		$('.location-menu').css('left','0px');
		$(".location-menuoverall").append("<div class='overlayfilterj col-lg-12'></div>");
		$('.location-menuoverall').addClass('location-menuoverallbg'); 
	@endif	
	
	$('#location-toggle').click(function(){    
		$('.location-menu').css('left','0px');
		$(".location-menuoverall").append("<div class='overlayfilterj col-lg-12'></div>");
		$('.location-menuoverall').addClass('location-menuoverallbg');      
	});   
	$('.location-menu button').on('click',function(e) {                                             
		$(".overlayfilterj").remove();       
		$('.location-menuoverall').removeClass('location-menuoverallbg'); 
        $('.location-menu').css('left','-100%');      
	});
</script>

<script>
    // function search_store(){

        $('#search_res').bind('keyup change', function () {
        var search_res = $('#search_res').val();
        if (search_res == '') {
            $('#rest_det').hide();
            $("#search_res").removeClass('active');
        } else {
            $("#search_res").addClass('active');
            $.ajax({
                type: 'get',
                url: "<?php echo url("search-restaurant"); ?>",
                beforeSend: function () {
                    $(".loading-image").show();
                },
                data: {'search_res': search_res},
                success: function (response) {
                        if (response != '') {
                            $('#rest_det').html(response);
                            $('#rest_det').show();
                        } else {
                            $('#rest_det').hide();
                        }
                }
            });
        }

    });
</script>

<!-- ------ auto complete search start -->
<!-- <script src="http://code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<script>


    $(document).ready(function () {
        res = JSON.parse('<?php echo addslashes($rest); ?>');
        var availableTags = res;

        var termTemplate = "<span class='ui-autocomplete-term'>%s</span>";
        var $elem = $("#search_res").autocomplete({
                source: availableTags
            }),
            elemAutocomplete = $elem.data("ui-autocomplete") || $elem.data("autocomplete");
        if (elemAutocomplete) {
            elemAutocomplete._renderItem = function (ul, item) {
                var cls = 'ui-menu-item';
                if (item.label === null) {
                    cls += ' center disabled';
                    item.label = '-- perhaps thou meaneth --';
                }
                var newText = String(item.value).replace(
                    new RegExp(this.term, "gi"),
                    "<span class='ui-state-highlight'>$&</span>");

                return $('<a href="javascript:res_page_redirect();"></a>')
                    .data("item.autocomplete", item)
                    .append("<div>" + newText + "</div>")
                    .appendTo(ul);
            };


        }
    });

    function res_page_redirect() {
        var name = $('#search_res').val();
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: '{{url('')}}/restaurant_redirect',
            data: {'name': name},
            type: "post",
            success: function (res) {
                // alert(res);
                if (res != '' || res != 0 || res != '0') {
                    window.location = '{{url('')}}/restaurant/' + res;

                    $('#search_res').val('');
                } else {
                    alert('no restaurants found');
                }
            }
        })
    }
</script> -->
<!-- ------ Auto Complete search start -------- -->

<script>

    function mer_otp_submit() {
        if ($('#mer_otp').val() == '') {
            $('#mer_otp_err').html("@lang(Session::get('front_lang_file').'.ADMIN_PLEASE_ENTER_OTP')");
            return false;
        }
        else {
            $('#mer_otp_err').html('');
            var otp = $('#mer_otp').val();
            var current_otp = $('#mer_current_otp').val();
            if (otp == current_otp) {
                $('.show_ph_msg').html("@lang(Session::get('front_lang_file').'.FRONT_OTP_VERI_SUXES')").css({'color': 'green'});
                $('#MerchantOtpmyModal').modal('hide');
                return true;
            }
            else {
                $('#mer_otp_err').html("@lang(Session::get('front_lang_file').'.ADMIN_INVALID_OTP')");
                return false;
            }

        }
    }

    function addtowishlist_ajax(pro_id, pro_type, action, need_text) {
        if (need_text == '0') {
            var addText = '';
            var removeText = '';
        }
        else {
            var addText = '@lang(Session::get('front_lang_file').'.FRONT_ADD_TO_WISHLIST')';
            var removeText = '@lang(Session::get('front_lang_file').'.FRONT_REMOVE_FROM_WISHLIST')';

        }
        $.ajax({
            type: "get",
            url: "<?php echo url('addtowish'); ?>",
            data: {'pro_id': pro_id, 'pro_type': pro_type},
            beforeSend: function () {
                if (action == 'add') {
                    $('#wishId_' + pro_id).attr('onclick', "addtowishlist_ajax('" + pro_id + "'," + pro_type + ",'remove'," + need_text + ")");
                    $('#wishId_' + pro_id).html('<i class="fa fa-heart" aria-hidden="true"></i>' + removeText);

                } else {
                    $('#wishId_' + pro_id).attr('onclick', "addtowishlist_ajax('" + pro_id + "'," + pro_type + ",'add'," + need_text + ")");
                    $('#wishId_' + pro_id).html('<i class="fa fa-heart-o"></i>' + addText);
                }
            },
            success: function (response) {
                var result = response.split('`');
                if (result[0] == '0') {
                    $('#wishId_' + pro_id).attr('onclick', "addtowishlist_ajax('" + pro_id + "'," + pro_type + ",'remove'," + need_text + ")");
                    $('#wishId_' + pro_id).html('<i class="fa fa-heart" aria-hidden="true"></i>' + removeText);
                } else {
                    $('#wishId_' + pro_id).attr('onclick', "addtowishlist_ajax('" + pro_id + "'," + pro_type + ",'add'," + need_text + ")");
                    $('#wishId_' + pro_id).html('<i class="fa fa-heart-o"></i>' + addText);
                }
                $('#mySuxesMsg').html('<div class="alert alert-success text-center animated fadeIn"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><strong id="suxsMsg">' + result[1] + '</strong></div>')
                $('html, body').animate({
                    'scrollTop': '0'
                });
            },
            error: function (xhr, status, error) {
                var err = eval("(" + xhr.responseText + ")");
                alert('@lang(Session::get('front_lang_file').'.FRONT_AJAX_ERROR')\n' + err.message);
                window.location.reload();
            }
        });
    }
	$('#subscribe_submit').click(function(){  
		var email=$('#sub_email').val(); 
		if(email=='') {
			alert("@lang(Session::get('front_lang_file').'.FRONT_FORGET_MAIL')");
			return false;
		}
		$('.mail-loader').css('display','block');
		$.ajax({ 
			type: 'post',
			data: {email},
			url: '<?php echo url('subscription_submit'); ?>',
			success: function(responseText){  
				if(responseText=='0')
				{ 
					alert("@lang(Session::get('front_lang_file').'.EMAIL_ALREADY_SUBSCRIBED')");
					$('.mail-loader').css('display','none');
				} else if(responseText=='1'){
					$('.mail-loader').css('display','none');
					$('.newsletter-email').val('');
					alert("@lang(Session::get('front_lang_file').'.EMAIL_HAS_BEEN_SUBSCRIPTION_SUCCESSFULLY')");
				}else{
					alert(responseText);
					$('.mail-loader').css('display','none');
				}
			}       
		});  


	});
	function validate_refer_friend_form(){
		var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
		var cus_email = $('#refer_friend_email_head').val();
		$('#refer_friend_submitBtn').css('pointer-events','none');
		$('#refer_friend_email_error').html('').hide();
		if (cus_email == '') {
			$('#refer_friend_submitBtn').css('pointer-events','auto');
			$('#refer_friend_email_error').html("@lang(Session::get('front_lang_file').'.FRONT_ENTR_EMAIL')").show();
			return false;
		}
		else if ((reg.test(cus_email) == false)) {
			$('#refer_friend_submitBtn').css('pointer-events','auto');
			$('#refer_friend_email_error').html("@lang(Session::get('front_lang_file').'.FRONT_EN_VA_MAIL')").show();
			return false;
		}
		else {
			$('#refer_friend_submitBtn').css('pointer-events','none');
			$('#refer_friend_email_error').html('').hide();
		}

	}
</script>
<script>  
	  $('.section8-filter button#filter_toggle').on('click',function(e) {                   
		 $(".filter-menuoverall").append("<div class='overlayfilterj col-lg-8'></div>");
		 $('.filter-menuoverall').addClass('filter-menuoverallbg');
	});
	$('.filter-menu button').on('click',function(e) {                                             
		$(".overlayfilterj").remove();       
		$('.filter-menuoverall').removeClass('filter-menuoverallbg');       
	});

</script>
</body>
</html>