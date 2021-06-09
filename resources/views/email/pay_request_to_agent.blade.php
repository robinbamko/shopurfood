<html>
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
							<img src="{{$path}}" alt="{{ (Lang::has(Session::get('DelMgr_lang_file').'.DELMGR_LOGO')!= '') ? trans(Session::get('DelMgr_lang_file').'.DELMGR_LOGO') : trans($DELMGR_OUR_LANGUAGE.'.DELMGR_LOGO') }}" class="img-responsive logo"  width="100">
						</td>						
					</tr>
				</table>
				</td>
			</tr>
			
			<tr>
				<td>
					<table style="width:100%;padding:0px 20px;">
						
						<tr>
							<td style="font-family:sans-serif; font-size:17px; padding-bottom: 20px;  color: #000; line-height: 25px;">{{ (Lang::has(Session::get('DelMgr_lang_file').'.HI')!= '') ? trans(Session::get('DelMgr_lang_file').'.HI') : trans($DELMGR_OUR_LANGUAGE.'.HI') }} {{ucfirst($name)}}<br>
							{{ (Lang::has(Session::get('DelMgr_lang_file').'.DEL_THE_DELMGR')!= '') ? trans(Session::get('DelMgr_lang_file').'.DEL_THE_DELMGR') : trans($DELMGR_OUR_LANGUAGE.'.DEL_THE_DELMGR') }}({{$dm_name}}) {{ (Lang::has(Session::get('DelMgr_lang_file').'.DEL_SENT_YOU')!= '') ? trans(Session::get('DelMgr_lang_file').'.DEL_SENT_YOU') : trans($DELMGR_OUR_LANGUAGE.'.DEL_SENT_YOU') }}</td>
						</tr>
						
					</table>
				</td>
			</tr>
			
			
			<tr>
				<td>
					<table style="font-family:sans-serif; font-size:14px; width:100%; padding:20px 20px;"  align="center">
						<tr>
							<td colspan="2" style="border-bottom: 2px solid #69c332; padding-bottom:5px; font-family:sans-serif; font-size:22px;color: #69c332;"> {{ (Lang::has(Session::get('DelMgr_lang_file').'.DEL_PAYREQUEST_DETAILS')!= '') ? trans(Session::get('DelMgr_lang_file').'.DEL_PAYREQUEST_DETAILS') : trans($DELMGR_OUR_LANGUAGE.'.DEL_PAYREQUEST_DETAILS') }}</td>
						</tr>

						<tr>
							<td style="padding:10px 0;">{{ (Lang::has(Session::get('DelMgr_lang_file').'.DEL_AMOUNT')!= '') ? trans(Session::get('DelMgr_lang_file').'.DEL_AMOUNT') : trans($DELMGR_OUR_LANGUAGE.'.DEL_AMOUNT') }}</td>
							<td style="color:#69c332; font-weight:600;"><a href="">{{$default_currency}} {{$amount}}</a></td>
						</tr>
						
					</table>
				</td>
			</tr>
			
			<tr>
				<td align="center" style="background:#69c332; padding:10px 15px; color:#fff; font-family:sans-serif; font-size:13px;">
					 <p style="padding:0px;margin:0px;"><a href="{{url('contact-us')}}" style="color:#fff; text-decoration:none;">{{ (Lang::has(Session::get('DelMgr_lang_file').'.DEL_CNCT_US')!= '') ? trans(Session::get('DelMgr_lang_file').'.DEL_CNCT_US') : trans($DELMGR_OUR_LANGUAGE.'.DEL_CNCT_US') }}</a> © {{$FOOTERNAME}}</p>
				</td>
			</tr>
			
		</table>	
	</body>
</html>