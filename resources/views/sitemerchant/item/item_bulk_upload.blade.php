@extends('sitemerchant.layouts.default')
@section('PageTitle')
		@if(isset($pagetitle))
			{{$pagetitle}}
		@endif
	@stop
@section('content')

<style>
	@media only screen and (max-width:767px)
	{
		.box-body ul{ padding-left:10px;}
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
					
					<div class="col-md-6">
						<div class="location panel">
							<div class="panel-heading p__title">
								{{$pagetitle}}
								
							</div>
							<!-- {{-- Display error message--}} -->
							
								@if ($errors->any())
								@if ($errors->has('success'))
							    <div class="alert alert-success alert-dismissible">
							    	<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
							        <ul>
							            @foreach ($errors->all() as $error)
							                <li>{{ $error }}</li>
							            @endforeach
							        </ul>
							    </div>
							    @endif
								@if ($errors->has('error'))
							    <div class="alert alert-danger alert-dismissible">
							    	<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
							        <ul>
							            @foreach ($errors->all() as $error)
							                <li>{{ $error }}</li>
							            @endforeach
							        </ul>
							    </div>
							    <a href="{{url('mer_item_delete_zip/'.$errors->first('filename') )}}">Click to delete that ZIP File</a>
							    @endif
							    @endif
							    @if (Session::has('zip_success_message'))
							    <div class="alert alert-success alert-dismissible">
							    	<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
							        <ul>
                           		  	<li>{!! Session::get('zip_success_message') !!}</li>
                           			</ul>
                           		</div>
                            	@endif
                            	@if (Session::has('message'))
									@foreach(Session::get('message') as $val)
									
									@php
										echo '<ul>';
										echo '<li style="color:red;">'.$val.'</li>';
										echo '</ul>'; 
										@endphp
									@endforeach
								@endif	
					          @if (Session::has('success_message'))
					             
					                @foreach(Session::get('success_message') as $val)
					                <?php  
					                  echo '<ul>';
					                  echo '<li style="color:green;">'.$val.'</li>';
					                  echo '</ul>';
					               
					              ?>
								  @endforeach
					          @endif
							<!-- {{-- Display error message--}} -->
							{{-- Add/Edit page starts--}}
							<div class="box-body spaced" style="padding:20px">
								<div id="location_form" class="collapse in panel-body">
									
									{{--Edit page values--}}
									<div class="row-fluid well">
										
										{!! Form::open(['method' => 'post','class' => 'form-auth-small','url' => 'mer_item_image_bulk_upload_submit','enctype'=>'multipart/form-data','id'=>'zip_form']) !!}
										
										
										<div class="row panel-heading">
											<div class="col-md-4">
												<span class="panel-title">
													{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_PLEASE_SELECT_IMAGE_ZIP_FILE')) ? trans(Session::get('mer_lang_file').'.ADMIN_PLEASE_SELECT_IMAGE_ZIP_FILE') : trans($MER_OUR_LANGUAGE.'.ADMIN_PLEASE_SELECT_IMAGE_ZIP_FILE')}}&nbsp;*
												</span>
											</div>
											<div class="col-md-8">
												
												{!! Form::file('zip_file',['class'=>'form-control','id' => 'zip_file','required','accept'=>'.zip']) !!}
												<div id="suggesstion-box" class="cntry_box" ><span id="no_country"> </span></div>
											</div>
										</div>
										<div class="panel-heading">
											
											
												@php $saveBtn=(Lang::has(Session::get('mer_lang_file').'.ADMIN_ZIP_UPLOAD')) ? trans(Session::get('mer_lang_file').'.ADMIN_ZIP_UPLOAD') : trans($MER_OUR_LANGUAGE.'.ADMIN_ZIP_UPLOAD') @endphp
											
											
											{!! Form::submit($saveBtn,['class' => 'btn btn-success'])!!}
											
											<input type="button" value="Cancel" class="btn btn-warning" onclick="javascript:window.location.href='<?php echo url('mer-manage-product'); ?>'">
											
										</div>
										{!! Form::close() !!}
									</div>

									<!-- CSV UPLOAD START -->
									<div class="row-fluid well">
										
										{!! Form::open(['method' => 'post','class' => 'form-auth-small','url' => 'mer_item_bulk_upload_submit','enctype'=>'multipart/form-data','id'=>'csv_form']) !!}
										
										
										<div class="row panel-heading">
											<div class="col-md-4">
												<span class="panel-title">
													{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_PLEASE_SELECT_CSV_FILE')) ? trans(Session::get('mer_lang_file').'.ADMIN_PLEASE_SELECT_CSV_FILE') : trans($MER_OUR_LANGUAGE.'.ADMIN_PLEASE_SELECT_CSV_FILE')}}&nbsp;*
												</span>
											</div>
											<div class="col-md-8">
												
												{!! Form::file('upload_file',['class'=>'form-control','id' => 'upload_file','required','accept'=>'.csv']) !!}
												<div id="suggesstion-box" class="cntry_box" ><span id="no_country"> </span></div>
											</div>
										</div>
										<div class="panel-heading">
											
											
												@php $saveBtn=(Lang::has(Session::get('mer_lang_file').'.ADMIN_BULK_UPLOAD')) ? trans(Session::get('mer_lang_file').'.ADMIN_BULK_UPLOAD') : trans($MER_OUR_LANGUAGE.'.ADMIN_BULK_UPLOAD') @endphp
											
											
											{!! Form::submit($saveBtn,['class' => 'btn btn-success'])!!}
											
											<input type="button" value="Cancel" class="btn btn-warning" onclick="javascript:window.location.href='<?php echo url('mer-manage-product'); ?>'">
											
										</div>
										{!! Form::close() !!}
									</div>
								</div>
							</div>
							{{-- Add page ends--}}
							
							</div>
							
						</div>
						<div class="col-md-6">
							<div class="location panel">
								<div class="panel-heading p__title">
								{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_UPLOAD_INSTRUCTION')) ? trans(Session::get('mer_lang_file').'.ADMIN_UPLOAD_INSTRUCTION') : trans($MER_OUR_LANGUAGE.'.ADMIN_UPLOAD_INSTRUCTION')}}
								</div>
								<div class="box-body spaced" style="padding:20px">
									<a href="{{ url('public/sample-csv/item-test.csv')}}" style="color: #337ab7;text-decoration: underline;">@lang(Session::get('mer_lang_file').'.ADMIN_DWLD_SAMPLE_DOC')</a>
									
									<ul>
						               <li>@lang(Session::get('mer_lang_file').'.ADMIN_SAM_BULK_UPLOAD')</li>
						                <li>@lang(Session::get('mer_lang_file').'.ADMIN_UPLOAD_CSV')</li>
						                <li>@lang(Session::get('mer_lang_file').'.ADMIN_DONT_CH_SAMPLE')</li>
						                <li>@lang(Session::get('mer_lang_file').'.ADMIN_CH_MANDATORY')</li>
						                <li>@lang(Session::get('mer_lang_file').'.ADMIN_CRT_ST_NAME')</li>
						                
						                <li><b>@lang(Session::get('mer_lang_file').'.ADMIN_UPLOAD_IMGAE')</b>
						                    <ul>
						                      <li>@lang(Session::get('mer_lang_file').'.ADMIN_UR_IMAGE_800')</li>
						                      <li>@lang(Session::get('mer_lang_file').'.ADMIN_IMAGE_ACCEPT_JPG')</li>
						                      <li>@lang(Session::get('mer_lang_file').'.ADMIN_UPLOAD_ZIP')</li>
						                      <li style="word-break:break-all;">@lang(Session::get('mer_lang_file').'.ADMIN_FORMAT_JPG')</li>
						                      <li>@lang(Session::get('mer_lang_file').'.ADMIN_DONT_LOCAL_IMG')</li>
						                      <li>@lang(Session::get('mer_lang_file').'.ADMIN_WIOUT_SPACE')</li>
						                    </ul>
						                </li>
						                
						                <li>@lang(Session::get('mer_lang_file').'.ADMIN_CATE_CSV')</li>
						                <li>@lang(Session::get('mer_lang_file').'.ADMIN_BELOW_500')</li>
						                <li>@lang(Session::get('mer_lang_file').'.ADMIN_EN_DISCOUNT')</li>
						                <li>@lang(Session::get('mer_lang_file').'.ADMIN_TAX_PERCENT')</li>
						              </ul>
								</div>
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
<script>
	$(document).ready(function() {
		$('#d-table').DataTable({
			responsive: true
		});
	});
</script>
<script>
	$(document).ready(function(){
	   	//$("#location_form").hide();
	   	/*$("#click-here").click(function(){	
	   		$("#location_form").toggle();			
	   		//$("#location_table").toggle();
		});*/
	   	var tech = getUrlParameter('addnow');
	   	if(tech==1)
	   	{
	   		$('#location_form').addClass('in');
		}
	   	
	});
	
	// function change() {
	// document.getElementById("click-here").text="Manage List";
	// }
	
	function change() {
		var elem = document.getElementById("click-here");
		if (elem.text=="Add Page")
		{ 
			elem.text = "Manage List";
		}
		else if (elem.text=="Manage List")
		{
			elem.text = "Add Page";
		}
		else {
			elem.text = "Manage List";
		}
	}
	function new_change()
	{
	   	location.href='{{url('admin-banner-settings?addnow=1')}}';
	   	
	}
</script>
<script type="text/javascript">
	$('#cnty_name').keyup(function(){
	   	var key = $('#cnty_name').val();
	   	$('#cnty_code').val('');
		$('#curr_sym').val('');
		$('#curr_code').val('');
		$('#tel_code').val('');
		if(key == '')
		{
			return;
		}
		
		$.ajax({
			type: 'get',
			url: '{{url('array_search_country')}}',
			data: {searched_country : key},
			success: function(response){
				myContent=response;
				
				result=$(myContent).text();
				
				if($.trim(result)==''){
					
					$("#suggesstion-box").show();
					$("#suggesstion-box").text("No country found!");
					return false;
				}
				
	            // alert(response);
				$("#suggesstion-box").show();
				$("#suggesstion-box").html(response);
				$("#cnty_name").css("background","#FFF");
				
			}
		});
	});
	
	function selectCountry(val) {
		
		$("#cnty_name").val(val);
		$("#suggesstion-box").hide();
		var searched_country_name=val;
		
		$.ajax({
			type: 'get',
			url: '{{url('add_searched_country')}}',
			data: {'searched_country_name': searched_country_name},
			
			success: function(response){
	            //alert(response); return false;
				id_numbers = response.split('||');
	            var Country_code=id_numbers[0];
	            var Country_name=id_numbers[1];
	            var currency_symbol=id_numbers[2];
	            var currency_code=id_numbers[3];
	            var dial_code=id_numbers[4];
				
	            $('#cnty_code').val(Country_code);
				$('#curr_sym').val(currency_symbol);
				$('#curr_code').val(currency_code);
				$('#tel_code').val('+'+dial_code);
				
			}
		}); 
	}
	
	
	$('#cnty_name').bind('keyup blur',function(){ 
		var node = $(this);
	node.val(node.val().replace(/[^a-z]/g,'') ); }
	);
</script>
<script>
	function make_default(val)
	{	
	   	$.ajax({
			type: 'get',
			url: 'country_default',
			data: {'co_id': val},
			success: function(response){
				location.reload();
				
			}
		});
	}
	
	var getUrlParameter = function getUrlParameter(sParam) {
	   	var sPageURL = decodeURIComponent(window.location.search.substring(1)),
		sURLVariables = sPageURL.split('&'),
		sParameterName,
		i;
		
	   	for (i = 0; i < sURLVariables.length; i++) {
	   		sParameterName = sURLVariables[i].split('=');
			
	   		if (sParameterName[0] === sParam) {
	   			return sParameterName[1] === undefined ? true : sParameterName[1];
			}
		}
	};
</script>
 
<script type="text/javascript">
 	$("#zip_form").validate({
		//onkeyup: true,
		onfocusout: function (element) {
			this.element(element);
		},
		rules: {
			zip_file: "required",
			
		},
		messages: {
			zip_file: "{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_PLEASE_SELECT_IMAGE_ZIP_FILE')) ? trans(Session::get('mer_lang_file').'.ADMIN_PLEASE_SELECT_IMAGE_ZIP_FILE') : trans($MER_OUR_LANGUAGE.'.ADMIN_PLEASE_SELECT_IMAGE_ZIP_FILE')}}"
		}
	});

	$("#csv_form").validate({
		//onkeyup: true,
		onfocusout: function (element) {
			this.element(element);
		},
		rules: {
			upload_file: "required",
			
		},
		messages: {
			upload_file: "{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_PLEASE_SELECT_CSV_FILE')) ? trans(Session::get('mer_lang_file').'.ADMIN_PLEASE_SELECT_CSV_FILE') : trans($MER_OUR_LANGUAGE.'.ADMIN_PLEASE_SELECT_CSV_FILE')}}"
		}
	});
	
 </script>
@endsection
@stop