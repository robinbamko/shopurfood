<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
   <head>
      <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
      <meta name="format-detection" content="telephone=no" />
      <!-- disable auto telephone linking in iOS -->
      <title>Abandoned Cart Mail</title>
      <style type="text/css">
         /* RESET STYLES */
         html { background-color:#E1E1E1; margin:0; padding:0; }
         body, #bodyTable, #bodyCell, #bodyCell{height:100% !important; margin:0; padding:0; width:100% !important;font-family:Helvetica, Arial, "Lucida Grande", sans-serif;}
         table{border-collapse:collapse;}
         table[id=bodyTable] {width:100%!important;margin:auto;max-width:500px!important;color:#7A7A7A;font-weight:normal;}
         img, a img{border:0; outline:none; text-decoration:none;height:auto; line-height:100%;}
         a {text-decoration:none !important;border-bottom: 1px solid;color:#ffffff;}
         h1, h2, h3,h5, h6{color:#5F5F5F; font-weight:normal; font-family:Helvetica; font-size:20px; line-height:125%; text-align:Left; letter-spacing:normal;margin-top:0;margin-right:0;margin-bottom:10px;margin-left:0;padding-top:0;padding-bottom:0;padding-left:0;padding-right:0;}
         h4{font-size: 18px;}
         .ii a[href] {
         color: #ffffff;
         }
         /* CLIENT-SPECIFIC STYLES */
         .ReadMsgBody{width:100%;} .ExternalClass{width:100%;} /* Force Hotmail/Outlook.com to display emails at full width. */
         .ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div{line-height:100%;} /* Force Hotmail/Outlook.com to display line heights normally. */
         table, td{mso-table-lspace:0pt; mso-table-rspace:0pt;} /* Remove spacing between tables in Outlook 2007 and up. */
         #outlook a{padding:0;} /* Force Outlook 2007 and up to provide a "view in browser" message. */
         img{-ms-interpolation-mode: bicubic;display:block;outline:none; text-decoration:none;} /* Force IE to smoothly render resized images. */
         body, table, td, p, a, li, blockquote{-ms-text-size-adjust:100%; -webkit-text-size-adjust:100%; font-weight:normal!important;} /* Prevent Windows- and Webkit-based mobile platforms from changing declared text sizes. */
         .ExternalClass td[class="ecxflexibleContainerBox"] h3 {padding-top: 10px !important;} /* Force hotmail to push 2-grid sub headers down */
         td a{color:#ffffff; text-decoration: none;}
         /* /\/\/\/\/\/\/\/\/ TEMPLATE STYLES /\/\/\/\/\/\/\/\/ */
         /* ========== Page Styles ========== */
         .flexibleImage{height:auto;}
         .linkRemoveBorder{border-bottom:0 !important;}
         table[class=flexibleContainerCellDivider] {padding-bottom:0 !important;padding-top:0 !important;}
         body, #bodyTable{background-color:#E1E1E1;}
         #emailHeader{background-color:#E1E1E1;}
         #emailBody{background-color:#FFFFFF;}
         #emailFooter{background-color:#E1E1E1;}
         .nestedContainer{background-color:#F8F8F8; border:1px solid #CCCCCC;}
         .emailButton{background-color:#205478; border-collapse:separate;}
         .buttonContent{color:#FFFFFF; font-family:Helvetica; font-size:18px; font-weight:bold; line-height:100%; padding:15px; text-align:center;}
         .buttonContent a{color:#FFFFFF; display:block; text-decoration:none!important; border:0!important;}
         .emailCalendar{background-color:#FFFFFF; border:1px solid #CCCCCC;}
         .emailCalendarMonth{background-color:#205478; color:#FFFFFF; font-family:Helvetica, Arial, sans-serif; font-size:16px; font-weight:bold; padding-top:10px; padding-bottom:10px; text-align:center;}
         .emailCalendarDay{color:#205478; font-family:Helvetica, Arial, sans-serif; font-size:60px; font-weight:bold; line-height:100%; padding-top:20px; padding-bottom:20px; text-align:center;}
         .imageContentText {margin-top: 10px;line-height:0;}
         .imageContentText a {line-height:0;}
         #invisibleIntroduction {display:none !important;} /* Removing the introduction text from the view */
         /*FRAMEWORK HACKS & OVERRIDES */
         span[class=ios-color-hack] a {color:#275100!important;text-decoration:none!important;} /* Remove all link colors in IOS (below are duplicates based on the color preference) */
         span[class=ios-color-hack2] a {color:#205478!important;text-decoration:none!important;}
         span[class=ios-color-hack3] a {color:#8B8B8B!important;text-decoration:none!important;}
         .a[href^="tel"], a[href^="sms"] {text-decoration:none!important;color:#606060!important;pointer-events:none!important;cursor:default!important;}
         .mobile_link a[href^="tel"], .mobile_link a[href^="sms"] {text-decoration:none!important;color:#606060!important;pointer-events:auto!important;cursor:default!important;}
         /* MOBILE STYLES */
         @media only screen and (max-width: 480px){
         /*////// CLIENT-SPECIFIC STYLES //////*/
         body{width:100% !important; min-width:100% !important;} /* Force iOS Mail to render the email at full width. */
         table[id="emailHeader"],
         table[id="emailBody"],
         table[id="emailFooter"],
         table[class="flexibleContainer"],
         td[class="flexibleContainerCell"] {width:100% !important;}
         td[class="flexibleContainerBox"], td[class="flexibleContainerBox"] table {display: block;width: 100%;text-align: left;}
         td[class="imageContent"] img {height:auto !important; width:100% !important; max-width:100% !important; }
         img[class="flexibleImage"]{height:auto !important; width:100% !important;max-width:100% !important;}
         img[class="flexibleImageSmall"]{height:auto !important; width:auto !important;}
         /*
         Create top space for every second element in a block
         */
         table[class="flexibleContainerBoxNext"]{padding-top: 10px !important;}
         table[class="emailButton"]{width:100% !important;}
         td[class="buttonContent"]{padding:0 !important;}
         td[class="buttonContent"] a{padding:15px !important;}
         }
         @media only screen and (-webkit-device-pixel-ratio:.75){
         /* Put CSS for low density (ldpi) Android layouts in here */
         }
         @media only screen and (-webkit-device-pixel-ratio:1){
         /* Put CSS for medium density (mdpi) Android layouts in here */
         }
         @media only screen and (-webkit-device-pixel-ratio:1.5){
         /* Put CSS for high density (hdpi) Android layouts in here */
         }
         /* end Android targeting */
         /* CONDITIONS FOR IOS DEVICES ONLY
         =====================================================*/
         @media only screen and (min-device-width : 320px) and (max-device-width:568px) {
         }
         /* end IOS targeting */
      </style>
   </head>
   <body bgcolor="#E1E1E1" leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0">
      <center style="background-color:#E1E1E1;">
         <table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%" id="bodyTable" style="table-layout: fixed;max-width:100% !important;width: 100% !important;min-width: 100% !important; padding: 20px 0px;">
         	Hi, <br>
         	We've noticed you forgot some items in your shopping cart.Here they are...
         	<tbody>
         		@if(count($cart_array) > 0)
         		@foreach($cart_array as $details)
         		<tr>
         			<td>
         				@php $img = explode('/**/',$details->pro_images); 
         					 $url = url(''); 
         					 $folder = "store/products/";
         					 $path = $url.'/public/images/noimage/default_image_item.png';
         				@endphp
         				@if($details->cart_type == '1') 
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
         				{{$details->pro_item_name}}<br>
         				{{$details->cart_currency}}&nbsp;{{$details->cart_total_amt}}
         			</td>
         		</tr>
         		@endforeach
         		@endif
         	</tbody>
         	<button>Complete Your Order </button>
         </table>
      </center>
   </body>
</html>