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
  .footer{visibility: hidden;}
  }
  .invoice-section .location 
  {
  border: 1px solid #69c332;
  background: #f8f7f3;
  }
  .invoice-section .panel-heading {
  text-align: center;
  padding: 10px;
  border-bottom: 1px solid #69c332;
  background: #69c332;
  font-weight: 500;
  font-size: 20px;
  }
  div.panel-body#location_table 
  {
  background: #f2fff4;
  }
  .invoice-table table thead tr{
  background: #69c332;
  }
  .invoice-info .greet {
    border: 1px solid #6c757d66;
    padding: 2px;
  }
  .invoice-info .greet h5
  {
        background-color: #ccc;
    width: 100%;
    text-align: center;
    padding: 7px;
  }
  .invoice-info .greet{
  border: 1px solid #6c757d66;
  padding: 2px;
  background: #eaeaea;
  }

  .greet p {
color: #484747;
padding: 10px;
font-size: 17px;
}
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
                  <button type="button" class="btn btn-primary" disabled="disabled" ><i class="fa fa-spinner fa-spin"></i> Loading...</button>
                </div>
                
                @php $sub_total=$grand_total=$tax_total=$shipping_total=0; @endphp
                <div>
                {{--@foreach($Invoice_Order as $Order)--}}
                  @php $Order = $or_details[0]; @endphp
                    <!-- info row -->
                    
                    <div class="row invoice-info">
					 <div class="col-lg-6 col-md-6 col-sm-6 greet">
                <h5>@lang(Session::get('front_lang_file').'.FRONT_TH_OR')</h5>
                <div class="greet-body">
                  <p>@lang(Session::get('front_lang_file').'.FRONT_OR_CNFM')</p>
                  <a href="{{url('/')}}"><button class="btn btn-default" style="margin-top: 51px;margin-left: 10px;"><i class="fa fa-arrow-left"></i> {{(Lang::has(Session::get('front_lang_file').'.FRONT_CN_SHOP')) ? trans(Session::get('front_lang_file').'.FRONT_CN_SHOP') : trans($FRONT_LANGUAGE.'.FRONT_CN_SHOP')}}</button>
                         </a>
                </div>
            </div>
						<div class="col-lg-6 col-md-6 col-sm-6">
                        
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
                }
                ?>
                <h2>{{(Lang::has(Session::get('front_lang_file').'.FRONT_SH_ADDR')) ? trans(Session::get('front_lang_file').'.FRONT_SH_ADDR') : trans($FRONT_LANGUAGE.'.FRONT_SH_ADDR')}}</h2>
                <table cellpadding="4">
                  <tbody style="font-size: 14px;cell-padding: 2px;color: #616161;">
                    <tr>
                      <td><b>@lang(Session::get('front_lang_file').'.ADMIN_REG_NAME')</b></td>
                      <td>{{ucfirst($OrderCustomerName)}}</td>
                    </tr>
                    <tr>
                      <td><b>@lang(Session::get('front_lang_file').'.ADMIN_ADDRESS')</b></td>
                      <td>{{$OrderCustomerAddress}} @if($OrderCustomerAddress1!='') <br> {{$OrderCustomerAddress1}} @endif</td>
                    </tr>
                    <tr>
                      <td><b>{{(Lang::has(Session::get('front_lang_file').'.ADMIN_PHONE')) ? trans(Session::get('front_lang_file').'.ADMIN_PHONE') : trans($FRONT_LANGUAGE.'.ADMIN_PHONE')}}</b></td>
                      <td>{{$OrderCustomerMobile}}</td>
                    </tr>
                    <tr>
                      <td><b>{{(Lang::has(Session::get('front_lang_file').'.ADMIN_EMAIL')) ? trans(Session::get('front_lang_file').'.ADMIN_EMAIL') : trans($FRONT_LANGUAGE.'.ADMIN_EMAIL')}}</b></td>
                      <td>{{$OrderCustomerEmail}}</td>
                    </tr>
                    <tr>
                      <td>
                        <b>@lang(Session::get('front_lang_file').'.FRONT_PAY_TYPE') </b>
                      </td>
                      <td>
                         @if($Order->ord_pay_type == 'COD')
                            @lang(Session::get('front_lang_file').'.ADMIN_COD')
                          @elseif($Order->ord_pay_type == 'PAYNAMICS')
                            @lang(Session::get('front_lang_file').'.ADMIN_PAYNAMICS')
                          @elseif($Order->ord_pay_type == 'PAYMAYA')
                            @lang(Session::get('front_lang_file').'.ADMIN_PAYMAYA')
                          @elseif($Order->ord_pay_type == 'WALLET')
                            @lang(Session::get('front_lang_file').'.FRONT_WALLET')
                          @endif
                      </td>
                    </tr>
                    <tr>
                      <td><b>{{(Lang::has(Session::get('front_lang_file').'.ADMIN_ORDER_ID')) ? trans(Session::get('front_lang_file').'.ADMIN_ORDER_ID') : trans($FRONT_LANGUAGE.'.ADMIN_ORDER_ID')}}</b></td>
                      <td>{{$Order->ord_transaction_id}}</td>
                    </tr>
					
                  </tbody>
                </table>
               
						</div>
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
                              <th>{{(Lang::has(Session::get('front_lang_file').'.FRONT_IMAGE')) ? trans(Session::get('front_lang_file').'.FRONT_IMAGE') : trans($FRONT_LANGUAGE.'.FRONT_IMAGE')}}</th>
                              <th>
                                {{(Lang::has(Session::get('front_lang_file').'.ADMIN_PRE_ORDER_DATE')) ? trans(Session::get('front_lang_file').'.ADMIN_PRE_ORDER_DATE') : trans($FRONT_LANGUAGE.'.ADMIN_PRE_ORDER_DATE')}}
                              </th>         
                              <th>{{(Lang::has(Session::get('front_lang_file').'.ADMIN_QTY')) ? trans(Session::get('front_lang_file').'.ADMIN_QTY') : trans($FRONT_LANGUAGE.'.ADMIN_QTY')}}</th>
							  <th >{{(Lang::has(Session::get('front_lang_file').'.ADMIN_UNIT_PRICE')) ? trans(Session::get('front_lang_file').'.ADMIN_UNIT_PRICE') : trans($FRONT_LANGUAGE.'.ADMIN_UNIT_PRICE')}}</th>
							  <th >{{(Lang::has(Session::get('front_lang_file').'.ADMIN_TAX')) ? trans(Session::get('front_lang_file').'.ADMIN_TAX') : trans($FRONT_LANGUAGE.'.ADMIN_TAX')}}</th>
                              <th>{{(Lang::has(Session::get('front_lang_file').'.ADMIN_SUBTOTAL')) ? trans(Session::get('front_lang_file').'.ADMIN_SUBTOTAL') : trans($FRONT_LANGUAGE.'.ADMIN_SUBTOTAL')}}</th>
                            </tr>
                          </thead>
                          <tbody>
                            @foreach($or_details as $Order_sub)
                              @php
            								$calc_sub_total = ($Order_sub->ord_quantity*$Order_sub->ord_unit_price)+$Order_sub->ord_tax_amt;
            								$sub_total +=$calc_sub_total;
            								$shipping_total =$Order_sub->ord_delivery_fee;
                                         // $grand_total +=($Order_sub->ord_sub_total)+($Order_sub->ord_tax_amt)+($Order_sub->ord_delivery_fee);
                              @endphp
                            <tr>
                              <td>{{ucfirst($Order_sub->st_name)}}</td>
                              <td>
                                {{ucfirst($Order_sub->it_name)}}
                                @if($Order_sub->ord_spl_req != '')
                                <br>{{$Order_sub->ord_spl_req}}
                                         
                                @endif
                              </td>
                              <td>
                              @if($Order_sub->ord_pre_order_date!=NULL)                   
                                 {{date('m/d/Y h:i A',strtotime($Order_sub->ord_pre_order_date))}} 
                              @else
                                -
                              @endif
                              </td>
                              <td>
                                @php $path = url('').'/public/images/noimage/'.$no_item; @endphp
                                @if($Order_sub->ord_type=='Product')   
                                 @php $filename = public_path('images/store/products/').$Order_sub->pro_image; @endphp
                                    @if($Order_sub->pro_image != '' && file_exists($filename))
                                      @php $path = url('').'/public/images/store/products/'.$Order_sub->pro_image; @endphp
                                    @endif
                                @else       
                                   @php $filename = public_path('images/restaurant/items/').$Order_sub->pro_image; @endphp
                                    @if($Order_sub->pro_image != '' && file_exists($filename))
                                      @php $path = url('').'/public/images/restaurant/items/'.$Order_sub->pro_image; @endphp
                                    @endif
                                 @endif
                                 <img src="{{$path}}" width="50px" height="50px">
                              </td>
              							  <td>{{$Order_sub->ord_quantity}}</td>
              							  <td>{{$Order_sub->ord_unit_price}} {{$Order_sub->ord_currency}}</td>
              							  <td>{{$Order_sub->ord_tax_amt}} {{$Order_sub->ord_currency}}</td>
              							  <td>{{number_format($calc_sub_total,2)}} {{$Order_sub->ord_currency}}</td>
                            </tr>
                            
                            @if($Order_sub->ord_had_choices=="Yes")
                            
                              @if(count($choices)>0)
                                <tr>
								<td colspan="6" align="right" class="table-includes">
								<h5>{{(Lang::has(Session::get('front_lang_file').'.ADMIN_INCLUDES')) ? trans(Session::get('front_lang_file').'.ADMIN_INCLUDES') : trans($FRONT_LANGUAGE.'.ADMIN_INCLUDES')}} : </h5>
								</td>
								<td colspan="2">
                                <table class="table">
                                @foreach($choices as $key=>$val)
                                  @php 
                                        $explode = explode('`',$key);
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
                      <div class="col-lg-8 col-md-6 col-xs-12 payment-method">
                       <a href="{{url('/')}}"><button class="btn btn-default"><i class="fa fa-arrow-left"></i> {{(Lang::has(Session::get('front_lang_file').'.FRONT_CN_SHOP')) ? trans(Session::get('front_lang_file').'.FRONT_CN_SHOP') : trans($FRONT_LANGUAGE.'.FRONT_CN_SHOP')}}</button>
                       </a>
                      </div>
                      <!-- /.col -->
                      <div class="col-lg-4 col-md-6 col-xs-12">
                        
                        <div class="table-responsive">
                          <table class="table">
                            <tbody>
                              <tr>
                                <th style="width:50%; font-weight:500;">{{(Lang::has(Session::get('front_lang_file').'.ADMIN_SUBTOTAL')) ? trans(Session::get('front_lang_file').'.ADMIN_SUBTOTAL') : trans($FRONT_LANGUAGE.'.ADMIN_SUBTOTAL')}} :</th>
                                <td align="right" >{{number_format($sub_total,2)}} {{$Order_sub->ord_currency}}</td>
                              </tr>
                              @if($Order_sub->ord_self_pickup == 0)
                              <tr>
                                <th style="width:50%; font-weight:500;">{{(Lang::has(Session::get('front_lang_file').'.ADMIN_DELIVERY_FEE')) ? trans(Session::get('front_lang_file').'.ADMIN_DELIVERY_FEE') : trans($FRONT_LANGUAGE.'.ADMIN_DELIVERY_FEE')}} :</th>
                                <td align="right">{{number_format($shipping_total,2)}} {{$Order_sub->ord_currency}}</td>
                              </tr>
                              @endif
                              <tr>
                                <th style="width:50%; font-weight:500;">{{(Lang::has(Session::get('front_lang_file').'.ADMIN_UESD_WALLET')) ? trans(Session::get('front_lang_file').'.ADMIN_UESD_WALLET') : trans($FRONT_LANGUAGE.'.ADMIN_UESD_WALLET')}} :</th>
                                <td align="right" >{{number_format($Order_sub->ord_wallet,2)}} {{$Order_sub->ord_currency}}</td>
                              </tr>
                              <tr>
                                <th style="width:50%; font-weight:700;">{{(Lang::has(Session::get('front_lang_file').'.ADMIN_TOTAL')) ? trans(Session::get('front_lang_file').'.ADMIN_TOTAL') : trans($FRONT_LANGUAGE.'.ADMIN_TOTAL')}} :</th>
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