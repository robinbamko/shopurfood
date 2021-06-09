<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<meta name="format-detection" content="telephone=no" /> <!-- disable auto telephone linking in iOS -->
		<title>{{ (Lang::has(Session::get('front_lang_file').'.ADMIN_REG_DETAILS')!= '') ? trans(Session::get('front_lang_file').'.ADMIN_REG_DETAILS') : trans($FRONT_LANGUAGE.'.ADMIN_REG_DETAILS') }}</title>
		
		
	</head>
	<body style="margin: 0; padding: 0;">
		<table cellpadding="0" cellspacing="0" width="600" align="center" style="border:1px solid #ddd;">
					
			
			<tr>
				<td style="border-top: 5px solid #69c332cc;">
				<table style="padding:10px;width:100%;">
					<tr>
						<td align="center">
							
							@php $path = url('').'/public/images/noimage/default_image_logo.jpg'; @endphp
							@if(count($logo_settings_details) > 0)
							@php
								foreach($logo_settings_details as $logo_set_val){ }
							@endphp
							@if($logo_set_val->admin_logo != '')
								@php $filename = public_path('images/logo/').$logo_set_val->admin_logo; @endphp 
								@if(file_exists($filename))
								@php $path = url('').'/public/images/logo/'.$logo_set_val->admin_logo; @endphp
								@endif
							@endif							
							@endif
							<img src="{{$path}}" alt="@lang(Session::get('front_lang_file').'.FRONT_REG_DETAILS')" class="img-responsive logo"  width="100">
						</td>						
					</tr>
				</table>
				</td>
			</tr>
			
			<tr>
				<td>
					<table style="width:100%;background:url({{url('').'/resources/views/email/'}}bg.jpg); padding:50px 20px;">
						<tr>
							<td colspan="1" style="text-align:center; font-family:cursive; font-size:35px; padding-bottom: 20px;  color: #fff;">{{ (Lang::has(Session::get('front_lang_file').'.WELCOME_TO_EDISON')!= '') ? trans(Session::get('front_lang_file').'.WELCOME_TO_EDISON') : trans($FRONT_LANGUAGE.'.WELCOME_TO_EDISON') }}{{ $SITENAME}}..</td>							
						</tr>
						<tr>
							<td style="text-align:center; font-family:sans-serif; font-size:17px; padding-bottom: 20px;  color: #fff; line-height: 25px;">{{ (Lang::has(Session::get('front_lang_file').'.YOUR_ACC_CREATED')!= '') ? trans(Session::get('front_lang_file').'.YOUR_ACC_CREATED') : trans($FRONT_LANGUAGE.'.YOUR_ACC_CREATED') }}&nbsp;@lang(Session::get('front_lang_file').'.YOUR_ACTIVE_AF_APPROVE')</td>
						</tr>
						
					</table>
				</td>
			</tr>
			
			
			<tr>
				<td>
					<table style="font-family:sans-serif; font-size:14px; width:100%; padding:20px 20px;"  align="center">
						<tr>
							<td colspan="2" style="border-bottom: 2px solid #69c332; padding-bottom:5px; font-family:sans-serif; font-size:22px;color: #69c332;">{{ (Lang::has(Session::get('front_lang_file').'.ADMIN_REG_DETAILS')!= '') ? trans(Session::get('front_lang_file').'.ADMIN_REG_DETAILS') : trans($FRONT_LANGUAGE.'.ADMIN_REG_DETAILS') }}</td>
						</tr>
						<tr>
							<td style="padding:20px 0 10px;">{{ (Lang::has(Session::get('front_lang_file').'.CUS_REG_NAME')!= '') ? trans(Session::get('front_lang_file').'.CUS_REG_NAME') : trans($FRONT_LANGUAGE.'.CUS_REG_NAME') }}</td>
							<td style="color:#551a8b; font-weight:600;padding:20px 0 10px;">{{$name}}</td>
						</tr>
						<tr>
							<td style="padding:10px 0;">{{ (Lang::has(Session::get('front_lang_file').'.CUS_REG_MAIL')!= '') ? trans(Session::get('front_lang_file').'.CUS_REG_MAIL') : trans($FRONT_LANGUAGE.'.CUS_REG_MAIL') }}</td>
							<td style="color:#69c332; font-weight:600;"><a href="">{{$email}}</a></td>
						</tr>
						<tr>
							<td style="padding:10px 0;">{{ (Lang::has(Session::get('front_lang_file').'.CUS_REG_PASS')!= '') ? trans(Session::get('front_lang_file').'.CUS_REG_PASS') : trans($FRONT_LANGUAGE.'.CUS_REG_PASS') }}</td>
							<td style="color:#551a8b; font-weight:600;">{{$password}}</td>
						</tr>
						
					</table>
				</td>
			</tr>
			<tr>
				<td style="background: #efefef;">
					<ul style="list-style: none; padding: 30px 15px 25px; text-align: center; margin: 0;">
						<li style="display: inline-block; padding-right: 15px;"><a href="{{$DEL_ANDR_LINK}}"><img src="{{url('').'/public/images/app2.png'}}" width="130"></a></li>
						<li style="display: inline-block;"><a href="{{$DEL_IOS_LINK}}"><img src="{{url('').'/public/images/app1.png'}}" width="130"></a></li>
					</ul>
				</td>
			</tr>
			<tr>
				<td align="center" style="background:#69c332; padding:10px 15px; color:#fff; font-family:sans-serif; font-size:13px;">
					<p style="padding:0px;margin:0px;"><a href="{{url('contact-us')}}" style="color:#fff; text-decoration:none;">@lang(Session::get('front_lang_file').'.FRONT_CNCT_US')</a> © {{$FOOTERNAME}}</p>
				</td>
			</tr>
			
		</table>	
	</body>
</html>
		