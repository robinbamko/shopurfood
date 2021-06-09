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
					<table style="font-family:sans-serif; font-size:14px; width:100%; padding:30px 20px;"  align="center">
						<tr>
							<td colspan="2" style="padding-bottom:5px; font-family:sans-serif; font-size:13px;color: #69c332;font-weight: 600;">
								@lang(Session::get('front_lang_file').'.SUBSCRIPTION_EMAIL_CREATED_SUCCESSFULLY_FOR_YOUR_MAIL_ID')
							</td>
						</tr>
						<tr>
							<td colspan="2" style="padding-bottom:5px; font-family:sans-serif; font-size:16px;color: #69c332;font-weight: 600;">
								@lang(Session::get('front_lang_file').'.THANK_YOU_FOR_SUBSCRIPTION')
							</td>
						</tr>
					</table>
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