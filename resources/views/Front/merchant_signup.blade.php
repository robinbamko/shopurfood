@extends('Front.layouts.default')
  @section('content')
  <style type="text/css">
    .row .panel-heading{
      margin-bottom: 10px;
    }
    .error {
    color: #F00;
    font-weight: normal;
    font-family: unset;
  }
  #enter_captcha_mer-error {
    display: block;
    width: 100%;
    margin: 10px 0px 0px;
  }
   label.error
  {
    font-size:13px;
  }
  .merchant-select{ color:#495057; }
  /*.valid {color: #fff !important;}*/
  </style>
		<div class="main-sec">
			<div class="section10">
				<div class="container">
					<div class="row">
						<div class="col-lg-12 col-md-12 col-sm-12 section8-search" style="">
							<h3 style="text-align: center;"> {!!(Lang::has(Session::get('front_lang_file').'.FRONT_BE_A_MERCHANT')) ? trans(Session::get('front_lang_file').'.FRONT_BE_A_MERCHANT') : trans($FRONT_LANGUAGE.'.FRONT_BE_A_MERCHANT')!!} </h3>
						</div>
					</div>
				</div>
			</div>
		
          <div class="section9-inner">
              <div class="container userContainer">
                <div class="row">
                <div class="col-lg-2 col-sm-12 col-md-8 col-xs-12">
                </div>
                <div class="col-lg-8 col-sm-12 col-md-8 col-xs-12">

              {{-- Add/Edit page starts--}}
              <div class="box-body spaced" >
                <div id="location_form">
                  {{--Edit page values--}}


                  {{--Edit page values--}}
                  <div class="row-fluid well">
                    <div id="mer_signup_error" style="color: red;"></div>
                    {!! Form::open(['method' => 'post','class' => 'form-auth-small','url' => 'merchant_signup_submit','enctype'=>'multipart/form-data','id'=>'profile_form','onSubmit' => 'return chk_captcha()']) !!}

                    {{ Form::hidden('currency_code',$default_currency,['id' => 'currency_code'])}}
                    <div class="row panel-heading">

                      <div class="col-lg-4 col-sm-12 col-md-4 col-xs-12">
                        <div class="form-group">
                        <span class="panel-title">
                          {{(Lang::has(Session::get('front_lang_file').'.ADMIN_FIRST_NAME')) ? trans(Session::get('front_lang_file').'.ADMIN_FIRST_NAME') : trans($FRONT_LANGUAGE.'.ADMIN_FIRST_NAME')}}&nbsp;*
                        </span>
                      </div>
                      </div>
                      <div class="col-lg-8 col-sm-12 col-md-8 col-xs-12">
                        <div class="form-group">
                        {!! Form::text('mer_fname','',['class'=>'form-control','placeholder'=>(Lang::has(Session::get('front_lang_file').'.FRONT_ENTR_FNAME')) ? trans(Session::get('front_lang_file').'.FRONT_ENTR_FNAME') : trans($FRONT_LANGUAGE.'.FRONT_ENTR_FNAME'),'id' => 'mer_fname','maxlength'=>50]) !!}
                        <div id="suggesstion-box" class="cntry_box" ><span id="no_country"> </span>
                        </div>
                        </div>
                        <div id="mer_fname_err" style="color:red"></div>
                      </div>
                    <!-- </div>

                    <div class="row panel-heading"> -->
                      <div class="col-lg-4 col-sm-12 col-md-4 col-xs-12">
                        <div class="form-group">
                        <span class="panel-title">
                          {{(Lang::has(Session::get('front_lang_file').'.ADMIN_LAST_NAME')) ? trans(Session::get('front_lang_file').'.ADMIN_LAST_NAME') : trans($FRONT_LANGUAGE.'.ADMIN_LAST_NAME')}}&nbsp;*
                        </span>
                        </div>
                      </div>
                      <div class="col-lg-8 col-sm-12 col-md-8 col-xs-12">
                        <div class="form-group">
                        {!! Form::text('mer_lname','',['class'=>'form-control','required','placeholder'=>(Lang::has(Session::get('front_lang_file').'.FRONT_ENTR_LNAME')) ? trans(Session::get('front_lang_file').'.FRONT_ENTR_LNAME') : trans($FRONT_LANGUAGE.'.FRONT_ENTR_LNAME'),'maxlength'=>50,'id' => 'mer_lname']) !!}
                        </div>
                        <div id="mer_lname_err" style="color:red"></div>
                      </div>
                    <!-- </div>

                    <div class="row panel-heading"> -->
                      <div class="col-lg-4 col-sm-12 col-md-4 col-xs-12">
                        <div class="form-group">
                        <span class="panel-title">
                          {{(Lang::has(Session::get('front_lang_file').'.ADMIN_EMAIL')) ? trans(Session::get('front_lang_file').'.ADMIN_EMAIL') : trans($FRONT_LANGUAGE.'.ADMIN_EMAIL')}}&nbsp;*
                        </span>
                        </div>
                      </div>
                      <div class="col-lg-8 col-sm-12 col-md-8 col-xs-12">
                        <div class="form-group">
                        {!! Form::email('mer_email','',['class'=>'form-control','placeholder'=>(Lang::has(Session::get('front_lang_file').'.FRONT_ENTR_EMAIL')) ? trans(Session::get('front_lang_file').'.FRONT_ENTR_EMAIL') : trans($FRONT_LANGUAGE.'.FRONT_ENTR_EMAIL'),'id'=>'mer_email','maxlength'=>100,'onkeyup'=>'validate_phone(\'cus_phone\');']) !!}
                        </div>
                         <div id="mer_email_err" style="color:red"></div>
                      </div>

                      <div class="col-lg-4 col-sm-12 col-md-4 col-xs-12">
                        <div class="form-group">
                        <span class="panel-title">
                          {{(Lang::has(Session::get('front_lang_file').'.ADMIN_MOBILENO')) ? trans(Session::get('front_lang_file').'.ADMIN_MOBILENO') : trans($FRONT_LANGUAGE.'.ADMIN_MOBILENO')}}&nbsp;*
                        </span>
                        </div>
                      </div>
                      <div class="col-lg-8 col-sm-12 col-md-8 col-xs-12">
                        <div class="form-group">
                        {!! Form::text('mer_phone','',['class'=>'form-control merchant-select','required','id'=>'mer_phone','maxlength'=>15,'onkeyup'=>'validate_phone(\'mer_phone\');']) !!}
                        </div>
                        <div id="mer_phone_err" style="color:red"></div>
                      </div>

                    <div class="col-lg-4 col-sm-12 col-md-4 col-xs-12">
                        <div class="form-group">
                        <span class="panel-title">
                         {{(Lang::has(Session::get('front_lang_file').'.ADMIN_BUSINESS_TYPE')) ? trans(Session::get('front_lang_file').'.ADMIN_BUSINESS_TYPE') : trans($FRONT_LANGUAGE.'.ADMIN_BUSINESS_TYPE')}}:&nbsp;*
                        </span>
                        </div>
                      </div>
                      <div class="col-lg-8 col-sm-12 col-md-8 col-xs-12">
                        <div class="form-group">
                        @php $business_type = array('2'=>'Restaurant'); @endphp
                         {{-- Form::select('mer_business_type',($business_type),null,['class' => 'form-control merchant-select','id'=>'business_type'] ) --}}
                         {{ Form::text('mer_business_type_name',(Lang::has(Session::get('front_lang_file').'.ADMIN_RESTAURANT')) ? trans(Session::get('front_lang_file').'.ADMIN_RESTAURANT') : trans($FRONT_LANGUAGE.'.ADMIN_RESTAURANT'),['class' => 'form-control','readonly']) }}
                        {{ Form::hidden('mer_business_type','2',['class' => 'form-control','readonly','id'=>'business_type']) }}
                        </div>
                    </div>

                    <!-- </div>



                    <div class="row panel-heading"> -->
                     @if($SHOW_CAPTCHA == 1)
                    <!-- <div class="col-lg-4 col-sm-12 col-md-4 col-xs-12">
                    </div> -->
                    <div class="col-lg-8 offset-lg-4 col-sm-12 col-md-8 offset-md-4 col-xs-12 ">
                      <div class="form-group input-group">
                        <span class="captcha" id="captcha_mer"></span>
                        <button type="button" class="btn " onClick="createCaptcha('mer')"><i class="fa fa-refresh" id="refresh"></i></button>
                      </div>
                      <div class="form-group input-group">

                     {{ Form::text('captcha','',['id' => 'enter_captcha_mer','class' => 'form-control', 'placeholder' => __(Session::get('front_lang_file').'.FRONT_EN_CAPTCHA'),'required'])}}

                        <div id="captcha_mer_err"></div>
                      </div>
                    </div>
                    @endif


                      <div class="col-lg-8 offset-lg-4 col-sm-12 col-md-8 offset-md-4 col-xs-12 ">
                        <div class="form-group formBtn">

                        @php $saveBtn=(Lang::has(Session::get('front_lang_file').'.FRONT_SIGNUP')) ? trans(Session::get('front_lang_file').'.FRONT_SIGNUP') : trans($FRONT_LANGUAGE.'.FRONT_SIGNUP') @endphp

                      @if(TWILIO_STATUS == 1)
                      <a href="#" class="login-btn" onclick="merchant_signup();">{{$saveBtn}}</a>

                      @else
                      {!! Form::submit($saveBtn,['class' => 'btn btn-success'])!!}
                      @endif
                      </div>
                      </div>
                    </div>
                    {!! Form::close() !!}
                  </div>
                </div>
              </div>
              {{-- Add page ends--}}
                      </div>

                    </div>
                  </div>
                </div>
			</div>

@section('script')
  <link rel="stylesheet" href="<?php echo URL::to('/'); ?>/public/css/intlTelInput.css">
  <script>
    $(document).ready(function() {

      var element = document.getElementById('mer_phone');
      if(element.value=='')
      {
        $('#mer_phone').val('{{$default_dial}}');
      }

    });

  </script>


  <script src="{{url('')}}/public/admin/assets/scripts/locationpicker.jquery.min.js"></script>

    <script src="<?php echo URL::to('/'); ?>/public/js/intlTelInput.js"></script>
     <script type="text/javascript">
      $("#mer_phone").intlTelInput({onlyCountries: ["{{$default_country_code}}"]});


     </script>
     <script type="text/javascript">
		@if(TWILIO_STATUS == 0)
		$.validator.addMethod("jsPhoneValidation", function(value, element) {
			var defaultDial = '{{$default_dial}}';
			return value.substr(0, (defaultDial.trim().length)) != value.trim()
		}, "{{(Lang::has(Session::get('front_lang_file').'.ADMIN_ENTER_PHONE')) ? trans(Session::get('front_lang_file').'.ADMIN_ENTER_PHONE') : trans($this->FRONT_LANGUAGE.'.ADMIN_ENTER_PHONE')}}");

         $("#profile_form").validate({
             //onkeyup: true,
             onfocusout: function (element) {
                 this.element(element);
             },
             rules: {
                 mer_fname: "required",
                 mer_lname: "required",
                 mer_email: {
                     required: true,
                     email: true
                 },

                 mer_phone : { jsPhoneValidation : true  },

                 mer_business_type: "required",
                 captcha:"required",
             },
             messages: {
                 mer_fname: "{{(Lang::has(Session::get('front_lang_file').'.ADMIN_ENTER_FNAME')) ? trans(Session::get('front_lang_file').'.ADMIN_ENTER_FNAME') : trans($FRONT_LANGUAGE.'.ADMIN_ENTER_FNAME')}}",
                 mer_lname: "{{(Lang::has(Session::get('front_lang_file').'.ADMIN_ENTER_LNAME')) ? trans(Session::get('front_lang_file').'.ADMIN_ENTER_LNAME') : trans($FRONT_LANGUAGE.'.ADMIN_ENTER_LNAME')}}",
                 mer_email: "{{(Lang::has(Session::get('front_lang_file').'.ADMIN_ENTER_EMAIL')) ? trans(Session::get('front_lang_file').'.ADMIN_ENTER_EMAIL') : trans($FRONT_LANGUAGE.'.ADMIN_ENTER_EMAIL')}}",
                 mer_phone: "{{(Lang::has(Session::get('front_lang_file').'.ADMIN_ENTER_PHONE')) ? trans(Session::get('front_lang_file').'.ADMIN_ENTER_PHONE') : trans($FRONT_LANGUAGE.'.ADMIN_ENTER_PHONE')}}",
                 mer_business_type: "{{(Lang::has(Session::get('front_lang_file').'.FRONT_SL_BU_TYPE')) ? trans(Session::get('front_lang_file').'.FRONT_SL_BU_TYPE') : trans($FRONT_LANGUAGE.'.FRONT_SL_BU_TYPE')}}",
                 captcha :"{{(Lang::has(Session::get('front_lang_file').'.FRONT_ENTER_CAPTCHA')) ? trans(Session::get('front_lang_file').'.FRONT_ENTER_CAPTCHA') : trans($FRONT_LANGUAGE.'.FRONT_ENTER_CAPTCHA')}}",

             },
             errorPlacement: function(error, element)
             {
                 if ( element.is(":radio") )
                 {
                     error.appendTo( $(element).next().next().next().next());
                     //error.insertAfter($(element).parents('div').prev($('.pre_order_label')));
                 }
                 else
                 { // This is the default behavior
                     error.insertAfter( element );
                 }
             }
         });
		@endif

     </script>

<script type="text/javascript">
  function merchant_signup()
  {

    var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
    var mer_email = $('#mer_email').val();
    if($('#mer_fname').val() == '')
    {
      $('#mer_fname_err').html('Please Enter Merchant First Name');
      return false;
      }else{
      $('#mer_fname_err').html('');
    }
    if($('#mer_lname').val() == '')
    {
      $('#mer_lname_err').html('Please Enter Merchant Last Name');
      return false;
      }else{
      $('#mer_lname_err').html('');
    }
    if($('#mer_phone').val() == '')
    {
      $('#mer_phone_err').html('Please Enter Phone Number');
      return false;
      }else{
      $('#mer_phone_err').html('');
    }

    if($('#mer_email').val() == '')
    {
      $('#mer_email_err').html('Please Enter Email');
      return false;
    }
    else if((reg.test(mer_email) == false))
    {
      $('#mer_email_err').html('Please Enter Valid Email');
      return false;
    }
    else{
      $('#mer_email_err').html('');
    }
    var mer_fname = $('#mer_fname').val();
    var mer_lname = $('#mer_lname').val();
    var mer_phone = $('#mer_phone').val();
    var mer_email = $('#mer_email').val();
    var cur_code  = $('#currency_code').val();
    /*var business_type = $("#business_type option:selected").val();*/
    var business_type = $("#business_type").val();

    $.ajax({
      type: 'POST',
      url:'{{ url('mer_signup_with_otp') }}',

     data:{'mer_fname':mer_fname,'mer_lname':mer_lname,'mer_phone':mer_phone,'mer_email':mer_email,'business_type':business_type,'cur_code':cur_code},
      success:function(res){
        var obj = jQuery.parseJSON(res);
        if(obj.msg == 'Success')
        {
          $('#mer_signup_error').html('');
          $('#MerchantOtpmyModal').modal({
            show: true,
            keyboard: false,
            backdrop: 'static'
          });
          $("#mer_otp_sec").html('<input type="hidden" id="mer_current_otp" value="'+obj.otp+'">');

          }else{
          $('#mer_signup_error').html(obj.msg);
        }
      }
    });

  }

  function mer_otp_submit()
  {
    if($('#mer_otp').val() == '')
    {
      $('#mer_otp_err').html('Please Enter OTP');
    }
    else{
      $('#mer_otp_err').html('');
      var otp = $('#mer_otp').val();
      var current_otp = $('#mer_current_otp').val();
      $.ajax({
        type: 'POST',
        url:'{{ url('mer_check_otp') }}',
        data:{'otp':otp,'current_otp':current_otp},
        beforeSend: function()
              {
               //document.getElementById("submit_otp").blur();
                $('#submit_otp').css('pointer-events','none');
              },
        success:function(res){
          if(res == 'match')
          {
            location.reload();
			$('#profile_form').reset();
            }else{
            $('#mer_otp_err').html('Wrong OTP');
            $('#submit_otp').css('pointer-events','auto');
          }
        }
      });

    }
  }

  $(document).ready(function(){
    createCaptcha('mer');
  });

  /* check captcha */
  function  chk_captcha()
  {
    <?php if( $SHOW_CAPTCHA == 1 ) { ?>

    if($('#enter_captcha_mer').val().trim()  != code && $('#enter_captcha_mer').val().trim() != '')
    {
      $('#captcha_mer_err').html("@lang(Session::get('front_lang_file').'.FRONT_INCORR_CAPTCHA')").css({'color' : 'red'});
      return false;
    }
    else
    {
      $('#captcha_err').html('');
    }
    <?php } ?>
  }
</script>
@endsection
@stop