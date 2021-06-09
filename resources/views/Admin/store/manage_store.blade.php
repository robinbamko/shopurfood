@extends('Admin.layouts.default')
@section('PageTitle')
{{$pagetitle}}
@endsection
@section('content')

<style>
    table.dataTable
    {
    margin-top: 20px !important;
    }
</style>

<!-- MAIN -->
<div class="main">
    <!-- MAIN CONTENT -->
    <div class="main-content">
        <h1 class="page-header">{{$pagetitle}}</h1>
        <div class="container-fluid add-country">
            <div class="row">
                <div class="container right-container">
                    <div class="r-btn">
                    </div>
                    <div class="col-md-12">
                        <div class="location panel">
                            <div class="panel-heading p__title">
                                {{$pagetitle}}
                                
                            </div>
                            
                            
                            {{-- Display  message--}}
                            <div class="err-alrt-msg">
                            @if(Session::has('message'))
                            <div class="alert alert-success alert-dismissible">
                                <a href="#" class="close" data-dismiss="alert" aria-label="close"><i class="fa fa-times-circle"></i></a>
                                {{Session::get('message')}}    
                            </div>
                            @endif
                            <div class="alert alert-danger alert-dismissible rec-select" role="alert" style="display:none">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true"><i class="fa fa-times-circle"></i></span></button>
                                {{(Lang::has(Session::get('admin_lang_file').'.ADMIN_SELECT_ONE')) ? trans(Session::get('admin_lang_file').'.ADMIN_SELECT_ONE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SELECT_ONE')}}
                            </div>
                            </div>
                            
                            {{-- Manage list starts--}}
                            <div class="panel-body" id="location_table">
                                {{-- 
                                <div class="panel-heading p__title">
                                    Manage List
                                </div>
                                --}}
                                <div id="loading-image" style="display:none">
                                    <button type="button" class="btn btn-primary" disabled="disabled" ><i class="fa fa-spinner fa-spin"></i> Loading...</button>
                                </div>
                                
                                <div class="top-button" style="position:relative;">
                                    {!! Form::button((Lang::has(Session::get('admin_lang_file').'.ADMIN_BLOCK')) ? trans(Session::get('admin_lang_file').'.ADMIN_BLOCK') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_BLOCK'),['id' => 'block_value','class' => 'btn btn-success'])!!}
                                    
                                    {!! Form::button((Lang::has(Session::get('admin_lang_file').'.ADMIN_UNBLOCK')) ? trans(Session::get('admin_lang_file').'.ADMIN_UNBLOCK') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_UNBLOCK'),['id' => 'unBlock_value','class' => 'btn btn-warning'])!!}
                                    
                                    {!! Form::button((Lang::has(Session::get('admin_lang_file').'.ADMIN_DELETE')) ? trans(Session::get('admin_lang_file').'.ADMIN_DELETE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_DELETE'),['id' => 'delete_value','class' => 'btn btn-danger'])!!}
                                </div>
                                <table class="table table-striped table-bordered table-hover" id="dataTables-example" style="text-align:center">
                                    <thead>
                                        <tr>
                                            <td>&nbsp;</td>
                                            <td>&nbsp;</td>

                                            <td><input type="text" id="storeName_search" class="form-control col-md-12" style="width:100%" placeholder="{{ __(Session::get('admin_lang_file').'.ADMIN_TXT_SEARCH') }}"/></td>

                                            <td><input type="text" id="merName_search" class="form-control col-md-12" style="width:100%" placeholder="{{ __(Session::get('admin_lang_file').'.ADMIN_TXT_SEARCH') }}"/></td>

                                            <td><input type="text" id="cat_search" class="form-control col-md-12" style="width:100%" placeholder="{{ __(Session::get('admin_lang_file').'.ADMIN_TXT_SEARCH') }}"/></td>

                                            <td>
                                                @php
                                                    $addedByArray[''] = (Lang::has(Session::get('admin_lang_file').'.ADMIN_SELECT')) ? trans(Session::get('admin_lang_file').'.ADMIN_SELECT') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SELECT');
                                                    $addedByArray['admin']=(Lang::has(Session::get('admin_lang_file').'.ADMIN_ADMIN')) ? trans(Session::get('admin_lang_file').'.ADMIN_ADMIN') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ADMIN');
                                                    $addedByArray['merchant']=(Lang::has(Session::get('admin_lang_file').'.ADMIN_MERCHANT')) ? trans(Session::get('admin_lang_file').'.ADMIN_MERCHANT') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_MERCHANT');
                                                @endphp
                                                {{ Form::select('addedBy_search',$addedByArray,'',['class' => 'form-control' , 'style' => 'width:100%','id'=>'addedBy_search'] ) }}
                                            </td>
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
                                        </tr>
                                        <tr>
                                            <th  style="text-align:center">{!! Form::checkbox('chkone[]','','',['id' => 'check_all','onchange' =>'checkAll(this)'])!!}</th>
                                            <th>
                                                {{(Lang::has(Session::get('admin_lang_file').'.ADMIN_SNO')) ? trans(Session::get('admin_lang_file').'.ADMIN_SNO') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SNO')}}
                                            </th>
                                            <th>
                                                {{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ST_NAME')) ? trans(Session::get('admin_lang_file').'.ADMIN_ST_NAME') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ST_NAME')}}
                                            </th>
                                            
                                            <th>
                                                {{(Lang::has(Session::get('admin_lang_file').'.ADMIN_MERCHANT')) ? trans(Session::get('admin_lang_file').'.ADMIN_MERCHANT') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_MERCHANT')}}
                                            </th>
                                            <th>
                                                {{(Lang::has(Session::get('admin_lang_file').'.ADMIN_CATE')) ? trans(Session::get('admin_lang_file').'.ADMIN_CATE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_CATE')}}
                                            </th>
                                            <th>
                                                {{(Lang::has(Session::get('admin_lang_file').'.ADMIN_ADDED_BY')) ? trans(Session::get('admin_lang_file').'.ADMIN_ADDED_BY') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_ADDED_BY')}}
                                            </th>
                                            <th>
                                                {{(Lang::has(Session::get('admin_lang_file').'.ADMIN_EDIT')) ? trans(Session::get('admin_lang_file').'.ADMIN_EDIT') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_EDIT')}}
                                            </th>
                                            <th>
                                                {{(Lang::has(Session::get('admin_lang_file').'.ADMIN_STATUS')) ? trans(Session::get('admin_lang_file').'.ADMIN_STATUS') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_STATUS')}}
                                            </th>
                                            <th>
                                                {{(Lang::has(Session::get('admin_lang_file').'.ADMIN_DELETE')) ? trans(Session::get('admin_lang_file').'.ADMIN_DELETE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_DELETE')}}
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    
                                    </tbody>
                                </table>
                            </div>
                            {{-- Manage list ends--}}
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /.panel-body -->
</div>
<!-- END MAIN CONTENT -->

<!--------model for block confirmation start -------------->
        <div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true" id="mi-modal">
             <div class="modal-dialog modal-sm">
                    <div class="modal-content">
                     <div class="modal-header">
                         <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">are you sure want to block</h4>
                     </div>
                     <div class="modal-footer">
                        <button type="button" class="btn btn-default" id="modal-btn-si">Yes</button>
                        <button type="button" class="btn btn-primary" id="modal-btn-no">No</button>
                    </div>
                    </div>
            </div>
        </div>
    <input type="hidden" name="cat_blkId" id="cat_blkId" value="">
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
    <input type="hidden" name="cat_unblockId" id="cat_unblockId" value="">
<!--------model for unblock confirmation end -------------->

</div>
@section('script')
<script>
    $(document).ready(function() {
        $('#d-table').DataTable({
            responsive: true
        });
    });

    /* -----block store and broducts start----*/
      function change_all_statusblk(id){         
             $('#cat_blkId').val(id);        
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
    var store_id = $('#cat_blkId').val();   
    $.ajax({
          headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
          url: "{{ url('ajax_change_store_status') }}",
          type: 'POST',
          data: {'store_id' : store_id,'status':'0'},
          success:function(response) {

          if(response == 1){
            alert("Blocked Succesfully");
            window.location.reload();
             $("#mi-modal").modal('hide');
          }else{
            alert("Store not Blocked");
            window.location.reload();
             $("#mi-modal").modal('hide');
          }            
      }
     });    
    }else{
    // alert('not confirm');
    }
});
    /* -----block store and broducts end-----*/

    /*-------unblock store and products start------*/

    function change_all_unblock(id){        
       $('#cat_unblockId').val(id);        
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

    var store_id = $('#cat_unblockId').val();   
    $.ajax({
          headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
          url: "{{ url('ajax_change_store_status') }}",
          type: 'POST',
          data: {'store_id' : store_id,'status':'1'},
          success:function(response) {

          if(response == 1){
            alert("UnBlocked Succesfully");
            window.location.reload();
             $("#mi-modal").modal('hide');
          } else{

             alert("Store not UnBlocked");
            window.location.reload();
             $("#mi-modal").modal('hide');
          }           
      }
     }); 

    }else{
        // alert('not confirm');
    }
});



    /*-------unblock store and products end--------*/
</script>

<script type="text/javascript">
    $('#block_value,#unBlock_value,#delete_value').click(function(event){
        $(".rec-select").css({"display" : "none"});
        var val = [];
        $('input[name="chk[]"]:checked').each(function(i){
            val[i] = $(this).val();
        });  console.log(val);
        
        
        if(val=='')
        {
            
            $(".rec-select").css({"display" : "block"});
            
            return;
        }
        //alert(val); return false;
        if($(event.target).attr('id')=='block_value'){
            var status = '0';
        } 
        else if($(event.target).attr('id')=='unBlock_value'){
            var status = '1';
        } 
        else if($(event.target).attr('id')=='delete_value'){
            var status = '2';
        }
        
        $.ajax({
            
            type:'get',
            url :"<?php echo url("multi_store_status"); ?>",
            beforeSend: function() {
                $("#loading-image").show();
            },
            data:{'val':val,'status':status},
            
            success:function(response){
                //$("#loading-image").hide();
                location.reload();
            }
        }); 
    });
    
    
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

    function change_st_status(id,status,type,mer_id)
    {   
        if(confirm("Are sure want to "+type+" ? "))
        {
            $.ajax({
                'type'  :'POST',
                'data'  : {'id' : id,'status' : status,'mer_id':mer_id},
                'url'   : '{{url('store_status')}}',
                success:function(response)
                {   
                    window.location.reload();
                }
            });
        }
        
    }

    var table='';
    $(document).ready(function () {
        
        /*$('#dataTables-example').dataTable({
            "bPaginate": false,
            //"scrollX": true,
            "bLengthChange": false,
            "bFilter": true,
            "bInfo": false,
        "bAutoWidth": false });*/
        table = $('#dataTables-example').DataTable({
            "processing": true,
            "serverSide": true,
            "bLengthChange": true,
            "bAutoWidth": false, 
            "searching": false,
            "ajax":{
                "url": "{{ url('ajax-store-list') }}",
                "dataType": "json",
                "type": "POST",
                "beforeSend":function(){ $('#check_all').prop('checked',false); },
                "data":{ _token: "{{csrf_token()}}",'storeName_search': function(){return $("#storeName_search").val(); },merName_search:function(){return $("#merName_search").val(); },cat_search:function(){return $("#cat_search").val(); },addedBy_search:function(){return $("#addedBy_search").val(); },status_search:function(){return $("#status_search").val(); }}
            },
            "columnDefs": [ {
                "targets": [0,1,6,8],
                "orderable": false
            } ],
            "order": [ 1, 'desc' ],
            "columns": [
            { "data": "checkBox", sWidth: '7%' },
            { "data": "SNo", sWidth: '7%' },
            { "data": "storeName", sWidth: '22%' },
            { "data": "merName", sWidth: '21%' },
            { "data": "category", sWidth: '12%' },
            { "data": "addedBy", sWidth: '10%' },
            { "data": "Edit", sWidth: '7%' },
            { "data": "Status", sWidth: '7%'},
            { "data": "delete", sWidth: '7%'}
            ],
            "fnDrawCallback": function (oSettings) {
                $('.tooltip-demo').tooltip({placement:'left'});
                $('[data-toggle="popover"]').popover({placement:'left'});   
            }
        });
        $('#storeName_search, #merName_search, #cat_search').keyup( function() {
            table.draw();
            //table.search( this.value ).draw();
        });
        $('#addedBy_search, #status_search').change(function(){
            table.draw();
        });
    });
</script>
@endsection
@stop