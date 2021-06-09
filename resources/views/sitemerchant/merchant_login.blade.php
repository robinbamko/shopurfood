<!doctype html>
<html lang="en" class="fullscreen-bg">
@php $logo_details = DB::table('gr_logo_settings')->select('admin_logo','favicon')->first(); @endphp
<head>
	<title>{{ $SITENAME }} | {{ (Lang::has(Session::get('mer_lang_file').'.MER_LOGIN_PAGE')!= '') ?  trans(Session::get('mer_lang_file').'.MER_LOGIN_PAGE') :  trans($MER_OUR_LANGUAGE.'.MER_LOGIN_PAGE') }} </title>
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
	<link rel="apple-touch-icon" sizes="76x76" href="{{url('')}}/public/admin/assets/img/apple-icon.png">
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
								@if(empty($logo_details) === false)
									<img src="{{url('public/images/logo/'.$logo_details->admin_logo)}}" alt="Logo">
								@endif
							</div>

							<div class="alert alert-success alert-dismissible" role="alert" @if(Session::get('message')=='') style="display:none" @endif>
								<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true"><i class="fa fa-times-circle"></i></span></button>
								{{ Session::get('message') }}
							</div>
							<p class="lead">
								{{(Lang::has(Session::get('mer_lang_file').'.MER_LOGIN_UR_ACC')) ? trans(Session::get('mer_lang_file').'.MER_LOGIN_UR_ACC') : trans($MER_OUR_LANGUAGE.'.MER_LOGIN_UR_ACC')}}
							</p>
						</div>
						{!! Form::open(['method' => 'post','class' => 'form-auth-small','url' => 'mer_login_check','onSubmit' => 'return login_check();']) !!}
						<div class="form-group login-input">

							{!! Form::text('mer_email','nagoor@mailinator.com',['class'=>'form-control','placeholder' => (Lang::has(Session::get('mer_lang_file').'.MER_EMAIL')) ? trans(Session::get('mer_lang_file').'.MER_EMAIL') : trans($MER_OUR_LANGUAGE.'.MER_EMAIL'),'id' => 'mer_email','autocomplete' => 'off','required']) !!}
							<p class="error-block" id="email_error" style="display:none;color:red;"></p>
							@if ($errors->has('email') )
								<p class="error-block" style="color:red;">{{ $errors->first('email') }}</p>
							@endif
							@if ($errors->has('mer_email') )
								<p class="error-block" style="color:red;">{{ $errors->first('mer_email') }}</p>
							@endif

						</div>
						<div class="form-group login-input">
							{!! Form::input('password','mer_pass','123456',['class'=>'form-control','placeholder' => (Lang::has(Session::get('mer_lang_file').'.MER_PASSWORD')) ? trans(Session::get('mer_lang_file').'.MER_PASSWORD') : trans($MER_OUR_LANGUAGE.'.MER_PASSWORD'),'id' => 'mer_pass','autocomplete' => 'off','password']) !!}
							<p class="error-block" id="password_error" style="color:red;display:none;">{{ $errors->first('pass') }}</p>
							@if ($errors->has('pass'))
								<p class="error-block" style="color:red;">{{ $errors->first('pass') }}</p>
							@endif

						</div>
						{!! Form::submit((Lang::has(Session::get('mer_lang_file').'.MER_LOGIN')) ? trans(Session::get('mer_lang_file').'.MER_LOGIN') : trans($MER_OUR_LANGUAGE.'.MER_LOGIN'),['class'=>'btn btn-primary btn-lg login-btn-input btn-block']) !!}

						<div class="bottom">
							<span class="helper-text"><i class="fa fa-lock"></i> <a href="{{url('mer_forgot_password')}}">{{(Lang::has(Session::get('mer_lang_file').'.MER_FORGET_PASS')) ? trans(Session::get('mer_lang_file').'.MER_FORGET_PASS') : trans($MER_OUR_LANGUAGE.'.MER_FORGET_PASS')}}</a></span>
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


<script>
    function login_check()
    {
        var merchant_email = $('#mer_email');
        var merchant_pass = $('#mer_pass');
        var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
        if(merchant_email.val() == '')
        {
            merchant_email.css({border:'1px solid red'});
            merchant_email.focus();
            $('#email_error').html('{{ (Lang::has(Session::get('mer_lang_file').'.MER_ENTER_YOUR_EMAIL')!= '') ?  trans(Session::get('mer_lang_file').'.MER_ENTER_YOUR_EMAIL'): trans($MER_OUR_LANGUAGE.'.MER_ENTER_YOUR_EMAIL') }}').show();
            return false;
        }
        else if(!emailReg.test(merchant_email.val()))
        {
            merchant_email.css({border:'1px solid red'});
            merchant_email.focus();
            $('#email_error').html('{{ (Lang::has(Session::get('mer_lang_file').'.MER_ENTER_YOUR_VALID_EMAIL')!= '') ?  trans(Session::get('mer_lang_file').'.MER_ENTER_YOUR_VALID_EMAIL') : trans($MER_OUR_LANGUAGE.'.MER_ENTER_YOUR_VALID_EMAIL') }}').show();
            return false;
        }
        else
        {
            merchant_email.css({border:'1px solid #eaeaea'});
            $('#email_error').html('').hide();
        }
        if($('#mer_pass').val() == '')
        {
            merchant_pass.css({border:'1px solid red'});
            merchant_pass.focus();
            $('#password_error').html('{{ (Lang::has(Session::get('mer_lang_file').'.MER_ENTER_YOUR_PASSWORD')!= '') ?  trans(Session::get('mer_lang_file').'.MER_ENTER_YOUR_PASSWORD'): trans($MER_OUR_LANGUAGE.'.MER_ENTER_YOUR_PASSWORD') }}').show();
            return false;
        }
        else
        {
            merchant_pass.css({border:'1px solid #eaeaea'});
            $('#password_error').html('').hide();
        }
    }
</script>

</body>

</html>
