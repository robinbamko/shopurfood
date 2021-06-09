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
                                            
              {{-- Add/Edit page starts--}}
              <div class="box-body spaced" >
                <div id="location_form">
                  {{--Edit page values--}}
                  @php 
					$cus_fname = $cus_lname = $cus_email = $cus_phone = $alt_phone = $cus_loc = $cus_lat = $cus_lon = $cus_id = $cus_zipcode = $sh_building_no =''; 
                  @endphp
                  @if($id != '' && empty($customer_detail) === false)
                    @php 
                      $cus_fname = $customer_detail->sh_cus_fname;
                      $cus_lname = $customer_detail->sh_cus_lname;
                      $cus_email = $customer_detail->sh_cus_email;
                      $cus_phone = $customer_detail->sh_phone1;
                      $alt_phone = $customer_detail->sh_phone2;
                      $cus_loc = $customer_detail->sh_location;
                      $sh_building_no = $customer_detail->sh_building_no;
                      $cus_lat = $customer_detail->sh_latitude;
                      $cus_lon = $customer_detail->sh_longitude;
                      $cus_zipcode = $customer_detail->sh_zipcode;
                      $cus_id = $customer_detail->sh_cus_id;
                    @endphp
                  @endif
                  {{--Edit page values--}}
                  <div class="row-fluid well">
                    
					{!! Form::open(['method' => 'post','class' => 'form-auth-small','url' => 'customer_shipping_update','enctype'=>'multipart/form-data','id'=>'customer_form' ]) !!}
                    {!! Form::hidden('sh_cus_id',$cus_id,['class'=>'form-control']) !!}
                    <div class="row panel-heading">
					
						<div class="col-lg-6 col-md-6">
							<div class="row">
								
								<div class="col-lg-12 col-md-12 form-group">
									<div class="profile-left-label">										
										<label class="panel-title">
										  {{(Lang::has(Session::get('front_lang_file').'.FRONT_FIRST_NAME')) ? trans(Session::get('front_lang_file').'.FRONT_FIRST_NAME') : trans($FRONT_LANGUAGE.'.FRONT_FIRST_NAME')}}&nbsp;*
										</label>									  
									 </div>
									 <div class="profile-right-input">									
										{!! Form::text('cus_fname',$cus_fname,['class'=>'form-control','required']) !!}
									 </div>
								</div>
								
								<div class="col-lg-12 col-md-12 form-group">
									<div class="profile-left-label">										
										<label class="panel-title">
										  {{(Lang::has(Session::get('front_lang_file').'.FRONT_LAST_NAME')) ? trans(Session::get('front_lang_file').'.FRONT_LAST_NAME') : trans($FRONT_LANGUAGE.'.FRONT_LAST_NAME')}}&nbsp;*
										</label>										
									 </div>
									  <div class="profile-right-input">
										
										{!! Form::text('cus_lname',$cus_lname,['class'=>'form-control','required']) !!}
										
									  </div> 
								</div>
								
								
								<div class="col-lg-12 col-md-12 form-group">
									<div class="profile-left-label">
										<label class="panel-title">
										  {{(Lang::has(Session::get('front_lang_file').'.ADMIN_REG_MAIL')) ? trans(Session::get('front_lang_file').'.ADMIN_REG_MAIL') : trans($FRONT_LANGUAGE.'.ADMIN_REG_MAIL')}}&nbsp;*
										</label>
									</div>
									<div class="profile-right-input">
									 {!! Form::text('cus_email',$cus_email,['class'=>'form-control','required','onchange'=>'check_mail_with_otp();','id' => 'new_mail']) !!}
									 {{ Form::hidden('old_mail',$cus_email,['id' => 'old_mail'])}}
									 <span id="new_mail_err" style="color:red"></span>
									</div>
								</div>
								
								<div class="col-lg-12 col-md-12 form-group">
									<div class="profile-left-label">									
									<label class="panel-title">
									  {{(Lang::has(Session::get('front_lang_file').'.ADMIN_CUSTOMER_PHONE')) ? trans(Session::get('front_lang_file').'.ADMIN_CUSTOMER_PHONE') : trans($FRONT_LANGUAGE.'.ADMIN_CUSTOMER_PHONE')}}&nbsp;*
									</label>									
								  </div>
								  <div class="profile-right-input">		
									<!--,'onchange'=>'check_mobile_with_otp();' Hide send otp when phone number got changed-->
									{!! Form::text('prof_cus_phone',$cus_phone,['class'=>'form-control','required','id'=>'prof_cus_phone','onkeyup'=>'validate_phone(\'prof_cus_phone\');']) !!}			
									{{ Form::hidden('old_phone',$cus_phone,['id'=>'old_phone'])}}
									<span id="new_phone_err" style="color:red"></span>						
								  </div>
								</div>
								
								<div class="col-lg-12 col-md-12 form-group">
									<div class="profile-left-label">									
									<label class="panel-title">
									  {{(Lang::has(Session::get('front_lang_file').'.ADMIN_ALTERNATIVE_NUMBER')) ? trans(Session::get('front_lang_file').'.ADMIN_ALTERNATIVE_NUMBER') : trans($FRONT_LANGUAGE.'.ADMIN_ALTERNATIVE_NUMBER')}}&nbsp;
									</label>									
									</div>
								  <div class="profile-right-input">									
									{!! Form::text('cus_alt_phone',$alt_phone,['class'=>'form-control','id'=>'cus_alt_phone','onkeyup'=>'validate_phone(\'cus_alt_phone\');']) !!}									
								  </div>
								</div>
								
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
								
								<div class="col-lg-12 col-md-12 form-group">
									<div class="profile-left-label">									
									<label class="panel-title">
									  {{(Lang::has(Session::get('front_lang_file').'.LANDMARK')) ? trans(Session::get('front_lang_file').'.LANDMARK') : trans($FRONT_LANGUAGE.'.LANDMARK')}}
									</label>									
								  </div>
								  <div class="profile-right-input">		
									@php $landmark_placeholder = (Lang::has(Session::get('front_lang_file').'.LANDMARK_PLACEHOLDER')) ? trans(Session::get('front_lang_file').'.LANDMARK_PLACEHOLDER') : trans($FRONT_LANGUAGE.'.LANDMARK_PLACEHOLDER'); @endphp
									{!! Form::text('sh_building_no',$sh_building_no,['class'=>'form-control','id' => 'sh_building_no','placeholder'=>$landmark_placeholder]) !!}									
								  </div>
								</div>
							</div>
						</div>
						
						<div class="col-lg-6 col-md-6">
							<div class="row">
								
								<div class="col-lg-12 col-md-12 form-group">
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
								
								<div class="col-lg-12 col-md-12 form-group">
									<div class="profile-left-label">									
									<label class="panel-title">
									  {{(Lang::has(Session::get('front_lang_file').'.FRONT_ZIPCODE')) ? trans(Session::get('front_lang_file').'.FRONT_ZIPCODE') : trans($FRONT_LANGUAGE.'.FRONT_ZIPCODE')}}&nbsp;*
									</label>								  
								  </div>
								  <div class="profile-right-input">									
									{!! Form::text('sh_zipcode',$cus_zipcode,['class'=>'form-control','required','id'=>'']) !!}
									
								  </div>
								</div>
							
							</div>
						</div>
						<div class="col-lg-12 col-sm-12 col-md-12 col-xs-12"> 
							<div class="form-group formBtn">
								@if(empty($customer_detail) === false)
									@php $saveBtn = (Lang::has(Session::get('front_lang_file').'.ADMIN_UPDATE')) ? trans(Session::get('front_lang_file').'.ADMIN_UPDATE') : trans($FRONT_LANGUAGE.'.ADMIN_UPDATE') @endphp
									{!! Form::button($saveBtn,['class' => 'btn btn-success','onclick'=>'funValidate()'])!!}
								@else
									@php $saveBtn=(Lang::has(Session::get('front_lang_file').'.ADMIN_SAVE')) ? trans(Session::get('front_lang_file').'.ADMIN_SAVE') : trans($FRONT_LANGUAGE.'.ADMIN_SAVE') @endphp
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

