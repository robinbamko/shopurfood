@extends('Admin.layouts.default')
@section('PageTitle')
    {{$pagetitle}}
@endsection
@section('content')
    <!-- MAIN -->
    <style>
        .order-restaurant h3 p {
            font-size: 14px;
            text-align: center;
            line-height: 8px;

        }
        .order-restaurant h3 p:first-child {
            margin-top:10px;
        }
    </style>
    <div class="main">
        <!-- MAIN CONTENT -->
        <div class="main-content">
            <h1 class="page-header">{{$pagetitle}}</h1>
            <div class="container-fluid add-country">
                <div class="row">
                    <div class="container right-container">
                        <div class="col-md-12">

                            <div class="location panel">
                                <div class="panel-heading p__title">
                                    {{$pagetitle}}
                                </div>
                                {{-- Display error message--}}

                            </div>


                            <div class="col-md-12 order-track">

                                <div class="order-track-date">
                                    <p><span>{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ORDER_DATE')) ? trans(Session::get('admin_lang_file').'.ADMIN_ORDER_DATE') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_ORDER_DATE')}} :</span> @if($customer_details->ord_pre_order_date!=NULL) {{date('m/d/y H:i:s',strtotime($customer_details->ord_pre_order_date))}} @ELSE {{date('m/d/y H:i:s',strtotime($customer_details->ord_date))}} @endif</p>
                                    <p><span>{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ORDER_ID')) ? trans(Session::get('admin_lang_file').'.ADMIN_ORDER_ID') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_ORDER_ID')}} :</span> {{$customer_details->ord_transaction_id}}</p>
                                </div>
                                <div class="order-track-sec1">
                                    <h4>{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_CUSTOMER_DETAILS')) ? trans(Session::get('admin_lang_file').'.ADMIN_CUSTOMER_DETAILS') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_CUSTOMER_DETAILS')}}</h4>
                                    <p>{{$customer_details->ord_shipping_cus_name}}</p>
                                    <p>{{$customer_details->ord_shipping_mobile}}</p>
                                    <p>{{$customer_details->ord_shipping_mobile1}}</p>
                                    <p>{{$customer_details->order_ship_mail}}</p>
                                    <p>{{$customer_details->ord_shipping_address1}}</p>
                                    <p>{{$customer_details->ord_shipping_address}}</p>
                                </div>
                                @if(count($storewise_details) > 0 )

                                    @foreach($storewise_details as $key=>$commonDet)

                                        @if(count($commonDet['general_detail']) > 0 )
                                            @foreach($commonDet['general_detail'] as $comDet)
											<div class="order-rest-div">
                                                <div class="order-track-sec2">
                                                    <div class="order-restaurant">
                                                        <div class="order-restaurant-logo">
                                                            @php $path = url('').'/public/images/noimage/'.$no_shop_logo;
                                                            @endphp
                                                            @if($comDet['st_type'] == 1)
                                                                @php $filename = public_path('images/restaurant/').$comDet['st_logo'];  @endphp
                                                                @if(file_exists($filename) && $comDet['st_logo'] != '')
                                                                    @php $path = url('').'/public/images/restaurant/'.$comDet['st_logo'];@endphp
                                                                @endif
                                                            @else
                                                                @php $filename = public_path('images/store/').$comDet['st_logo'];  @endphp
                                                                @if(file_exists($filename) && $comDet['st_logo'] != '')
                                                                    @php $path = url('').'/public/images/store/'.$comDet['st_logo'];@endphp
                                                                @endif
                                                            @endif
                                                            <img src="{{$path}}" alt="{{$key}}">
                                                        </div>
                                                        <h3>{{$key}}
                                                            <p>{{$comDet['st_address']}}</p>
                                                            <p>{{$comDet['mer_fname'].' '.$comDet['mer_lname']}}</p>
                                                            <p>{{$comDet['mer_email']}}</p>
                                                            <p>{{$comDet['mer_phone']}}</p>
                                                        </h3>

                                                    </div>
													@if($AGENTMODULE==1)
                                                    <div class="order-agent1">
                                                        <h4>{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_AGENT_DETAILS')) ? trans(Session::get('admin_lang_file').'.ADMIN_AGENT_DETAILS') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_AGENT_DETAILS')}}</h4>
                                                        @if($comDet['ord_task_status']==0)
                                                            @if($comDet['ord_self_pickup']=='1')
                                                                {{(Lang::has(Session::get('admin_lang_file').'.ADMIN_SELF_PICKUP')) ? trans(Session::get('admin_lang_file').'.ADMIN_SELF_PICKUP') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SELF_PICKUP')}}
                                                            @else
                                                                <p>{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_NOT_YET_ASSIGNED')) ? trans(Session::get('admin_lang_file').'.ADMIN_NOT_YET_ASSIGNED') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_NOT_YET_ASSIGNED')}}</p>
                                                            @endif
                                                        @elseif($comDet['ord_task_status'] == 1 && $comDet['ord_agent_acpt_status'] == 1)
                                                            <p>{{ucfirst($comDet['agent_fname']).' '.$comDet['agent_lname']}}</p>
                                                            <p>{{$comDet['agent_phone1']}}</p>
                                                            <p>{{$comDet['agent_email']}}</p>
                                                        @else
                                                            <p>{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_NOT_YET_ASSIGNED')) ? trans(Session::get('admin_lang_file').'.ADMIN_NOT_YET_ASSIGNED') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_NOT_YET_ASSIGNED')}}</p>
                                                        @endif
                                                    </div>
													@endif
                                                    <div class="order-agent2">
                                                        <h4>{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_DELBOY_DETAILS')) ? trans(Session::get('admin_lang_file').'.ADMIN_DELBOY_DETAILS') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_DELBOY_DETAILS')}}</h4>
                                                        @if($comDet['ord_task_status']==0)
                                                            @if($comDet['ord_self_pickup']=='1')
                                                                {{(Lang::has(Session::get('admin_lang_file').'.ADMIN_SELF_PICKUP')) ? trans(Session::get('admin_lang_file').'.ADMIN_SELF_PICKUP') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SELF_PICKUP')}}
                                                            @else
                                                                <p>{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_NOT_YET_ASSIGNED')) ? trans(Session::get('admin_lang_file').'.ADMIN_NOT_YET_ASSIGNED') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_NOT_YET_ASSIGNED')}}</p>
                                                            @endif
                                                        @elseif($comDet['ord_task_status'] == 1 && $comDet['ord_delboy_act_status'] == 1)
                                                            <p>{{ucfirst($comDet['deliver_fname']).' '.$comDet['deliver_lname']}}</p>
                                                            <p>{{$comDet['deliver_phone1']}}</p>
                                                            <p>{{$comDet['deliver_email']}}</p>
                                                        @else
                                                            <p>{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_NOT_YET_ASSIGNED')) ? trans(Session::get('admin_lang_file').'.ADMIN_NOT_YET_ASSIGNED') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_NOT_YET_ASSIGNED')}}</p>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div id="track-steps">
                                                    <h4>{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ORDER_STATUS')) ? trans(Session::get('admin_lang_file').'.ADMIN_ORDER_STATUS') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_ORDER_STATUS')}}</h4>
                                                    <!-- Order flow diagram -->
                                                    @if(count($commonDet['delivery_detail'])>0)
                                                        @php $show_ord_status = 0; @endphp
                                                        @foreach($commonDet['delivery_detail'] as $detailVal)
                                                            @php
                                                                $chk_ord_status = $chk_cancel_status = array();
                                                            @endphp
                                                            @foreach($detailVal as $detVal)
                                                                @php
                                                                    array_push($chk_ord_status,$detVal->ord_status);
                                                                    array_push($chk_cancel_status,$detVal->ord_cancel_status);
                                                                @endphp
                                                                @if($detVal->ord_cancel_status != 1)
                                                                    @php $ord_id  = $detVal->ord_id;
															 $show_ord_status = $detVal->ord_status;
                                                                    @endphp
                                                                @endif
                                                            @endforeach
                                                        @endforeach
                                                        <ul id="ul_{{$detVal->ord_id}}">
                                                            {{-- All items are cancelled --}}
                                                            @if(!in_array('0',$chk_cancel_status))
                                                                {{-- <li style="float:left" data-target="#payCustModal_{{$detVal->ord_id}}" data-toggle="modal"><div class="step active" data-desc="{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_CANCELLED_BY_USER')) ? trans(Session::get('admin_lang_file').'.ADMIN_CANCELLED_BY_USER') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_CANCELLED_BY_USER')}}" data-toggle="tooltip" title="{{(Lang::has(Session::get('admin_lang_file').'.MER_REJECTED_REASON')) ? trans(Session::get('admin_lang_file').'.MER_REJECTED_REASON') : trans($ADMIN_OUR_LANGUAGE.'.MER_REJECTED_REASON')}}"><i class="fa fa-times"></i></div></li> --}}
                                                                <li>
                                                                    <div class="step done" data-desc="{{(Lang::has(Session::get('front_lang_file').'.MER_NEW_ORDER')) ? trans(Session::get('front_lang_file').'.MER_NEW_ORDER') : trans($this->FRONT_LANGUAGE.'.MER_NEW_ORDER')}}">
                                                                        <i class="icon-valid"></i>
                                                                    </div>
                                                                </li>
                                                                <li data-target="#payCustModal_{{$detVal->ord_id}}" data-toggle="modal">
                                                                    <div class="step  active" data-desc="{{(Lang::has(Session::get('front_lang_file').'.FRONT_CANCELLED')) ? trans(Session::get('front_lang_file').'.FRONT_CANCELLED') : trans($this->FRONT_LANGUAGE.'.FRONT_CANCELLED')}}" data-toggle="tooltip" title="{{(Lang::has(Session::get('admin_lang_file').'.MER_REJECTED_REASON')) ? trans(Session::get('admin_lang_file').'.MER_REJECTED_REASON') : trans($ADMIN_OUR_LANGUAGE.'.MER_REJECTED_REASON')}}">
                                                                        <i class="icon-valid"></i>
                                                                    </div>
                                                                </li>
                                                            @elseif($show_ord_status == 9)
                                                                <li style="float:left" data-target="#del_failModal_{{$detVal->ord_id}}" data-toggle="modal"><div class="step active" data-desc="{{(Lang::has(Session::get('admin_lang_file').'.MER_FAILED')) ? trans(Session::get('admin_lang_file').'.MER_FAILED') : trans($this->ADMIN_OUR_LANGUAGE.'.MER_FAILED')}}" data-toggle="tooltip" title="{{(Lang::has(Session::get('admin_lang_file').'.DELIVERY_FAILED_REASON')) ? trans(Session::get('admin_lang_file').'.DELIVERY_FAILED_REASON') : trans($ADMIN_OUR_LANGUAGE.'.DELIVERY_FAILED_REASON')}}"><i class="fa fa-times"></i></div></li>
                                                                {{-- failed reason popup --}}
                                                                <div id="del_failModal_{{$detVal->ord_id}}" class="modal payModal" role="dialog" style="color:#000;" data-backdrop="static" data-keyboard="false">
                                                                    <div class="modal-dialog">
                                                                        <!-- Modal content-->
                                                                        <div class="modal-content">
                                                                            <div class="modal-header">
                                                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                                                <h4 class="modal-title">{{(Lang::has(Session::get('admin_lang_file').'.DELI_FAILED_REASON')) ? trans(Session::get('admin_lang_file').'.DELI_FAILED_REASON') : trans($ADMIN_OUR_LANGUAGE.'.DELI_FAILED_REASON')}}</h4>
                                                                            </div>
                                                                            <div class="modal-body">
                                                                                {{$detVal->ord_failed_reason}}
                                                                            </div>
                                                                            <div class="modal-footer">
                                                                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                                                            </div>
                                                                        </div>

                                                                    </div>
                                                                </div>

                                                            @else
                                                                @php $iconArray = array('1'=>'fa fa-shopping-basket','2'=>'fa fa-check','3'=>'fa fa-times','4'=>'fa fa-truck','5'=>'fa fa-truck','6'=>'fa fa-spinner','7'=>'fa fa-suitcase','8'=>'fa fa-male'); @endphp
                                                                @if($show_ord_status==8) @php $iconArray['8']='icon-valid'; @endphp @endif
                                                                @php $ordStatusArray = order_status_array('admin_lang_file',$ADMIN_OUR_LANGUAGE);  @endphp
                                                                @for($i=1;$i<=8;$i++)
                                                                    @if($detVal->ord_self_pickup=='1')
                                                                        @if($i > 3 && $i<=7)
                                                                        @elseif($show_ord_status >= 4 && $i==3)
                                                                        @elseif($show_ord_status == 2 && $i==3)
                                                                        @elseif($show_ord_status == 3 && $i==2)
                                                                        @else
                                                                            @if($i==$show_ord_status) @php $active='active'; @endphp @else @php $active=''; @endphp  @endif
                                                                            <li><div class="step {{$active}}" data-desc="{{$ordStatusArray[$i]}}"><i class="{{$iconArray[$i]}}"></i></div></li>
                                                                        @endif
                                                                    @else
                                                                        @if($show_ord_status >= 4 && $i==3)
                                                                        @elseif($show_ord_status == 2 && $i==3)
                                                                        @elseif($show_ord_status == 3 && $i==2)
                                                                        @else
                                                                            @if($i==$show_ord_status) @php $active='active'; @endphp @else @php $active=''; @endphp  @endif
                                                                            @php if($i<$show_ord_status) { $done='done';} else { $done=''; } @endphp
                                                                            <li><div class="step {{$active}} {{$done}}" data-desc="{{$ordStatusArray[$i]}}"><i class="{{$iconArray[$i]}}"></i></div></li>
                                                                        @endif
                                                                    @endif
                                                                @endfor
                                                            @endif
                                                            <ul>
                                                            @endif
                                                            <!-- End Order flow diagram -->

                                                                @if($comDet['ord_status']==8)
                                                                    <script>
                                                                        $(document).ready(function(){
                                                                            $("#ul_{{$comDet['ord_id']}} li").removeClass('pulse');
                                                                            $("#ul_{{$comDet['ord_id']}} li div").removeClass('active').addClass('done');
                                                                        });
                                                                    </script>
                                                    @endif
                                                </div>
                                            @endforeach
                                        @endif

                                        @if(count($commonDet['delivery_detail']) > 0 )
                                            <div class="order-track-sec3">
                                                <table>
                                                    @foreach($commonDet['delivery_detail'] as $comDet)
                                                        @if(count($comDet) > 0 )
                                                            @foreach($comDet as $commDet)
                                                                @php $pro_images = explode('/**/',$commDet->pro_images);@endphp
                                                                @if($commDet->pro_images!='')
                                                                    @if($commDet->ord_type=='Item')
                                                                        @php $path = url('').'/public/images/noimage/'.$no_item ;@endphp
                                                                        @php $filename = public_path('images/restaurant/items/').$pro_images[0]; @endphp
                                                                        @if($pro_images[0] != '' && file_exists($filename))
                                                                            @php $path = url('').'/public/images/restaurant/items/'.$pro_images[0]; @endphp
                                                                        @else
                                                                            @php $path = url('').'/public/images/noimage/'.$no_item; @endphp
                                                                        @endif
                                                                    @else
                                                                        @php $filename = public_path('images/store/products/').$pro_images[0]; @endphp
                                                                        @if($pro_images[0] != '' && file_exists($filename))
                                                                            @php $path = url('').'/public/images/store/products/'.$pro_images[0]; @endphp
                                                                        @else
                                                                            @php $path = url('').'/public/images/noimage/'.$no_item; @endphp
                                                                        @endif
                                                                    @endif
                                                                @endif
                                                                <tr>
                                                                    <td width="25%">
                                                                        @if($commDet->ord_cancel_status=='1')
                                                                            <a href="javascript:;" data-target="#payCustModal_{{$commDet->ord_id}}" data-toggle="modal"><span data-toggle="tooltip" title="{{(Lang::has(Session::get('admin_lang_file').'.MER_REJECTED_REASON')) ? trans(Session::get('admin_lang_file').'.MER_REJECTED_REASON') : trans($ADMIN_OUR_LANGUAGE.'.MER_REJECTED_REASON')}}" >
																			@if($commDet->ord_status=='3')
                                                                                        {{(Lang::has(Session::get('admin_lang_file').'.ADMIN_CANCELLED_BY_MERCHANT')) ? trans(Session::get('admin_lang_file').'.ADMIN_CANCELLED_BY_MERCHANT') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_CANCELLED_BY_MERCHANT')}}
                                                                                    @else
                                                                                        {{(Lang::has(Session::get('admin_lang_file').'.ADMIN_CANCELLED_BY_USER')) ? trans(Session::get('admin_lang_file').'.ADMIN_CANCELLED_BY_USER') : trans($this->ADMIN_OUR_LANGUAGE.'.ADMIN_CANCELLED_BY_USER')}}
                                                                                    @endif
																			</span></a>
                                                                            <!--MODAL -->
                                                                            <div id="payCustModal_{{$commDet->ord_id}}" class="modal payModal" role="dialog" style="color:#000;" data-backdrop="static" data-keyboard="false">
                                                                                <div class="modal-dialog">
                                                                                    <!-- Modal content-->
                                                                                    <div class="modal-content">
                                                                                        <div class="modal-header">
                                                                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                                                            <h4 class="modal-title">{{(Lang::has(Session::get('admin_lang_file').'.MER_REJECTED_REASON')) ? trans(Session::get('admin_lang_file').'.MER_REJECTED_REASON') : trans($ADMIN_OUR_LANGUAGE.'.MER_REJECTED_REASON')}}</h4>
                                                                                        </div>
                                                                                        <div class="modal-body">
                                                                                            {{$commDet->ord_cancel_reason}}
                                                                                        </div>
                                                                                        <div class="modal-footer">
                                                                                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                                                                        </div>
                                                                                    </div>

                                                                                </div>
                                                                            </div>
                                                                            <!-- END OF MODAL-->
                                                                        @else
                                                                            @if($commDet->ord_status == '1')
                                                                                @lang(Session::get('admin_lang_file').'.MER_ORDER_PLACED')
                                                                            @elseif($commDet->ord_status == '2')
                                                                                @lang(Session::get('admin_lang_file').'.MER_ACCEPT')
                                                                            @elseif($commDet->ord_status == '4')
                                                                                @lang(Session::get('admin_lang_file').'.MER_PREPARE_DELIVER')
                                                                            @elseif($commDet->ord_status == '5')
                                                                                @lang(Session::get('admin_lang_file').'.MER_DISPATCHED')
                                                                            @elseif($commDet->ord_status == '6')
                                                                                @lang(Session::get('admin_lang_file').'.MER_STARTED')
                                                                            @elseif($commDet->ord_status == '7')
                                                                                @lang(Session::get('admin_lang_file').'.MER_ARRIVED')
                                                                            @elseif($commDet->ord_status == '8')
                                                                                @lang(Session::get('admin_lang_file').'.MER_DELIVERED')
                                                                            @elseif($commDet->ord_status == '9')
                                                                                @lang(Session::get('admin_lang_file').'.MER_FAILED')
                                                                            @endif
                                                                        @endif
                                                                    </td>

                                                                    <td width="15%"><img src="{{$path}}"></td>
                                                                    <td width="35%">
                                                                        <p>{{$commDet->item_name}}</p>
                                                                        <p>{{$commDet->itemCodeType}} : {{$commDet->pro_item_code}}</p>
                                                                    </td>
                                                                    <td width="25%">{{$commDet->ord_grant_total .' '.$commDet->ord_currency}}</td>
                                                                </tr>
                                                            @endforeach
                                                        @endif
                                                    @endforeach
                                                </table>
                                            </div>
										</div>
                                        @endif
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <script>
        $('.step').each(function(index, el) {
            $(el).not('.active').addClass('done');
            $('.done').html('<i class="icon-valid"></i>');
            if($(this).is('.active')) {
                $(this).parent().addClass('pulse')
                return false;
            }
        });

    </script>
    <script>
        $(document).ready(function(){
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>



@stop