<!doctype html>
<html lang="en" class="fullscreen-bg">
@php $logo_details = DB::table('gr_logo_settings')->select('admin_logo','favicon')->first(); @endphp
<head>
	<title>{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_FORGET_PASSWORD')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_FORGET_PASSWORD') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_FORGET_PASSWORD')}} </title>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
	<!-- VENDOR CSS -->
	<link rel="stylesheet" href="{{url('')}}/public/admin/assets/css/bootstrap.min.css">
	<link rel="stylesheet" href="{{url('')}}/public/admin/assets/vendor/font-awesome/css/font-awesome.min.css">
	<link rel="stylesheet" href="{{url('')}}/public/admin/assets/vendor/linearicons/style.css">
	<!-- MAIN CSS -->
	<link rel="stylesheet" href="{{url('')}}/public/admin/assets/css/main.css">
	<!-- GOOGLE FONTS -->
	<link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700" rel="stylesheet">
	<!-- ICONS -->
	@if(empty($logo_details) === false)
	<link rel="icon" type="image/png" sizes="96x96" href="{{url('public/images/logo/'.$logo_details->favicon)}}">
	
	@endif
	
</head>

<body>
	<!-- WRAPPER -->
	<div id="wrapper" class="login-form-bg">
		<div class="vertical-align-wrap">
			<div class="vertical-align-middle">
				<div class="auth-box ">
					<div class="left">
						<div class="content">
							<div class="header forgot-pwd-form">													
								<h3>
									{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_FORGET_PASSWORD')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_FORGET_PASSWORD') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_FORGET_PASSWORD')}}
								 </h3>
								 @if(Session::has('message'))
								 <p>{{Session::get('message')}}</p>
								 @endif
								<div class="logo">
									<p>
										{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_RESET_PASSWORD')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_RESET_PASSWORD') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_RESET_PASSWORD')}}
								 	</p>
								</div>
							</div>	

							{!! Form::open(['method' => 'post','class' => 'form-auth-small','url' => 'delmgr_forgot_password_submit','id'=>'profile-form']) !!}
								{!! Form::hidden('_token',csrf_token())!!}
								<div class="form-group login-input">																	
									{!! Form::email('frgt_email','',['class'=>'form-control','placeholder' => (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_EMAIL')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_EMAIL') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_EMAIL'),'id' => 'frg_email','required','autocomplete' => 'off']) !!}
									
									@if ($errors->has('email')) 
									<p class="error-block" style="color:red;">{{ $errors->first('email') }}</p>
									@endif
									
								</div>
								
									{!! Form::submit((Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_SUBMIT')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_SUBMIT') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_SUBMIT'),['class'=>'btn btn-primary btn-lg btn-block login-btn-input']) !!}	
									@php $url = url('delivery-manager-login')@endphp									
										{!! Form::button('Cancel',['class' => 'btn btn-warning btn-lg btn-block login-btn-input' ,'onclick'=>"javascript:window.location.href='$url'"])!!}				
							{!! Form::close()!!}
						</div>
					</div>
						<div class="right">
							<div class="overlay"></div>
							<div class="content text">
								<!-- <h1 class="heading">Free Bootstrap dashboard template</h1>
								<p>by The Develovers</p> -->
							</div>
						</div>
					<div class="clearfix"></div>					
				</div>
			</div>
		</div>
	</div>
	<!-- END WRAPPER -->
	<script src="{{url('')}}/public/admin/assets/scripts/jquery.validate.js"></script>
<script>

	$("#profile-form").validate({
		onfocusout: function (element) {
			this.element(element);
		},
		rules: {
			frgt_email: {required: true,email: true}
		},
		messages: {
			frgt_email: "{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_ENTER_EMAIL')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_ENTER_EMAIL') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_ENTER_EMAIL')}}",
		}
	});
	</script>
</body>

</html>