{{-- mail verify modal --}}
      <div id="mail_verify_modal" class="modal fade" role="dialog">
        <div class="modal-dialog">
          <div class="modal-content">
            
            <div class="modal-body">              
              
              <p><b>@lang(Session::get('front_lang_file').'.ADMIN_OTP_SEND_INMAIL')</b> <br>  @lang(Session::get('front_lang_file').'.ADMIN_PLEASE_ENTER_VERIFY_MAIL')</p><br>
              <div class="verify-mail-left">
              {{ Form::text('code','',['class' => 'form-control','required','placeholder' => __(Session::get('front_lang_file').'.ADMIN_PLEASE_ENTER_OTP'),'required','id' => 'enter_code'])}}
              <span id="code_err" style="font-size: 14px;"></span></div>
              <div class="verify-mail-right">
              {{ Form::submit(__(Session::get('front_lang_file').'.ADMIN_SUBMIT_OTP'),['class' => 'btn btn-success','onClick' =>'return chk_mail_otp()'])}} 
              {{ Form::button(__(Session::get('front_lang_file').'.FRONT_CANCEL'),['class' => 'btn btn-success','onClick' =>'javascript:window.location.reload();'])}}
          	  </div>
              
                
              <div class="show_code"></div>            
            </div>
            
          </div>
          
        </div>
      </div>
{{-- Mobile verification mail --}}
<div id="mobile_verify_modal" class="modal fade" role="dialog">
        <div class="modal-dialog">
          <div class="modal-content">
            
            <div class="modal-body">              
              
              <p><b>@lang(Session::get('front_lang_file').'.FRONT_MOB_VERIFY_MSG')</b></p>
              <div class="verify-mail-left">
              {{ Form::text('code','',['class' => 'form-control','required','placeholder' => __(Session::get('front_lang_file').'.ADMIN_PLEASE_ENTER_OTP'),'required','id' => 'enter_code_mobile'])}}
              <span id="code_err_mob" style="font-size: 14px;color:red"></span></div>
              <div class="verify-mail-right">
              {{ Form::submit(__(Session::get('front_lang_file').'.ADMIN_SUBMIT_OTP'),['class' => 'btn btn-success','onClick' =>'return chk_mob_otp()'])}} 
              {{ Form::button(__(Session::get('front_lang_file').'.FRONT_CANCEL'),['class' => 'btn btn-success','onClick' =>'javascript:window.location.reload();'])}}
          	  </div>             
              
              <div class="show_code_mobile"></div>            
            </div>
            
          </div>
          
        </div>
      </div>
