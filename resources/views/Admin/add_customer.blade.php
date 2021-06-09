@extends('Admin.layouts.default')
@section('PageTitle')
		@if(isset($pagetitle))
			{{$pagetitle}}
		@endif
	@stop
@section('content')

<style>
	
</style>

<!-- MAIN -->
<div class="main">
	<!-- MAIN CONTENT -->
	<div class="main-content">
		<h1 class="page-header">{{$pagetitle}}</h1>
		<div class="container-fluid add-country">
			<div class="row">
	            <div class="container right-container">
					<div class="r-btn">
					</div>
					<div class="col-md-12">
						<div class="location panel">
							
							{{-- Display error message--}}
							@if ($errors->any())
							    <div class="alert alert-warning alert-dismissible">
							    	<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
							        <ul>
							            @foreach ($errors->all() as $error)
							                <li>{{ $error }}</li>
							            @endforeach
							        </ul>
							    </div>
							@endif
							
							{{-- Add/Edit page starts--}}
							<div class="box-body spaced" style="padding:10px">
								<div id="location_form" class="collapse in panel-body">
									{{--Edit page values--}}
									@php $cus_name = $cus_email = $cus_phone = $alt_phone =  $cus_address = $cus_loc = $cus_lat = $cus_lon = $cus_pass = $cus_profile = $cus_id = $login_type = ''; 
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
									$cus_pass = $customer_detail->cus_decrypt_password;
									$cus_profile = $customer_detail->cus_image;
									$cus_id = $customer_detail->cus_id;
									$login_type  = $customer_detail->cus_login_type;
									@endphp
									@endif

									{{--Edit page values--}}
									<div class="">
										@if($id != '' && empty($customer_detail) === false)
										{!! Form::open(['method' => 'post','class' => 'form-auth-small','url' => 'add-update-customer','enctype'=>'multipart/form-data','id'=>'customer_form']) !!}
										{!! Form::hidden('cus_id',$cus_id,['id' => 'customer_id'])!!}
										{!! Form::hidden('login_type',$login_type)!!}
										@else
										{!! Form::open(['method' => 'post','class' => 'form-auth-small','url' => 'add-update-customer','enctype'=>'multipart/form-data','id'=>'customer_form']) !!}
										{!! Form::hidden('cus_id',$cus_id,['id' => 'customer_id'])!!}
										{!! Form::hidden('login_type',$login_type)!!}
										
										@endif
										
										<div class="row panel-heading">
											<label class="col-sm-2">
												
													{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_CUSTOMER_NAME')) ? trans(Session::get('admin_lang_file').'.ADMIN_CUSTOMER_NAME') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_CUSTOMER_NAME')}}&nbsp;*
												
											</label>
											<div class="col-sm-6">
												
												{!! Form::text('cus_name',$cus_name,['class'=>'form-control','id' => 'cus_name','required','maxlength'=>50]) !!}
												<div id="suggesstion-box" class="cntry_box" ><span id="no_country"> </span></div>
											</div>
										</div>
										
										<div class="row panel-heading">
											<label class="col-sm-2">
												
													{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_CUSTOMER_EMAIL')) ? trans(Session::get('admin_lang_file').'.ADMIN_CUSTOMER_EMAIL') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_CUSTOMER_EMAIL')}}&nbsp;*
												
											</label>
											<div class="col-sm-6">
												{!! Form::email('cus_email',$cus_email,['class'=>'form-control','required']) !!}
												{{ Form::hidden('old_email',$cus_email)}}
											</div>
										</div>
										@if($cus_id != '')
										<div class="row panel-heading">
											<label class="col-sm-2">
												
													{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_PASSWORD')) ? trans(Session::get('admin_lang_file').'.ADMIN_PASSWORD') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PASSWORD')}}&nbsp;*
												
											</label>
											<div class="col-sm-6">
												{!! Form::text('cus_pass',$cus_pass,['class'=>'form-control','minlength' => '6','required']) !!}
											</div>
										</div>
										{{ Form::hidden('old_password',$cus_pass)}}
										@endif
										<div class="row panel-heading">
											<label class="col-sm-2">
												
													{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_CUSTOMER_PHONE')) ? trans(Session::get('admin_lang_file').'.ADMIN_CUSTOMER_PHONE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_CUSTOMER_PHONE')}}&nbsp;*
												
											</label>
											<div class="col-sm-6">
												{!! Form::text('cus_phone',$cus_phone,['class'=>'form-control','required','id'=>'cus_phone','onkeyup'=>'validate_phone(\'cus_phone\');']) !!}
												</div>
										</div>

										<div class="row panel-heading">
											<label class="col-sm-2">
												
													{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ALTERNATIVE_NUMBER')) ? trans(Session::get('admin_lang_file').'.ADMIN_ALTERNATIVE_NUMBER') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ALTERNATIVE_NUMBER')}}&nbsp;
												
											</label>
											<div class="col-sm-6">
												{!! Form::text('cus_alt_phone',$alt_phone,['class'=>'form-control','id'=>'cus_alt_phone','onkeyup'=>'validate_phone(\'cus_alt_phone\');']) !!}
											</div>
										</div>

										

										<div class="row panel-heading">
											<label class="col-sm-2">
												
													{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_CUSTOMER_ADDRESS')) ? trans(Session::get('admin_lang_file').'.ADMIN_CUSTOMER_ADDRESS') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_CUSTOMER_ADDRESS')}}&nbsp;*
												
											</label>
											<div class="col-sm-6">
												{!! Form::text('cus_address',$cus_loc,['class'=>'form-control','id' => 'us3-address','required']) !!}
											</div>
										</div>
										<div class="row panel-heading">
											<label class="col-sm-2">
											</label>
											<div class="col-sm-6">
												<div id="us3"></div>
											</div>
										</div>
										<div class="row panel-heading">
											<label class="col-sm-2">
											</label>
											<div class="col-sm-6">
												{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_LATITUDE')) ? trans(Session::get('admin_lang_file').'.ADMIN_LATITUDE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_LATITUDE')}}&nbsp;*
												 
												 {!! Form::text('cus_lat',$cus_lat,['class'=>'form-control','id' => 'us3-lat','required','readonly']) !!}

												 {{(Lang::has(Session::get('admin_lang_file').'.ADMIN_LONGITUDE')) ? trans(Session::get('admin_lang_file').'.ADMIN_LONGITUDE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_LONGITUDE')}}&nbsp;*
												 
												 {!! Form::text('cus_long',$cus_lon,['class'=>'form-control','id' => 'us3-lon','required','readonly']) !!}
											</div>
										</div>
										
										<div class="row panel-heading">
											<label class="col-sm-2">
												
													{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_CUSTOMER_PROFILE')) ? trans(Session::get('admin_lang_file').'.ADMIN_CUSTOMER_PROFILE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_CUSTOMER_PROFILE')}}&nbsp;*
												
											</label>
											<div class="col-sm-6">
												@php $cus_image = null; @endphp
												@if($id != '' && empty($customer_detail) === false)
													{{ Form::file('cus_image',array('class' => 'form-control','accept'=>'image/*')) }}
													
													<p>{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_CUSTOMER_PROFILE_IMG_VAL')) ? trans(Session::get('admin_lang_file').'.ADMIN_CUSTOMER_PROFILE_IMG_VAL') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_CUSTOMER_PROFILE_IMG_VAL')}}</p>
													
													@php 
													$path = url('public/images/noimage/default_user_image.jpg');
													 @endphp
													@if($customer_detail->cus_image != '')
							                            @php
							                            $filename = public_path('images/customer/').$customer_detail->cus_image;
							                            @endphp
							                            
							                            @if(file_exists($filename))
							                              @php 
							                              $path = url('public/images/customer/').'/'.$customer_detail->cus_image; 
							                              	@endphp
							                          
							                            @endif
							                          
							                          @endif
													<img src="{{$path}}" width="50px" height="50px">
												@else
													{{ Form::file('cus_image',array('class' => 'form-control','accept'=>'image/*')) }}
												
													<p>{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_CUSTOMER_PROFILE_IMG_VAL')) ? trans(Session::get('admin_lang_file').'.ADMIN_CUSTOMER_PROFILE_IMG_VAL') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_CUSTOMER_PROFILE_IMG_VAL')}}</p>
												@endif

												
												</div>
										</div>
										
										
										<div class="row panel-heading">
											<div class="col-sm-2"></div>
											<div class="col-sm-6">
											@if($id!='')
												@php $saveBtn = (Lang::has(Session::get('admin_lang_file').'.ADMIN_UPDATE')) ? trans(Session::get('admin_lang_file').'.ADMIN_UPDATE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_UPDATE') @endphp
											@else
												@php $saveBtn=(Lang::has(Session::get('admin_lang_file').'.ADMIN_SAVE')) ? trans(Session::get('admin_lang_file').'.ADMIN_SAVE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SAVE') @endphp
											@endif
											
											{!! Form::submit($saveBtn,['class' => 'btn btn-success'])!!}
											@php $url = url('manage-customer') @endphp
											{!! Form::button('Cancel',['class' => 'btn btn-warning','onclick'=>"javascript:window.location.href='$url'"])!!}
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
		<!-- /.panel-body -->
	</div>
	<!-- END MAIN CONTENT -->
