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
				<h1 class="page-header">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_Social_Media_Settings')) ? trans(Session::get('admin_lang_file').'.ADMIN_Social_Media_Settings') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_Social_Media_Settings')}}</h1>

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
					{{ Form::open(array('url' => 'admin-social-settings-submit','id'=>'social_settings','method' => 'post','class'=>'form-horizontal')) }}

					{{ Form::hidden('siteid', $settings_details->gs_id,array('class' => 'form-control')) }}
				<div class="form-group">
					{{ Form::label('facebook_app_id', (Lang::has(Session::get('admin_lang_file').'.ADMIN_Facebook_App_ID_WEB')) ? trans(Session::get('admin_lang_file').'.ADMIN_Facebook_App_ID_WEB') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_Facebook_App_ID_WEB'),array('class'=>'control-label col-sm-2')) }}
					<div class="col-sm-6">
					{{ Form::text('facebook_app_id', $settings_details->facebook_app_id_web,array('class' => 'form-control','maxlength' => '200')) }} </div>
				</div>
				<div class="form-group">
					{{ Form::label('facebook_app_secret', (Lang::has(Session::get('admin_lang_file').'.ADMIN_Facebook_App_Secret')) ? trans(Session::get('admin_lang_file').'.ADMIN_Facebook_App_Secret') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_Facebook_App_Secret'),array('class'=>'control-label col-sm-2')) }}
					<div class="col-sm-6">
					{{ Form::text('facebook_app_secret', $settings_details->facebook_secret_key,array('class' => 'form-control','placeholder'=>(Lang::has(Session::get('admin_lang_file').'.ADMIN_Facebook_App_Secret')) ? trans(Session::get('admin_lang_file').'.ADMIN_Facebook_App_Secret') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_Facebook_App_Secret'),'maxlength' => '200')) }} </div>
				</div>
				<div class="form-group">
					{{ Form::label('gs_facebook_redirect_url', (Lang::has(Session::get('admin_lang_file').'.ADMIN_Facebook_REDIRECT_URL')) ? trans(Session::get('admin_lang_file').'.ADMIN_Facebook_REDIRECT_URL') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_Facebook_REDIRECT_URL'),array('class'=>'control-label col-sm-2')) }}
					<div class="col-sm-6">
					{{ Form::text('gs_facebook_redirect_url', $settings_details->gs_facebook_redirect_url,array('class' => 'form-control','maxlength' => '300')) }} </div>
				</div>
				
				<div class="form-group">
					{{ Form::label('facebook_app_link', (Lang::has(Session::get('admin_lang_file').'.ADMIN_Facebook_Link')) ? trans(Session::get('admin_lang_file').'.ADMIN_Facebook_Link') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_Facebook_Link'),array('class'=>'control-label col-sm-2')) }}
					<div class="col-sm-6">
					{{ Form::text('facebook_app_link', $settings_details->facebook_page_url,array('class' => 'form-control','placeholder'=>(Lang::has(Session::get('admin_lang_file').'.ADMIN_Facebook_Link')) ? trans(Session::get('admin_lang_file').'.ADMIN_Facebook_Link') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_Facebook_Link'),'maxlength' => '200' )) }} </div>
				</div>
				<div class="form-group">
					{{ Form::label('google_client_id_web', (Lang::has(Session::get('admin_lang_file').'.ADMIN_Google_App_ID_WEB')) ? trans(Session::get('admin_lang_file').'.ADMIN_Google_App_ID_WEB') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_Google_App_ID_WEB'),array('class'=>'control-label col-sm-2')) }}
					<div class="col-sm-6">
					{{ Form::text('google_client_id_web', $settings_details->google_client_id_web,array('class' => 'form-control','maxlength' => '200' )) }} </div>
				</div>
				
				<div class="form-group">
					{{ Form::label('google_secret_key', (Lang::has(Session::get('admin_lang_file').'.ADMIN_GOOGLE_SECRET_KEY')) ? trans(Session::get('admin_lang_file').'.ADMIN_GOOGLE_SECRET_KEY') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_GOOGLE_SECRET_KEY'),array('class'=>'control-label col-sm-2')) }}
					<div class="col-sm-6">
					{{ Form::text('google_secret_key', $settings_details->google_secret_key,array('class' => 'form-control','maxlength' => '200' )) }} </div>
				</div>
				<div class="form-group">
					{{ Form::label('google_page_url', (Lang::has(Session::get('admin_lang_file').'.ADMIN_GOOGLE_REDIRECT_URL')) ? trans(Session::get('admin_lang_file').'.ADMIN_GOOGLE_REDIRECT_URL') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_GOOGLE_REDIRECT_URL'),array('class'=>'control-label col-sm-2')) }}
					<div class="col-sm-6">
					{{ Form::text('google_redirect_url', $settings_details->google_redirect_url,array('class' => 'form-control','maxlength' => '200' )) }} </div>
				</div>
				<div class="form-group">
					{{ Form::label('google_page_url', (Lang::has(Session::get('admin_lang_file').'.ADMIN_GOOGLE_PAGE_URL')) ? trans(Session::get('admin_lang_file').'.ADMIN_GOOGLE_PAGE_URL') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_GOOGLE_PAGE_URL'),array('class'=>'control-label col-sm-2')) }}
					<div class="col-sm-6">
					{{ Form::text('google_page_url', $settings_details->google_page_url,array('class' => 'form-control','maxlength' => '200' )) }} </div>
				</div>
				<div class="form-group">
					{{ Form::label('google_map_key_web', (Lang::has(Session::get('admin_lang_file').'.ADMIN_GOOGLE_MAP_KEY_WEB')) ? trans(Session::get('admin_lang_file').'.ADMIN_GOOGLE_MAP_KEY_WEB') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_GOOGLE_MAP_KEY_WEB'),array('class'=>'control-label col-sm-2')) }}
					<div class="col-sm-6">
					{{ Form::text('google_map_key_web', $settings_details->google_map_key_web,array('class' => 'form-control' ,'maxlength' => '200')) }} </div>
				</div>
				
				<div class="form-group">
					@php 
						$caption = (Lang::has(Session::get('admin_lang_file').'.ADMIN_GOOGLE_PLAYSTORE_LINK')) ? trans(Session::get('admin_lang_file').'.ADMIN_GOOGLE_PLAYSTORE_LINK') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_GOOGLE_PLAYSTORE_LINK');
						$caption .='&nbsp;(&nbsp;';
						$caption .= (Lang::has(Session::get('admin_lang_file').'.ADMIN_CUS_APP')) ? trans(Session::get('admin_lang_file').'.ADMIN_CUS_APP') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_CUS_APP');
						$caption .='&nbsp;)';
					@endphp
					{{ Form::label('play_store_link', $caption,array('class'=>'control-label col-sm-2')) }}
					<div class="col-sm-6">
					{{ Form::text('play_store_link', $settings_details->playstore_link,array('class' => 'form-control' ,'maxlength' => '200')) }} </div>
				</div>
				<div class="form-group">
					@php 
						$caption = (Lang::has(Session::get('admin_lang_file').'.ADMIN_ITUNES_LINK')) ? trans(Session::get('admin_lang_file').'.ADMIN_ITUNES_LINK') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ITUNES_LINK');
						$caption .='&nbsp;(&nbsp;';
						$caption .= (Lang::has(Session::get('admin_lang_file').'.ADMIN_CUS_APP')) ? trans(Session::get('admin_lang_file').'.ADMIN_CUS_APP') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_CUS_APP');
						$caption .='&nbsp;)';
					@endphp
					{{ Form::label('itunes_link', $caption,array('class'=>'control-label col-sm-2')) }}
					<div class="col-sm-6">
					{{ Form::text('itunes_link', $settings_details->itunes_link,array('class' => 'form-control' ,'maxlength' => '200')) }} </div>
				</div>
				<div class="form-group">
					@php 
						$caption = (Lang::has(Session::get('admin_lang_file').'.ADMIN_GOOGLE_PLAYSTORE_LINK')) ? trans(Session::get('admin_lang_file').'.ADMIN_GOOGLE_PLAYSTORE_LINK') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_GOOGLE_PLAYSTORE_LINK');
						$caption .='&nbsp;(&nbsp;';
						$caption .= (Lang::has(Session::get('admin_lang_file').'.ADMIN_AGN_APP')) ? trans(Session::get('admin_lang_file').'.ADMIN_AGN_APP') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_AGN_APP');
						$caption .='&nbsp;)';
					@endphp
					{{ Form::label('play_store_link', $caption,array('class'=>'control-label col-sm-2')) }}
					<div class="col-sm-6">
					{{ Form::text('play_store_link_mer', $settings_details->playstore_link_merchant,array('class' => 'form-control' ,'maxlength' => '200')) }} </div>
				</div>
				<div class="form-group">
					@php 
						$caption = (Lang::has(Session::get('admin_lang_file').'.ADMIN_ITUNES_LINK')) ? trans(Session::get('admin_lang_file').'.ADMIN_ITUNES_LINK') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ITUNES_LINK');
						$caption .='&nbsp;(&nbsp;';
						$caption .= (Lang::has(Session::get('admin_lang_file').'.ADMIN_AGN_APP')) ? trans(Session::get('admin_lang_file').'.ADMIN_AGN_APP') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_AGN_APP');
						$caption .='&nbsp;)';
					@endphp
					{{ Form::label('itunes_link', $caption,array('class'=>'control-label col-sm-2')) }}
					<div class="col-sm-6">
					{{ Form::text('itunes_link_mer', $settings_details->playstore_link_merchant,array('class' => 'form-control' ,'maxlength' => '200')) }} </div>
				</div>
				<div class="form-group">
					@php 
						$caption = (Lang::has(Session::get('admin_lang_file').'.ADMIN_GOOGLE_PLAYSTORE_LINK')) ? trans(Session::get('admin_lang_file').'.ADMIN_GOOGLE_PLAYSTORE_LINK') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_GOOGLE_PLAYSTORE_LINK');
						$caption .='&nbsp;(&nbsp;';
						$caption .= (Lang::has(Session::get('admin_lang_file').'.ADMIN_DEL_APP')) ? trans(Session::get('admin_lang_file').'.ADMIN_DEL_APP') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_DEL_APP');
						$caption .='&nbsp;)';
					@endphp
					{{ Form::label('play_store_link', $caption,array('class'=>'control-label col-sm-2')) }}
					<div class="col-sm-6">
					{{ Form::text('play_store_link_del', $settings_details->playstore_link_deliver,array('class' => 'form-control' ,'maxlength' => '200')) }} </div>
				</div>
				<div class="form-group">
					@php 
						$caption = (Lang::has(Session::get('admin_lang_file').'.ADMIN_ITUNES_LINK')) ? trans(Session::get('admin_lang_file').'.ADMIN_ITUNES_LINK') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ITUNES_LINK');
						$caption .='&nbsp;(&nbsp;';
						$caption .= (Lang::has(Session::get('admin_lang_file').'.ADMIN_DEL_APP')) ? trans(Session::get('admin_lang_file').'.ADMIN_DEL_APP') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_DEL_APP');
						$caption .='&nbsp;)';
					@endphp
					{{ Form::label('itunes_link', $caption,array('class'=>'control-label col-sm-2')) }}
					<div class="col-sm-6">
					{{ Form::text('itunes_link_del', $settings_details->itunes_link_deliver,array('class' => 'form-control' ,'maxlength' => '200')) }} </div>
				</div>
				<div class="form-group">
					{{ Form::label('twitter_page_url', (Lang::has(Session::get('admin_lang_file').'.ADMIN_TWITTER_PAGE_URL')) ? trans(Session::get('admin_lang_file').'.ADMIN_TWITTER_PAGE_URL') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_TWITTER_PAGE_URL'),array('class'=>'control-label col-sm-2')) }}
					<div class="col-sm-6">
					{{ Form::text('twitter_page_url', $settings_details->twitter_page_url,array('class' => 'form-control' ,'maxlength' => '200')) }} </div>
				</div>
				<div class="form-group">
					{{ Form::label('linkedin_page_url', (Lang::has(Session::get('admin_lang_file').'.ADMIN_LINKEDIN_PAGE_URL')) ? trans(Session::get('admin_lang_file').'.ADMIN_LINKEDIN_PAGE_URL') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_LINKEDIN_PAGE_URL'),array('class'=>'control-label col-sm-2')) }}
					<div class="col-sm-6">
					{{ Form::text('linkedin_page_url', $settings_details->linkedin_page_url,array('class' => 'form-control' ,'maxlength' => '200')) }} </div>
				</div>
				<div class="form-group">
				

					{{ Form::label('analytics_code', (Lang::has(Session::get('admin_lang_file').'.ADMIN_ANALYTICS_CODE')) ? trans(Session::get('admin_lang_file').'.ADMIN_ANALYTICS_CODE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ANALYTICS_CODE'),array('class'=>'control-label col-sm-2')) }}
					<div class="col-sm-6">
					{{ Form::textarea('analytics_code', $settings_details->analytics_code,array('class' => 'form-control' )) }} </div>
				</div>
				
					<div class="form-group">
						<div class="col-sm-2"></div>
						<div class="col-sm-6">
						{{ Form::submit('Submit',array('class'=>'btn btn-success')) }}
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
	$("#social_settings").validate({
		//onkeyup: true,
		onfocusout: function (element) {
			this.element(element);
		},
		rules: {
			facebook_app_id: "required",
			facebook_app_secret: "required",
			facebook_app_link: {
				required : true,
				url: true
			},
			
			gs_facebook_redirect_url:"required",
			google_client_id_web: "required",
			google_secret_key: "required",
			google_page_url: {
				required : true,
				url: true
			},
			google_map_key_web:"required",
			twitter_page_url: {
				required : true,
				url: true
			},
			linkedin_page_url:{
				required : true,
				url: true
			},
			analytics_code: "required"	
		},
		messages: {
			facebook_app_id: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_PLEASE_ENTER_FB_APP_ID')) ? trans(Session::get('admin_lang_file').'.ADMIN_PLEASE_ENTER_FB_APP_ID') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PLEASE_ENTER_FB_APP_ID')}}",
			facebook_app_secret: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_PLEASE_ENTER_FB_APP_SECRET')) ? trans(Session::get('admin_lang_file').'.ADMIN_PLEASE_ENTER_FB_APP_SECRET') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PLEASE_ENTER_FB_APP_SECRET')}}",
			facebook_app_link: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_PLEASE_ENTER_FB_LINK')) ? trans(Session::get('admin_lang_file').'.ADMIN_PLEASE_ENTER_FB_LINK') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PLEASE_ENTER_FB_LINK')}}",
			gs_facebook_redirect_url: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_PLEASE_ENTER_REDIRECT_URL')) ? trans(Session::get('admin_lang_file').'.ADMIN_PLEASE_ENTER_REDIRECT_URL') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PLEASE_ENTER_REDIRECT_URL')}}",
			google_client_id_web: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_Please_Enter_Google_Client_ID')) ? trans(Session::get('admin_lang_file').'.ADMIN_Please_Enter_Google_Client_ID') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_Please_Enter_Google_Client_ID')}}",
			google_secret_key: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_PLEASE_SECRET_KEY')) ? trans(Session::get('admin_lang_file').'.ADMIN_PLEASE_SECRET_KEY') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PLEASE_SECRET_KEY')}}",
			google_page_url: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_PLEASE_GOOGLE_PAGE_URL')) ? trans(Session::get('admin_lang_file').'.ADMIN_PLEASE_GOOGLE_PAGE_URL') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PLEASE_GOOGLE_PAGE_URL')}}",
			google_map_key_web: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_PLEASE_ENTER_GOOGLE_MAP_KEY')) ? trans(Session::get('admin_lang_file').'.ADMIN_PLEASE_ENTER_GOOGLE_MAP_KEY') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PLEASE_ENTER_GOOGLE_MAP_KEY')}}",
			twitter_page_url: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_PLEASE_ENTER_TWITTER_URL')) ? trans(Session::get('admin_lang_file').'.ADMIN_PLEASE_ENTER_TWITTER_URL') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PLEASE_ENTER_TWITTER_URL')}}",
			linkedin_page_url: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_PLEASE_ENTER_LINKEDIN_URL')) ? trans(Session::get('admin_lang_file').'.ADMIN_PLEASE_ENTER_LINKEDIN_URL') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PLEASE_ENTER_LINKEDIN_URL')}}",
			analytics_code: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_PLEASE_ENTER_ANALYTICS_CODE')) ? trans(Session::get('admin_lang_file').'.ADMIN_PLEASE_ENTER_ANALYTICS_CODE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PLEASE_ENTER_ANALYTICS_CODE')}}"
		}
	});
</script>
@endsection
@stop
