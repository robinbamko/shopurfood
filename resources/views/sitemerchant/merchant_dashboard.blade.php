@extends('sitemerchant.layouts.default')
@section('PageTitle')
    @if(isset($pagetitle))
        {{$pagetitle}}
    @endif
@stop
@section('content')

    <script src="{{url('')}}/public/admin/assets/vendor/jquery.easy-pie-chart/jquery.easypiechart.min.js"></script>
    <script src="{{url('')}}/public/admin/assets/vendor/chartist/js/chartist.min.js"></script>
    <!-- MAIN -->
    <div class="main">
        <style>
            .switch {
                position: relative;
                display: inline-block;
                width: 60px;
                height: 34px;
            }

            .switch input {
                opacity: 0;
                width: 0;
                height: 0;
            }

            .slider {
                position: absolute;
                cursor: pointer;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background-color: #ccc;
                -webkit-transition: .4s;
                transition: .4s;
            }

            .slider:before {
                position: absolute;
                content: "";
                height: 26px;
                width: 26px;
                left: 4px;
                bottom: 4px;
                background-color: white;
                -webkit-transition: .4s;
                transition: .4s;
            }

            input:checked + .slider {
                background-color: #373756;
            }

            input:focus + .slider {
                box-shadow: 0 0 1px #373756;
            }

            input:checked + .slider:before {
                -webkit-transform: translateX(26px);
                -ms-transform: translateX(26px);
                transform: translateX(26px);
            }

            /* Rounded sliders */
            .slider.round {
                border-radius: 34px;
            }

            .slider.round:before {
                border-radius: 50%;
            }
        </style>
        <style>
            body #chartdiv {
                width:100%;
                height: 236px;
            }
            /*.merchant-grp-height {
                min-height: 400px;
            }*/
        </style>
        <!-- MAIN CONTENT -->
        <div class="main-content">
            <div class="container-fluid">
                <!-- OVERVIEW -->
				<div class="panel-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="metric">
                                <span class="icon"><i class="fa fa-list"></i></span>
                                <p>
											<span class="number">
                        @if(Session::get('mer_business_type') == 1)
                                                    {!! (Lang::has(Session::get('mer_lang_file').'.ADMIN_TOTAL_PDTS')) ? trans(Session::get('mer_lang_file').'.ADMIN_TOTAL_PDTS') : trans($MER_OUR_LANGUAGE.'.ADMIN_TOTAL_PDTS') !!}
                                                @elseif(Session::get('mer_business_type') == 2)
                                                    {!! (Lang::has(Session::get('mer_lang_file').'.MER_ITEMS')) ? trans(Session::get('mer_lang_file').'.MER_ITEMS') : trans($MER_OUR_LANGUAGE.'.MER_ITEMS') !!}
                                                @endif
                      </span>
                                    <span class="title">{{$item_count}}</span>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="metric">
                                <span class="icon"><i class="fa fa-shopping-bag"></i></span>
                                <p>
                                    <span class="number">{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_ORDS')) ? trans(Session::get('mer_lang_file').'.ADMIN_ORDS') : trans($MER_OUR_LANGUAGE.'.ADMIN_ORDS')}}</span>
                                    <span class="title">{{$order_count}}</span>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="metric">
                                <span class="icon"><i class="fa fa-truck"></i></span>
                                <p>
                                    <span class="number">{{(Lang::has(Session::get('mer_lang_file').'.ADMIN_DELI_ORDERS')) ? trans(Session::get('mer_lang_file').'.ADMIN_DELI_ORDERS') : trans($MER_OUR_LANGUAGE.'.ADMIN_DELI_ORDERS')}}</span>
                                    <span class="title">{{$delivery_count}}</span>
                                </p>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="panel-body">
                    <div class="row">

                        <div class="panel-body">
                            <div class="x_panel">
                                <div class="x_title ord-det">
                                    <h2>{!! (Lang::has(Session::get('mer_lang_file').'.ADMIN_ORDER_DETAILS')) ? trans(Session::get('mer_lang_file').'.ADMIN_ORDER_DETAILS') : trans($MER_OUR_LANGUAGE.'.ADMIN_ORDER_DETAILS') !!} </h2>

                                    <div class="clearfix"></div>
                                </div>
                                <div class="x_content">
                                    <div id="chartdiv"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- END OVERVIEW -->
                <div class="row">
                    <div class="col-md-6">
                        <!-- RECENT PURCHASES -->
                        <div class="panel merchant-grp-height">
                            <div class="panel-body">
                                <div class="x_panel">
                                    <div class="x_title">
                                        <h2>
                                            @if(Session::get('mer_business_type') == 1)
                                                {!! (Lang::has(Session::get('mer_lang_file').'.ADMIN_TOTAL_PDTS')) ? trans(Session::get('mer_lang_file').'.ADMIN_TOTAL_PDTS') : trans($MER_OUR_LANGUAGE.'.ADMIN_TOTAL_PDTS') !!}
                                            @elseif(Session::get('mer_business_type') == 2)
                                                {!! (Lang::has(Session::get('mer_lang_file').'.MER_ITEMS')) ? trans(Session::get('mer_lang_file').'.MER_ITEMS') : trans($MER_OUR_LANGUAGE.'.MER_ITEMS') !!}
                                            @endif
                                        </h2>
                                        <ul class="nav navbar-right panel_toolbox">

                                        </ul>
                                        <div class="clearfix"></div>
                                    </div>
                                    <div class="x_content">

                                        <?php if( (($item_active)==0)  && (($item_deactive)==0)  )
                                        {
                                            if(Session::get('mer_business_type') == '2')
                                            {
                                                echo  (Lang::has(Session::get('mer_lang_file').'.MER_ITEMS')) ? trans(Session::get('mer_lang_file').'.MER_ITEMS') : trans($MER_OUR_LANGUAGE.'.MER_ITEMS') ;
                                            }
                                            else
                                            {
                                                echo  (Lang::has(Session::get('mer_lang_file').'.ADMIN_TOTAL_PDTS')) ? trans(Session::get('mer_lang_file').'.ADMIN_TOTAL_PDTS') : trans($MER_OUR_LANGUAGE.'.ADMIN_TOTAL_PDTS') ;
                                            }
                                        }
                                        else { ?>
                                        <canvas id="canvasDoughnut2"></canvas>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- END RECENT PURCHASES -->
                    </div>
                    <div class="col-md-6">
                        <!-- MULTI CHARTS -->
                        <div class="panel merchant-grp-height">
                            <div class="panel-body">
                                <div class="x_panel">
                                    <div class="x_title ord-det">
                                        <h2>{!! (Lang::has(Session::get('mer_lang_file').'.ADMIN_ORDS')) ? trans(Session::get('mer_lang_file').'.ADMIN_ORDS') : trans($MER_OUR_LANGUAGE.'.ADMIN_ORDS') !!} <label style="display: none" id="titlechangecustomer">-{!! (Lang::has(Session::get('mer_lang_file').'.ADMIN_LATEST')) ? trans(Session::get('mer_lang_file').'.ADMIN_LATEST') : trans($MER_OUR_LANGUAGE.'.ADMIN_LATEST') !!}</label> </h2>
                                       <div class="clearfix"></div>
                                    </div>
                                    <div class="x_content">
                                        <div id="last5" class="panel-body no-padding">
                                            <table class="table table-striped">
                                                <thead>
                                                <tr>
                                                    <th>S.No</th>
                                                    <th>{!! (Lang::has(Session::get('admin_lang_file').'.ADMIN_CUSTOMER_NAME')) ? trans(Session::get('admin_lang_file').'.ADMIN_CUSTOMER_NAME') : trans($MER_OUR_LANGUAGE.'.ADMIN_CUSTOMER_NAME') !!}</th>
                                                    <th>{!! (Lang::has(Session::get('admin_lang_file').'.ADMIN_ITEM_QTY')) ? trans(Session::get('admin_lang_file').'.ADMIN_ITEM_QTY') : trans($MER_OUR_LANGUAGE.'.ADMIN_ITEM_QTY') !!}</th>
                                                    <th>{!! (Lang::has(Session::get('admin_lang_file').'.ADMIN_TOTAL_AMT')) ? trans(Session::get('admin_lang_file').'.ADMIN_TOTAL_AMT') : trans($MER_OUR_LANGUAGE.'.ADMIN_TOTAL_AMT') !!}</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @foreach($lastorders as  $key => $order)
                                                    <tr>
                                                        <td>{{++$key}}</td>
                                                        <td>{{$order->ord_shipping_cus_name}}</td>
                                                        <td>{{$order->ord_quantity}}</td>
                                                        <td>{{$order->ord_grant_total}}</td>
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        <div style="display:none;" id="last24" class="panel-body no-padding">
                                            <table class="table table-striped">
                                                <thead>
                                                <tr>
                                                    <th>S.No</th>
                                                    <th>{!! (Lang::has(Session::get('mer_lang_file').'.ADMIN_CUSTOMER_NAME')) ? trans(Session::get('mer_lang_file').'.ADMIN_CUSTOMER_NAME') : trans($MER_OUR_LANGUAGE.'.ADMIN_CUSTOMER_NAME') !!}</th>
                                                    <th>{!! (Lang::has(Session::get('mer_lang_file').'.ADMIN_ITEM_QTY')) ? trans(Session::get('mer_lang_file').'.ADMIN_ITEM_QTY') : trans($MER_OUR_LANGUAGE.'.ADMIN_ITEM_QTY') !!}</th>
                                                    <th>{!! (Lang::has(Session::get('mer_lang_file').'.ADMIN_TOTAL_AMT')) ? trans(Session::get('mer_lang_file').'.ADMIN_TOTAL_AMT') : trans($MER_OUR_LANGUAGE.'.ADMIN_TOTAL_AMT') !!}</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @foreach($recentorders as  $key => $order)
                                                    <tr>
                                                        <td>{{++$key}}</td>
                                                        <td>{{$order->ord_shipping_cus_name}}</td>
                                                        <td>{{$order->ord_quantity}}</td>
                                                        <td>{{$order->ord_grant_total}}</td>
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="panel-footer">
                                            <div class="row">
                                                <div class="col-md-6"><label class="switch">
                                                        <input id="customerchange" type="checkbox" checked>
                                                        <span class="slider round"></span>
                                                    </label>
                                                </div>
                                                <div class="col-md-6 text-right"><a href="{{url('mer-manage-orders')}}" class="btn btn-primary">{!! (Lang::has(Session::get('admin_lang_file').'.ADMIN_VIEW_ALL')) ? trans(Session::get('admin_lang_file').'.ADMIN_VIEW_ALL') : trans($MER_OUR_LANGUAGE.'.ADMIN_VIEW_ALL') !!}</a></div>
                                            </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- END MULTI CHARTS -->
                    </div>
                </div>
                

            </div>
        </div>
        <!-- END MAIN CONTENT -->
    </div>
    <!-- END MAIN -->
@section('script')
    <script src="{{url('')}}/public/js/canvasjs.min.js"></script>
    <script src="{{url('')}}/public/js/Chart.min.js"></script>
    <script src="{{url('')}}/public/js/amcharts.js" type="text/javascript"></script>
    <script src="{{url('')}}/public/js/serial.js" type="text/javascript"></script>
    <script>
        var ctx = document.getElementById("canvasDoughnut2");
        var data = {
            labels: [
                '<?php
                    if(Session::get('mer_business_type') == '1') { echo (Lang::has(Session::get('mer_lang_file').'.ADMIN_ACTIVE_PDT')) ? trans(Session::get('mer_lang_file').'.ADMIN_ACTIVE_PDT') : trans($MER_OUR_LANGUAGE.'.ADMIN_ACTIVE_PDT') ; }
                    else
                    {
                        echo (Lang::has(Session::get('mer_lang_file').'.MER_AC_ITEMS')) ? trans(Session::get('mer_lang_file').'.MER_AC_ITEMS') : trans($MER_OUR_LANGUAGE.'.MER_AC_ITEMS') ;
                    }
                    ?>',
                '<?php
                    if(Session::get('mer_business_type') == '1') {
                        echo (Lang::has(Session::get('mer_lang_file').'.ADMIN_DEACTIVE_PDT')) ? trans(Session::get('mer_lang_file').'.ADMIN_DEACTIVE_PDT') : trans($MER_OUR_LANGUAGE.'.ADMIN_DEACTIVE_PDT') ;
                    }
                    else
                    {
                        echo (Lang::has(Session::get('mer_lang_file').'.MER_DEAC_ITEMS')) ? trans(Session::get('mer_lang_file').'.MER_DEAC_ITEMS') : trans($MER_OUR_LANGUAGE.'.MER_DEAC_ITEMS') ;
                    }
                    ?>'
            ],
            datasets: [{
                data: [<?php echo ($item_active); ?>, <?php echo ($item_deactive); ?>],

                backgroundColor: [
                    "#455C73",
                    "#BDC3C7",
                    "#26B99A",
                    "#3498DB"
                ],
                hoverBackgroundColor: [
                    "#34495E",
                    "#CFD4D8",
                    "#36CAAB",
                    "#49A9EA"

                ]

            }]
        };
            <?php if($item_active==0 && $item_deactive==0) { } else { ?>
        var canvasDoughnut2 = new Chart(ctx, {
                type: 'doughnut',
                tooltipFillColor: "rgba(51, 51, 51, 0.55)",
                data: data
            });
            <?php } ?>

        var chart;
        var graph;
        var categoryAxis;

        <?php
        $month = array('January'=>'','February'=>'','March'=>'','April'=>'','May'=>'','June'=>'','July'=>'','August'=>'','September'=>'','October'=>'','November'=>'','December'=>'');
        for($i=01;$i<=date('m');$i++){
            //$order_count = DB::table('gr_order')->whereMonth('ord_date','=',$i)->whereYear('ord_date','=',date('Y'))->where('ord_merchant_id' ,'=',Session::get('merchantid'))->count();
            $order_count = DB::table('gr_order')->select(DB::raw('COUNT(DISTINCT ord_transaction_id) As TotalOrder'))->whereMonth('ord_date',$i)->whereYear('ord_date',date('Y'))->where('ord_merchant_id' ,'=',Session::get('merchantid'))->first()->TotalOrder;
            if($i == 1){
                $k = 'January';
            }
            elseif($i == 2){
                $k = 'February';
            }
            elseif($i == 3){
                $k = 'March';
            }
            elseif($i == 4){
                $k = 'April';
            }
            elseif($i == 5){
                $k = 'May';
            }
            elseif($i == 6){
                $k = 'June';
            }
            elseif($i == 7){
                $k = 'July';
            }
            elseif($i == 8){
                $k = 'August';
            }
            elseif($i == 9){
                $k = 'September';
            }
            elseif($i == 10){
                $k = 'October';
            }
            elseif($i == 11){
                $k = 'November';
            }
            elseif($i == 12){
                $k = 'December';
            }
            $month[$k] = $order_count;
        }

        ?>
    </script>
    <script src="{{url('')}}/public/js/vendor/chartist/js/chartist.min.js" type="text/javascript"></script>
    <script>
        var data = {
            labels: ["{!! (Lang::has(Session::get('mer_lang_file').'.ADMIN_JANUARY')) ? trans(Session::get('mer_lang_file').'.ADMIN_JANUARY') : trans($MER_OUR_LANGUAGE.'.ADMIN_JANUARY') !!}",
                "{!! (Lang::has(Session::get('mer_lang_file').'.ADMIN_FEBRUARY')) ? trans(Session::get('mer_lang_file').'.ADMIN_FEBRUARY') : trans($MER_OUR_LANGUAGE.'.ADMIN_FEBRUARY') !!}",
                "{!! (Lang::has(Session::get('mer_lang_file').'.ADMIN_MARCH')) ? trans(Session::get('mer_lang_file').'.ADMIN_MARCH') : trans($MER_OUR_LANGUAGE.'.ADMIN_MARCH') !!}",
                "{!! (Lang::has(Session::get('mer_lang_file').'.ADMIN_APRIL')) ? trans(Session::get('mer_lang_file').'.ADMIN_APRIL') : trans($MER_OUR_LANGUAGE.'.ADMIN_APRIL') !!}",
                "{!! (Lang::has(Session::get('mer_lang_file').'.ADMIN_MAY')) ? trans(Session::get('mer_lang_file').'.ADMIN_MAY') : trans($MER_OUR_LANGUAGE.'.ADMIN_MAY') !!}",

                "{!! (Lang::has(Session::get('mer_lang_file').'.ADMIN_JUNE')) ? trans(Session::get('mer_lang_file').'.ADMIN_JUNE') : trans($MER_OUR_LANGUAGE.'.ADMIN_JUNE') !!}",
                "{!! (Lang::has(Session::get('mer_lang_file').'.ADMIN_JULY')) ? trans(Session::get('mer_lang_file').'.ADMIN_JULY') : trans($MER_OUR_LANGUAGE.'.ADMIN_JULY') !!}",
                "{!! (Lang::has(Session::get('mer_lang_file').'.ADMIN_AUGUST')) ? trans(Session::get('mer_lang_file').'.ADMIN_AUGUST') : trans($MER_OUR_LANGUAGE.'.ADMIN_AUGUST') !!}",
                "{!! (Lang::has(Session::get('mer_lang_file').'.ADMIN_SEPTEMBER')) ? trans(Session::get('mer_lang_file').'.ADMIN_SEPTEMBER') : trans($MER_OUR_LANGUAGE.'.ADMIN_SEPTEMBER') !!}",
                "{!! (Lang::has(Session::get('mer_lang_file').'.ADMIN_OCTOBER')) ? trans(Session::get('mer_lang_file').'.ADMIN_OCTOBER') : trans($MER_OUR_LANGUAGE.'.ADMIN_OCTOBER') !!}",
                "{!! (Lang::has(Session::get('mer_lang_file').'.ADMIN_NOVEMBER')) ? trans(Session::get('mer_lang_file').'.ADMIN_NOVEMBER') : trans($MER_OUR_LANGUAGE.'.ADMIN_NOVEMBER') !!}",
                "{!! (Lang::has(Session::get('mer_lang_file').'.ADMIN_DECEMBER')) ? trans(Session::get('mer_lang_file').'.ADMIN_DECEMBER') : trans($MER_OUR_LANGUAGE.'.ADMIN_DECEMBER') !!}"
            ],
            series: [
                [Number(<?php echo $month['January'];?>),Number(<?php echo $month['February'];?>),Number(<?php echo $month['March'];?>),Number(<?php echo $month['April'];?>),Number(<?php echo $month['May'];?>),Number(<?php echo $month['June'];?>),Number(<?php echo $month['July'];?>),Number(<?php echo $month['August'];?>),Number(<?php echo $month['September'];?>),Number(<?php echo $month['October'];?>),Number(<?php echo $month['November'];?>),Number(<?php echo $month['December'];?>)],
            ]
        };
        //Area chart
        options = {
            height: "270px",
            showArea: true,
            showLine: true,
            showPoint: true,
			showLabel: true,
            axisX: {
                showGrid: true
            },
            lineSmooth: true,
        };
        new Chartist.Line('#chartdiv', data, options);
</script>
<script>
    $('#customerchange').change(function() {
        if($(this).is(":checked")) {
            $("#last5").show();
            $("#last24").hide();
            $('#titlechangecustomer').hide();
        }
        else{
            $("#last24").show();
            $("#last5").hide();
            $('#titlechangecustomer').show();
        }
    });
</script>

@endsection
@stop