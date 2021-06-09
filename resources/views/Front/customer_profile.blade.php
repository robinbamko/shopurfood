@extends('Front.layouts.default')
  @section('content')
  
  
  <style type="text/css">
    .row .panel-heading{
      margin-bottom: 10px;
    }
  </style>    
		
		<div class="profile-sidebar">
			<div class="container">
				<div class="row">
					<div class="col-lg-12 col-sm-12 col-md-12 col-xs-12 profile-sidebar-sec">
						<!-- Sidebar -->
						@include('Front.includes.profile_sidebar')
						<!-- Sidebar -->
					</div>
				</div>
			</div>
		</div>
		
          <div class="section9-inner">
              <div class="container userContainer">
                <div class="row">  
					<!--<div class="col-lg-12">
						<h5 class="sidebar-head">             
								@lang(Session::get('front_lang_file').'.FRONT_MY_ACCOUNT')
						</h5>
					</div>-->
			<div class="userContainer-bg row">                  
				  
                <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12 section9-inner-div"> 
					
              {{-- Display error message--}}
                                      
              {{-- Add/Edit page starts--}}
              <div class="box-body spaced" >
                <div id="location_form">
                  {{--Edit page values--}}
                  @php $cus_name = $cus_email = $cus_phone = $alt_phone =  $cus_address = $cus_loc = $cus_lat = $cus_lon = $cus_pass = $cus_profile = $cus_id =''; 
                  @endphp



                  @if($id != '' && empty($customer_detail) === false)
                  @php 
                      $cus_name = $customer_detail->cus_fname;
                      $cus_email = $customer_detail->cus_email;
                      $cus_phone = $customer_detail->cus_phone1;
                      $alt_phone = $customer_detail->cus_phone2;
                      $cus_loc = $customer_detail->cus_address;
                      $cus_lat = $customer_detail->cus_latitude;
                      $cus_lon = $customer_detail->cus_longitude;
                      $cus_pass = $customer_detail->cus_password;
                      $cus_profile = $customer_detail->cus_image;
                      $cus_id = $customer_detail->cus_id;
                  @endphp
                  @endif
                  {{--Edit page values--}}
                  <div class="row-fluid well">
                    @if($id != '' && empty($customer_detail) === false)
                    {!! Form::open(['method' => 'post','class' => 'form-auth-small','url' => 'customer_profile_update','enctype'=>'multipart/form-data','id'=>'customer_form']) !!}
                    
                    @else
                    {!! Form::open(['method' => 'post','class' => 'form-auth-small','url' => 'customer_profile_update','enctype'=>'multipart/form-data','id'=>'customer_form']) !!}
                    
                    @endif
                    <div class="row panel-heading">
						<div class="col-lg-6 col-md-6">
						 <div class="row">
						 
						<div class="col-lg-12 col-md-12 form-group">				
						
                        <div class="profile-left-label">
                        <label class="panel-title">
                          {{(Lang::has(Session::get('front_lang_file').'.ADMIN_CUSTOMER_PROFILE')) ? trans(Session::get('front_lang_file').'.ADMIN_CUSTOMER_PROFILE') : trans($FRONT_LANGUAGE.'.ADMIN_CUSTOMER_PROFILE')}}&nbsp;*
                        </label>
                        </div>
                      
                      
                        <div class="profile-right-input profilePic">
                          <div>
                            @php $cus_image = null; @endphp
                            <label class="ovrLay">
                              <i class="fa fa-pencil" aria-hidden="true"></i>
                            
                                                
                        @if($id != '' && empty($customer_detail) === false)
                          {{ Form::file('cus_image',array('class' => 'form-control','accept'=>'image/*')) }}   
                          </label>                       
                          <!-- Image -->
                          @if($customer_detail->cus_image != '')
                            @php
                            $filename = public_path('images/customer/').$customer_detail->cus_image;
                            @endphp
                            
                            @if(file_exists($filename))
                              <img src="{{ url('public/images/customer/'.$customer_detail->cus_image )}}" >
                            @else
                              <img src="{{url('public/images/noimage/default_user_image.jpg')}}" >
                            @endif
                          @else
                              <img src="{{url('public/images/noimage/default_user_image.jpg')}}" >
                          @endif
                          </div>
                          <p>@lang(Session::get('front_lang_file').'.CUS_PROFILE_SIZE')</p>
                          <!-- End Image -->
                        @else
                          {{ Form::file('cus_image',array('class' => 'form-control','accept'=>'image/*')) }}
                          <p>@lang(Session::get('front_lang_file').'.CUS_PROFILE_SIZE')</p>
                        @endif
                      </div>                       
                      
					
					</div>
					
					
					<div class="col-lg-12 col-md-12 form-group">					
                      <div class="profile-left-label">                        
                        <label class="panel-title">
                          {{(Lang::has(Session::get('front_lang_file').'.ADMIN_CUSTOMER_NAME')) ? trans(Session::get('front_lang_file').'.ADMIN_CUSTOMER_NAME') : trans($FRONT_LANGUAGE.'.ADMIN_CUSTOMER_NAME')}}&nbsp;*
                        </label>                      
                      </div>
                      <div class="profile-right-input">                       
                        {!! Form::text('cus_name',$cus_name,['class'=>'form-control','id' => 'cus_name','required','maxlength'=>50]) !!}
                        <div id="suggesstion-box" class="cntry_box" ><span id="no_country"> </span>
                        </div>
                      </div>					  
					</div>
					
					<div class="col-lg-12 col-md-12 form-group">					
                      
                        <div class="profile-left-label">
                        <label class="panel-title">
                          {{(Lang::has(Session::get('front_lang_file').'.ADMIN_CUSTOMER_PHONE')) ? trans(Session::get('front_lang_file').'.ADMIN_CUSTOMER_PHONE') : trans($FRONT_LANGUAGE.'.ADMIN_CUSTOMER_PHONE')}}&nbsp;*
                        </label>
                        </div>
                      
                      
                     
                        <div class="profile-right-input">
						<!--,'onchange'=>'check_mobile_with_otp();'-->
                        {!! Form::text('cus_phone1',$cus_phone,['class'=>'form-control','required','id'=>'cus_phone1','maxlength'=>15,'onkeyup'=>'validate_phone(\'cus_phone1\');']) !!}
                        </div>
                      
                     
					    <div class="show_ph_msg"></div>
					  </div>
					  
					  <div class="col-lg-12 col-md-12 form-group">
					
                      
                        <div class="profile-left-label">
                        <label class="panel-title">
                          {{(Lang::has(Session::get('front_lang_file').'.ADMIN_ALTERNATIVE_NUMBER')) ? trans(Session::get('front_lang_file').'.ADMIN_ALTERNATIVE_NUMBER') : trans($FRONT_LANGUAGE.'.ADMIN_ALTERNATIVE_NUMBER')}}&nbsp;
                        </label>
                        </div>
                      
                      
                        <div class="profile-right-input">
                        {!! Form::text('cus_phone2',$alt_phone,['class'=>'form-control','id'=>'cus_phone2','maxlength'=>15,'onkeyup'=>'validate_phone(\'cus_phone2\');']) !!}
                        </div>
                      
					  
					  </div>
					  
					  <div class="col-lg-12 col-md-12 form-group">
					
                      
                        <div class="profile-left-label">
                        <label class="panel-title">
                          {{(Lang::has(Session::get('front_lang_file').'.ADMIN_CUSTOMER_EMAIL')) ? trans(Session::get('front_lang_file').'.ADMIN_CUSTOMER_EMAIL') : trans($FRONT_LANGUAGE.'.ADMIN_CUSTOMER_EMAIL')}}&nbsp;*
                        </label>
                        </div>
                      
                      
                        <div class="profile-right-input">
                      <!--   {!! Form::email('cus_email',$cus_email,['class'=>'form-control','required']) !!} -->

                           {!! Form::email('cus_email',$cus_email,['class'=>'form-control','required','onchange'=>'check_mail_with_otp();','id' => 'new_mail']) !!}

                           {{ Form::hidden('old_mail',$cus_email,['id' => 'old_mail'])}}

                            <span id="new_mail_err" style="color:red"></span>


                        </div>
                      
					  
					  </div>
					  
					</div>
						</div>
						<div class="col-lg-6 col-md-6">
							<div class="row">
							
							<div class="col-lg-12 col-md-12 form-group">						
							  
								<div class="profile-left-label">
								<label class="panel-title">
								  {{(Lang::has(Session::get('front_lang_file').'.ADMIN_CUSTOMER_ADDRESS')) ? trans(Session::get('front_lang_file').'.ADMIN_CUSTOMER_ADDRESS') : trans($FRONT_LANGUAGE.'.ADMIN_CUSTOMER_ADDRESS')}}&nbsp;*
								</label>
								</div>
							  
							  
								<div class="profile-right-input">
								{!! Form::text('cus_address',$cus_loc,['class'=>'form-control','id' => 'us3-address-new','required']) !!}
								</div>
							</div>
							  
							<div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
								<div class="form-group">
								<div id="us3" style="width: 100%; height: 250px;"></div>
								</div>
							</div>
							  
							  
							  
							  <div class="col-lg-12 col-md-12 form-group">
								
								  
									<div class="profile-left-label">
									<label class="panel-title">
									{{(Lang::has(Session::get('front_lang_file').'.ADMIN_LATITUDE')) ? trans(Session::get('front_lang_file').'.ADMIN_LATITUDE') : trans($FRONT_LANGUAGE.'.ADMIN_LATITUDE')}}&nbsp;*
									</label>
									</div>
								  
								  
									<div class="profile-right-input">                         
									 {!! Form::text('cus_lat',$cus_lat,['class'=>'form-control','id' => 'us3-lat','required','readonly']) !!}
									</div>                         
								  
								 </div>
								<div class="col-lg-12 col-md-12 form-group"> 
									<div class="profile-left-label">
									<label class="panel-title">
									 {{(Lang::has(Session::get('front_lang_file').'.ADMIN_LONGITUDE')) ? trans(Session::get('front_lang_file').'.ADMIN_LONGITUDE') : trans($FRONT_LANGUAGE.'.ADMIN_LONGITUDE')}}&nbsp;*
									 </label>
									</div>
								  
								 
									<div class="profile-right-input">
									 {!! Form::text('cus_long',$cus_lon,['class'=>'form-control','id' => 'us3-lon','required','readonly']) !!}
									</div>
								  
								  
								  </div>
						
							</div>
						</div>
						
						
						
					
                    <!--<div class="row panel-heading"> -->
					
                      <div class="panel-heading row">
                      <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12 mx-auto">
                        <div class="formBtn">
                      @if($id!='')
                        @php $saveBtn = (Lang::has(Session::get('front_lang_file').'.ADMIN_UPDATE')) ? trans(Session::get('front_lang_file').'.ADMIN_UPDATE') : trans($FRONT_LANGUAGE.'.ADMIN_UPDATE') @endphp
                      @else
                        @php $saveBtn=(Lang::has(Session::get('front_lang_file').'.ADMIN_SAVE')) ? trans(Session::get('front_lang_file').'.ADMIN_SAVE') : trans($FRONT_LANGUAGE.'.ADMIN_SAVE') @endphp
                      @endif
                      
                      {!! Form::submit($saveBtn,['class' => 'btn btn-success'])!!}
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
			</div>
				    {{-- mail verify modal --}}
      <div id="mail_verify_modal" class="modal fade" role="dialog">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal">&times;</button>             
            </div>
            <div class="modal-body">              
              
              <p><b>@lang(Session::get('front_lang_file').'.ADMIN_OTP_SEND_INMAIL')</b> <br>  @lang(Session::get('front_lang_file').'.ADMIN_PLEASE_ENTER_VERIFY_MAIL')</p><br>
              <div class="verify-mail-left">
              {{ Form::text('code','',['class' => 'form-control','required','placeholder' => __(Session::get('front_lang_file').'.ADMIN_PLEASE_ENTER_OTP'),'required','id' => 'enter_code'])}}
              <span id="code_err" style="font-size: 14px;"></span></div>
              <div class="verify-mail-right">
              {{ Form::submit(__(Session::get('front_lang_file').'.ADMIN_SUBMIT_OTP'),['class' => 'btn btn-success','onClick' =>'return chk_mail_otp()'])}} </div>
              
              {{-- <p>@lang(Session::get('front_lang_file').'.ADMIN_DID_YOU_RECEIVE_MAIL') <a href="#" onClick = "send_verify_mail()" style="color: #7f1900;font-size:13px;">@lang(Session::get('front_lang_file').'.ADMIN_CLICK_RESEND')</a></p> --}}  
              <div class="show_code"></div>            
            </div>
            
          </div>
          
        </div>
      </div>
