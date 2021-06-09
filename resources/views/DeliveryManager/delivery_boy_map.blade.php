@extends('DeliveryManager.layouts.default')
@section('pagetitle')
	{{$pagetitle}}
@endsection
@section('content')


	<style>
	.isa_info, .isa_success, .isa_warning, .isa_error {
	margin: 10px 0px;
	padding:12px;
	 
	}
	.isa_info {
		color: #00529B;
		background-color: #BDE5F8;
	}
	.isa_success {
		color: #4F8A10;
		background-color: #DFF2BF;
	}
	.isa_warning {
		color: #9F6000;
		background-color: #FEEFB3;
	}
	.isa_error {
		color: #D8000C;
		background-color: #FFD2D2;
	}
	.isa_info i, .isa_success i, .isa_warning i, .isa_error i {
		margin:1px 22px;
		font-size:2em;
		vertical-align:middle;
	}
	</style>
	
	<style>
	.modal-header-inline{
		padding: 4px;
		border-bottom: 1px solid #e5e5e5;
	}
	
	.well-inline {
    min-height: 13px;
    padding: 5px;
    margin-bottom: 20px;
    background-color: #f5f5f5;
    border: 1px solid #e3e3e3;
    border-radius: 4px;
    -webkit-box-shadow: inset 0 1px 1px rgba(0,0,0,.05);
    box-shadow: inset 0 1px 1px rgba(0,0,0,.05);
	}

	</style>

	<!-- MAIN -->
	<div class="main">
		<!-- MAIN CONTENT -->
		<div class="main-content">
			<h1 class="page-header">{{$pagetitle}}</h1>
			<div class="container-fluid add-country">
				<div class="row">
					<div class="container right-container">
						<div class="col-md-12">
							<div class="location panel">
								<div class="panel-heading p__title">
									{{$pagetitle}}
								</div>
								
								{{--Manage page starts--}}
								<div class="panel-body" id="location_table" >
									
									
								@if(count($delivery_boy_details) == 0)
									<div class="isa_info" style="margin:2%;">
										<i class="fa fa-info-circle"></i>
										{{(Lang::has(Session::get('DelMgr_lang_file').'.DEL_NO_DELIVERY_BOY_FOUND')) ? trans(Session::get('DelMgr_lang_file').'.DEL_NO_DELIVERY_BOY_FOUND') : trans($DELMGR_OUR_LANGUAGE.'.DEL_NO_DELIVERY_BOY_FOUND')}}
									</div>
									
								@endif
									
									
								<div class="well-inline" style="margin:2%;">
									<div class="cal-search-filter">
										{!! Form::open(['method' => 'post','class' => 'form-horizontal','url' => url('delivery-boy-map'),'id'=>'validate_form']) !!}
											<div class="modal-header-inline">
												<h5 class="modal-title" id="myModalLabel" style="cursor:pointer" onclick="toggle_fun();">
													{{ (Lang::has(Session::get('DelMgr_lang_file').'.DEL_SEARCH_FILTER')) ? trans(Session::get('DelMgr_lang_file').'.DEL_SEARCH_FILTER') : trans($DELMGR_OUR_LANGUAGE.'.DEL_SEARCH_FILTER')}}
													<span class="pull-right" id="mySymbol"><i class="fa fa-arrows-h"></i></span>
												</h5>
											</div>
											<div class="modal-body">
												<div class="form-group">
													<label class="control-label col-sm-2">{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_DELBOY_NAME')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_DELBOY_NAME') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_DELBOY_NAME')}} :</label>
													<div class="col-md-4">
														@if(count($delivery_list) > 0)
															{{ Form::select('del_boy_name',$delivery_list,'',['class' => 'form-control select2' , 'style' => 'width:100%','id' => 'deliver_id'] ) }}
														@else
															{{ (Lang::has(Session::get('DelMgr_lang_file').'.ADMIN_NO_DELIVERY_BOY_FOUND')) ? trans(Session::get('DelMgr_lang_file').'.ADMIN_NO_DELIVERY_BOY_FOUND') : trans($DELMGR_OUR_LANGUAGE.'.ADMIN_NO_DELIVERY_BOY_FOUND') }}
														@endif
														@if ($errors->has('deliver_id') )
															<p class="error-block" style="color:red;">{{ $errors->first('deliver_id') }}</p>
														@endif
													</div>
													
													<label class="control-label col-sm-2">{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_MOBILENO')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_MOBILENO') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_MOBILENO')}} :</label>
													<div class="col-md-4">
														{!! Form::text('del_boy_phone','',['class'=>'form-control','maxlength'=>'15','autocomplete'=>'off','id' => 'del_boy_phone' ,'onkeyup'=>'validate_phone(\'del_boy_phone\');']) !!}
													</div>
													
												  </div>
												  
												  <div class="form-group">
													<label class="control-label col-sm-2">{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_LOCATION')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_LOCATION') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_LOCATION')}} :</label>
													<div class="col-md-4">
														{!! Form::text('del_boy_location',$del_boy_location,['class'=>'form-control','placeholder' => (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_LOCATION')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_LOCATION') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_LOCATION'),'id' => 'del_boy_location']) !!}
														<input type="hidden" name="us4_lat" id="us4-lat" value="{{$us4_lat}}" />
														<input type="hidden" name="us4_lon" id="us4-lon" value="{{$us4_lon}}" />
														<input type="hidden" name="us4_radius" id="us4-radius" value="{{$us4_radius}}"  />
													</div>
													
													<!--<label class="control-label col-sm-2">{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_STATUS')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_STATUS') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_STATUS')}} :</label>
													<div class="col-md-4">
														
														@php 
															$delivery_boy_status_arr = array(
															
															''=> (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_SELECT')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_SELECT') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_SELECT'),
															'1'=>(Lang::has(Session::get('DelMgr_lang_file').'.DEL_AVAILABLE')) ? trans(Session::get('DelMgr_lang_file').'.DEL_AVAILABLE') : trans($DELMGR_OUR_LANGUAGE.'.DEL_AVAILABLE'),
															'2'=>(Lang::has(Session::get('DelMgr_lang_file').'.DEL_BUSY')) ? trans(Session::get('DelMgr_lang_file').'.DEL_BUSY') : trans($DELMGR_OUR_LANGUAGE.'.DEL_BUSY'),
															'3'=>(Lang::has(Session::get('DelMgr_lang_file').'.DEL_UNAVAILABLE')) ? trans(Session::get('DelMgr_lang_file').'.DEL_UNAVAILABLE') : trans($DELMGR_OUR_LANGUAGE.'.DEL_UNAVAILABLE'),
															
															); 	@endphp
															
															{{ Form::select('del_boy_status',$delivery_boy_status_arr,$delivery_boy_status,['class' => 'form-control' , 'style' => 'width:100%','id'=>'del_boy_status'] ) }}
														
													</div>-->
													
													
												  </div>
												
												  <div class="clearfix"></div>
													<div class="col-md-offset-2">
														<button type="submit" class="btn btn-success" id="newConsigneeReset" name="submit" >@lang(Session::get('DelMgr_lang_file').'.DEL_SEARCH')</button>
														<a href="{{url('delivery-boy-map')}}"><button type="button" name="reset" class="btn btn-default"> @lang(Session::get('DelMgr_lang_file').'.DEL_RESET') </button></a>
														
													</div>
											  </div>
											<!-- END -->
										{!! Form::close() !!}
									</div>
								</div>
								
								
								
								<!---------------DELIVERY BOY LOCATION - MAP FUNCTION ---->
								
								<div id="map" style="height:428px;margin:2%;"></div>
								
									@if(Session::get('DelMgr_lang_code') == '' || Session::get('DelMgr_lang_code') == 'en')
										@php $map_lang = 'en'; @endphp
									@else
										@php $map_lang = Session::get('DelMgr_lang_code'); @endphp
									@endif
								
									@php $devboy_latitude = '0.0'; $devboy_longitude ='0.0'; @endphp
									
									<script type="text/javascript" src='https://maps.google.com/maps/api/js?libraries=places&key=AIzaSyCsDoY1OPjAqu1PlQhH3UljYsfw-81bLkI&language=$map_lang'></script>
									
									
									<script type="text/javascript">
									   var locations = [

											@if(count($delivery_boy_details) > 0)
												@foreach($delivery_boy_details as $details)
												
													@if($details->deliver_avail_status == 1)
															@php $status = (Lang::has(Session::get('DelMgr_lang_file').'.DEL_AVAILABLE')) ? trans(Session::get('DelMgr_lang_file').'.DEL_AVAILABLE') : trans($DELMGR_OUR_LANGUAGE.'.DEL_AVAILABLE'); @endphp
													@elseif($details->deliver_avail_status == 3)	
															@php $status = (Lang::has(Session::get('DelMgr_lang_file').'.DEL_UNAVAILABLE')) ? trans(Session::get('DelMgr_lang_file').'.DEL_UNAVAILABLE') : trans($DELMGR_OUR_LANGUAGE.'.DEL_UNAVAILABLE'); @endphp
													@elseif($details->deliver_avail_status == 2)
															@php $status = (Lang::has(Session::get('DelMgr_lang_file').'.DEL_BUSY')) ? trans(Session::get('DelMgr_lang_file').'.DEL_BUSY') : trans($DELMGR_OUR_LANGUAGE.'.DEL_BUSY'); @endphp
													@endif
													
														["<b><span style='color:green;font-weight:900;'><i class='fa fa-male'></i>&nbsp;{{$status}}</span></b> <br/><b>{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_DELBOY_NAME')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_DELBOY_NAME') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_DELBOY_NAME')}}:</b>&nbsp;{{$details->deliver_fname}} {{$details->deliver_lname}},<br/><b>{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_ADDRESS')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_ADDRESS') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_ADDRESS')}}:</b>&nbsp;{{$details->deliver_location}},<br/><b>{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_PHONE')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_PHONE') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_PHONE')}}:</b>&nbsp;{{$details->deliver_phone1}},{{$details->deliver_phone2}}&nbsp;",{{$details->deliver_latitude}},{{$details->deliver_longitude}}, 4],
														@php $devboy_latitude=$details->deliver_latitude; $devboy_longitude=$details->deliver_longitude; @endphp
												@endforeach
											@endif
										
									   ];
									   
									   var map = new google.maps.Map(document.getElementById('map'), {
										 zoom: 10,
										 center: new google.maps.LatLng(<?php if($us4_lat=='') { echo '10.991090'; } else { echo $us4_lat; } ?>, <?php if($us4_lon=='') { echo '76.960040'; } else { echo $us4_lon; } ?>),
										 mapTypeId: google.maps.MapTypeId.ROADMAP
									   });
									   
									   var infowindow = new google.maps.InfoWindow();
									   var marker, i;
									   
									   for (i = 0; i < locations.length; i++) {  
										 marker = new google.maps.Marker({
										   position: new google.maps.LatLng(locations[i][1], locations[i][2]),
										   map: map
										 });
									   
										 google.maps.event.addListener(marker, 'click', (function(marker, i) {
										   return function() {
											 infowindow.setContent(locations[i][0]);
											 infowindow.open(map, marker);
										   }
										 })(marker, i));
									   }
									</script>
									
								<!---------------END DELIVERY BOY LOCATION - MAP FUNCTION ---->
			
			
								</div>
								{{--Manage page ends--}}
							</div>

						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- END MAIN CONTENT -->
	</div>
<div id="us4" style="width: 100%; height: 400px;display:none;"></div>
@section('script')
<script src="{{url('')}}/public/admin/assets/scripts/locationpicker.jquery.min.js"></script>
<script>
$('#del_boy_location').focus(function(){
		//if($(this).val()!=''){
			$('#us4').locationpicker({
				location: {
					latitude: <?php if($us4_lat=='') { echo '10.991090'; } else { echo $us4_lat; } ?>,
					longitude: <?php if($us4_lon=='') { echo '76.960040'; } else { echo $us4_lon; } ?>
				},
				radius: 10,
				inputBinding: {
					latitudeInput: $('#us4-lat'),
					longitudeInput: $('#us4-lon'),
					radiusInput: $('#us4-radius'),
					locationNameInput: $('#del_boy_location')
				},
				enableAutocomplete: true,
				enableReverseGeocode: true,
				mapTypeId: google.maps.MapTypeId.ROADMAP,
				onchanged: function (currentLocation, radius, isMarkerDropped) {
					/*console.log($(this).locationpicker('map'));
					var addressComponents = $(this).locationpicker('map').location.addressComponents;
					var formattedAddress = $(this).locationpicker('map').location.formattedAddress;
					var addressParts = formattedAddress.split(', '); // <- new code
					//result.addressLine1 = addressParts[0]
					console.log('Address Line 1'+addressParts[0]+'\n'+addressParts[1]+'\n'+addressParts[2]);
					console.log('City'+addressComponents.city);
					console.log('State'+addressComponents.stateOrProvince);
					console.log('Postal Code'+addressComponents.postalCode);
					console.log('Country'+addressComponents.country);*/
					// Uncomment line below to show alert on each Location Changed event
					//alert("Location changed. New location (" + currentLocation.latitude + ", " + currentLocation.longitude + ")");
				}
			});
		//}else{
		//	$(this).val('');
		//}
	});
</script>

<script>
function toggle_fun(){
		
		if($('.modal-body:visible').length){
			$('.modal-body').slideUp(500);
			$('#mySymbol').html('<i class="fa fa-arrows-v"></i>');
		}else{
			$('.modal-body').slideDown(500); 
			$('#mySymbol').html('<i class="fa fa-arrows-h"></i>');
		}
 }
</script>


@endsection
@stop