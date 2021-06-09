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
							<img src="{{$path}}" alt="{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_LOGO')) ? trans(Session::get('mer_lang_file').'.ADMIN_LOGO') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_LOGO')}}" class="img-responsive logo"  width="100">
						</td>						
					</tr>
				</table>
				</td>
			</tr>
			
			
			<tr>
				<td>
					<table style="width:100%;background:url('{{url('public/images/order_image.jpg')}}')no-repeat; padding:56px 20px;">
						<tr>
							<td colspan="1" style="text-align:center; font-family:cursive; font-size:35px; padding-bottom: 20px;  color: #fff;">
							{{$heading}}
							</td>							
						</tr>
						
					</table>
				</td>
			</tr>	


			<tr>
				<td>
					<table style="font-family:sans-serif; font-size:14px; width:100%; padding:0px 20px 10px; border-bottom: 1px solid #ddd;"  align="center">
						<tr>
							<td colspan="2" style="border-bottom: 2px solid #69c332; padding-bottom:5px; font-family:sans-serif; font-size:22px;color: #69c332;">{{(Lang::has(Session::get('mer_lang_file').'.MER_REQUEST_DETAILS')) ? trans(Session::get('mer_lang_file').'.MER_REQUEST_DETAILS') : trans($this->MER_OUR_LANGUAGE.'.MER_REQUEST_DETAILS')}} </td>
						</tr>
						<tr>
							<td>
								<table style="width:100%;">
									<tr>
										<td>
											<table style="width:100%" cellpadding="5" cellspacing="5">
												<tr>
													<td width="30%"><strong>{{(Lang::has(Session::get('mer_lang_file').'.MER_FEATURED_DATES')) ? trans(Session::get('mer_lang_file').'.MER_FEATURED_DATES') : trans($this->MER_OUR_LANGUAGE.'.MER_FEATURED_DATES')}} : </strong></td>
													<td align="left">{{$featured_date}}</td>
												</tr>
												{!!$payment_detail!!}
											</table>
										</td>

									</tr>
								</table>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td align="center" style="background:#69c332; padding:10px 15px; color:#fff; font-family:sans-serif; font-size:13px;">
					<p style="padding:0px;margin:0px;"><a href="{{url('contact-us')}}" style="color:#fff; text-decoration:none;">{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_CNCT_US')) ? trans(Session::get('mer_lang_file').'.ADMIN_CNCT_US') : trans($this->MER_OUR_LANGUAGE.'.ADMIN_CNCT_US')}} </a> ©&nbsp;{{$FOOTERNAME}}</p>
				</td>
			</tr>
			
		</table>	
	</body>
</html>