@section('script')
  <link rel="stylesheet" href="<?php echo URL::to('/'); ?>/public/css/intlTelInput.css">
  <script>
    $(document).ready(function() {
        //tryGeolocation();

      var element = document.getElementById('cus_phone1');
      if(element.value=='')
      {
        $('#cus_phone1').val('{{$default_dial}}');
      }
      
      var element = document.getElementById('cus_phone2');
      if(element.value=='')
      {
        $('#cus_phone2').val('{{$default_dial}}');
      }
    });
  </script>


  <script>
   var map;

    function initialize_profile() 
    {
        var ip_lat = {{ ($cus_loc == '') ?  $ip_latitude : $cus_lat }};
        var ip_long = {{ ($cus_loc == '') ?  $ip_longitude : $cus_lon }};
        var myLatlng = new google.maps.LatLng(ip_lat,ip_long);
		geocoder = new google.maps.Geocoder();
        var mapOptions = {
                          zoom         : 15,
                          center        : myLatlng,
                          disableDefaultUI: true,
                          panControl    : true,
                          zoomControl   : true,
                          mapTypeControl: true,
                          streetViewControl: true,
                          mapTypeId     : google.maps.MapTypeId.ROADMAP,
                          fullscreenControl: true
                        };
        map = new google.maps.Map(document.getElementById('us3'),mapOptions);
        var marker = new google.maps.Marker({position: myLatlng,
                                              map     : map,
                                              draggable:true,    
                                          }); 
        google.maps.event.addListener(marker, 'dragend', function(e) 
        {
			geocoder.geocode({'latLng': marker.getPosition()}, function(results, status) {
				if (status == google.maps.GeocoderStatus.OK) {
					if (results[0]) {
						$('#us3-address-new').val(results[0].formatted_address);
					}
				}
			});
             var lat = this.getPosition().lat();
             var lng = this.getPosition().lng();
			 $('#us3-lat').val(lat);
            $('#us3-lon').val(lng);
        });
        var input = document.getElementById('us3-address-new');
        var autocomplete = new google.maps.places.Autocomplete(input);
        autocomplete.bindTo('bounds', map);
        google.maps.event.addListener(autocomplete, 'place_changed', function ()
        {
            var place = autocomplete.getPlace();
            var lat = place.geometry.location.lat();
            var lng = place.geometry.location.lng();
            if (place.geometry.viewport) 
            {
                map.fitBounds(place.geometry.viewport);
                var myLatlng = place.geometry.location; 
                var latlng = new google.maps.LatLng(lat, lng);
                marker.setPosition(latlng);

            } 
            else 
            {
                map.setCenter(place.geometry.location); 
                map.setZoom(17);
            }
            $('#us3-lat').val(lat);
            $('#us3-lon').val(lng);
        });
  }
  //initialize_profile();
  google.maps.event.addDomListener(window, 'load', initialize_profile);
  </script>
  
    <script src="<?php echo URL::to('/'); ?>/public/js/intlTelInput.js"></script>
     <script type="text/javascript">
      $("#cus_phone1").intlTelInput({onlyCountries: ["{{$default_country_code}}"]});
      $("#cus_phone2").intlTelInput({onlyCountries: ["{{$default_country_code}}"]});

     </script>
     <script type="text/javascript">
      $('#customer_form').on('keyup keypress', function(e) {
        var keyCode = e.keyCode || e.which;
        if (keyCode === 13) { 
          e.preventDefault();
          return false;
        }
      });
        function check_mobile_with_otp()
		{

         var cus_phone1 = $('#cus_phone1').val();
         $.ajax({
            type: "get",
            url:"<?php echo url('check_mobile_num_is_new'); ?>",
            data:{'cus_phone1':cus_phone1},
           
            success:function(res)
            {
                var obj = jQuery.parseJSON(res);
                if(obj.msg == 'New')
                {
                    $('#otpMobileChangemyModal').modal({
                      show: true,
                      keyboard: false,
                      backdrop: 'static'
                    });
                    $("#mobile_otp_sec").html('<input type="hidden" id="mobile_current_otp" value="'+obj.otp+'">');
                }else{
                  alert(obj.msg);
                  location.reload();
                }
            },
      error: function(xhr, status, error) {
        var err = eval("(" + xhr.responseText + ")");
        alert('@lang(Session::get('front_lang_file').'.FRONT_AJAX_ERROR')\n'+err.message);
      }
         });
       }

      var apiGeolocationSuccess = function(position) {
          alert("API geolocation success!\n\nlat = " + position.coords.latitude + "\nlng = " + position.coords.longitude);
      };

      var tryAPIGeolocation = function() {
      jQuery.post( "https://www.googleapis.com/geolocation/v1/geolocate?key=\n" +
          "AIzaSyASlJOxGv4MzRN1B8uN9AJlPl_MFNvDQjg", function(success) {
              apiGeolocationSuccess({coords: {latitude: success.location.lat, longitude: success.location.lng}});
          })
              .fail(function(err) {
                  alert("API Geolocation error! \n\n"+err);
              });
      };

      var browserGeolocationSuccess = function(position) {
          alert("Browser geolocation success!\n\nlat = " + position.coords.latitude + "\nlng = " + position.coords.longitude);
      };

      var browserGeolocationFail = function(error) {
          switch (error.code) {
              case error.TIMEOUT:
                  alert("Browser geolocation error !\n\nTimeout.");
                  break;
              case error.PERMISSION_DENIED:
                  if(error.message.indexOf("Only secure origins are allowed") == 0) {
                      tryAPIGeolocation();
                  }
                  break;
              case error.POSITION_UNAVAILABLE:
                  alert("Browser geolocation error !\n\nPosition unavailable.");
                  break;
          }
      };

      var tryGeolocation = function() {
          if (navigator.geolocation) {
              navigator.geolocation.getCurrentPosition(
                  browserGeolocationSuccess,
                  browserGeolocationFail,
                  {maximumAge: 50000, timeout: 20000, enableHighAccuracy: true});
          }
      };


        /*  send mail with otp */
		function check_mail_with_otp()
		{
			var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
			var old = $('#old_mail').val();
			var new_mail = $('#new_mail').val();
			$('#new_mail_err').html("");
			if(new_mail.trim() == '')
			{
				//$('#new_mail_err').html("@lang(Session::get('front_lang_file').'.FRONT_EN_VA_MAIL')");
				return false;
			}
			else if ((reg.test(new_mail) == false)) {
                //$('#new_mail_err').html("@lang(Session::get('front_lang_file').'.FRONT_EN_VA_MAIL')");
                return false;
            }
			else if(new_mail.trim() != old.trim())
			{  
				$('#mail_verify_modal').modal('show');
				$.ajax({
					'type' : 'POST',
					'url' : '{{ url('send_verification_mail') }}',
					'data'  : {'_token' : '{{csrf_token()}}','mail' : new_mail},
					success:function(response)
					{
						var data = response.split('`');
						if(data[0] == "success")
						{
							$('.show_code').html("<input type='hidden' name='verify_code' value='"+data[1]+"' id='verify_code'>");
						}           
					},
					error: function(xhr, status, error) {
						var err = eval("(" + xhr.responseText + ")");
						alert('@lang(Session::get('front_lang_file').'.FRONT_AJAX_ERROR')\n'+err.message);
					}
				});
			}
		}
       /* check mail otp */
       function chk_mail_otp()
       {
        var added_code = document.getElementById('verify_code').value;
        var enter_code = document.getElementById('enter_code').value;
        //alert(added_code); return false;
        if(enter_code.trim() == '')
        {
          $('#enter_code').css({'border' : '1px solid red'});
          $('#code_err').html("Enter code").css({'color':'red'});
          return false;
        }
        else if(added_code.trim() == enter_code.trim())
        {
          $('#mail_verify_modal').modal('hide');
          $('#new_mail_err').html("@lang(Session::get('front_lang_file').'.FRONT_VR_SUCCESS')").css({'color':'green'});
            return true;
        }
       }
	   $.validator.addMethod("jsPhoneValidation", function(value, element) { 
		var defaultDial = '{{Config::get('config_default_dial')}}';
		return value.substr(0, (defaultDial.trim().length)) != value.trim()
	}, "No space please and don't leave it empty");
	   $("#customer_form").validate({
		//onkeyup: true,
		onfocusout: function (element) {
			this.element(element);
		},
		rules: {
			cus_name: "required",
			cus_email: {
				required: true,
				email: true
			},
			cus_phone1 : { jsPhoneValidation : true  },
			cus_phone2 : { jsPhoneValidation : true  },
			cus_address :"required",
		},
		messages: {
			cus_name: "{{(Lang::has(Session::get('front_lang_file').'.ADMIN_CUSTOMER_NAME_VAL')) ? trans(Session::get('front_lang_file').'.ADMIN_CUSTOMER_NAME_VAL') : trans($FRONT_LANGUAGE.'.ADMIN_CUSTOMER_NAME_VAL')}}",
			cus_email: "{{(Lang::has(Session::get('front_lang_file').'.ADMIN_ENTER_EMAIL')) ? trans(Session::get('front_lang_file').'.ADMIN_ENTER_EMAIL') : trans($FRONT_LANGUAGE.'.ADMIN_ENTER_EMAIL')}}",
			cus_phone1: "{{(Lang::has(Session::get('front_lang_file').'.ADMIN_ENTER_PHONE')) ? trans(Session::get('front_lang_file').'.ADMIN_ENTER_PHONE') : trans($FRONT_LANGUAGE.'.ADMIN_ENTER_PHONE')}}",
			cus_phone2: "{{(Lang::has(Session::get('front_lang_file').'.ADMIN_CUSTOMER_ALTPHONE_VAL')) ? trans(Session::get('front_lang_file').'.ADMIN_CUSTOMER_ALTPHONE_VAL') : trans($FRONT_LANGUAGE.'.ADMIN_CUSTOMER_ALTPHONE_VAL')}}",
			cus_address: "{{(Lang::has(Session::get('front_lang_file').'.ADMIN_CUSTOMER_ADDR_VAL')) ? trans(Session::get('front_lang_file').'.ADMIN_CUSTOMER_ADDR_VAL') : trans($FRONT_LANGUAGE.'.ADMIN_CUSTOMER_ADDR_VAL')}}"

			
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
     </script>
@endsection
@stop