@section('script')
  <link rel="stylesheet" href="<?php echo URL::to('/'); ?>/public/css/intlTelInput.css">
  <script>
    $(document).ready(function() {
      
      var element = document.getElementById('prof_cus_phone');
      if(element.value=='')
      {
        $('#prof_cus_phone').val('{{$default_dial}}');
      }
      
      var element = document.getElementById('cus_alt_phone');
      if(element.value=='')
      {
        $('#cus_alt_phone').val('{{$default_dial}}');
      }
    });

    	function check_mobile_with_otp()
		{

         var cus_phone1 = $('#prof_cus_phone').val();
         var old_phone1 = $('#old_phone').val();
         $('#new_phone_err').html("");
         $('#code_err_mob').html("");
         if(cus_phone1 == '')
         {
         	$('#new_phone_err').html("@lang(Session::get('front_lang_file').'.ADMIN_ENTER_PHONE')");
         }
         if(old_phone1 != cus_phone1)
         {
         	$('#mobile_verify_modal').modal({backdrop: 'static',keyboard: false });
         	$.ajax({
					'type' : 'POST',
					'url' : '{{ url('send_verification_msg') }}',
					'data'  : {'_token' : '{{csrf_token()}}','phone' : cus_phone1},
					success:function(response)
					{
						var data = response.split('`');
						if(data[0] == "success")
						{
							$('.show_code_mobile').html("<input type='hidden' name='verify_code' value='"+data[1]+"' id='verify_code_mobile'>");
						}
						else if(data[0] == "fail")           
						{
							$('#code_err_mob').html("@lang(Session::get('front_lang_file').'.FRONT_TWILIO_ERR')");
							//$('#mobile_verify_modal').modal('hide');
							setTimeout(function() { $('#mobile_verify_modal').modal('hide'); window.location.reload();}, 7000);
							
						}
					},
					error: function(xhr, status, error) {
						var err = eval("(" + xhr.responseText + ")");
						alert('@lang(Session::get('front_lang_file').'.FRONT_AJAX_ERROR')\n'+err.message);
					}
				});
         }
       }

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
				$('#mail_verify_modal').modal({backdrop: 'static',keyboard: false });
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
          $('#code_err').html("@lang(Session::get('front_lang_file').'.ADMIN_PLEASE_ENTER_OTP')").css({'color':'red'});
          return false;
        }
        else if(added_code.trim() != enter_code.trim())
        {
        	$('#enter_code').css({'border' : '1px solid red'});
	          $('#code_err').html("@lang(Session::get('front_lang_file').'.FRONT_INCORRECT_OTP')").css({'color':'red'});
	          return false;
        }
        else if(added_code.trim() == enter_code.trim())
        {
          $('#mail_verify_modal').modal('hide');
          $('#new_mail_err').html("@lang(Session::get('front_lang_file').'.FRONT_VR_SUCCESS')").css({'color':'green'});
            return true;
        }
       }

       /* check mobile otp */
       function chk_mob_otp()
       {
        var added_code = document.getElementById('verify_code_mobile').value;
        var enter_code = document.getElementById('enter_code_mobile').value;
        //alert(added_code); return false;
        if(enter_code.trim() == '')
        {
          $('#enter_code').css({'border' : '1px solid red'});
          $('#code_err_mob').html("@lang(Session::get('front_lang_file').'.ADMIN_PLEASE_ENTER_OTP')").css({'color':'red'});
          return false;
        }
        else if(added_code.trim() != enter_code.trim())
        {
        	$('#enter_code').css({'border' : '1px solid red'});
	          $('#code_err_mob').html("@lang(Session::get('front_lang_file').'.FRONT_INCORRECT_OTP')").css({'color':'red'});
	          return false;
        }
        else if(added_code.trim() == enter_code.trim())
        {
          $('#mobile_verify_modal').modal('hide');
          $('#new_phone_err').html("@lang(Session::get('front_lang_file').'.FRONT_PH_VERIFIED')").css({'color':'green'});
            return true;
        }
       }
  </script>
  <script>
   var map;

    function initialize_shipping()
    {
    	var sr_lat = {{ (Session::has('search_location')== '1') ?  Session::get('search_latitude'): $ip_latitude }};
    	var sr_lon = {{ (Session::has('search_location')== '1') ?  Session::get('search_longitude'): $ip_longitude }};
       var ip_lat;
    	var ip_long;
    	<?php if($cus_loc == '')
    	{ ?>
    		ip_lat = sr_lat;
    		ip_long = sr_lon;
    	<?php }
    	else
    		{ ?>
    			ip_lat = {{$cus_lat}};
    			ip_long = {{$cus_lon}};
    		<?php }  ?>
        var myLatlng = new google.maps.LatLng(ip_lat,ip_long);
        var mapOptions = {
			           		zoom 			: 15,
			                center 			: myLatlng,
			                disableDefaultUI: true,
			                panControl 		: true,
			                zoomControl 	: true,
			                mapTypeControl	: true,
			                streetViewControl: true,
			                mapTypeId 		: google.maps.MapTypeId.ROADMAP,
			                fullscreenControl: true
       					 };
        map = new google.maps.Map(document.getElementById('us3'),mapOptions);
	    var marker = new google.maps.Marker({position : myLatlng,
									        map 	  : map,
									        draggable :true,    
										    }); 
        google.maps.event.addListener(marker, 'dragend', function(e) 
        {
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
            else {
                map.setCenter(place.geometry.location); 
                map.setZoom(17);
            }
            $('#us3-lat').val(lat);
            $('#us3-lon').val(lng);
        });
   }
  //initialize_shipping();
    google.maps.event.addDomListener(window, 'load', initialize_shipping);

    function funValidate(){
			var current_address = $('#us3-address-new').val();
			var old_address = '{{$cus_loc}}';
			var current_lat = $('#us3-lat').val();
			var old_lat = '{{$cus_lat}}';
			var current_long = $('#us3-lon').val();
			var old_long = '{{$cus_lon}}';
			if((current_address!=old_address) || (current_lat!=old_lat) || (current_long!=old_long)){
				var confirmMessage = "@lang(Session::get('front_lang_file').'.YOUR_CART_WILL_EMPTY')";
				if(confirm(confirmMessage)){
					/*var cus_fname 		= $("input[name=cus_fname]").val();
					var cus_lname 		= $("input[name=cus_lname]").val();
					var cus_email 		= $("input[name=cus_email]").val();
					var prof_cus_phone	= $("input[name=prof_cus_phone]").val();
					var cus_alt_phone 	= $("input[name=cus_alt_phone]").val();
					var cus_address 	= $("input[name=cus_address]").val();
					var sh_building_no 	= $("input[name=sh_building_no]").val();
					var cus_lat 		= $("input[name=cus_lat]").val();
					var cus_long 		= $("input[name=cus_long]").val();
					var sh_zipcode 		= $("input[name=sh_zipcode]").val();
					alert(cus_fname+'\n'+cus_lname+'\n'+cus_email+'\n'+prof_cus_phone+'\n'+cus_alt_phone+'\n'+cus_address+'\n'+sh_building_no+'\n'+cus_lat+'\n'+cus_long+'\n'+sh_zipcode);*/
					//return false;
					$.ajax({
						type:'post',
						url :"<?php echo url("clearcart"); ?>",
						data:{},
						success:function(response){
							$('#customer_form').submit();
						}
					});
				}else{
					window.location.reload();
					return false;
				}
						  
			}else{
				$('#customer_form').submit();
			}
		}
  </script>
 <script src="{{url('')}}/public/admin/assets/scripts/locationpicker.jquery.min.js"></script>
  
    <script src="<?php echo URL::to('/'); ?>/public/js/intlTelInput.js"></script>
     <script type="text/javascript">
      $("#prof_cus_phone").intlTelInput({onlyCountries: ["{{$default_country_code}}"]});
      $("#cus_alt_phone").intlTelInput({onlyCountries: ["{{$default_country_code}}"]});

     </script>
@endsection
@stop

