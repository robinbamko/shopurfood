@extends('Admin.layouts.default')
@section('PageTitle')
    @if(isset($pagetitle))
        {{$pagetitle}}
    @endif
@stop
@section('content')

    <style>
        table.dataTable
        {
            margin-top: 20px !important;
        }
        .tooltip{overflow: visible;}
        @media only screen and (min-width:768px) and (max-width:1024px){
            input#check_all{margin-left: 19px;}
        }
        
        /*
        .overlay {
          position: fixed;
          top: 0;
          bottom: 0;
          left: 0;
          right: 0;
          background: rgba(0, 0, 0, 0.7);
          transition: opacity 500ms;
          visibility: hidden;
          opacity: 0;
        }
        .overlay:target {
          visibility: visible;
          opacity: 1;
        }

        .popup {
          margin: 70px auto;
          padding: 20px;
          background: #fff;
          border-radius: 5px;
          width: 30%;
          position: relative;
          transition: all 5s ease-in-out;
        }

        .popup h2 {
          margin-top: 0;
          color: #333;
          font-family: Tahoma, Arial, sans-serif;
        }
        .popup .close {
          position: absolute;
          top: 20px;
          right: 30px;
          transition: all 200ms;
          font-size: 30px;
          font-weight: bold;
          text-decoration: none;
          color: #333;
        }
        .popup .close:hover {
          color: #06D85F;
        }
        .popup .content {
          max-height: 30%;
          overflow: auto;
        }

        @media screen and (max-width: 700px){
          .box{
            width: 70%;
          }
          .popup{
            width: 70%;
          }
        }*/
    </style>

    <!-- MAIN -->
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
                                <div class="err-alrt-msg">
                                    <div class="alert alert-success alert-dismissible" id="successMsgRole" style="@if (Session::has('message')) display:block @else display:none @endif">
                                        <a href="#" class="close" data-dismiss="alert" aria-label="close"><i class="fa fa-times-circle"></i></a>
                                        <span id="successMsgInfo"></span>
                                        @if(Session::has('message')){{Session::get('message')}} @endif
                                    </div>
                                    @if ($errors->has('errors'))
                                        <div class="alert alert-danger alert-dismissible" role="alert" >
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true"><i class="fa fa-times-circle"></i></span></button>
                                            {{ $errors->first('errors') }}

                                        </div>
                                    @endif
                                    @if ($errors->has('upload_file'))
                                        <p class="error-block" style="color:red;">{{ $errors->first('upload_file') }}</p>
                                    @endif
                                    <div id="successMsgRole"></div>


                                    <div class="alert alert-danger alert-dismissible rec-select" role="alert" style="display:none">
                                        <button type="button" class="close" data-hide="alert" aria-label="Close"><span aria-hidden="true"><i class="fa fa-times-circle"></i></span></button>
                                        {{(Lang::has(Session::get('admin_lang_file').'.ADMIN_SELECT_ONE')) ? trans(Session::get('admin_lang_file').'.ADMIN_SELECT_ONE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SELECT_ONE')}}
                                    </div>
                                </div>


                                {{--Manage page starts--}}
                                <div class="panel-body" id="location_table" style="margin-top: 10px;">
                                    <div class="loading-image" style="display:none">
                                        <button type="button" class="btn btn-primary" disabled="disabled" ><i class="fa fa-spinner fa-spin"></i> Loading...</button>
                                    </div>


                                    <div class="top-button top-btn-full" style="position:relative;">
                                        {!! Form::button((Lang::has(Session::get('admin_lang_file').'.ADMIN_BLOCK')) ? trans(Session::get('admin_lang_file').'.ADMIN_BLOCK') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_BLOCK'),['id' => 'block_value','class' => 'btn btn-success'])!!}

                                        {!! Form::button((Lang::has(Session::get('admin_lang_file').'.ADMIN_UNBLOCK')) ? trans(Session::get('admin_lang_file').'.ADMIN_UNBLOCK') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_UNBLOCK'),['id' => 'unBlock_value','class' => 'btn btn-warning'])!!}

                                        {!! Form::button((Lang::has(Session::get('admin_lang_file').'.ADMIN_DELETE')) ? trans(Session::get('admin_lang_file').'.ADMIN_DELETE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_DELETE'),['id' => 'delete_value','class' => 'btn btn-danger'])!!}

                                        <a href="{{url('download_merchant_list/csv')}}">
                                            {!! Form::button((Lang::has(Session::get('admin_lang_file').'.ADMIN_DWNLD_EXCEL')) ? trans(Session::get('admin_lang_file').'.ADMIN_DWNLD_EXCEL') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_DWNLD_EXCEL'),['id' => 'delete_value','class' => 'btn btn-info'])!!}
                                        </a>
                                    </div>
                                    <table class="table table-striped table-bordered table-hover" id="dataTables-example" style="text-align:center">
                                        <thead>
                                        <tr>
                                            <td>&nbsp;</td>
                                            <td>&nbsp;</td>
                                            <td><input type="text" id="merName_search" class="form-control col-md-12" style="width:100%" placeholder="{{ __(Session::get('admin_lang_file').'.ADMIN_TXT_SEARCH') }}"/></td>
                                            <td>
												<input type="hidden" name="busType_search" id="busType_search" value="2">
                                                <?php /*<select id="busType_search" class="form-control" style="width:100%">
                                                    <option value="">@lang(Session::get('admin_lang_file').'.ADMIN_ALL')</option>
                                                    <option value="2">@lang(Session::get('admin_lang_file').'.ADMIN_RESTAURANT')</option>
                                                    <option value="1">@lang(Session::get('admin_lang_file').'.ADMIN_STORE')</option>
                                                </select>*/ ?>  
                                            </td>
                                            <td><input type="text" id="restName_search" class="form-control col-md-12" style="width:100%" placeholder="{{ __(Session::get('admin_lang_file').'.ADMIN_TXT_SEARCH') }}"/></td>
                                            <td><input type="text" id="email_search" class="form-control col-md-12" style="width:100%" placeholder="Enter text to search"/></td>
                                            <td>&nbsp;</td>
                                            <td>
                                                @php
                                                    $statusArray[''] = (Lang::has(Session::get('admin_lang_file').'.ADMIN_SELECT')) ? trans(Session::get('admin_lang_file').'.ADMIN_SELECT') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SELECT');
                                                    $statusArray['1']=(Lang::has(Session::get('admin_lang_file').'.ADMIN_PUBLISH')) ? trans(Session::get('admin_lang_file').'.ADMIN_PUBLISH') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PUBLISH');
                                                    $statusArray['0']=(Lang::has(Session::get('admin_lang_file').'.ADMIN_UNPUB')) ? trans(Session::get('admin_lang_file').'.ADMIN_UNPUB') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_UNPUB');
                                                @endphp
                                                {{ Form::select('status_search',$statusArray,'',['class' => 'form-control' , 'style' => 'width:100%','id'=>'status_search'] ) }}
                                            </td>
                                            <td>&nbsp;</td>
                                            <td>
                                                @php
                                                    $addedByArray[''] = (Lang::has(Session::get('admin_lang_file').'.ADMIN_SELECT')) ? trans(Session::get('admin_lang_file').'.ADMIN_SELECT') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SELECT');
                                                    $addedByArray['0']=(Lang::has(Session::get('admin_lang_file').'.ADMIN_ADMIN')) ? trans(Session::get('admin_lang_file').'.ADMIN_ADMIN') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ADMIN');
                                                    $addedByArray['1']=(Lang::has(Session::get('admin_lang_file').'.ADMIN_MERCHANT')) ? trans(Session::get('admin_lang_file').'.ADMIN_MERCHANT') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_MERCHANT');
                                                @endphp
                                                {{ Form::select('addedBy_search',$addedByArray,'',['class' => 'form-control' , 'style' => 'width:100%','id'=>'addedBy_search'] ) }}
                                            </td>
                                        </tr>
                                        <tr style="z-index: 0;">
                                            <th  style="text-align:center">{!! Form::checkbox('chkone[]','','',['id' => 'check_all','onchange' =>'checkAll(this)'])!!}</th>
                                            <th style="text-align:center" class="sorting_no_need">
                                                {{(Lang::has(Session::get('admin_lang_file').'.ADMIN_SNO')) ? trans(Session::get('admin_lang_file').'.ADMIN_SNO') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SNO')}}
                                            </th>
                                            <th style="text-align:center">
                                                {{(Lang::has(Session::get('admin_lang_file').'.ADMIN_NAME')) ? trans(Session::get('admin_lang_file').'.ADMIN_NAME') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_NAME')}}
                                            </th>
                                            <th style="text-align:center">
                                                {{(Lang::has(Session::get('admin_lang_file').'.ADMIN_BUSINESS_TYPE')) ? trans(Session::get('admin_lang_file').'.ADMIN_BUSINESS_TYPE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_BUSINESS_TYPE')}}
                                            </th>
                                            <th>
                                                {{(Lang::has(Session::get('admin_lang_file').'.ADMIN_RESTSTORE_NAME')) ? trans(Session::get('admin_lang_file').'.ADMIN_RESTSTORE_NAME') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_RESTSTORE_NAME')}}
                                            </th>
                                            <th style="text-align:center">
                                                {{(Lang::has(Session::get('admin_lang_file').'.ADMIN_EMAIL')) ? trans(Session::get('admin_lang_file').'.ADMIN_EMAIL') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_EMAIL')}}
                                            </th>
                                            <th style="text-align:center">
                                                {{(Lang::has(Session::get('admin_lang_file').'.ADMIN_EDIT')) ? trans(Session::get('admin_lang_file').'.ADMIN_EDIT') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_EDIT')}}
                                            </th>
                                            <th style="text-align:center">
                                                {{(Lang::has(Session::get('admin_lang_file').'.ADMIN_STATUS')) ? trans(Session::get('admin_lang_file').'.ADMIN_STATUS') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_STATUS')}}
                                            </th>
                                            <th style="text-align:center">
                                                {{(Lang::has(Session::get('admin_lang_file').'.ADMIN_DELETE')) ? trans(Session::get('admin_lang_file').'.ADMIN_DELETE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_DELETE')}}
                                            </th>
                                            <th style="text-align:center">
                                                {{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ADDED_BY')) ? trans(Session::get('admin_lang_file').'.ADMIN_ADDED_BY') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ADDED_BY')}}
                                            </th>
                                        </tr>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                    </table>
                                    <div class="loading-image" style="display:none">
                                        <button type="button" class="btn btn-primary" disabled="disabled" ><i class="fa fa-spinner fa-spin"></i> Loading...</button>
                                    </div>
                                </div>
                                {{--Manage page ends--}}


                            <!--------model for block confirmation start -------------->
                                <div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true" id="mi-modal">
                                    <div class="modal-dialog modal-sm">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                <h4 class="modal-title" id="myModalLabel">are you sure want to Block</h4>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-default" id="modal-btn-si">Yes</button>
                                                <button type="button" class="btn btn-primary" id="modal-btn-no">No</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" name="mer_blkId" id="mer_blkId" value="">
                                <!--------model for block confirmation end -------------->

                                <!--------model for unblock confirmation start -------------->
                                <div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true" id="mi-modalDlete">
                                    <div class="modal-dialog modal-sm">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                <h4 class="modal-title" id="myModalLabel">are you sure want to UnBlock</h4>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-default" id="modal-btn-yesDel">Yes</button>
                                                <button type="button" class="btn btn-primary" id="modal-btn-noDel">No</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" name="mer_unblockId" id="mer_unblockId" value=""><!--------model for unblock confirmation end -------------->
                            </div>
                            @if(count($all_details) > 0)
                                {!! $all_details->render() !!}
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.panel-body -->
        </div>
        <!-- END MAIN CONTENT -->
    </div>

@section('script')
    <script>
        $('#block_value').click(function(){
            $(".rec-select").css({"display" : "none"});
            var val = [];
            val[0]='';
            $('input[name="chk[]"]:checked').each(function(i){
                var j=i+1;
                //alert(j);
                val[j] = $(this).val();
            });

            if(val=='')
            {
                $(".rec-select").css({"display" : "block"});
                return;
            }
            blockUnblock(val,0);
        });

        /** multiple unblock **/
        $('#unBlock_value').click(function(){
            $(".rec-select").css({"display" : "none"});
            var val = [];
            val[0]='';
            $('input[name="chk[]"]:checked').each(function(i){
                var j=i+1;
                //alert(j);
                val[j] = $(this).val();
            });

            if(val=='')
            {
                $(".rec-select").css({"display" : "block"});
                return;
            }
            blockUnblock(val,1);
        });
        /** multiple delete **/
        $('#delete_value').click(function(){
            $(".rec-select").css({"display" : "none"});
            var val = [];
            val[0]='';
            $('input[name="chk[]"]:checked').each(function(i){
                var j=i+1;
                //alert(j);
                val[j] = $(this).val();
            });

            if(val=='')
            {
                $(".rec-select").css({"display" : "block"});
                return;
            }
            blockUnblock(val,2);
        });
        function blockUnblock(gotVal,gotStatus)
        {
            $.ajax({
                type:'get',
                url :"<?php echo url("multi_merchant_block"); ?>",
                beforeSend: function() {
                    $(".loading-image").show();
                },
                data:{'val':gotVal,'status':gotStatus},

                success:function(response){
                    $(".loading-image").hide();
                    $('#successMsgRole').show();
                    $('#successMsgRole').focus();
                    $('#successMsgRole').html('<div class="alert alert-success alert-dismissible"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true"><i class="fa fa-times-circle"></i></span></button>'+response+'</div>').show();

                    if(gotStatus==0)
                    {
                        for(var i=0;i<gotVal.length;i++)
                        {
                           // $('#statusLink_'+gotVal[i]).attr("href", "javascript:individual_change_status('"+gotVal[i]+"',1)").html('<i class="fa fa-ban tooltip-demo" aria-hidden="true" title="Click to Unblock" ></i>');
                            //$('.tooltip-demo').tooltip({placement:'left'})
							table.draw(false);
                        }
                    }
                    else if(gotStatus==1)
                    {
                        for(var i=0;i<gotVal.length;i++)
                        {
                           // $('#statusLink_'+gotVal[i]).attr("href", "javascript:individual_change_status('"+gotVal[i]+"',0)").html('<i class="fa fa-check tooltip-demo" aria-hidden="true" title="Click to Block"></i>');
                           // $('.tooltip-demo').tooltip({placement:'left'})
						   table.draw(false);
                        }
                    }
                    else
                    {
                        table.row( this ).remove().draw( false );
                    }
                    $('.checkboxclass').prop('checked', false);
                }
            });
        }
        function individual_change_status(gotVal,gotStatus)
        {
            var val = [];
            val[0]=gotVal;
            blockUnblock(val,gotStatus);
        }
        function checkAll(ele) {
            var checkboxes = document.getElementsByName('chk[]');
            if (ele.checked) {
                for (var i = 0; i < checkboxes.length; i++) {
                    if (checkboxes[i].type == 'checkbox') {
                        checkboxes[i].checked = true;
                    }
                }
            } else {
                for (var i = 0; i < checkboxes.length; i++) {
                    console.log(i)
                    if (checkboxes[i].type == 'checkbox') {
                        checkboxes[i].checked = false;
                    }
                }
            }
        }

        /*------block all status start -----------*/
        function change_all_statusblk(id){
            // alert(id);
            $('#mer_blkId').val(id);
            $("#mi-modal").modal('show');
        }

        var modalConfirm = function(callback){
            $("#modal-btn-si").on("click", function(){
                callback(true);
                $("#mi-modal").modal('hide');
            });

            $("#modal-btn-no").on("click", function(){
                callback(false);
                $("#mi-modal").modal('hide');
            });
        };

        modalConfirm(function(confirm){
            if(confirm){
                var mer_id = $('#mer_blkId').val();
                // alert(mer_id);

                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{ url('ajax_change_mer_all_status') }}",
                    type: 'POST',
                    data: {'mer_id' : mer_id,'status':'0'},
                    success:function(response) {
                        if(response == 1)  {
                            alert("Blocked Succesfully");
                            window.location.reload();
                        }else{
                            alert("Blocked UnSuccess");
                            window.location.reload();
                        }

                    }
                });
            }else{
                // alert('not confirm');
            }
        });
        /*-----block all status end ---------*/


        /*------unblock all staus  start ------------*/
        function change_all_unblock(id){
            $('#mer_unblockId').val(id);
            $("#mi-modalDlete").modal('show');
        }

        var modalConfirmDel = function(callback){
            $("#modal-btn-yesDel").on("click", function(){
                callback(true);
                $("#mi-modalDlete").modal('hide');
            });

            $("#modal-btn-noDel").on("click", function(){
                callback(false);
                $("#mi-modalDlete").modal('hide');
            });
        };

        modalConfirmDel(function(confirm){
            if(confirm){
                var mer_id = $('#mer_unblockId').val();
                // alert(mer_id);

                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{ url('ajax_change_mer_all_status') }}",
                    type: 'POST',
                    data: {'mer_id' : mer_id,'status':'1'},
                    success:function(response) {
                        if(response == 1)  {
                            alert("UnBlocked Succesfully");
                            window.location.reload();
                        }else{
                            alert("UnBlocked UnSuccess");
                            window.location.reload();
                        }

                    }
                });
            }else{
// alert('not confirm');
            }
        });

        /*------unblock all staus  end ------------*/

    </script>
    <script>
        var table='';
        $(document).ready(function () {
            /*$('#dataTables-example').dataTable({
            "bPaginate": false,
              //"scrollX": true,
            "bLengthChange": false,
            "bFilter": true,
            "bInfo": false,
            "bAutoWidth": true });*/
            table = $('#dataTables-example').DataTable({
                "processing": true,
                "serverSide": true,
                "responsive": true,
                "bLengthChange": true,
                "bAutoWidth": false,
                "searching": false,
                "ajax":{
                    "url": "{{ url('ajax-merchant-list') }}",
                    "dataType": "json",
                    "type": "POST",
                    "beforeSend":function(){ $('#check_all').prop('checked',false); },
                    "data":{ _token: "{{csrf_token()}}",'merName_search': function(){return $("#merName_search").val(); },busType_search:function(){return $("#busType_search").val(); },email_search:function(){return $("#email_search").val(); },status_search:function(){return $("#status_search").val(); },addedBy_search:function(){return $("#addedBy_search").val(); },restName_search:function(){return $("#restName_search").val(); }}
                },
                "columnDefs": [ {
                    "targets": [0,1,6,8],
                    "orderable": false
                } ],
                "order": [ 1, 'desc' ],
                "columns": [
                    { "data": "checkBox",sWidth: '8%' },
                    { "data": "SNo", sWidth: '8%' },
                    { "data": "merName", sWidth: '16%' },
                    { "data": "busType", sWidth: '10%' },
                    { "data": "storeName", sWidth: '10%' },
                    { "data": "merEmail", sWidth: '15%' },
                    { "data": "Edit", sWidth: '9%' },
                    { "data": "Status", sWidth: '9%'},
                    { "data": "delete", sWidth: '9%'},
                    { "data": "addedBy", sWidth: '10%'},
                ],
                "fnDrawCallback": function (oSettings) {
                    $('.tooltip-demo').tooltip({placement:'top'});
                    //$('[data-toggle="popover"]').popover({placement:'left'});
                    //$('[rel="tooltip"]').tooltip();
                }
            });
            $('#merName_search, #email_search, #agentPhone_search, #restName_search').keyup( function() {
                table.draw();
                //table.search( this.value ).draw();
            });
            $('#busType_search, #status_search, #addedBy_search').change(function(){
                table.draw();
            });
        });
    </script>
    <script>
        $("#validate_form").validate({
            rules: {
                cate_name: "required"
            },
            messages: {
                cate_name: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ENTR_CATE_NAME')) ? trans(Session::get('admin_lang_file').'.ADMIN_ENTR_CATE_NAME') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ENTR_CATE_NAME')}}"
            }
        });

    </script>
@endsection
@stop