</div>
@section('script')
<link rel="stylesheet" href="<?php echo URL::to('/'); ?>/public/css/intlTelInput.css">
<script>
	$(document).ready(function() {

		var element = document.getElementById('cus_phone');
		if(element.value=='')
		{
			$('#cus_phone').val('{{$default_dial}}');
		}
		
		var element = document.getElementById('cus_alt_phone');
		if(element.value=='')
		{
			$('#cus_alt_phone').val('{{$default_dial}}');
		}
	});
</script>

 <script type="text/javascript" src='https://maps.google.com/maps/api/js?libraries=places&key={{$MAP_KEY}}&language=en'></script>
<script src="{{url('')}}/public/admin/assets/scripts/locationpicker.jquery.min.js"></script>
@if($cus_loc == '')
 <script>
            $('#us3').locationpicker({
                location: {
                    latitude: 46.15242437752303,
                    longitude: 2.7470703125
                },
                radius: 300,
                inputBinding: {
                    latitudeInput: $('#us3-lat'),
                    longitudeInput: $('#us3-lon'),
                    radiusInput: $('#us3-radius'),
                    locationNameInput: $('#us3-address')
                },
                enableAutocomplete: true,
                onchanged: function (currentLocation, radius, isMarkerDropped) {
                    // Uncomment line below to show alert on each Location Changed event
                    //alert("Location changed. New location (" + currentLocation.latitude + ", " + currentLocation.longitude + ")");
                }
            });
 </script>
 @else
  <script>
            $('#us3').locationpicker({
                location: {
                    latitude: <?php echo $cus_lat; ?>,
                    longitude: <?php echo $cus_lon; ?>
                },
                radius: 300,
                inputBinding: {
                    latitudeInput: $('#us3-lat'),
                    longitudeInput: $('#us3-lon'),
                    radiusInput: $('#us3-radius'),
                    locationNameInput: $('#us3-address')
                },
                enableAutocomplete: true,
                onchanged: function (currentLocation, radius, isMarkerDropped) {
                    // Uncomment line below to show alert on each Location Changed event
                    //alert("Location changed. New location (" + currentLocation.latitude + ", " + currentLocation.longitude + ")");
                }
            });
 </script>
 
 @endif
