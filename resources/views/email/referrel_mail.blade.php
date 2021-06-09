<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
   <head>
      <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
      <meta name="format-detection" content="telephone=no" />
      <!-- disable auto telephone linking in iOS -->      
      <title>{{ (Lang::has(Session::get('front_lang_file').'.REFEREL_MAIL')!= '') ? trans(Session::get('front_lang_file').'.REFEREL_MAIL') : trans($FRONT_LANGUAGE.'.REFEREL_MAIL') }}</title>
      
   </head>
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
						 <img src="{{$path}}" alt="@lang(Session::get('front_lang_file').'.ADMIN_LOGO')" class="img-responsive logo"  width="100">
					  </td>                
				   </tr>
				</table>
            </td>
        </tr>
         
         
        <tr>
            <td>
               <table style="width:100%;background:url({{url('').'/resources/views/email/'}}bg.jpg); padding:50px 20px;">
                  <tr>
                     <td colspan="1" style="text-align:center; font-family:cursive; font-size:35px; padding-bottom: 20px;  color: #fff;">{{ (Lang::has(Session::get('front_lang_file').'.YOU_ARE_INVITED')!= '') ? trans(Session::get('front_lang_file').'.YOU_ARE_INVITED') : trans($FRONT_LANGUAGE.'.YOU_ARE_INVITED') }} </td>                    
                  </tr>
                  <tr>
                     <td style="text-align:center; font-family:sans-serif; font-size:17px; padding-bottom: 20px;  color: #fff; line-height: 25px;">{{ (Lang::has(Session::get('front_lang_file').'.FRONT_HELLO')!= '') ? trans(Session::get('front_lang_file').'.FRONT_HELLO') : trans($FRONT_LANGUAGE.'.FRONT_HELLO') }}

                     {{$SITENAME}}

                     {{ (Lang::has(Session::get('front_lang_file').'.FRONT_YOU_CAN')!= '') ? trans(Session::get('front_lang_file').'.FRONT_YOU_CAN') : trans($FRONT_LANGUAGE.'.FRONT_YOU_CAN') }}</td>
                     </tr>
                  <tr>
                     <td align="center"><a href="{{url('refer-login')}}/{{base64_encode($refer_mail)}}"><button type="submit" style="background:#69c332cc; border:0; padding:7px 20px; color:#fff; font-size:15px; border-radius: 15px;">{{ (Lang::has(Session::get('front_lang_file').'.FRONT_SIGNUP')!= '') ? trans(Session::get('front_lang_file').'.FRONT_SIGNUP') : trans($FRONT_LANGUAGE.'.FRONT_SIGNUP') }}</a></button></td>
                  </tr>
               </table>
            </td>
         </tr>       
         
              
       
         <tr>
            <td align="center" style="background:#69c332; padding:10px 15px; color:#fff; font-family:sans-serif; font-size:13px;">
               <p><a href="#" style="color:#fff; text-decoration:none;">Contact Us</a> | <a href="#" style="color:#fff; text-decoration:none;">Terms and Conditions</a> | <a href="#" style="color:#fff; text-decoration:none;">Privacy Policy</a></p>
            </td>
         </tr>
         
      </table> 
   </body>
</html>