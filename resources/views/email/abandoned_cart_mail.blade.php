<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
   <head>
         <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
         <meta name="viewport" content="width=device-width, initial-scale=1.0">
         <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
         <meta name="format-detection" content="telephone=no" />
         <title>Abandoned Cart Mail</title>

   </head>
   <body style="margin: 0; padding: 0;">

      <table cellpadding="0" cellspacing="0" width="600" align="center" style="border:1px solid #ddd;">
         
         <tr>
            <td style="border-top: 5px solid #69c332cc;">
            <table style="padding:10px;width:100%;">
               <tr>
                  <td align="center">
                  @php $path = url('').'/public/images/noimage/default_image_logo.jpg'; @endphp
                     @if(!empty($logo))
                        @if($logo->admin_logo != '')
                           @php $filename = public_path('images/logo/').$logo->admin_logo; @endphp 
                           @if(file_exists($filename))
                              @php $path = url('').'/public/images/logo/'.$logo->admin_logo; @endphp
                           @endif
                        @endif 
                     @endif 
                  <img src="{{$path}}" alt="Logo" class="img-responsive logo"  width="100">
                  </td>                
               </tr>
            </table>
            </td>
         </tr>
         
         
         <tr>
            <td>
               <table style="width:100%;background:url('{{url('public/images/order_image.jpg')}}')no-repeat; padding:56px 20px;">
                  <tr>
                     <td colspan="1" style="text-align:center; font-family:cursive; font-size:35px; padding-bottom: 20px;  color: #fff;">Abandoned Cart Mail</td>                    
                  </tr>
                  <tr>
                     <td style="text-align:center; font-family:sans-serif; font-size:17px; padding-bottom: 20px;  color: #fff; line-height: 25px;">Hello, We've noticed you forgot some items in your shopping cart.Here they are...</td>
                  </tr>
                  <tr>
                     <td align="center"><a href="{{url('')}}"><button type="button" style="background:#e48743; border:0; padding:10px 20px; color:#fff; font-size:15px;">Login your account</button></a></td>
                  </tr>
               </table>
            </td>
         </tr>

         <tr>
            <td>
               <table style="font-family:sans-serif; font-size:14px; width:100%; padding:0px 20px 10px; border-bottom: 1px solid #ddd;"  align="center">
                  <tr>
                     <td colspan="2" style="border-bottom: 2px solid #69c332; padding-bottom:5px; font-family:sans-serif; font-size:22px;color: #69c332;">Items Details</td>
                  </tr>
                  <tr>
                     <td>
                        <table style="width:100%;">
                           
                           
                           @if(count($cart_array) > 0)
                           @foreach($cart_array as $details)
                           <tr>
                              <td width="100" style="padding:10px 20px 10px 0px;">
                                  @php $img = explode('/**/',$details->pro_images); 
                                     $url = url(''); 
                                     $folder = "store/products/";
                                     $path = $url.'/public/images/noimage/default_image_item.png';
                                 @endphp
                                 @if($details->cart_type == '2') 
                                    @php $folder = "restaurant/items/"; @endphp
                                 @endif
                                 @if(count($img) > 0)
                                    @php $filename = public_path('images/').$folder.$img[0]; @endphp
                                    @if($img[0] != '' && file_exists($filename))
                                       @php $path = $url.'/public/images/'.$folder.$img[0]; @endphp
                                    @endif
                                 @endif
                                <img src="{{$path}}" style="width:100px;height:100px;float:left">
                              </td>
                              <td>
                                 <span style="color: #69c332; font-size: 18px;">{{$details->cart_total_amt}}&nbsp;{{$details->cart_currency}}</span> 
                                 <p style="font-size: 18px; margin: 0; padding-top: 5px;">{{$details->pro_item_name}}</p>
                                
                              </td>
                           </tr>
                           @endforeach
                           @endif
                           
                        </table>
                     </td>
                  </tr>
                  
               </table>
            </td>
         </tr>

         <tr>
            <td align="center" style="background:#69c332; padding:10px 15px; color:#fff; font-family:sans-serif; font-size:13px;">
               <p><a href="{{url('contact-us')}}" style="color:#fff; text-decoration:none;">Contact Us</a> © {{$footer->footer_text}}</p>
            </td>
         </tr>
         
      </table> 
   </body>
</html>
