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
							<img src="{{$path}}" alt="" class="img-responsive logo"  width="100">
						
						</td>
					</tr>
				</table>
				</td>
			</tr>
			
			
			
			
			<tr>
				<td>
					<table style="font-family:sans-serif; font-size:14px; width:100%; padding:30px 20px;"  align="center">
						<tr>
							<td colspan="2" style="padding-bottom:5px; font-family:sans-serif; "> Hi {{$name}},</td>
						</tr>
						<tr>
							<td colspan="2" style="padding-bottom:5px; font-family:sans-serif; font-size:16px;font-weight: 600;">
								@if($status == 0)
									@php echo __(Session::get('admin_lang_file').'.ADMIN_CH_CUS_STATUS',['status' => 'blocked']); @endphp
								@elseif($status == 1)
									@php echo __(Session::get('admin_lang_file').'.ADMIN_CH_CUS_STATUS',['status' => 'unblocked']); @endphp 
								@elseif($status == 2)
									@php echo __(Session::get('admin_lang_file').'.ADMIN_CH_CUS_STATUS',['status' => 'deleted']); @endphp
								@endif
								{{-- <br>@lang(Session::get('admin_lang_file').'.ADMIN_REVERT_CN_ADMIN') --}}
							</td>
						</tr>
						
					</table>
				</td>
			</tr>
			
			<tr>
				<td align="center" style="background:#e48743; padding:10px 15px; color:#fff; font-family:sans-serif; font-size:13px;">
					<p><a href="{{url('contact-us')}}" style="color:#fff; text-decoration:none;">@lang(Session::get('admin_lang_file').'.CONTACT_US')</a> © {{$FOOTERNAME}}</p>
				</td>
			</tr>
			
		</table>	
	</body>
</html>