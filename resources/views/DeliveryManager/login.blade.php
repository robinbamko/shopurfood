<!doctype html>
<html lang="en" class="fullscreen-bg">
@php $logo_details = DB::table('gr_logo_settings')->select('admin_logo','favicon')->first(); @endphp
<head>
	<title>{{ $SITENAME }} | {{ (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_LOGIN_PAGE')!= '') ?  trans(Session::get('DelMgr_lang_file').'.DELMGR_LOGIN_PAGE') :  trans($DELMGR_OUR_LANGUAGE.'.DELMGR_LOGIN_PAGE') }} </title>
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
							<div class="alert alert-success alert-dismissible" role="alert" @if(Session::get('message')=='') style="display:none" @endif>
								<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true"><i class="fa fa-times-circle"></i></span></button>
								{{ Session::get('message') }}
							</div>
							<p class="lead">
								{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_LOGIN_UR_ACC')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_LOGIN_UR_ACC') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_LOGIN_UR_ACC')}}
							</p>
						</div>
						{!! Form::open(['method' => 'post','class' => 'form-auth-small','url' => 'delivery-manager-authentication','onSubmit' => 'return login_check();']) !!}
						<div class="form-group login-input">

							{!! Form::text('dm_email','admindelivery@mailinator.com',['class'=>'form-control','placeholder' => (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_EMAIL')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_EMAIL') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_EMAIL'),'id' => 'dm_email','autocomplete' => 'off']) !!}
							<p class="error-block" id="email_error" style="display:none;color:red;"></p>
							@if ($errors->has('dm_email') )
								<p class="error-block" style="color:red;">{{ $errors->first('dm_email') }}</p>
							@endif

						</div>
						<div class="form-group login-input">
							{!! Form::input('password','dm_password','123456',['class'=>'form-control','placeholder' => (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_PASSWORD')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_PASSWORD') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_PASSWORD'),'id' => 'dm_password','autocomplete' => 'off']) !!}
							<p class="error-block" id="password_error" style="color:red;display:none;">{{ $errors->first('pass') }}</p>
							@if ($errors->has('dm_password'))
								<p class="error-block" style="color:red;">{{ $errors->first('dm_password') }}</p>
							@endif

						</div>
						{!! Form::submit((Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_LOGIN')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_LOGIN') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_LOGIN'),['class'=>'btn btn-primary btn-lg btn-block login-btn-input']) !!}

						<div class="bottom">
							<span class="helper-text"><i class="fa fa-lock"></i> <a href="{{url('delivery-manager-forgot-password')}}">{{(Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_FORGET_PASS')) ? trans(Session::get('DelMgr_lang_file').'.DELMGR_FORGET_PASS') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_FORGET_PASS')}}</a></span>
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
        var merchant_email = $('#dm_email');
        var merchant_pass = $('#dm_password');
        var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
        if(merchant_email.val() == '')
        {
            merchant_email.css({border:'1px solid red'});
            merchant_email.focus();
            $('#email_error').html('{{ (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_ENTER_YOUR_EMAIL')!= '') ?  trans(Session::get('DelMgr_lang_file').'.DELMGR_ENTER_YOUR_EMAIL'): trans($DELMGR_OUR_LANGUAGE.'.DELMGR_ENTER_YOUR_EMAIL') }}').show();
            return false;
        }
        else if(!emailReg.test(merchant_email.val()))
        {
            merchant_email.css({border:'1px solid red'});
            merchant_email.focus();
            $('#email_error').html('{{ (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_ENTER_YOUR_VALID_EMAIL')!= '') ?  trans(Session::get('DelMgr_lang_file').'.DELMGR_ENTER_YOUR_VALID_EMAIL') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_ENTER_YOUR_VALID_EMAIL') }}').show();
            return false;
        }
        else
        {
            merchant_email.css({border:'1px solid #eaeaea'});
            $('#email_error').html('').hide();
        }
        if($('#dm_password').val() == '')
        {
            merchant_pass.css({border:'1px solid red'});
            merchant_pass.focus();
            $('#password_error').html('{{ (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_ENTER_YOUR_PASSWORD')!= '') ?  trans(Session::get('DelMgr_lang_file').'.DELMGR_ENTER_YOUR_PASSWORD'): trans($DELMGR_OUR_LANGUAGE.'.DELMGR_ENTER_YOUR_PASSWORD') }}').show();
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