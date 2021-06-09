<html>
	<body style="margin: 0; padding: 0;">
		<table cellpadding="0" cellspacing="0" width="600" align="center" style="border:1px solid #ddd;">
			<tr>
				<td>
				<table style="padding:10px;width:100%;">
					<tr>
						<td align="center">
							
							<img src="{{$LOGOPATH}}" alt="@lang($lang.'.API_VERIFICATION_MAIL')" class="img-responsive logo"  width="100">
						
						</td>
					</tr>
				</table>
				</td>
			</tr>
			
			<tr>
				<td>
					<table style="width:100%;background:url({{url('').'/resources/views/email/'}}bg.jpg); padding:50px 20px;">
						<tr>
							<td colspan="1" style="text-align:center; font-family:cursive; font-size:20px; font-weight:bold; padding-bottom: 20px;"> @lang($lang.'.API_VERIFY_UR_MAIL')</td>							
						</tr>
						<tr>
							<td style="text-align:center; font-family:sans-serif; font-size:17px; padding-bottom: 20px;">@lang($lang.'.API_VR_CODE')</td>
						</tr>
						<tr>
							<td style="text-align:center; font-family:sans-serif; font-size:17px; padding-bottom: 20px;">{{$code}}</td>
						</tr>
						
					</table>
				</td>
			</tr>
			
			<tr>
				<td align="center" style="background:#69c332; color:#fff; font-family:sans-serif; font-size:13px;padding: 10px 15px;">
					<p style="padding:0px;margin:0px;"><a href="{{url('contact-us')}}" style="color:#fff; text-decoration:none;">@lang($lang.'.API_CONTACT_US')</a> © {{$FOOTERNAME}}</p>
				</td>
			</tr>
			
		</table>	
	</body>
</html>