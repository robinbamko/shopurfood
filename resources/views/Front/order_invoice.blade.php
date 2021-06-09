@extends('Front.layouts.default')
@section('PageTitle')
  {{$pagetitle}}
@endsection
@section('content')
  <!-- MAIN -->
  <style type="text/css">
    @media print
    {
      #sidebar-nav,.navbar { visibility: hidden; }
      .div * { visibility: visible; }
      .div2 { position: absolute; top: 40px; left: 30px; }
      .header{visibility: hidden;}
      .footer{display: none;}
      .lead {margin-bottom: 10px;}
      .no-print .btn {display: none;}
      table th, table td {color: #212121 !important;}
      .payment-method p {width: 100%;border: 0px solid #ff5215;text-align: left;font-weight: 800; }
    }
    .invoice-section .location {border: 1px solid #383757;border-radius: 20px;overflow: hidden;}
    .invoice-section .panel-heading {background: #383757;color: #ffffff;border: 0px;}
    table.table .table {background-color: #f1f1f1;}
    .main-content {background: #ffffff;}
    .invoice-section .panel-body {background: #fff;}
    .table td, .table th {border-top: 1px solid #cfcfcf;}
    .table tr:first-child td, .table tr:first-child th {border-top: 0px solid #cfcfcf;}
  </style>
  <div class="main-sec">
    <!-- MAIN CONTENT -->
    <div class="main-content">

      <div class="container-fluid add-country">
        <div class="row">
          <div class="container right-container">
            <div class="col-md-12 invoice-section">
              <div class="location panel">
                <div class="panel-heading p__title">
                  {{$pagetitle}}
                </div>
                {{-- Display error message--}}



                {{--Manage page starts--}}
                <div class="panel-body" id="location_table" >
                  <div id="loading-image" style="display:none">
                    <button type="button" class="btn btn-primary" disabled="disabled" ><i class="fa fa-spinner fa-spin"></i> @lang(Session::get('front_lang_file').'.LOAD')</button>
                  </div>

                  @php $sub_total=$grand_total=$tax_total=$shipping_total=0 @endphp
                  <div>
                  {{--@foreach($Invoice_Order as $Order)--}}
                  @php $Order = $Invoice_Order[0]; @endphp
                  <!-- info row -->

                    <div class="row invoice-info">

                      <div class="col-lg-6 col-md-12 col-sm-12 invoice-col order-id-sec">

                        <b>
							@if($Order->ord_pay_type == "COD")
							  {{(Lang::has(Session::get('front_lang_file').'.FRONT_OR_PLCED_ON')) ? trans(Session::get('front_lang_file').'.FRONT_OR_PLCED_ON') : trans($FRONT_LANGUAGE.'.FRONT_OR_PLCED_ON')}}
							@else
							  {{(Lang::has(Session::get('front_lang_file').'.ADMIN_PAYMENT_PAID_ON')) ? trans(Session::get('front_lang_file').'.ADMIN_PAYMENT_PAID_ON') : trans($FRONT_LANGUAGE.'.ADMIN_PAYMENT_PAID_ON')}}
							@endif
                          :</b> {{date('m/d/Y',strtotime($Order->ord_date))}}
                        <br>
                        <b>{{(Lang::has(Session::get('front_lang_file').'.ADMIN_ORDER_ID')) ? trans(Session::get('front_lang_file').'.ADMIN_ORDER_ID') : trans($FRONT_LANGUAGE.'.ADMIN_ORDER_ID')}} :</b> {{$Order->ord_transaction_id}}

                      </div>


                      <div class="col-lg-6 col-md-12 col-sm-12 invoice-col invoice-cli-ad">
                          <?php

                          if($Order->ord_shipping_cus_name!='' && $Order->ord_shipping_address!=''  && $Order->ord_shipping_mobile!='' && $Order->ord_self_pickup!=1)
                          {
                              $OrderCustomerName = $Order->ord_shipping_cus_name;
                              $OrderCustomerAddress = $Order->ord_shipping_address;
                              $OrderCustomerAddress1 = $Order->ord_shipping_address1;
                              $OrderCustomerMobile = $Order->ord_shipping_mobile;
                             
                               $OrderCustomerEmail = $Order->order_ship_mail;
                          }
                          else
                          {
                              $OrderCustomerName = $Order->cus_fname.' '.$Order->cus_lname;
                              $OrderCustomerAddress = $Order->cus_address;
                              $OrderCustomerAddress1 = '';
                              $OrderCustomerMobile = $Order->cus_phone1;
                              $OrderCustomerEmail = $Order->cus_email;
//                              $OrderCustomerEmail = $Order->order_ship_mail;
                          }
                          ?>
                        <h2>{{(Lang::has(Session::get('front_lang_file').'.ADMIN_TO')) ? trans(Session::get('front_lang_file').'.ADMIN_TO') : trans($FRONT_LANGUAGE.'.ADMIN_TO')}}</h2>
                        <address>
                          <strong>{{ucfirst($OrderCustomerName)}}</strong>
                          @if($OrderCustomerMobile != '')<p>{{(Lang::has(Session::get('front_lang_file').'.ADMIN_ADDRESS')) ? trans(Session::get('front_lang_file').'.ADMIN_ADDRESS') : trans($FRONT_LANGUAGE.'.ADMIN_ADDRESS')}} : @if($OrderCustomerAddress1!='')  {{$OrderCustomerAddress1}} <br> @endif {{$OrderCustomerAddress}} </p>@endif

                          @if($OrderCustomerMobile != '')<p>{{(Lang::has(Session::get('front_lang_file').'.ADMIN_PHONE')) ? trans(Session::get('front_lang_file').'.ADMIN_PHONE') : trans($FRONT_LANGUAGE.'.ADMIN_PHONE')}} : {{$OrderCustomerMobile}}</p>@endif

                          @if($OrderCustomerEmail != '')<p>{{(Lang::has(Session::get('front_lang_file').'.ADMIN_EMAIL')) ? trans(Session::get('front_lang_file').'.ADMIN_EMAIL') : trans($FRONT_LANGUAGE.'.ADMIN_EMAIL')}} : {{$OrderCustomerEmail}}</p>@endif
                        </address>

                      </div>
                      <!-- /.col -->

                      <!-- /.col -->
                    </div>
                    <!-- /.row -->

                    <!-- Table row -->
                    <div class="row">
                      <div class="col-lg-12 col-md-12 col-xs-12 table invoice-table">
                        <table class="table table-striped">
                          <thead>
                          <tr>
                            <th>{{(Lang::has(Session::get('front_lang_file').'.ADMIN_RESTSTORE_NAME')) ? trans(Session::get('front_lang_file').'.ADMIN_RESTSTORE_NAME') : trans($FRONT_LANGUAGE.'.ADMIN_RESTSTORE_NAME')}}</th>
                            <th>{{(Lang::has(Session::get('front_lang_file').'.ADMIN_ITEMRPDT_NAME')) ? trans(Session::get('front_lang_file').'.ADMIN_ITEMRPDT_NAME') : trans($FRONT_LANGUAGE.'.ADMIN_ITEMRPDT_NAME')}}</th>


                              <th>{{(Lang::has(Session::get('front_lang_file').'.ADMIN_PRE_ORDER_DATE')) ? trans(Session::get('front_lang_file').'.ADMIN_PRE_ORDER_DATE') : trans($FRONT_LANGUAGE.'.ADMIN_PRE_ORDER_DATE')}}</th>


                              <th>{{(Lang::has(Session::get('front_lang_file').'.ADMIN_QTY')) ? trans(Session::get('front_lang_file').'.ADMIN_QTY') : trans($FRONT_LANGUAGE.'.ADMIN_QTY')}}</th>



                            
                <th >{{(Lang::has(Session::get('front_lang_file').'.ADMIN_UNIT_PRICE')) ? trans(Session::get('front_lang_file').'.ADMIN_UNIT_PRICE') : trans($FRONT_LANGUAGE.'.ADMIN_UNIT_PRICE')}}</th>


                            <th >{{(Lang::has(Session::get('front_lang_file').'.ADMIN_TAX')) ? trans(Session::get('front_lang_file').'.ADMIN_TAX') : trans($FRONT_LANGUAGE.'.ADMIN_TAX')}}</th>
                            <th>{{(Lang::has(Session::get('front_lang_file').'.ADMIN_SUBTOTAL')) ? trans(Session::get('front_lang_file').'.ADMIN_SUBTOTAL') : trans($FRONT_LANGUAGE.'.ADMIN_SUBTOTAL')}}</th>
                          </tr>
                          </thead>
                          <tbody>
                          @foreach($Invoice_Order as $Order_sub)
                            @php
                              $calc_sub_total = ($Order_sub->ord_quantity*$Order_sub->ord_unit_price)+$Order_sub->ord_tax_amt;
                              $sub_total +=$calc_sub_total;
                              $shipping_total =$Order_sub->ord_delivery_fee;
                           // $grand_total +=($Order_sub->ord_sub_total)+($Order_sub->ord_tax_amt)+($Order_sub->ord_delivery_fee);
                            @endphp
                            <tr>
                              <td>{{$Order_sub->st_store_name}} <br>{{$Order_sub->st_address}}  </td>
                              <td>
                                {{$Order_sub->pro_item_name}}
                                @if($Order_sub->ord_spl_req != '')
                                  <br>
                                  <a href="" class="" data-toggle="modal" data-target="#spl_nt">@lang(Session::get('front_lang_file').'.FRONT_VIEW_SPL_NT')</a>
                                  {{-- spl req popup --}}
                                  <div id="spl_nt" class="modal fade choice-modal" role="dialog">
                                    <div class="modal-dialog">
                                      <button type="button" class="close" data-dismiss="modal">&times;</button>
                                      <!-- Modal content-->
                                      <div class="modal-content">
                                        <div class="modal-header">
                                          @lang(Session::get('front_lang_file').'.FRONT_VIEW_SPL_NT')
                                        </div>
                                        <div class="modal-body">

                                          <div class="choice-modal-content">
                                            {{$Order_sub->ord_spl_req}}

                                          </div>
                                        </div>

                                      </div>

                                    </div>
                                  </div>
                                @endif
                              </td>

                                <td>
                                    @if($Order_sub->ord_pre_order_date != '')
                                        {{date('m/d/Y h:i A',strtotime($Order_sub->ord_pre_order_date))}}
                                    @else
                                        -
                                    @endif
                                </td>
                              <td>{{$Order_sub->ord_quantity}}</td>
                              <td>{{$Order_sub->ord_unit_price}} {{$Order_sub->ord_currency}}</td>
                              <td>{{$Order_sub->ord_tax_amt}} {{$Order_sub->ord_currency}}</td>
                              <td>{{number_format($calc_sub_total,2)}} {{$Order_sub->ord_currency}}</td>
                            </tr>

                            @if($Order_sub->ord_had_choices=="Yes")

                              @if(count($choices)>0)
                                <tr>
                                  <td colspan="5" align="right" class="table-includes">
                                    <h5>{{(Lang::has(Session::get('front_lang_file').'.ADMIN_INCLUDES')) ? trans(Session::get('front_lang_file').'.ADMIN_INCLUDES') : trans($FRONT_LANGUAGE.'.ADMIN_INCLUDES')}} : </h5>
                                  </td>
                                  <td colspan="2">
                                    <table class="table">
                                      @foreach($choices as $key=>$val)
                                        {{--@php $sub_total +=$val;  $grand_total +=$val; @endphp--}}
                                        {{--<tr><td>{{$key}}</td><td style="text-align:right">{{$val}} {{$Order_sub->ord_currency}}</td></tr>--}}

                                            @php  $explode = explode('`',$key);
                                        $name = $explode[0];
                                        $ch_ord_id = $explode[1];
                                            @endphp
                                            @if($ch_ord_id == $Order_sub->ord_id)
                                                @php $sub_total +=($val);  $grand_total +=$val; @endphp
                                                <tr><td>{{$name}}</td><td style="text-align:right">{{$val}} {{$Order_sub->ord_currency}}</td></tr>
                                            @endif
                                      @endforeach
                                    </table>
                                  </td>
                                </tr>
                              @endif
                            @endif

                          @endforeach



                          </tbody>
                        </table>
                      </div>
                      <!-- /.col -->
                    </div>
                    <!-- /.row -->

                    <div class="row">
                      <!-- accepted payments column -->
                      <div class="col-lg-7 col-md-6 col-xs-12 payment-method">
                        <p class="lead">{{(Lang::has(Session::get('front_lang_file').'.ADMIN_PAYMENT_METHODS')) ? trans(Session::get('front_lang_file').'.ADMIN_PAYMENT_METHODS') : trans($FRONT_LANGUAGE.'.ADMIN_PAYMENT_METHODS')}} : {{$Order->ord_pay_type}} </p>
                        <!--img src="images/visa.png" alt="Visa">
                          <img src="images/mastercard.png" alt="Mastercard">
                          <img src="images/american-express.png" alt="American Express">
                        <img src="images/paypal.png" alt="Paypal"-->

                      <!--<p>{{$Order->ord_pay_type}}</p>-->
                      </div>
                      <!-- /.col -->
                      <div class="col-lg-5 col-md-6 col-xs-12 order-invoice-col-5">

                        <div class="table-responsive">
                          <table class="table" style="background: #f2f2f2;">
                            <tbody>
                            <tr>
                              <th style="font-weight:500;">{{(Lang::has(Session::get('front_lang_file').'.ADMIN_SUBTOTAL')) ? trans(Session::get('front_lang_file').'.ADMIN_SUBTOTAL') : trans($FRONT_LANGUAGE.'.ADMIN_SUBTOTAL')}} :</th>
                              <td align="right" >{{number_format($sub_total,2)}} {{$Order_sub->ord_currency}}</td>
                            </tr>
                            @if($Order_sub->ord_self_pickup == 0)
                              <tr>
                                <th style="font-weight:500;">{{(Lang::has(Session::get('front_lang_file').'.ADMIN_DELIVERY_FEE')) ? trans(Session::get('front_lang_file').'.ADMIN_DELIVERY_FEE') : trans($FRONT_LANGUAGE.'.ADMIN_DELIVERY_FEE')}} :</th>
                                <td align="right">{{number_format($shipping_total,2)}} {{$Order_sub->ord_currency}}</td>
                              </tr>
                            @endif
                            <tr>
                              <th style="font-weight:500;">{{(Lang::has(Session::get('front_lang_file').'.ADMIN_UESD_WALLET')) ? trans(Session::get('front_lang_file').'.ADMIN_UESD_WALLET') : trans($FRONT_LANGUAGE.'.ADMIN_UESD_WALLET')}} :</th>
                              <td align="right" >{{number_format($Order_sub->ord_wallet,2)}} {{$Order_sub->ord_currency}}</td>
                            </tr>
                            <tr style="background: #373756;color: #fff;">
                              <th style="font-weight:700;">{{(Lang::has(Session::get('front_lang_file').'.ADMIN_TOTAL')) ? trans(Session::get('front_lang_file').'.ADMIN_TOTAL') : trans($FRONT_LANGUAGE.'.ADMIN_TOTAL')}} :</th>
                              @if($Order_sub->ord_self_pickup == 0)
                                <td align="right" style="font-weight:700;">{{number_format($sub_total+$shipping_total - $Order_sub->ord_wallet,2)}} {{$Order_sub->ord_currency}}</td>
                              @else
                                <td align="right" style="font-weight:700;">{{number_format($sub_total - $Order_sub->ord_wallet,2)}} {{$Order_sub->ord_currency}}</td>
                              @endif
                            </tr>
                            </tbody>
                          </table>
                        </div>
                      </div>
                      <!-- /.col -->
                    </div>

                    <!-- /.row -->

                    <!-- this row will not appear when printing -->
                    <div class="row no-print">
                      <div class="col-lg-12 col-md-12 col-xs-12">
                        <button style="background: #ff3c15;" class="btn btn-default" onclick="window.print();"><i class="fa fa-print"></i> {{(Lang::has(Session::get('front_lang_file').'.ADMIN_PRINT')) ? trans(Session::get('front_lang_file').'.ADMIN_PRINT') : trans($FRONT_LANGUAGE.'.ADMIN_PRINT')}}</button>

                      </div>
                    </div>


                  </div>

                </div>
                {{--Manage page ends--}}
              </div>

            </div>
          </div>
        </div>
      </div>
      <!-- /.panel-body -->
    </div>
    <!-- END MAIN CONTENT -->
  </div>
  <div id="myModal" class="modal">
    <span class="close">&times;</span>
    <img class="modal-content" id="img01">
    <div id="caption"></div>
  </div>


@endsection