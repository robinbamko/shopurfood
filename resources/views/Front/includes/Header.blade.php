
<script src="{{url('')}}/public/front/js/popper.min.js"></script>
<style>
    .emt-cart{ text-align: center; font-size: 25px; width: 100%;    margin: 60px 0px; }
    .footer-social ul li:hover{ background: #ccc; }

    .itm-con{  }
    .all-slider .slick-slide img:focus{ outline: none; }
    .invalid {
        color: red;
    }
    .valid {
        color: green;
    }
    .header-input .fa-map-marker{
        color: #ff5215;
        font-size: 25px;
        position: relative;
        top: 2px;
    }
	.location-menuoverall .card-body {
		padding: 1.00rem;
	}
    canvas{
        /*prevent interaction with the canvas*/
        pointer-events:none;
    }
    .header-dropdown {border-top: 0px solid #ff3c15;animation: none;}
    .header-dropdown li a {color: #383757;}

    @media screen and (min-width: 768px) and (max-width: 991px) {

        .login-menu {height: 100%;}
        .login-menu ul li a {color: #fff;}
        .login-menu-div .login-menu ul {position: relative !important;transform: none !important;width: 100%;height: 100%;box-shadow: none;border: 0px solid transparent;transition: 0.5s;}
    }
    @media screen and (min-width: 576px) and (max-width: 767px) {
        .dropdown a span {color: #fff;}
        .login-menu {height: 100%;}
        .login-menu ul li a {}
        .login-menu-div .login-menu ul {position: relative !important;transform: none !important;width: 100%;height: 100%;box-shadow: none;border: 0px solid transparent;transition: 0.5s;}
    }
    @media screen and (min-width: 320px) and (max-width: 575px) {
        .dropdown a span {color: #fff;}
        .login-menu {height: 100%;}
        .login-menu ul li a {}
        .login-menu-div .login-menu ul {position: relative !important;transform: none !important;width: 100%;height: 100%;box-shadow: none;border: 0px solid transparent;transition: 0.5s;}
    }
	
	span#quick-cart-product-count {
    border-radius: 10px;
    font-family: "robotobold", sans-serif;
    font-size: 76.92308%;
    line-height: 110.0%;
    background-color: #ff5215;
    color: #fff;
    display: block;
    height: 20px;
    overflow: hidden;
    padding-top: 4px;
    position: absolute;
    left: 22px;
    top: 0;
    text-align: center;
    width: 20px;
    z-index: 10;
}
</style>

<!--HEADER -->
@php $offer = 0; @endphp
<div class="header">
    <div class="container">
        <div class="row">
            <div class="col-7 col-sm-8 col-md-8 col-lg-2 logo">
                <!-- <img src="images/logo.png" alt=""> -->
                @if(count($logo_settings_details) > 0)
                    @php
                        foreach($logo_settings_details as $logo_set_val){ }
                            $filename = public_path('images/logo/').$logo_set_val->front_logo;
                    @endphp
                    @if(file_exists($filename))
                        <a href="{{url('')}}"><img src="{{url('public/images/logo/'.$logo_set_val->front_logo)}}" alt="Logo" class="img-responsive logo-img"></a>
                    @else
                        <a href="{{url('')}}"><img src="{{url('public/images/noimage/'.$no_logo)}}" alt="Logo" class="img-responsive logo-img" width="50px"></a>
                    @endif
                @else
                    <a href="{{url('')}}"><img src="{{url('public/images/noimage/'.$no_logo)}}" alt="Logo" class="img-responsive logo-img" width="50px"></a>
                @endif
            </div>

            @if(Session::has('customer_id') == 1)
                @php $cart_count = cart_count(Session::get('customer_id'));@endphp
            <div class="col-2 col-sm-2 col-md-2 d-lg-none mobile-cart">
                <a href="{{url('').'/cart'}}" ><i class="fa fa-shopping-cart"></i></a>
                <span  class="cart-tot" id="cart_count">{{ $cart_count }}</span>
            </div>
            @else
                <div class="col-2 col-sm-2 col-md-2 d-lg-none mobile-cart">
                    <a href="#login" data-toggle="modal" data-target="#myModal" class="modal-toggle" ><i class="fa fa-shopping-cart"></i></a>
                    <span>0</span>
                </div>

                @endif


            <div class="col-1 col-sm-1 col-md-1 d-lg-none mobile-loc">
                <i class="fa fa-map-marker"></i>
            </div>
            <div class="col-2 col-sm-1 col-md-1 d-lg-none mobile-menu">
                <i class="fa fa-bars"></i>
            </div>
            {{--<h1>{{ $ip_latitude }}</h1>--}}
			<!--LOCATION MAP STARTS HERE -->
			<div class="location-menuoverall">
		
				<div class="col-md-5 col-lg-5 location-menu">
					<div class="" style="margin-bottom: 15px;border-bottom: 1px solid #ccc;padding-bottom: 10px;">@if(Session::has('search_location') == 1)<button class=" pull-right"><i class="fa fa-times"></i></button>@endif<span><i class="fa fa-map-marker"></i> &nbsp; @lang(Session::get('front_lang_file').'.FRONT_MYLOCATION')</span></div>
					<form class="form-horizontal">
						<div class="card">
							<div class="card-body"><a href="javascript:set_my_currentLocation();"><i class="fa fa-snowflake-o" aria-hidden="true"></i> &nbsp; Use my current location</a></div>
						</div>
						<div class="clearfix" style="clear: both;margin: 5px;"></div>
						<div class="card">
							<div class="card-body">
								<input class="form-control valid" id="footer_us3-address" required="" name="cus_address" type="text" value="{{(Session::has('search_location') == 1) ? Session::get('search_location') : 'Enter a location'}}" placeholder="Enter a location" autocomplete="off" aria-invalid="false" style="margin: 15px 0px;">
								
								<div id="footer_us3" style="height: 400px !important;"></div>
							</div>
						</div>
						<div class="text-center">
							<input class="form-control valid" id="footer_us3-lat" required="" readonly="" name="cus_lat" type="hidden" value="{{(Session::has('search_latitude') == 1) ? Session::get('search_latitude') : ''}}" aria-invalid="false">
							<input class="form-control valid" id="footer_us3-lon" required="" readonly="" name="cus_long" type="hidden" value="{{(Session::has('search_longitude') == 1) ? Session::get('search_longitude') : ''}}" aria-invalid="false">
						</div>
						<?php /*
						<div class="clearfix" style="clear: both;margin: 10px;"></div>
						<div class="card">
							<div class="card-body">
								<div class="form-group"> 
									@if(Session::has('search_location') == 1)
										<button type="button" class="btn btn-info pull-right" data-dismiss="modal">@lang(Session::get('front_lang_file').'.FRONT_CLOSE')</button>
									@endif
								</div>	  
							</div>
						</div> */ ?>
					</form>
					
				</div>
			</div>
            <!-- LOCATION MAP ENDS HERE -->
			<div class="col-md-12 col-lg-7">
                <div class="location-sec">
                    <div class="location-content" id="location-toggle">
                        <p><i class="fa fa-map-marker"></i>@lang(Session::get('front_lang_file').'.FRONT_MYLOCATION')<span>
							@if(Session::has('search_location') == 1)
                                {{Session::get('search_location')}}
                                <button style="background: white" type="button" -data-toggle="modal" -data-target="#centralModalLGInfoDemo"><i class="fa fa-pencil"></i></button>
                            @else
                                @lang(Session::get('front_lang_file').'.FRONT_SELECT_LOCATION')
                                <button style="background: white" type="button" -data-toggle="modal" -data-target="#centralModalLGInfoDemo"><i class="fa fa-pencil"></i></button>

 							@endif
						</span></p>
                    </div>

                <!-- <div class="col-md-8 header-input">
						@if(Session::has('search_location') == 1)
                    <i class="fa fa-map-marker"></i>My Location <span>{{Session::get('search_location')}}<i class="fa fa-angle-down"></i>
						<button type="button" class="address-btn" data-toggle="modal" data-target="#centralModalLGInfoDemo">@lang(Session::get('front_lang_file').'.FRONT_CHANGE_ADDRESS') </button>
						@else
                    <button type="button" class="address-btn" data-toggle="modal" data-target="#centralModalLGInfoDemo">@lang(Session::get('front_lang_file').'.FRONT_ENTER_ADDRESS') </button>
					</span>
						@endif
                        </div> -->


                    @php
                        $cus_details = DB::table('gr_customer')->where('cus_id','=',Session::get('customer_id'))->first();
                        $wallet = 0;
                        $cus_name_is = '';
                    @endphp
                    @if(empty($cus_details) === false)
                        @php
                            $cus_name_is = $cus_details->cus_fname;
                            $wallet	= $cus_details->cus_wallet - $cus_details->used_wallet;
                        @endphp
                    @endif
                    <div class="login d-none d-lg-block">


                        @if(Session::has('customer_id') == 1)
                            <div class="dropdown"><a class="current-open" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" href=""><i class="fa fa-user"></i><span>@lang(Session::get('front_lang_file').'.FRONT_HI')   {{ucfirst($cus_name_is)}}  </span> <i class="fa fa-angle-down"></i></a>
                                <ul class="dropdown-menu header-dropdown" role="menu">
                                    <li><a href="{{ url('customer_profile') }}"> @lang(Session::get('front_lang_file').'.FRONT_ACC')  </a></li>
                                    <li><a href="{{ url('user-wishlist') }}"> @lang(Session::get('front_lang_file').'.FRONT_WISHLIST')  </a></li>
                                    <li><a href="{{ url('my-orders') }}"> @lang(Session::get('front_lang_file').'.FRONT_OR_TRACK')  </a></li>
                                    <li><a href="{{ url('user-wallet')}}"> @lang(Session::get('front_lang_file').'.FRONT_WALLET')&nbsp;({{number_format($wallet,2)}})</a></li>

                                    @php $get_settings = get_general_settings(); @endphp
                                    @if(empty($get_settings) === false)
                                        @if(Session::has('customer_id') == 1)
                                            @if($get_settings->gs_refer_friend == '1')
                                                <li>

                                                    <button type="button" class="btn refer-frd-btn" aria-hidden="true" data-toggle ="modal" data-target="#referModal" ><i class="fa fa-paper-plane" aria-hidden="true"></i>@lang(Session::get('front_lang_file').'.ADMIN_REFER_FRIENDS')
                                                    </button>

                                                    {{--{{ Form::button('Refer friends<i class="fa fa-paper-plane" aria-hidden="true"></i> ',['class' => 'btn  refer-frd-btn','data-toggle' => 'modal', 'data-target' => '#referModal'])}}--}}
                                                </li>
                                            @endif
                                        @endif
                                    @endif

                                    <li><a href="{{ url('cus_logout') }}">@lang(Session::get('front_lang_file').'.FRONT_LOGOUT')</a></li>
                                <!--<li><a href="http://facebook.com/logout.php?confirm=1&next={{ url('cus_logout') }}&access_token={{Session::get('access_token')}};">FLogout</a></li>--->
                                <!--<li><a href="{{ url('fb_logout') }}">FLogout</a></li>-->
                                </ul>
                            </div>
                        @else
                            <p>
                                <a href="#login" data-toggle="modal" data-target="#myModal" class="modal-toggle">
                                    <i class="fa fa-user"></i>@lang(Session::get('front_lang_file').'.ADMIN_LOGIN')
                                </a>
                            </p>
                        @endif

                    </div>

                    <div class="location-input">

                        <!-- onchange="res_page_redirect(); -->
                        @php
                            $rest = search_restaurantdet_sample();
                        @endphp
                        {{--<input type="text" id="search_res" placeholder="@lang(Session::get('front_lang_file').'.FRONT_SEARCH_RESTAURANT')"><i id="restaurant_search" class="fa fa-search"></i>--}}

                        <input type="text" id="search_res" onkeyup="search_store();" placeholder="@lang(Session::get('front_lang_file').'.FRONT_SEARCH_RESTAURANT')"><i id="restaurant_search" class="fa fa-search"></i>

                        <div id ="rest_det"></div>


                    </div>

                </div>
            </div>
{{--<h2>--}}
    {{--@php--}}
        {{--$name = (Session::get('front_lang_code') == 'en') ? 'st_store_name' : 'st_store_name_'.Session::get('front_lang_code');--}}

            {{--$sql = DB::table('gr_store')->select(DB::raw('group_concat("\"",'.$name.',"\"") as cities'))->pluck('cities')->first();--}}
        {{--print_R($sql);--}}
        {{--exit;--}}
    {{--@endphp</h2>--}}

            <div class="col-md-12 col-lg-3 login-menu d-lg-none">
                 <div class="">
                    <ul>
                        @if(Session::has('customer_id') == 1)
                            <li>
                                <div class=""><a class="current-open" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" href=""><span>@lang(Session::get('front_lang_file').'.FRONT_HI')   {{$cus_name_is}}  </span> <i class="fa fa-angle-down"></i></a>
                                    <ul class="header-dropdown" role="menu">
                                        <li><a href="{{ url('customer_profile') }}"> @lang(Session::get('front_lang_file').'.FRONT_ACC')  </a></li>
                                        <li><a href="{{ url('user-wishlist') }}"> @lang(Session::get('front_lang_file').'.FRONT_WISHLIST')  </a></li>
                                        <li><a href="{{ url('my-orders') }}"> @lang(Session::get('front_lang_file').'.FRONT_OR_TRACK')  </a></li>
                                        <li><a href="{{ url('user-wallet')}}"> @lang(Session::get('front_lang_file').'.FRONT_WALLET')&nbsp;({{number_format($wallet,2)}})</a></li>
                                        @php $offer=''@endphp
                                        @php $get_settings = get_general_settings(); @endphp

                                        @if(empty($get_settings) === false)

                                            @php $offer = $get_settings->gs_offer_percentage; @endphp
                                            @if(Session::has('customer_id') == 1)
                                                @if($get_settings->gs_refer_friend == '1')
                                                    <li>
                                                        {{--{{ Form::button('Refer Friends <i class="fa fa-paper-plane" aria-hidden="true"></i> ',['class' => 'btn  refer-frd-btn','data-toggle' => 'modal', 'data-target' => '#referModal'])}}--}}
                                                        <button type="button" class="btn refer-frd-btn" aria-hidden="true" data-toggle ="modal" data-target="#referModal" ><i class="fa fa-paper-plane" aria-hidden="true"></i>@lang(Session::get('front_lang_file').'.ADMIN_REFER_FRIENDS')
                                                        </button>


                                                    </li>
                                                @endif
                                            @endif
                                        @endif

                                        <li><a href="{{ url('cus_logout') }}">@lang(Session::get('front_lang_file').'.FRONT_LOGOUT')</a></li>
                                    <!--<li><a href="http://facebook.com/logout.php?confirm=1&next={{ url('cus_logout') }}&access_token={{Session::get('access_token')}};">FLogout</a></li>-
                                        <li><a href="{{ url('fb_logout') }}">FLogout</a></li>-->
                                    </ul>
                                </div>
                            </li>
                        @else
                            <li><i class="fa fa-user"></i><a href="#login" data-toggle="modal" data-target="#myModal" class="modal-toggle">@lang(Session::get('front_lang_file').'.ADMIN_LOGIN')</a></li>
                        @endif
                    </ul>
                </div>
            </div>

            <div class="col-md-12 col-lg-3 login-menu-div" >

               

                @if(Session::has('customer_id') == 1)
                    @php $cart_count = cart_count(Session::get('customer_id'));

					$cart_amount = cart_amount(Session::get('customer_id'));
                    @endphp

                    <div class="header-cart-sec">
                        <div class="header-cart-sec-in">
                        <div class="head-cart-price">
							<span class="count fadeUp hide" id="quick-cart-product-count">{{ $cart_count}}</span>
							<span><i class="fa fa-shopping-cart basket_pofi"></i>
								<span class="cart-tot" id="cart_count">{{ $cart_count}}</span>  @lang(Session::get('front_lang_file').'.FRONT_ITEMS')
							</span>
                            @if(Session::get('customer_id') == '' || number_format($cart_amount) == 0)
                                <p class="cart-amt">{{$default_currency}}&nbsp;0.00</p>
                            @else
                                <p class="cart-amt">{{$default_currency}}&nbsp;{{ number_format($cart_amount) }}</p>
                            @endif
                            <div class="wave-img"><img src="{{url('')}}/public/front/images/wave.png" alt=""></div>
                        </div>
                        <div class="cart-sec-btn">
                            <a href="{{url('').'/cart'}}" class="btn">@lang(Session::get('front_lang_file').'.FRONT_ORDER')</a>
                        </div>
                    </div>
                </div>                    
                @else

                    <div class="header-cart-sec">
                    <div class="header-cart-sec-in">
                        <div class="head-cart-price">
							<span><i class="fa fa-shopping-cart"></i>
								0 @lang(Session::get('front_lang_file').'.FRONT_ITEMS')
							</span>
                            <p>{{$default_currency}}&nbsp;00.00</p>
                            <div class="wave-img"><img src="{{url('')}}/public/front/images/wave.png" alt=""></div>
                        </div>
                        <div class="cart-sec-btn">

                            <a href="#login" data-toggle="modal" data-target="#myModal" class="modal-toggle btn" >@lang(Session::get('front_lang_file').'.FRONT_ORDER')</a>
                            <!-- <button>ORDER</button> -->
                        </div>
                    </div>
                    </div>
                @endif


            </div>



        </div>
    </div>
</div>





<!-- LOGIN Modal -->
<div class="modal fade loginModal" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body loginActive">
                <div class="oneImg ">

                    @php $filename = public_path('front/frontImages/').$LOGINIMAGE;
                    @endphp
                    @if(file_exists($filename))
                        <img class="loginImg" src="{{url('public/front/frontImages/'.$LOGINIMAGE)}}">
                    @else
                        <img class="loginImg" src="{{url('')}}/public/front/images/login.png">
                @endif

                    <img class="registerImg" src="{{url('')}}/public/front/images/register.png">
                <!-- </div> -->
                </div>
                <div role="tabpanel">
                    <!-- Nav tabs -->

                    <!-- <ul class="nav nav-tabs" role="tablist">
                        <li role="presentation"><a href="#login" class="active" aria-controls="login" role="tab" data-toggle="tab">Login</a>

                        </li>
                        <li role="presentation"><a href="#signup" aria-controls="signup" role="tab" data-toggle="tab">Signup</a>
                        </li>
                    </ul> -->
                    <!-- Tab panes -->
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane active" id="login">

                            {!! Form::open(['method' => 'post','id'=>'headerLoginForm']) !!}

                            <div class="title">
                                <h4><span>@lang(Session::get('front_lang_file').'.WELCOME_TO_EDISON')</span>{{$SITENAME}}</h4>
                            </div>
                            <div class="form-group">
                                <span class="spanTitle">@lang(Session::get('front_lang_file').'.FRONT_MER_LOGIN')</span>
                            </div>
                            <div class="form-group">
                                {!! Form::email('cus_email','suganya.t@pofitec.com',['class'=>'form-control','required','id'=>'email','placeholder'=>(Lang::has(Session::get('front_lang_file').'.ADMIN_REG_MAIL')) ? trans(Session::get('front_lang_file').'.ADMIN_REG_MAIL') : trans($FRONT_LANGUAGE.'.ADMIN_REG_MAIL')]) !!}
                            </div>
                            <div class="form-group">
                                {!! Form::input('password','cus_password','123456',['class'=>'form-control','required','id'=>'pwd','placeholder'=>(Lang::has(Session::get('front_lang_file').'.ADMIN_REG_PASS')) ? trans(Session::get('front_lang_file').'.ADMIN_REG_PASS') : trans($FRONT_LANGUAGE.'.ADMIN_REG_PASS')]) !!}
                            </div>

                            <span id="login_err" style="color:red"></span>
                            <div class="form-group" style="text-align: center;">
                                {{--{!! Form::button('LOGIN',['class' => 'login-btn','onClick' => 'return chk_login()'])!!}--}}

                                <button type="button" class="login-btn" onClick="return chk_login()" >@lang(Session::get('front_lang_file').'.FRONT_MER_LOGIN')
                                </button>
                            </div>
                            <div class="frgt-pwd clear">
                                <label class="forgetPass"><a  href="#" data-toggle="modal" data-target="#myModal1" id="forget-pwd">@lang(Session::get('front_lang_file').'.ADMIN_FORGET_PASS')</a></label>


                            </div>
                            <div class="or-block">
                                <span>@lang(Session::get('front_lang_file').'.ADMIN_OR')</span>
                            </div>
                            <div class="loginIcon">
                                <a href="{{ url('auth/facebook') }}" class="fb">
                                <!-- @lang(Session::get('front_lang_file').'.ADMIN_SIGNIN_WITH_FB') -->

                                </a>
                                <a href="{{ url('auth/google') }}" class="gPlus">
                                <!-- @if (Lang::has(Session::get('front_lang_file').'.FRONT_CN_G+')!= '') {{  trans(Session::get('front_lang_file').'.FRONT_CN_G+') }} @else {{ trans($FRONT_LANGUAGE.'.FRONT_CN_G+') }} @endif -->
                                </a>
                            </div>
                            <div class="signUpLinkDiv">
                                <a href="#signup" aria-controls="signup" class="signup" role="tab" data-toggle="tab">@lang(Session::get('front_lang_file').'.FRONT_CREATE_YOUR')<span>{{ $SITENAME }}</span> <span>@lang(Session::get('front_lang_file').'.FRONT_ACC')!</span></a>
                            </div>
                            {!! Form::close() !!}

                        </div>
                        <div role="tabpanel" class="tab-pane" id="signup" style="display: block;">
                            {!! Form::open(['method' => 'post','enctype'=>'multipart/form-data']) !!}
                            <div class="title">
                                <h4>@lang(Session::get('front_lang_file').'.FRONT_REGISTER')</h4>
                            </div>
                            <!-- <div class="input-left"> -->
                            <div class="form-group">
                                {!! Form::text('cus_fname','',['class'=>'form-control signupClass','required','placeholder'=>(Lang::has(Session::get('front_lang_file').'.ADMIN_REG_NAME')) ? trans(Session::get('front_lang_file').'.ADMIN_REG_NAME') : trans($FRONT_LANGUAGE.'.ADMIN_REG_NAME')  ,'id'=>'cus_fname','maxlength'=>'50']) !!}
                                <div id="cus_fname_err" ></div>
                            </div>

                            <!-- </div>
                            <div class="input-right"> -->
                            <div class="form-group">
                                {!! Form::text('cus_phone','',['class'=>'form-control signupClass','required','placeholder'=>(Lang::has(Session::get('front_lang_file').'.ADMIN_PHONE')) ? trans(Session::get('front_lang_file').'.ADMIN_PHONE') : trans($FRONT_LANGUAGE.'.ADMIN_PHONE'),'id'=>'cus_phone','onkeyup'=>'validate_phone(\'cus_phone\');','maxlength'=>'15']) !!}
                                <div id="cus_phone_err" ></div>
                            </div>
                            <!-- </div> -->

                            <div class="form-group">
							
								@if(Request::segment(2)!='' && Request::segment(1)=='refer-login')
									 {!! Form::email('cus_email',base64_decode(Request::segment(2)),['class'=>'form-control signupClass','required','placeholder'=>(Lang::has(Session::get('front_lang_file').'.ADMIN_REG_MAIL')) ? trans(Session::get('front_lang_file').'.ADMIN_REG_MAIL') : trans($FRONT_LANGUAGE.'.ADMIN_REG_MAIL'),'id'=>'cus_email','maxlength'=>'100','readonly'=>'readonly']) !!}
									{!! Form::hidden('referer_id',Request::segment(2)) !!}
								@else
									{!! Form::email('cus_email','',['class'=>'form-control signupClass','required','placeholder'=>(Lang::has(Session::get('front_lang_file').'.ADMIN_REG_MAIL')) ? trans(Session::get('front_lang_file').'.ADMIN_REG_MAIL') : trans($FRONT_LANGUAGE.'.ADMIN_REG_MAIL'),'id'=>'cus_email','maxlength'=>'100']) !!}
									{!! Form::hidden('referer_id','') !!}
								@endif
                               

                                <div id="cus_email_err" ></div>
                                
                            </div>
                            <div class="form-group">

                                {!! Form::input('password','cus_password','',['class'=>'form-control signupClass','required','placeholder'=>(Lang::has(Session::get('front_lang_file').'.ADMIN_REG_PASS')) ? trans(Session::get('front_lang_file').'.ADMIN_REG_PASS') : trans($FRONT_LANGUAGE.'.ADMIN_REG_PASS'),'id'=>'cus_pwd','maxlength'=>'15']) !!}
                                <div id="cus_pwd_err" class="signup_pwd"></div>
                                @if($PW_PROTECT == 1)
                                    <ul style="list-style-type:disc;" id="pwd_require">
                                        <li id="letter" class="invalid">{!! trans(Session::get('front_lang_file').'.FRONT_LW_CASE')!!}</li>



                                        <li id="capital" class="invalid">{!! trans(Session::get('front_lang_file').'.FRONT_CA_CASE')!!}</li>


                                        <li id="number" class="invalid">{!! trans(Session::get('front_lang_file').'.FRONT_SP_CASE')!!}</li>

                                        <li id="length" class="invalid">{!! trans(Session::get('front_lang_file').'.FRONT_MIN6_CASE')!!}</li>

                                    </ul>
                                @endif

                            </div>
                            @if($SHOW_CAPTCHA == 1)
                                <div class="form-group input-group">
                                    <span class="captcha" id="captcha_cus"></span>
                                    {{ Form::text('captcha','',['id' => 'enter_captcha_cus','class' => 'form-control','placeholder' => (Lang::has(Session::get('front_lang_file').'.FRONT_ENTER_CAPTCHA')) ? trans(Session::get('front_lang_file').'.FRONT_ENTER_CAPTCHA') : trans($FRONT_LANGUAGE.'.FRONT_ENTER_CAPTCHA'),'required'])}}
                                    <button style="padding: 0px 8px;" type="button" class="btn btn-success" onClick="createCaptcha('cus')"><i class="fa fa-refresh" id="refresh"></i></button>

                                </div>
                                <div id="captcha_err"></div>
                            @endif
                            <div class="clear form-group">

                                <label class="checkboxLabel" style="font-size: 12px;">
                                    {!! Form::checkbox('condition_check',null,null,array('id'=>'term')) !!}
                                    <span class="chkSpan"></span>
                                    @lang(Session::get('front_lang_file').'.FRONT_HAVE_ACCEPTED')

                                    @isset($terms)
                                        <a href="{{url('cms').'/'.$terms->id}}" target="new">@lang(Session::get('front_lang_file').'.FRONT_TERM_CONDITION') </a>
                                    @endisset
                                    @lang(Session::get('front_lang_file').'.FRONT_AND')
                                    @isset($policy)
                                        <a href="{{url('cms').'/'.$policy->id}}" target="new"> @lang(Session::get('front_lang_file').'.FRONT_PRIVACY_POLICY') </a>
                                    @endisset

                                </label>
                                <div id="term_err" style="color:red"></div>


                            </div>
                            <div class="clear form-group" style="text-align: center;">
                                <img src="{{url('').'/public/images/spinning-loading-bar.gif'}}" style="width:23%;display:none;margin-left: 35%;" id="normal_loader" />
                                <a href="#" class="login-btn" id="normal_signup" onclick="signup();">@lang(Session::get('front_lang_file').'.SIGN_UP')</a>

                                <div id="signup_error" ></div>
                            </div>
                            <div class="or-block">
                                <span>@lang(Session::get('front_lang_file').'.ADMIN_OR')</span>

                            </div>
                            <div class="loginIcon">
                                <a href="{{ url('auth/facebook') }}" class="fb">
                                    <!-- Signin with Facebook  -->
                                </a>
                                <a href="{{ url('auth/google') }}" class="gPlus">
                                    <!-- Signin with Google +  -->
                                </a>
                            </div>
                            <div class="signUpLinkDiv">
                                <a href="#signup" aria-controls="signup" class="login" role="tab" data-toggle="tab">@lang(Session::get('front_lang_file').'.FRONT_ALREADY_HAVE_ACC')? <span>@lang(Session::get('front_lang_file').'.FRONT_MER_LOGIN')</span></a>
                            </div>
                            {!! Form::close() !!}

                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>


<!--Forgot Password Modal -->
<div class="modal fade" id="myModal1" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" onClick ="clear_data('forgot_email')"><span aria-hidden="true">&times;</span>

                </button>
            </div>
            <div class="modal-body">
                {{ Form::open(['method' => 'post','id' => 'forget_form','onSubmit' => 'return check_mail()','url' => 'user_forgot_password'])}}
                <div class="form-group">
                    {{ Form::text('mail','',['id' => 'forgot_email','class' => 'form-control','placeholder' => (TWILIO_STATUS == 1 ) ? __(Session::get('front_lang_file').'.FRONT_FORGET_MAIL_PH') : __(Session::get('front_lang_file').'.FRONT_FORGET_MAIL') ,'required'])}}
                    <p id="error" style="color:red"></p>
                </div>
                {{ Form::submit(__(Session::get('front_lang_file').'.FRONT_SUBMIT'),['class' => 'form-control login-btn'])}}

                {{ Form::close()}}
            </div>

        </div>
    </div>
</div>

<!--OTP Modal -->
<div class="modal fade" id="otpmyModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">

            </div>
            <div class="modal-body">
                <div class="form-group">
                    <input type="text" class="form-control" id="otp" placeholder="Enter OTP">
                    <div id="otp_sec"></div>
                    <div id="otp_err" style="color: red;"></div>
                </div>
                <a href="#" class="login-btn" onclick="otp_submit();" id="submit_otp">@lang(Session::get('front_lang_file').'.FRONT_SUBMIT')</a>
            </div>

        </div>
    </div>
</div>
<!-- mobile number change Model -->
<div class="modal fade" id="otpMobileChangemyModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">

            </div>
            <div class="modal-body">
                <div class="form-group">
                    <input type="text" class="form-control" id="mobile_otp" placeholder="Enter OTP">
                    <div id="mobile_otp_sec"></div>
                    <div id="mobile_otp_err" style="color: red;"></div>
                </div>
                <a href="#" class="login-btn" onclick="mobile_otp_submit();" id="submit_otp">@lang(Session::get('front_lang_file').'.FRONT_SUBMIT')</a>
            </div>

        </div>
    </div>
</div>
<!-- OTP for Merchant signup -->
<div class="modal fade" id="MerchantOtpmyModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">

            </div>
            <div class="modal-body">
                <div class="form-group">
                    <input type="text" class="form-control" id="mer_otp" placeholder="Enter OTP">
                    <div id="mer_otp_sec"></div>
                    <div id="mer_otp_err" style="color: red;"></div>
                </div>
                <a href="#" class="login-btn" onclick="mer_otp_submit();" id="submit_otp">@lang(Session::get('front_lang_file').'.FRONT_SUBMIT')</a>
            </div>

        </div>
    </div>
</div>
<!-- Refer friend Modal -->
<div id="referModal" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        {{ Form::open(['method' => 'post','url' => 'refer_friend','onsubmit'=>'return validate_refer_friend_form()'])}}
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">@lang(Session::get('front_lang_file').'.ADMIN_REFER_FRIENDS')</h4>
            </div>
            <div class="modal-body">
	
                <p>@lang(Session::get('front_lang_file').'.ADMIN_REFER_FRIENDS') {{$offer}} % @lang(Session::get('front_lang_file').'.ADMIN_OFFER_NEXT_PURCHASE')</p>

                {{ Form::email('mail','',['class' => 'form-control','required','placeholder'=>'Referee Mail','id'=>'refer_friend_email_head'])}}
				<p id="refer_friend_email_error" style="color:red;display:none">Please enter email</p>
                {{ Form::hidden('offer',$offer)}}
                {{ Form::submit('Submit',['class' => 'btn btn-success','id'=>'refer_friend_submitBtn'])}}

            </div>

        </div>
        {{ Form::close()}}
    </div>
</div>
			