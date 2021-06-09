<html>
	<body style="margin: 0; padding: 0;">
		<table cellpadding="0" cellspacing="0" width="600" align="center" style="border:1px solid #ddd;">
					
			
			<tr>
				<td>
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
							<td colspan="1" style="text-align:center; font-family:cursive; font-size:20px; font-weight:bold; padding-bottom: 20px;"> @lang(Session::get('front_lang_file').'.FRONT_WELCOME_TO') {{ $SITENAME}}..</td>							
						</tr>
						<tr>
							<td style="text-align:center; font-family:sans-serif; font-size:17px; padding-bottom: 20px;">@lang(Session::get('front_lang_file').'.FRONT_ACCOUNT_CRATED_SUXES')</td>
						</tr>
						<tr>
							<td align="center"><a href="{{url('')}}"><button type="button" style="background:#e48743; border:0; padding:10px 20px; color:#fff; font-size:15px;">@lang(Session::get('front_lang_file').'.FRONT_LOGIN_YOUR_ACCOUNT')</button></a></td>
						</tr>
					</table>
				</td>
			</tr>
			
			
			
			<tr>
				<td>
					<table style="font-family:sans-serif; font-size:14px; width:100%; padding:30px 20px;"  align="center">
						<tr>
							<td colspan="2" style="border-bottom: 2px solid #69c332; padding-bottom:5px; font-family:sans-serif; font-size:16px;color: #69c332;font-weight: 600;">
								@php echo __(Session::get('front_lang_file').'.FRONT_REG_DETAILS',['Name' => $SITENAME]); @endphp
							</td>
						</tr>
						<tr>
							<td style="padding:20px 0 10px;">@lang(Session::get('front_lang_file').'.ADMIN_REG_NAME')</td>
							<td style="color:#551a8b; font-weight:600;padding:20px 0 10px;">{{$name}}</td>
						</tr>
						<tr>
							<td style="padding:10px 0;">@lang(Session::get('front_lang_file').'.ADMIN_REG_MAIL')</td>
							<td style="color:#69c332; font-weight:600;"><a href="javascript:;">{{$email}}</a></td>
						</tr>
						<tr>
							<td style="padding:10px 0;">@lang(Session::get('front_lang_file').'.ADMIN_REG_PASS')</td>
							<td style="color:#551a8b; font-weight:600;">{{$password}}</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td style="background: #efefef;">
					<ul style="list-style: none; padding: 30px 15px 25px; text-align: center; margin: 0;">
						<li style="display: inline-block; padding-right: 15px;"><a href="{{$CUS_ANDR_LINK}}"><img src="{{url('').'/public/images/app2.png'}}" width="130"></a></li>
						<li style="display: inline-block;"><a href="{{$CUS_IOS_LINK}}"><img src="{{url('').'/public/images/app1.png'}}" width="130"></a></li>
					</ul>
				</td>
			</tr>
			<tr>
				<td align="center" style="background:#e48743; padding:10px 15px; color:#fff; font-family:sans-serif; font-size:13px;">
					<p><a href="{{url('contact-us')}}" style="color:#fff; text-decoration:none;">@lang(Session::get('front_lang_file').'.FRONT_CNCT_US')</a> © {{$FOOTERNAME}}</p>
				</td>
			</tr>
			
		</table>	
	</body>
</html>