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
		<h1 class="page-header">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_LOGO_SETTINGS')) ? trans(Session::get('admin_lang_file').'.ADMIN_LOGO_SETTINGS') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_LOGO_SETTINGS')}}</h1>
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

				
					

					<div class="panel-body">
					{{ Form::open(array('url' => 'admin-logo-settings-submit','id'=>'logo_settings','method' => 'post','enctype'=>'multipart/form-data','class'=>'form-horizontal')) }}

					{{ Form::hidden('logoid',$get_details->id,array('class' => 'form-control','id'=>'logoid')) }}
					<div class="form-group">
					{{ Form::label('front_logo', (Lang::has(Session::get('admin_lang_file').'.ADMIN_FRONT_LOGO')) ? trans(Session::get('admin_lang_file').'.ADMIN_FRONT_LOGO') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_FRONT_LOGO'),array('class'=>'control-label col-sm-2')) }}
					<div class="col-sm-6">

					{{ Form::file('front_logo',array('class' => 'form-control','accept'=>'image/*','id'=>'front_logo','onchange'=>'Upload(this.id,"140","50","200","50");')) }} 
					
					<span class="help-block">
					{{ (Lang::has(Session::get('admin_lang_file').'.ADMIN_HOME_LOGO_IMG_VAL')) ? trans(Session::get('admin_lang_file').'.ADMIN_HOME_LOGO_IMG_VAL') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_HOME_LOGO_IMG_VAL') }}</span>
					@if($get_details->front_logo != '')
						<img src="{{url('public/images/logo/'.$get_details->front_logo)}}" width="140px" height="30px">
					@endif
					</div>
					<input type="hidden" name="pre_front_logo" value="{{$get_details->front_logo}}">
					
				</div>
				<div class="form-group">
					{{ Form::label('admin_logo', (Lang::has(Session::get('admin_lang_file').'.ADMIN_ADMIN_LOGO')) ? trans(Session::get('admin_lang_file').'.ADMIN_ADMIN_LOGO') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ADMIN_LOGO'),array('class'=>'control-label col-sm-2')) }}
					<div class="col-sm-6">
					{{ Form::file('admin_logo',array('class' => 'form-control','accept'=>'image/*','id'=>'admin_logo','onchange'=>'Upload(this.id,"140","50","200","50");')) }}
				
					<span class="help-block">
					{{ (Lang::has(Session::get('admin_lang_file').'.ADMIN_ADMIN_LOGO_IMG_DIMEN_VAL')) ? trans(Session::get('admin_lang_file').'.ADMIN_ADMIN_LOGO_IMG_DIMEN_VAL') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ADMIN_LOGO_IMG_DIMEN_VAL') }}</span>
					@if($get_details->admin_logo != '')
						<img src="{{url('public/images/logo/'.$get_details->admin_logo)}}" width="140px" height="30px">
					@endif
					</div>
					<input type="hidden" name="pre_admin_logo" value="{{$get_details->admin_logo}}">
					<!-- {{ Form::text('pre_admin_logo',$get_details->admin_logo,array('class' => 'form-control')) }} -->
				</div>
				<div class="form-group">
					{{ Form::label('favicon', (Lang::has(Session::get('admin_lang_file').'.ADMIN_FAVICON')) ? trans(Session::get('admin_lang_file').'.ADMIN_FAVICON') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_FAVICON'),array('class'=>'control-label col-sm-2')) }}
					<div class="col-sm-6">
					{{ Form::file('favicon',array('class' => 'form-control','accept'=>'image/*','id'=>'favicon','onchange'=>'Upload(this.id,"50","50","80","80");')) }}
					
					<span class="help-block">
					{{ (Lang::has(Session::get('admin_lang_file').'.ADMIN_FAVICON_IMG_DIMEN_VAL')) ? trans(Session::get('admin_lang_file').'.ADMIN_FAVICON_IMG_DIMEN_VAL') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_FAVICON_IMG_DIMEN_VAL') }}</span>
					@if($get_details->favicon != '')
						<img src="{{url('public/images/logo/'.$get_details->favicon)}}" width="50px" height="50px">
					@endif
					 </div>
					 <input type="hidden" name="pre_favicon" value="{{$get_details->favicon}}">
					 <!-- {{ Form::hidden('pre_favicon',$get_details->favicon,array('class' => 'form-control')) }} -->
				</div>
				
				<div class="form-group" style="display:none">
					{{ Form::label('favicon', (Lang::has(Session::get('admin_lang_file').'.ADMIN_LANDING_ST_IMAGE')) ? trans(Session::get('admin_lang_file').'.ADMIN_LANDING_ST_IMAGE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_LANDING_ST_IMAGE'),array('class'=>'control-label col-sm-2')) }}
					<div class="col-sm-6">
					{{ Form::file('store_image',array('class' => 'form-control','accept'=>'image/*','id'=>'store_image','onchange'=>'Upload(this.id,"540","300","850","450");')) }}
					
					<span class="help-block">
					{{ (Lang::has(Session::get('admin_lang_file').'.ADMIN_LANDING_ST_IMG_VAL')) ? trans(Session::get('admin_lang_file').'.ADMIN_LANDING_ST_IMG_VAL') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_LANDING_ST_IMG_VAL') }}</span>
					@if($get_details->store_image != '')
						<img src="{{url('public/images/logo/'.$get_details->store_image)}}" width="50px" height="50px">
					@endif
					 </div>
					 <input type="hidden" name="pre_st_img" value="{{$get_details->store_image}}">
					 
				</div>
				<div class="form-group" style="display:none">
					{{ Form::label('favicon', (Lang::has(Session::get('admin_lang_file').'.ADMIN_LANDING_RES_IMAGE')) ? trans(Session::get('admin_lang_file').'.ADMIN_LANDING_RES_IMAGE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_LANDING_RES_IMAGE'),array('class'=>'control-label col-sm-2')) }}
					<div class="col-sm-6">
					{{ Form::file('res_image',array('class' => 'form-control','accept'=>'image/*','id'=>'res_image','onchange'=>'Upload(this.id,"540","300","850","450");')) }}
					
					<span class="help-block">
					{{ (Lang::has(Session::get('admin_lang_file').'.ADMIN_LANDING_RES_IMG_VAL')) ? trans(Session::get('admin_lang_file').'.ADMIN_LANDING_RES_IMG_VAL') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_LANDING_RES_IMG_VAL') }}</span>
					@if($get_details->restaurant_image != '')
						<img src="{{url('public/images/logo/'.$get_details->restaurant_image)}}" width="50px" height="50px">
					@endif
					 </div>
					 <input type="hidden" name="pre_res_image" value="{{$get_details->restaurant_image}}">
					
				</div>
				{{------------SPLASH IMAGE AND LOGO FOR MOBILE NO NEED TO UPLOAD IN WEB. HIDE THE PANEL--------------}}
				<div style="display:none">
				<div class="form-group">
					<h4 style="margin-left: 20px;">
						<b>@lang(Session::get('admin_lang_file').'.ADMIN_MOB_IMG_AND')</b>
					</h4>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-2">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_LOGO_IMAGE')) ? trans(Session::get('admin_lang_file').'.ADMIN_LOGO_IMAGE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_LOGO_IMAGE')}}</label>
					<div class="col-sm-6">
					{{ Form::file('mob_logo',array('class' => 'form-control','accept'=>'image/*','id'=>'mob_logo','onchange'=>'mob_Upload(this.id,"'.Config::get("mob_logo_wid").'","'.Config::get("mob_logo_hei").'");')) }}
					<span class="help-block">{{__(Session::get('admin_lang_file').'.ADMIN_IMG_HEI_WID', ['width' => Config::get('mob_logo_wid'),'height' => Config::get('mob_logo_hei')])}} {{ (Lang::has(Session::get('admin_lang_file').'.ADMIN_WIDTH_HEIGHT')) ? trans(Session::get('admin_lang_file').'.ADMIN_WIDTH_HEIGHT') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_WIDTH_HEIGHT') }}</span>
					@if($get_details->andr_logo != '')
						<img src="{{url('public/images/logo/'.$get_details->andr_logo)}}" width="50px" height="50px">
					@endif
					 </div>
					 <input type="hidden" name="pre_mob_logo" value="{{$get_details->andr_logo}}">
					
				</div>
				<div class="form-group">
					<label class="control-label col-sm-2">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_MB_SP_SC_IMAGE')) ? trans(Session::get('admin_lang_file').'.ADMIN_MB_SP_SC_IMAGE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_MB_SP_SC_IMAGE')}}&nbsp;(@lang(Session::get('admin_lang_file').'.ADMIN_CUS_APP'))</label>
					<div class="col-sm-6">
					{{ Form::file('splash_screen',array('class' => 'form-control','accept'=>'image/*','id'=>'splash_screen','onchange'=>'mob_Upload(this.id,"'.Config::get("mob_splash_width").'","'.Config::get("mob_splash_height").'");')) }}
					<span class="help-block">{{__(Session::get('admin_lang_file').'.ADMIN_IMG_HEI_WID', ['width' => Config::get('mob_splash_width'),'height' => Config::get('mob_splash_height')])}} {{ (Lang::has(Session::get('admin_lang_file').'.ADMIN_WIDTH_HEIGHT')) ? trans(Session::get('admin_lang_file').'.ADMIN_WIDTH_HEIGHT') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_WIDTH_HEIGHT') }}</span>
					@if($get_details->andr_splash_img_cus != '')
						<img src="{{url('public/images/logo/'.$get_details->andr_splash_img_cus)}}" width="50px" height="50px">
					@endif
					 </div>
					 <input type="hidden" name="pre_splash_img" value="{{$get_details->andr_splash_img_cus}}">
					
				</div>
				
				<div class="form-group">
					<label class="control-label col-sm-2">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_MB_SP_SC_IMAGE')) ? trans(Session::get('admin_lang_file').'.ADMIN_MB_SP_SC_IMAGE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_MB_SP_SC_IMAGE')}}&nbsp;(@lang(Session::get('admin_lang_file').'.ADMIN_AGN_APP'))</label>
					<div class="col-sm-6">
					{{ Form::file('splash_img_vendor',array('class' => 'form-control','accept'=>'image/*','id'=>'splash_img_vendor','onchange'=>'mob_Upload(this.id,"'.Config::get("mob_splash_width").'","'.Config::get("mob_splash_height").'");')) }}
					<span class="help-block">{{__(Session::get('admin_lang_file').'.ADMIN_IMG_HEI_WID', ['width' => Config::get('mob_splash_width'),'height' => Config::get('mob_splash_height')])}} {{ (Lang::has(Session::get('admin_lang_file').'.ADMIN_WIDTH_HEIGHT')) ? trans(Session::get('admin_lang_file').'.ADMIN_WIDTH_HEIGHT') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_WIDTH_HEIGHT') }}</span>
					@if($get_details->andr_splash_img_vendor != '')
						<img src="{{url('public/images/logo/'.$get_details->andr_splash_img_vendor)}}" width="50px" height="50px">
					@endif
					 </div>
					 <input type="hidden" name="pre_splash_img_vendor" value="{{$get_details->andr_splash_img_vendor}}">
					
				</div>
				<div class="form-group">
					<label class="control-label col-sm-2">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_MB_SP_SC_IMAGE')) ? trans(Session::get('admin_lang_file').'.ADMIN_MB_SP_SC_IMAGE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_MB_SP_SC_IMAGE')}}&nbsp;(@lang(Session::get('admin_lang_file').'.ADMIN_DEL_APP'))</label>
					<div class="col-sm-6">
					{{ Form::file('splash_img_delivery',array('class' => 'form-control','accept'=>'image/*','id'=>'splash_img_delivery','onchange'=>'mob_Upload(this.id,"'.Config::get("mob_splash_width").'","'.Config::get("mob_splash_height").'");')) }}
					<span class="help-block">{{__(Session::get('admin_lang_file').'.ADMIN_IMG_HEI_WID', ['width' => Config::get('mob_splash_width'),'height' => Config::get('mob_splash_height')])}} {{ (Lang::has(Session::get('admin_lang_file').'.ADMIN_WIDTH_HEIGHT')) ? trans(Session::get('admin_lang_file').'.ADMIN_WIDTH_HEIGHT') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_WIDTH_HEIGHT') }}</span>
					@if($get_details->andr_splash_img_delivery != '')
						<img src="{{url('public/images/logo/'.$get_details->andr_splash_img_delivery)}}" width="50px" height="50px">
					@endif
					 </div>
					 <input type="hidden" name="pre_splash_img_delivery" value="{{$get_details->andr_splash_img_delivery}}">
					
				</div>
				<div class="form-group">
					<h4 style="margin-left: 20px;">
						<b>@lang(Session::get('admin_lang_file').'.ADMIN_MOB_IMG_IOS')</b>
					</h4>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-2">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_LOGO_IMAGE')) ? trans(Session::get('admin_lang_file').'.ADMIN_LOGO_IMAGE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_LOGO_IMAGE')}}&nbsp;(@lang(Session::get('admin_lang_file').'.ADMIN_LOGIN_SC'))</label>
					<div class="col-sm-6">
					{{ Form::file('ios_login_logo',array('class' => 'form-control','accept'=>'image/*','id'=>'ios_login_logo','onchange'=>'mob_Upload(this.id,"'.Config::get("ios_login_logo_wi").'","'.Config::get("ios_login_logo_he").'");')) }}
					<span class="help-block">{{__(Session::get('admin_lang_file').'.ADMIN_IMG_HEI_WID', ['width' => Config::get('ios_login_logo_wi'),'height' => Config::get('ios_login_logo_he')])}} {{ (Lang::has(Session::get('admin_lang_file').'.ADMIN_WIDTH_HEIGHT')) ? trans(Session::get('admin_lang_file').'.ADMIN_WIDTH_HEIGHT') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_WIDTH_HEIGHT') }}</span>
					@if($get_details->ios_login_sc_logo != '')
						<img src="{{url('public/images/logo/'.$get_details->ios_login_sc_logo)}}" width="50px" height="50px">
					@endif
					 </div>
					 <input type="hidden" name="pre_ios_login_logo" value="{{$get_details->ios_login_sc_logo}}">
					
				</div>
				<div class="form-group">
					<label class="control-label col-sm-2">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_LOGO_IMAGE')) ? trans(Session::get('admin_lang_file').'.ADMIN_LOGO_IMAGE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_LOGO_IMAGE')}}&nbsp;(@lang(Session::get('admin_lang_file').'.ADMIN_REG_SC'))</label>
					<div class="col-sm-6">
					{{ Form::file('ios_signup_logo',array('class' => 'form-control','accept'=>'image/*','id'=>'ios_signup_logo','onchange'=>'mob_Upload(this.id,"'.Config::get("ios_register_logo_wi").'","'.Config::get("ios_register_logo_he").'");')) }}
					<span class="help-block">{{__(Session::get('admin_lang_file').'.ADMIN_IMG_HEI_WID', ['width' => Config::get('ios_register_logo_wi'),'height' => Config::get('ios_register_logo_he')])}} {{ (Lang::has(Session::get('admin_lang_file').'.ADMIN_WIDTH_HEIGHT')) ? trans(Session::get('admin_lang_file').'.ADMIN_WIDTH_HEIGHT') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_WIDTH_HEIGHT') }}</span>
					@if($get_details->ios_register_sc_logo != '')
						<img src="{{url('public/images/logo/'.$get_details->ios_register_sc_logo)}}" width="50px" height="50px">
					@endif
					 </div>
					 <input type="hidden" name="pre_ios_reg_sc_logo" value="{{$get_details->ios_register_sc_logo}}">
					
				</div>

				<div class="form-group">
					<label class="control-label col-sm-2">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_LOGO_IMAGE')) ? trans(Session::get('admin_lang_file').'.ADMIN_LOGO_IMAGE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_LOGO_IMAGE')}}&nbsp;(@lang(Session::get('admin_lang_file').'.ADMIN_FP_SC'))</label>
					<div class="col-sm-6">
					{{ Form::file('ios_frpw_logo',array('class' => 'form-control','accept'=>'image/*','id'=>'ios_frpw_logo','onchange'=>'mob_Upload(this.id,"'.Config::get("ios_forget_logo_wi").'","'.Config::get("ios_forget_logo_he").'");')) }}
					<span class="help-block">{{__(Session::get('admin_lang_file').'.ADMIN_IMG_HEI_WID', ['width' => Config::get('ios_forget_logo_wi'),'height' => Config::get('ios_forget_logo_he')])}} {{ (Lang::has(Session::get('admin_lang_file').'.ADMIN_WIDTH_HEIGHT')) ? trans(Session::get('admin_lang_file').'.ADMIN_WIDTH_HEIGHT') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_WIDTH_HEIGHT') }}</span>
					@if($get_details->ios_forget_pw_logo != '')
						<img src="{{url('public/images/logo/'.$get_details->ios_forget_pw_logo)}}" width="50px" height="50px">
					@endif
					 </div>
					 <input type="hidden" name="pre_ios_fp_pw_logo" value="{{$get_details->ios_forget_pw_logo}}">
					
				</div>

				<div class="form-group">
					<label class="control-label col-sm-2">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_MB_SP_SC_IMAGE')) ? trans(Session::get('admin_lang_file').'.ADMIN_MB_SP_SC_IMAGE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_MB_SP_SC_IMAGE')}}&nbsp;(@lang(Session::get('admin_lang_file').'.ADMIN_CUS_APP'))</label>
					<div class="col-sm-6">
					{{ Form::file('ios_splash_screen',array('class' => 'form-control','accept'=>'image/*','id'=>'ios_splash_screen','onchange'=>'mob_Upload(this.id,"'.Config::get("ios_splash_width").'","'.Config::get("ios_splash_height").'");')) }}
					<span class="help-block">{{__(Session::get('admin_lang_file').'.ADMIN_IMG_HEI_WID', ['width' => Config::get('ios_splash_width'),'height' => Config::get('ios_splash_height')])}} {{ (Lang::has(Session::get('admin_lang_file').'.ADMIN_WIDTH_HEIGHT')) ? trans(Session::get('admin_lang_file').'.ADMIN_WIDTH_HEIGHT') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_WIDTH_HEIGHT') }}</span>
					@if($get_details->ios_splash_img_cus != '')
						<img src="{{url('public/images/logo/'.$get_details->ios_splash_img_cus)}}" width="50px" height="50px">
					@endif
					 </div>
					 <input type="hidden" name="pre_splash_img_ios" value="{{$get_details->ios_splash_img_cus}}">
					
				</div>
				
				<div class="form-group">
					<label class="control-label col-sm-2">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_MB_SP_SC_IMAGE')) ? trans(Session::get('admin_lang_file').'.ADMIN_MB_SP_SC_IMAGE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_MB_SP_SC_IMAGE')}}&nbsp;(@lang(Session::get('admin_lang_file').'.ADMIN_AGN_APP'))</label>
					<div class="col-sm-6">
					{{ Form::file('ios_splash_vendor',array('class' => 'form-control','accept'=>'image/*','id'=>'ios_splash_vendor','onchange'=>'mob_Upload(this.id,"'.Config::get("ios_splash_width").'","'.Config::get("ios_splash_height").'");')) }}
					<span class="help-block">{{__(Session::get('admin_lang_file').'.ADMIN_IMG_HEI_WID', ['width' => Config::get('ios_splash_width'),'height' => Config::get('ios_splash_height')])}} {{ (Lang::has(Session::get('admin_lang_file').'.ADMIN_WIDTH_HEIGHT')) ? trans(Session::get('admin_lang_file').'.ADMIN_WIDTH_HEIGHT') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_WIDTH_HEIGHT') }}</span>
					@if($get_details->ios_splash_img_vendor != '')
						<img src="{{url('public/images/logo/'.$get_details->ios_splash_img_vendor)}}" width="50px" height="50px">
					@endif
					 </div>
					 <input type="hidden" name="vendor_splash_img_ios" value="{{$get_details->ios_splash_img_vendor}}">
					
				</div>
				<div class="form-group">
					<label class="control-label col-sm-2">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_MB_SP_SC_IMAGE')) ? trans(Session::get('admin_lang_file').'.ADMIN_MB_SP_SC_IMAGE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_MB_SP_SC_IMAGE')}}&nbsp;(@lang(Session::get('admin_lang_file').'.ADMIN_DEL_APP'))</label>
					<div class="col-sm-6">
					{{ Form::file('ios_splash_delivey',array('class' => 'form-control','accept'=>'image/*','id'=>'ios_splash_delivey','onchange'=>'mob_Upload(this.id,"'.Config::get("ios_splash_width").'","'.Config::get("ios_splash_height").'");')) }}
					<span class="help-block">{{__(Session::get('admin_lang_file').'.ADMIN_IMG_HEI_WID', ['width' => Config::get('ios_splash_width'),'height' => Config::get('ios_splash_height')])}} {{ (Lang::has(Session::get('admin_lang_file').'.ADMIN_WIDTH_HEIGHT')) ? trans(Session::get('admin_lang_file').'.ADMIN_WIDTH_HEIGHT') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_WIDTH_HEIGHT') }}</span>
					@if($get_details->ios_splash_img_delivery != '')
						<img src="{{url('public/images/logo/'.$get_details->ios_splash_img_delivery)}}" width="50px" height="50px">
					@endif
					 </div>
					 <input type="hidden" name="deli_splash_img_ios" value="{{$get_details->ios_splash_img_delivery}}">
					
				</div>
			</div>
			{{------------SPLASH IMAGE AND LOGO FOR MOBILE NO NEED TO UPLOAD IN WEB. HIDE THE PANEL--------------}}
				<div class="form-group">
					<label class="col-sm-2"></label>
					<div class="col-sm-6">
						<!-- <div class="col-md-offset-2"> -->
					{{ Form::submit('Submit',array('class'=>'btn btn-success')) }}
				<!-- </div> -->	
				</div>
				</div>
					{{ Form::close() }}
					
					
					
				</div>
				
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

	$("#logo_settings").validate({
		//onkeyup: true,
		onfocusout: function (element) {
			this.element(element);
		},
		rules: {
			"front_logo": {
				required: {
					depends: function(element) {
						if($('#logoid').val()==''){ return true; } else { return false; } 
					}
				}
			},
			"admin_logo": {
				required: {
					depends: function(element) {
						if($('#logoid').val()==''){ return true; } else { return false; } 
					}
				}
			},
			"favicon": {
				required: {
					depends: function(element) {
						if($('#logoid').val()==''){ return true; } else { return false; } 
					}
				}
			},
			"mob_logo": {
				required: {
					depends: function(element) {
						if($('#logoid').val()==''){ return true; } else { return false; } 
					}
				}
			},
			"splash_screen": {
				required: {
					depends: function(element) {
						if($('#logoid').val()==''){ return true; } else { return false; } 
					}
				}
			},
			"splash_img_vendor": {
				required: {
					depends: function(element) {
						if($('#logoid').val()==''){ return true; } else { return false; } 
					}
				}
			},
			"splash_img_delivery": {
				required: {
					depends: function(element) {
						if($('#logoid').val()==''){ return true; } else { return false; } 
					}
				}
			},
			"ios_login_logo": {
				required: {
					depends: function(element) {
						if($('#logoid').val()==''){ return true; } else { return false; } 
					}
				}
			},
			"ios_signup_logo": {
				required: {
					depends: function(element) {
						if($('#logoid').val()==''){ return true; } else { return false; } 
					}
				}
			},
			"ios_frpw_logo": {
				required: {
					depends: function(element) {
						if($('#logoid').val()==''){ return true; } else { return false; } 
					}
				}
			},
			"ios_splash_screen": {
				required: {
					depends: function(element) {
						if($('#logoid').val()==''){ return true; } else { return false; } 
					}
				}
			},
			"ios_splash_vendor": {
				required: {
					depends: function(element) {
						if($('#logoid').val()==''){ return true; } else { return false; } 
					}
				}
			},
			"ios_splash_delivey": {
				required: {
					depends: function(element) {
						if($('#logoid').val()==''){ return true; } else { return false; } 
					}
				}
			},		
		},
		messages: {
			front_logo: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_FRONT_LOGO_VAL')) ? trans(Session::get('admin_lang_file').'.ADMIN_FRONT_LOGO_VAL') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_FRONT_LOGO_VAL')}}",
			admin_logo: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ADMIN_LOGO_VAL')) ? trans(Session::get('admin_lang_file').'.ADMIN_ADMIN_LOGO_VAL') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ADMIN_LOGO_VAL')}}",
			favicon: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ADMIN_FAV_VAL')) ? trans(Session::get('admin_lang_file').'.ADMIN_ADMIN_FAV_VAL') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ADMIN_FAV_VAL')}}",
			splash_screen: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_VALID_DIMENSION')) ? trans(Session::get('admin_lang_file').'.ADMIN_VALID_DIMENSION') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_VALID_DIMENSION')}}",
			splash_img_vendor: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_VALID_DIMENSION')) ? trans(Session::get('admin_lang_file').'.ADMIN_VALID_DIMENSION') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_VALID_DIMENSION')}}",
			splash_img_delivery: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_VALID_DIMENSION')) ? trans(Session::get('admin_lang_file').'.ADMIN_VALID_DIMENSION') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_VALID_DIMENSION')}}",
			ios_splash_screen: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_VALID_DIMENSION')) ? trans(Session::get('admin_lang_file').'.ADMIN_VALID_DIMENSION') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_VALID_DIMENSION')}}",
			ios_splash_vendor: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_VALID_DIMENSION')) ? trans(Session::get('admin_lang_file').'.ADMIN_VALID_DIMENSION') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_VALID_DIMENSION')}}",
			ios_splash_delivey: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_VALID_DIMENSION')) ? trans(Session::get('admin_lang_file').'.ADMIN_VALID_DIMENSION') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_VALID_DIMENSION')}}",
			mob_logo: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_VALID_DIMENSION')) ? trans(Session::get('admin_lang_file').'.ADMIN_VALID_DIMENSION') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_VALID_DIMENSION')}}",
			ios_signup_logo: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_VALID_DIMENSION')) ? trans(Session::get('admin_lang_file').'.ADMIN_VALID_DIMENSION') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_VALID_DIMENSION')}}",
			ios_login_logo: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_VALID_DIMENSION')) ? trans(Session::get('admin_lang_file').'.ADMIN_VALID_DIMENSION') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_VALID_DIMENSION')}}",
			ios_frpw_logo: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_VALID_DIMENSION')) ? trans(Session::get('admin_lang_file').'.ADMIN_VALID_DIMENSION') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_VALID_DIMENSION')}}",
		}
	});
	
	
	function Upload(files,widthParam,heightParam,maxwidthparam,maxheightparam)
	{
		//alert('s'+files+'\n'+widthParam+'\n'+heightParam+'\n'+maxwidthparam+'\n'+maxheightparam);
		var fileUpload = document.getElementById(files);
		//alert(fileUpload.name);
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
		/*else
		{			
			document.getElementById("image_type_error").style.display = "inline";
			$("#image_type_error").fadeOut(9000);
			$("#image").val('');
			$("#image").focus();
			return false;
		}*/
	}
	
	/* check mobile images */
	function mob_Upload(files,widthParam,heightParam)
	{//alert(files+'//'+widthParam+'//'+heightParam);
		//alert('s'+files+'\n'+widthParam+'\n'+heightParam+'\n'+maxwidthparam+'\n'+maxheightparam);
		var fileUpload = document.getElementById(files);
		//alert(fileUpload.name);
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
						if (height != heightParam || width != widthParam)
						{
							//document.getElementById("image_valid_error").style.display = "inline";
							//$("#image_valid_error").fadeOut(9000);
							alert('Please select image with dimensions '+widthParam+'*'+heightParam);
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
		/*else
		{			
			document.getElementById("image_type_error").style.display = "inline";
			$("#image_type_error").fadeOut(9000);
			$("#image").val('');
			$("#image").focus();
			return false;
		}*/
	}

</script>
@endsection

@stop
