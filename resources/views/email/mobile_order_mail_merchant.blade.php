<html>
	<body style="margin: 0; padding: 0;">
		@if(count($order_details) > 0)

		<table cellpadding="0" cellspacing="0" width="600" align="center" style="border:1px solid #ddd;">
					
			
			<tr>
				<td style="border-top: 5px solid #69c332cc;">
				<table style="padding:10px;width:100%;">
					<tr>
						<td align="center">
							
							<img src="{{$LOGOPATH}}" alt="@lang($lang.'_mob_lang'.'.API_LOGO')" class="img-responsive logo"  width="100">
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
								@lang($lang.'_mob_lang.API_NEW_OR_RECE')
							</td>							
						</tr>
						<tr>
							<td style="text-align:center; font-family:sans-serif; font-size:17px; padding-bottom: 20px;  color: #fff; line-height: 25px;">
								@lang($lang.'_mob_lang.API_NEW_OR')&nbsp;{{ucfirst($customerDet->ord_shipping_cus_name)}}
							</td>
						</tr>
						<tr>
							<td colspan="1" style="text-align:center; font-family:cursive; font-size:15px; padding-bottom: 20px;  color: #fff;">
									@lang($lang.'_mob_lang.API_TRANSACTION_ID')&nbsp;:&nbsp;{{$transaction_id}}
							</td>
						</tr>
					</table>
				</td>
			</tr>	
			@if($self_pickup!=1)
			<tr>
				<td>
					<table style="font-family:sans-serif; font-size:14px; width:100%; padding:20px 20px;"  align="center">
						<tr>
							<td colspan="2" style="border-bottom: 2px solid #69c332; padding-bottom:5px; font-family:sans-serif; font-size:22px;color: #69c332;">
								@lang($lang.'_mob_lang.API_DEL_ADDR')
							</td>
						</tr>
						@if(empty($customerDet)===false)
							<tr><td style="padding:5px 0;">{{ucfirst($customerDet->ord_shipping_cus_name)}}</td></tr>
							<tr><td style="padding:5px 0;">{{$customerDet->ord_shipping_address1}}</td></tr>
							<tr><td style="padding:5px 0;">{{$customerDet->ord_shipping_address}}</td></tr>
							<tr><td style="padding:5px 0;">{{$customerDet->ord_shipping_mobile}}</td></tr>
							<tr><td style="padding:5px 0;">{{$customerDet->ord_shipping_mobile1}}</td></tr>
							<tr><td style="padding:5px 0;">{{$customerDet->order_ship_mail}}</td></tr>
						@endif
					</table>
				</td>
			</tr>
			@endif
			<tr>
				<td>
					<table style="font-family:sans-serif; font-size:14px; width:100%; padding:0px 20px 10px; border-bottom: 1px solid #ddd;"  align="center">
						<tr>
							<td colspan="2" style="border-bottom: 2px solid #69c332; padding-bottom:5px; font-family:sans-serif; font-size:22px;color: #69c332;">@lang($lang.'_mob_lang.API_ITEM_DISPATCH')</td>
						</tr>
						
						
						<tr>
							<td>
								<table style="width:100%;">
									<tr>
										<td style="padding:20px 0 5px; font-size: 19px; color: #e48743;" colspan="2">
											{{ucfirst($store_name)}}
										</td>							
									</tr>
									@foreach($order_details  as $itemDet)
										@if(count($itemDet) > 0)
											@foreach($itemDet as $itDet)
													<tr>
														<td width="100" style="padding:10px 20px 10px 0px;">
															@php $img = explode('/**/',$itDet->pro_images); $url = url('');
																$path = $url.'/public/images/noimage/'.$no_item;
															 @endphp
															@if($itDet->pro_type == '2')
																	@php $filename = public_path('images/restaurant/items/').$img[0]; @endphp
																	@if($img[0] != '' && file_exists($filename))
																		@php $path = $url.'/public/images/restaurant/items/'.$img[0]; @endphp
																	@endif
																@elseif($itDet->pro_type == '1')
																	@php $filename = public_path('images/store/products/').$img[0]; @endphp
																	@if($img[0] != '' && file_exists($filename))
																		@php $path = $url.'/public/images/store/products/'.$img[0]; @endphp
																	@endif
																@endif
															<img src="{{$path}}" width="100">
														</td>
														<td>
															<span style="color: #69c332; font-size: 18px;">{{$itDet->ord_currency}}&nbsp;{{$itDet->ord_grant_total}}</span> 
															<p style="font-size: 18px; margin: 0; padding-top: 5px;">{{ucfirst($itDet->productName)}}
															</p>
															@if($itDet->ord_choices != '')
																
																@php $ch_array = json_decode($itDet->ord_choices); @endphp
																@if(count($ch_array) > 0 )
																	@php $i=1; @endphp
																	<p style="font-size: 15px; margin: 0; padding-top: 3px;">Includes </p>
																	@foreach($ch_array as $array)
																		@php $ch = get_choice_name_defaultLang($array->choice_id); @endphp
																		
																		<span style="font-size: 15px; margin: 0; padding-top: 3px;">
																			{{$ch->ch_name}}@if($i!=count($ch_array)), @endif
																		</span>
																	@php $i++; @endphp
																	@endforeach
																@endif	
															@endif
														</td>
													</tr>
												@endforeach
											@endif
										@endforeach
								</table>
							</td>
						</tr>
						
												
												
					</table>
				</td>
			</tr>
			<tr>
				<td align="center" style="background:#69c332; color:#fff; font-family:sans-serif; font-size:13px;padding: 10px 15px;">
					<p style="padding:0px;margin:0px;"><a href="{{url('contact-us')}}" style="color:#fff; text-decoration:none;">@lang($lang.'_mob_lang.API_CNCT_US')</a> ©&nbsp;{{$FOOTERNAME}}</p>
				</td>
			</tr>
			
		</table>	
		@endif
	</body>
</html>