<script type="text/javascript">
	$.validator.addMethod("jsPhoneValidation", function(value, element) { 
		//return value.trim().length != 0; 
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
			cus_pass : {
				required : {
					depends: function(element) {
						if($('input[name=cus_id]').val() != '') { return true; } else{ return false; }
					}
				}
			},
			cus_phone : { jsPhoneValidation : true  },
			/*cus_alt_phone: {
				required: true,
				number: true
			},*/
			cus_address: "required",
			"cus_image": {
				required: {
					depends: function(element) {
						if($('#customer_id').val()==''){ return true; } else { return false; } 
					}
				}
			},
			
		},
		messages: {
			cus_name: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_CUSTOMER_NAME_VAL')) ? trans(Session::get('admin_lang_file').'.ADMIN_CUSTOMER_NAME_VAL') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_CUSTOMER_NAME_VAL')}}",
			cus_email: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTER_EMAIL')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTER_EMAIL') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ENTER_EMAIL')}}",
			cus_phone: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTER_PHONE')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTER_PHONE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ENTER_PHONE')}}",
			//cus_alt_phone: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_CUSTOMER_ALTPHONE_VAL')) ? trans(Session::get('admin_lang_file').'.ADMIN_CUSTOMER_ALTPHONE_VAL') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_CUSTOMER_ALTPHONE_VAL')}}",
			cus_address:  "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_CUSTOMER_ADDR_VAL')) ? trans(Session::get('admin_lang_file').'.ADMIN_CUSTOMER_ADDR_VAL') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_CUSTOMER_ADDR_VAL')}}",
			cus_pass:  "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_CUSTOMER_PASS_VAL')) ? trans(Session::get('admin_lang_file').'.ADMIN_CUSTOMER_PASS_VAL') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_CUSTOMER_PASS_VAL')}}",
			cus_image:  "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_CUSTOMER_IMAGE_VAL')) ? trans(Session::get('admin_lang_file').'.ADMIN_CUSTOMER_IMAGE_VAL') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_CUSTOMER_IMAGE_VAL')}}",
		}
	});
 </script>
 <script src="<?php echo URL::to('/'); ?>/public/js/intlTelInput.js"></script>
   <script type="text/javascript">
    $("#cus_phone").intlTelInput({onlyCountries: ["{{$default_country_code}}"]});
    $("#cus_alt_phone").intlTelInput({onlyCountries: ["{{$default_country_code}}"]});

   </script>
@endsection
@stop