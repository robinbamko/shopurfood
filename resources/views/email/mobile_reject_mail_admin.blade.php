<html>
	<body style="margin: 0; padding: 0;">
		@if(!empty($order_details))

		<table cellpadding="0" cellspacing="0" width="600" align="center" style="border:1px solid #ddd;">
					
			
			<tr>
				<td style="border-top: 5px solid #69c332cc;">
				<table style="padding:10px;width:100%;">
					<tr>
						<td align="center">
							<img src="{{$LOGOPATH}}" alt="@lang($lang.'.API_REJECT_OR')" class="img-responsive logo"  width="100">
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
								@lang($lang.'.API_ORDER_REJECT_TOADMIN')
							</td>							
						</tr>
						<tr>
							<td colspan="1" style="text-align:center; font-family:sans-serif; font-size:17px; padding-bottom: 20px;  color: #fff; line-height: 25px;">
								@lang($lang.'.API_ORDER_CANCELLED_SUMMARY')
							</td>							
						</tr>
						<tr>
							<td colspan="1" style="text-align:center; font-family:sans-serif; font-size:17px; padding-bottom: 20px;  color: #fff; line-height: 25px;">
								@lang($lang.'.ADMIN_TRANSACTION_ID')&nbsp;:&nbsp;{{$transaction_id}}
							</td>
						</tr>
					</table>
				</td>
			</tr>	
			@if($order_details->ord_self_pickup == 0)
			<tr>
				<td>
					<table style="font-family:sans-serif; font-size:14px; width:100%; padding:20px 20px;"  align="center">
						<tr>
							<td colspan="2" style="border-bottom: 2px solid #69c332; padding-bottom:5px; font-family:sans-serif; font-size:22px;color: #69c332;">
								@lang($lang.'.API_CUSTOMER_DETAILS')
							</td>
						</tr>
								<tr><td style="padding:5px 0;">{{$order_details->ord_shipping_cus_name}}</td></tr>
								<tr><td style="padding:5px 0;">{{$order_details->ord_shipping_address1}}</td></tr>
								<tr><td style="padding:5px 0;">{{$order_details->ord_shipping_address}}</td></tr>
								<tr><td style="padding:5px 0;">{{$order_details->ord_shipping_mobile}}</td></tr>
								<tr><td style="padding:5px 0;">{{$order_details->ord_shipping_mobile1}}</td></tr>
								<tr><td style="padding:5px 0;">{{$order_details->order_ship_mail}}</td></tr>
							
					</table>
				</td>
			</tr>
			@endif
			<tr>
				<td>
					<table style="font-family:sans-serif; font-size:14px; width:100%; padding:20px 20px;"  align="center">
						<tr>
							<td colspan="2" style="border-bottom: 2px solid #69c332; padding-bottom:5px; font-family:sans-serif; font-size:22px;color: #69c332;">
								@lang($lang.'.API_REASON_REJECT')
							</td>
						</tr>

						
							<tr><td style="padding:5px 0;">{{$reason}}</td></tr>
						
					</table>
				</td>
			</tr>
			
			<tr>
				<td>
					<table style="font-family:sans-serif; font-size:14px; width:100%; padding:0px 20px 10px; border-bottom: 1px solid #ddd;"  align="center">
						<tr>
							<td colspan="2" style="border-bottom: 2px solid #69c332; padding-bottom:5px; font-family:sans-serif; font-size:22px;color: #69c332;">@lang($lang.'.API_ITEM_REJECT')</td>
						</tr>
						
						<tr>
							<td>
								<table style="width:100%;">
									<tr>
										<td style="padding:20px 0 5px; font-size: 19px; color: #e48743;" colspan="2">
											{{ucfirst($order_details->store_name)}}
										</td>							
									</tr>
									
												
													<tr>
														<td width="100" style="padding:10px 20px 10px 0px;">
															@php $img = explode('/**/',$order_details->pro_images); $url = url(''); 
																$path = $url.'/public/images/noimage/'.$no_item;
															@endphp
															@if(count($img) > 0)
																{{-- restaurant image --}}
																@if($order_details->st_type == '1')
																	@php $filename = public_path('images/restaurant/items/').$img[0]; @endphp
																	@if($img[0] != '' && file_exists($filename))
																		@php $path = $url.'/public/images/restaurant/items/'.$img[0]; @endphp
																	@endif
																@elseif($order_details->st_type == '2')
																	@php $filename = public_path('images/store/products/').$img[0]; @endphp
																	@if($img[0] != '' && file_exists($filename))
																		@php $path = $url.'/public/images/store/products/'.$img[0]; @endphp
																	@endif
																@endif
															@endif
															<img src="{{$path}}" width="100">
														</td>
														<td>
															<span style="color: #69c332; font-size: 18px;">{{$order_details->ord_currency}}&nbsp;{{$order_details->ord_grant_total}}</span> 
															<p style="font-size: 18px; margin: 0; padding-top: 5px;">{{ucfirst($order_details->item_name)}}
															</p>
															@if($order_details->ord_choices != '')
																
																@php $ch_array = json_decode($order_details->ord_choices); @endphp
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
												
								</table>
							</td>
						</tr>
						
												
												
					</table>
				</td>
			</tr>
	
			
			<tr>
				<td align="center" style="background:#69c332; padding:10px 15px; color:#fff; font-family:sans-serif; font-size:13px;">
					<p style="padding:0px;margin:0px;"><a href="{{url('contact-us')}}" style="color:#fff; text-decoration:none;">@lang($lang.'.API_CONTACT_US')</a> © {{$FOOTERNAME}}</p>
				</td>
			</tr>
			
		</table>	
		@endif
		
	</body>
</html>