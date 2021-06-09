<!doctype html>
<html lang="en" class="fullscreen-bg">
@php $logo_details = DB::table('gr_logo_settings')->select('admin_logo','favicon')->first(); @endphp
<head>
	<title>{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_LOGIN')) ? trans(Session::get('admin_lang_file').'.ADMIN_LOGIN') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_LOGIN')}} </title>
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
						<div class="header login-form-head">
						
							<h3>Welcome to {{$SITENAME}}</h3>
							<h6>Login</h6>

							<div class="logo text-center">
								@if(empty($logo_details) === false && $logo_details->admin_logo != '')
									@php $filename = public_path('images/logo/').$logo_details->admin_logo;  @endphp
									@if(file_exists($filename))
										<img src="{{url('public/images/logo/'.$logo_details->admin_logo)}}" alt="Logo">
									@else
										<img src="{{url('public/images/noimage/'.$no_logo)}}" alt="Logo">
									@endif
								@else
									<img src="{{url('public/images/noimage/'.$no_logo)}}" alt="Logo">
								@endif
							</div>
							<p class="lead">
								{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_LOGIN_UR_ACC')) ? trans(Session::get('admin_lang_file').'.ADMIN_LOGIN_UR_ACC') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_LOGIN_UR_ACC')}}
							</p>
							<div class="alert alert-success alert-dismissible" role="alert" @if(Session::get('message')=='') style="display:none" @endif>
								<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true"><i class="fa fa-times-circle"></i></span></button>
								{{ Session::get('message') }}
							</div>
						</div>
						{!! Form::open(['method' => 'post','class' => 'form-auth-small','url' => 'admin_check_login']) !!}
						{!! Form::hidden('_token',csrf_token())!!}
						<div class="form-group login-input">

							{!! Form::email('adm_email','admin@gmail.com',['class'=>'form-control','placeholder' => (Lang::has(Session::get('admin_lang_file').'.ADMIN_EMAIL')) ? trans(Session::get('admin_lang_file').'.ADMIN_EMAIL') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_EMAIL'),'id' => 'adm_email','required','autocomplete' => 'off']) !!}

							@if ($errors->has('email'))
								<p class="error-block" style="color:red;">{{ $errors->first('email') }}</p>
							@endif

						</div>
						<div class="form-group login-input">
							{!! Form::input('password','adm_pass','123456',['class'=>'form-control','placeholder' => (Lang::has(Session::get('admin_lang_file').'.ADMIN_PASSWORD')) ? trans(Session::get('admin_lang_file').'.ADMIN_PASSWORD') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PASSWORD'),'id' => 'adm_password','required','autocomplete' => 'off']) !!}
							@if ($errors->has('pass'))
								<p class="error-block" style="color:red;">{{ $errors->first('pass') }}</p>
							@endif

						</div>
						{!! Form::submit((Lang::has(Session::get('admin_lang_file').'.ADMIN_LOGIN')) ? trans(Session::get('admin_lang_file').'.ADMIN_LOGIN') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_LOGIN'),['class'=>'btn btn-primary login-btn-input btn-lg btn-block']) !!}

						<div class="bottom">
							<span class="helper-text"><i class="fa fa-lock"></i> <a href="{{ url('forgot_password') }}">{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_FORGET_PASS')) ? trans(Session::get('admin_lang_file').'.ADMIN_FORGET_PASS') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_FORGET_PASS')}}</a></span>
						</div>
						{!! Form::close()!!}
					</div>
				</div>
				<div class="right">
					<img src="{{url('')}}/public/admin/assets/img/login_bg1.png">
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
<script src="{{url('')}}/public/admin/assets/vendor/jquery/jquery.min.js"></script>
<script src="{{url('')}}/public/admin/assets/vendor/bootstrap/js/bootstrap.min.js"></script>
</body>
</html>