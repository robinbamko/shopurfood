@extends('Admin.layouts.default')
@section('PageTitle')
@if(isset($pagetitle))
{{$pagetitle}}
@endif
@stop
@section('content')
<!-- MAIN -->
<div class="main">
	
	<!-- MAIN CONTENT -->
	<div class="main-content">
		<h3 class="page-header">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_NOIMAGE_SETTINGS')) ? trans(Session::get('admin_lang_file').'.ADMIN_NOIMAGE_SETTINGS') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_NOIMAGE_SETTINGS')}}</h3>
		<div class="container-fluid">
			
			
			<div class="row">
				<div class="container right-container">
					<div class="r-btn">
					</div>
					<div class="col-md-12">
						
						
						<!-- INPUTS -->
						<div class="panel">
							
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
							
							@if(count($noimage_settings) > 0)
							@php
							foreach($noimage_settings as $logo_set_val){ }
							@endphp
							
							<div class="panel-body">
								{{ Form::open(array('url' => 'admin-noimage-settings-submit','id'=>'no_image_settings','method' => 'post','enctype'=>'multipart/form-data','class'=>'form-horizontal')) }}
								<div class="" style="">
									{{ Form::hidden('noimageid',$logo_set_val->id,array('class' => 'form-control','id'=>'noimageid')) }}
									
									<div class="form-group">
										{{ Form::label('banner_image', (Lang::has(Session::get('admin_lang_file').'.ADMIN_BANNER_IMAGE')) ? trans(Session::get('admin_lang_file').'.ADMIN_BANNER_IMAGE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_BANNER_IMAGE'),array('class'=>'control-label col-sm-2')) }}
										<div class="col-sm-2">
											<img src="{{url('public/images/noimage/'.$logo_set_val->banner_image)}}" width="140px" height="30px">
										</div>
										<div class="col-sm-6">
											{{ Form::file('banner_image',array('class' => 'form-control','accept'=>'image/*','onchange'=>'Upload2(this.id,"1366","300","1500","500");')) }} 
											<span class="help-block">{{ (Lang::has(Session::get('admin_lang_file').'.ADIN_NOBANNER_IMG_SIZE')) ? trans(Session::get('admin_lang_file').'.ADIN_NOBANNER_IMG_SIZE') : trans($ADMIN_OUR_LANGUAGE.'.ADIN_NOBANNER_IMG_SIZE')}}</span>
										</div>
										<input type="hidden" name="old_banner_image" value="{{$logo_set_val->banner_image}}">
										<!-- {{ Form::hidden('old_banner_image',$logo_set_val->banner_image,array('class' => 'form-control')) }} -->
									</div>
									
									<div class="form-group">
										{{ Form::label('res_store_image', (Lang::has(Session::get('admin_lang_file').'.ADMIN_RESTAURANT_STORE_DETAIL_BANNER')) ? trans(Session::get('admin_lang_file').'.ADMIN_RESTAURANT_STORE_DETAIL_BANNER') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_RESTAURANT_STORE_DETAIL_BANNER'),array('class'=>'control-label col-sm-2')) }}
										<div class="col-sm-2">
											<img src="{{url('public/images/noimage/'.$logo_set_val->restaurant_store_image)}}" width="140px" height="30px">
										</div>
										<div class="col-sm-6">
											{{ Form::file('res_store_image',array('class' => 'form-control','accept'=>'image/*','onchange'=>'Upload2(this.id,"1366","300","1500","500");')) }} 
											<span class="help-block">{{(Lang::has(Session::get('admin_lang_file').'.ADIN_NOBANNER_IMG_SIZE')) ? trans(Session::get('admin_lang_file').'.ADIN_NOBANNER_IMG_SIZE') : trans($ADMIN_OUR_LANGUAGE.'.ADIN_NOBANNER_IMG_SIZE')}}</span>
										</div>
										<input type="hidden" name="old_res_store_image" value="{{$logo_set_val->restaurant_store_image}}">
										<!-- {{ Form::hidden('old_res_store_image',$logo_set_val->restaurant_store_image,array('class' => 'form-control')) }} -->
									</div>
									
									<div class="form-group">
										{{ Form::label('res_item_image', (Lang::has(Session::get('admin_lang_file').'.ADMIN_Restaurant_Item')) ? trans(Session::get('admin_lang_file').'.ADMIN_Restaurant_Item') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_Restaurant_Item'),array('class'=>'control-label col-sm-2')) }}
										<div class="col-sm-2">
											<img src="{{url('public/images/noimage/'.$logo_set_val->restaurant_item_image)}}" width="50px" height="50px">
										</div>
										<div class="col-sm-6">
											{{ Form::file('res_item_image',array('class' => 'form-control','accept'=>'image/*','onchange'=>'Upload(this.id,"800","800");')) }} 
											<span class="help-block">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_DIMEN800x800_VAL')) ? trans(Session::get('admin_lang_file').'.ADMIN_DIMEN800x800_VAL') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_DIMEN800x800_VAL')}}</span>
										</div>
										<input type="hidden" name="old_res_item_image" value="{{$logo_set_val->restaurant_item_image}}">
										<!-- {{ Form::hidden('old_res_item_image',$logo_set_val->restaurant_item_image,array('class' => 'form-control')) }} -->
									</div>
									
									{{-- <div class="form-group">
										{{ Form::label('store_item_image', (Lang::has(Session::get('admin_lang_file').'.ADMIN_Store_product')) ? trans(Session::get('admin_lang_file').'.ADMIN_Store_product') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_Store_product'),array('class'=>'control-label col-sm-2')) }}
										<div class="col-sm-2">
											<img src="{{url('public/images/noimage/'.$logo_set_val->product_image)}}" width="50px" height="50px">
										</div>
										<div class="col-sm-6">
											{{ Form::file('store_item_image',array('class' => 'form-control','accept'=>'image/*','onchange'=>'Upload(this.id,"800","800");')) }} 
											<span class="help-block">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_DIMEN800x800_VAL')) ? trans(Session::get('admin_lang_file').'.ADMIN_DIMEN800x800_VAL') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_DIMEN800x800_VAL')}}</span>
										</div>
										<input type="hidden" name="old_store_item_image" value="{{$logo_set_val->product_image}}">
										<!-- {{ Form::hidden('old_store_item_image',$logo_set_val->product_image,array('class' => 'form-control')) }} -->
									</div> --}}
									
								 	<!--<div class="form-group">
										{{ Form::label('landing_image', (Lang::has(Session::get('admin_lang_file').'.ADMIN_LANDING_IMAGE')) ? trans(Session::get('admin_lang_file').'.ADMIN_LANDING_IMAGE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_LANDING_IMAGE'),array('class'=>'control-label col-sm-2')) }}
										<div class="col-sm-2">
											<img src="{{url('public/images/noimage/'.$logo_set_val->landing_image)}}" width="50px" height="50px">
										</div>
										<div class="col-sm-6">
											{{ Form::file('landing_image',array('class' => 'form-control','accept'=>'image/*','onchange'=>'Upload(this.id,"560","294");')) }}
											<span class="help-block">560px * 294px</span>
										</div>
										<input type="hidden" name="old_landing_image" value="{{$logo_set_val->landing_image}}">
										
									</div> -->
									
									<div class="form-group">
										{{ Form::label('landing_image', (Lang::has(Session::get('admin_lang_file').'.ADMIN_LOGO_IMAGE')) ? trans(Session::get('admin_lang_file').'.ADMIN_LOGO_IMAGE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_LOGO_IMAGE'),array('class'=>'control-label col-sm-2')) }}
										<div class="col-sm-2">
											<img src="{{url('public/images/noimage/'.$logo_set_val->logo_image)}}" width="50px" height="50px">
										</div>
										<div class="col-sm-6">
											{{ Form::file('logo_image',array('class' => 'form-control','id'=>'logo_image','accept'=>'image/*','onchange'=>'Upload2(this.id,"140","50","200","50");')) }}
											<span class="help-block">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ADMIN_DIMEN_VAL1')) ? trans(Session::get('admin_lang_file').'.ADMIN_ADMIN_DIMEN_VAL1') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ADMIN_DIMEN_VAL1')}}</span>
										</div>
										<input type="hidden" name="old_logo_image" value="{{$logo_set_val->logo_image}}">
										<!-- {{ Form::hidden('old_logo_image',$logo_set_val->logo_image,array('class' => 'form-control')) }} -->
									</div>
									<div class="form-group">
										{{ Form::label('landing_image', (Lang::has(Session::get('admin_lang_file').'.ADMIN_SHOP_IMAGE')) ? trans(Session::get('admin_lang_file').'.ADMIN_SHOP_IMAGE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SHOP_IMAGE'),array('class'=>'control-label col-sm-2')) }}
										<div class="col-sm-2">
											<img src="{{url('public/images/noimage/'.$logo_set_val->shop_logo_image)}}" width="50px" height="50px">
										</div>
										<div class="col-sm-6">
											{{ Form::file('sh_logo_image',array('class' => 'form-control','id'=>'sh_logo_image','accept'=>'image/*','onchange'=>'Upload2(this.id,"300","300","500","500");')) }}
											<span class="help-block">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_SH_LOGO_VAL')) ? trans(Session::get('admin_lang_file').'.ADMIN_SH_LOGO_VAL') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SH_LOGO_VAL')}}</span>
										</div>
										<input type="hidden" name="old_shlogo_image" value="{{$logo_set_val->shop_logo_image}}">
										<!-- {{ Form::hidden('old_logo_image',$logo_set_val->logo_image,array('class' => 'form-control')) }} -->
									</div>
									<div class="form-group"> 
										    <div class="col-sm-2"></div>
											<div class="col-sm-2"></div>
											<div class="col-sm-6">
											<!-- 	<div class="panel-heading col-md-offset-2"> -->
										            {{ Form::submit('Submit',array('class'=>'btn btn-success')) }}
									            <!--  </div> -->
											</div>
										
									</div>
								</div>
								{{ Form::close() }}
								
							</div>
							@else
							<div class="panel-body">
								{{ Form::open(array('url' => 'admin-noimage-settings-submit','id'=>'no_image_settings','method' => 'post','enctype'=>'multipart/form-data','class'=>'form-horizontal')) }}
								<div class="row well" style="margin:5px;">
									{{ Form::hidden('noimageid','',array('class' => 'form-control','id'=>'noimageid')) }}
									<div class="form-group">
										{{ Form::label('banner_image', (Lang::has(Session::get('admin_lang_file').'.ADMIN_BANNER')) ? trans(Session::get('admin_lang_file').'.ADMIN_BANNER') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_BANNER'),array('class'=>'control-label col-sm-2')) }}
										<div class="col-sm-6">
											{{ Form::file('banner_image',array('class' => 'form-control','accept'=>'image/*','onchange'=>'Upload2(this.id,"1366","300","1500","500");')) }} 
											<span class="help-block">{{ (Lang::has(Session::get('admin_lang_file').'.ADIN_NOBANNER_IMG_SIZE')) ? trans(Session::get('admin_lang_file').'.ADIN_NOBANNER_IMG_SIZE') : trans($ADMIN_OUR_LANGUAGE.'.ADIN_NOBANNER_IMG_SIZE')}}</span>
										</div>
									</div>
									
									<div class="form-group">
										{{ Form::label('res_store_image', (Lang::has(Session::get('admin_lang_file').'.ADMIN_BANNER_IMAGE')) ? trans(Session::get('admin_lang_file').'.ADMIN_BANNER_IMAGE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_BANNER_IMAGE'),array('class'=>'control-label col-sm-2')) }}
										<div class="col-sm-6">
											{{ Form::file('res_store_image',array('class' => 'form-control','accept'=>'image/*','onchange'=>'Upload2(this.id,"1366","300","1500","500");')) }}
											<span class="help-block">1366px * 300px</span>
										</div>
									</div>
									
									<div class="form-group">
										{{ Form::label('res_item_image', (Lang::has(Session::get('admin_lang_file').'.ADMIN_Restaurant_Item')) ? trans(Session::get('admin_lang_file').'.ADMIN_Restaurant_Item') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_Restaurant_Item'),array('class'=>'control-label col-sm-2')) }}
										<div class="col-sm-6">
											{{ Form::file('res_item_image',array('class' => 'form-control','accept'=>'image/*','onchange'=>'Upload(this.id,"800","800");')) }}
											<span class="help-block">800 * 800 px</span>
										</div>
									</div>
									
									{{-- <div class="form-group">
										{{ Form::label('store_item_image', (Lang::has(Session::get('admin_lang_file').'.ADMIN_Store_product')) ? trans(Session::get('admin_lang_file').'.ADMIN_Store_product') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_Store_product'),array('class'=>'control-label col-sm-2')) }}
										<div class="col-sm-6">
											{{ Form::file('store_item_image',array('class' => 'form-control','accept'=>'image/*','onchange'=>'Upload(this.id,"173","190");')) }}
											<span class="help-block">800 * 800 px</span>
										</div>
									</div> --}}
									
									{{-- <div class="form-group">
										{{ Form::label('landing_image', (Lang::has(Session::get('admin_lang_file').'.ADMIN_LANDING_IMAGE')) ? trans(Session::get('admin_lang_file').'.ADMIN_LANDING_IMAGE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_LANDING_IMAGE'),array('class'=>'control-label col-sm-2')) }}
										<div class="col-sm-6">
											{{ Form::file('landing_image',array('class' => 'form-control','accept'=>'image/*','onchange'=>'Upload(this.id,"560","294");')) }}
											<span class="help-block">560px * 294px</span>
										</div>
									</div> --}}
									<div class="form-group">
										{{ Form::label('landing_image', (Lang::has(Session::get('admin_lang_file').'.ADMIN_LOGO_IMAGE')) ? trans(Session::get('admin_lang_file').'.ADMIN_LOGO_IMAGE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_LOGO_IMAGE'),array('class'=>'control-label col-sm-2')) }}
										<div class="col-sm-6">
											{{ Form::file('logo_image',array('class' => 'form-control','id'=>'logo_image','accept'=>'image/*','onchange'=>'Upload2(this.id,"140","50","200","50");')) }}
											<span class="help-block">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ADMIN_DIMEN_VAL')) ? trans(Session::get('admin_lang_file').'.ADMIN_ADMIN_DIMEN_VAL') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ADMIN_DIMEN_VAL')}}</span>
										</div>
									</div>
									<div class="form-group">
										{{ Form::label('landing_image', (Lang::has(Session::get('admin_lang_file').'.ADMIN_SHOP_IMAGE')) ? trans(Session::get('admin_lang_file').'.ADMIN_SHOP_IMAGE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SHOP_IMAGE'),array('class'=>'control-label col-sm-2')) }}
										<div class="col-sm-6">
											{{ Form::file('sh_logo_image',array('class' => 'form-control','id'=>'sh_logo_image','accept'=>'image/*','onchange'=>'Upload(this.id,"300","300","500","500");')) }}
											<span class="help-block">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_SH_LOGO_VAL')) ? trans(Session::get('admin_lang_file').'.ADMIN_SH_LOGO_VAL') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SH_LOGO_VAL')}}</span>
										</div>
									</div>
									<div class="form-group">
									<label class="col-sm-3"></label>
									<div class="col-sm-6">
										<!-- <div class="panel-heading col-md-offset-3"> -->
									{{ Form::submit('Submit',array('class'=>'btn btn-success')) }}
								<!-- </div> -->
									</div>									
								</div>
									
									
								</div>
								{{ Form::close() }}
								
								
								
							</div>
							@endif
						</div>
						<!-- END INPUTS -->
						
						
					</div>
				</div>
			</div>
			
			
		</div>
	</div>
	<!-- END MAIN CONTENT -->
</div>
<!-- END MAIN -->

@section('script')
<script type="text/javascript">
	$("#no_image_settings").validate({
		//onkeyup: true,
		onfocusout: function (element) {
			this.element(element);
		},
		rules: {
			"banner_image": {
				required: {
					depends: function(element) {
						if($('#noimageid').val()==''){ return true; } else { return false; } 
					}
				}
			},
			"res_store_image": {
				required: {
					depends: function(element) {
						if($('#noimageid').val()==''){ return true; } else { return false; } 
					}
				}
			},
			"res_item_image": {
				required: {
					depends: function(element) {
						if($('#noimageid').val()==''){ return true; } else { return false; } 
					}
				}
			},
			"store_item_image": {
				required: {
					depends: function(element) {
						if($('#noimageid').val()==''){ return true; } else { return false; } 
					}
				}
			},
			"landing_image": {
				required: {
					depends: function(element) {
						if($('#noimageid').val()==''){ return true; } else { return false; } 
					}
				}
			},
			"logo_image": {
				required: {
					depends: function(element) {
						if($('#noimageid').val()==''){ return true; } else { return false; } 
					}
				}
			},
			
		},
		messages: {
			banner_image: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_PLEASE_SELECT_NOBANNER_IMAGE')) ? trans(Session::get('admin_lang_file').'.ADMIN_PLEASE_SELECT_NOBANNER_IMAGE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PLEASE_SELECT_NOBANNER_IMAGE')}}",
			res_store_image: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_PLEASE_SELECT_RES_STR_BANNER_IMAGE')) ? trans(Session::get('admin_lang_file').'.ADMIN_PLEASE_SELECT_RES_STR_BANNER_IMAGE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PLEASE_SELECT_RES_STR_BANNER_IMAGE')}}",
			res_item_image: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_PLEASE_SELECT_REST_ITEM_IMAGE')) ? trans(Session::get('admin_lang_file').'.ADMIN_PLEASE_SELECT_REST_ITEM_IMAGE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PLEASE_SELECT_REST_ITEM_IMAGE')}}",
			store_item_image: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_PLEASE_SELECT_STR_PROD_IMAGE')) ? trans(Session::get('admin_lang_file').'.ADMIN_PLEASE_SELECT_STR_PROD_IMAGE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PLEASE_SELECT_STR_PROD_IMAGE')}}",
			landing_image: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_PLEASE_SELECT_LAND_IMAGE')) ? trans(Session::get('admin_lang_file').'.ADMIN_PLEASE_SELECT_LAND_IMAGE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PLEASE_SELECT_LAND_IMAGE')}}",
			logo_image: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_PLEASE_SELECT_LOGO_IMAGE')) ? trans(Session::get('admin_lang_file').'.ADMIN_PLEASE_SELECT_LOGO_IMAGE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PLEASE_SELECT_LOGO_IMAGE')}}",
			
		}
	});
	
	function Upload(files,widthParam,heightParam)
	{
		var fileUpload = document.getElementById(files);

		var regex = new RegExp("([a-zA-Z0-9\s_\\.\-:])+(.jpg|.png|.gif|.jpeg)$");
		if (regex.test(fileUpload.value.toLowerCase()))
		{
			if (typeof (fileUpload.files) != "undefined")
			{
				var reader = new FileReader();
				reader.readAsDataURL(fileUpload.files[0]);
				reader.onload = function (e)
				{
					var image = new Image();
					image.src = e.target.result;

					image.onload = function ()
					{
						var height = this.height;
						var width = this.width;

						if (height < heightParam || width < widthParam)
						{
							//document.getElementById("image_valid_error").style.display = "inline";
							//$("#image_valid_error").fadeOut(9000);
							alert('Please select image above '+widthParam+'X'+heightParam);
							$("#"+files).val('');
							$("#"+files).focus();
							return false;
						}
						return true;
					};
				}
			}
			else
			{
				alert("This browser does not support HTML5.");
				$("#image").val('');
				return false;
			}
		}
		else
		{			
			document.getElementById("image_type_error").style.display = "inline";
			$("#image_type_error").fadeOut(9000);
			$("#image").val('');
			$("#image").focus();
			return false;
		}
	}
	
	function Upload2(files,widthParam,heightParam,maxwidthparam,maxheightparam)
	{
		var fileUpload = document.getElementById(files);

		var regex = new RegExp("([a-zA-Z0-9\s_\\.\-:])+(.jpg|.png|.gif|.jpeg)$");
		if (regex.test(fileUpload.value.toLowerCase()))
		{
			if (typeof (fileUpload.files) != "undefined")
			{
				var reader = new FileReader();
				reader.readAsDataURL(fileUpload.files[0]);
				reader.onload = function (e)
				{
					var image = new Image();
					image.src = e.target.result;

					image.onload = function ()
					{
						var height = this.height;
						var width = this.width;
						//alert(height +'<'+ heightParam +'&&'+ height +'>'+ maxheightparam+')|| ('+width+' < '+widthParam +'&& '+width+' > '+maxwidthparam+')');
						if (height < heightParam || height > maxheightparam|| width < widthParam || width > maxwidthparam)
						{
							//document.getElementById("image_valid_error").style.display = "inline";
							//$("#image_valid_error").fadeOut(9000);
							alert('Please select image above '+widthParam+'X'+heightParam+' and below '+maxwidthparam+'X'+maxheightparam);
							$("#"+files).val('');
							$("#"+files).focus();
							return false;
						}
						return true;
					};
				}
			}
			else
			{
				alert("This browser does not support HTML5.");
				$("#image").val('');
				return false;
			}
		}
		else
		{			
			document.getElementById("image_type_error").style.display = "inline";
			$("#image_type_error").fadeOut(9000);
			$("#image").val('');
			$("#image").focus();
			return false;
		}
	}
</script>
@endsection


@stop
