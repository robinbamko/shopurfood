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

                               <!-- ADMIN SUCCESS MESSAGE -->
								@if(Session::has('admin_message'))
									<div class="alert alert-success alert-dismissible">
										<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
										{{Session::get('admin_message')}}
									</div>	
								@endif
							<!-- END ADMIN SUCCESS MESSAGE -->
					

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

									<div class="alert alert-danger alert-dismissible rec-select" style="display: none;">
										<a href="#" class="close " data-hide="alert" aria-label="close">&times;</a>
										{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_SELECT_ONE')) ? trans(Session::get('admin_lang_file').'.ADMIN_SELECT_ONE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SELECT_ONE')}}
							       </div>
								   
                                   <p class="demo-button">
								
										{!! Form::button((Lang::has(Session::get('admin_lang_file').'.ADMIN_BLOCK')) ? trans(Session::get('admin_lang_file').'.ADMIN_BLOCK') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_BLOCK'),['id' => 'block_value','class' => 'btn btn-success'])!!}
										
										{!! Form::button((Lang::has(Session::get('admin_lang_file').'.ADMIN_UNBLOCK')) ? trans(Session::get('admin_lang_file').'.ADMIN_UNBLOCK') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_UNBLOCK'),['id' => 'unBlock_value','class' => 'btn btn-warning'])!!}	
										
										{!! Form::button((Lang::has(Session::get('admin_lang_file').'.ADMIN_DELETE')) ? trans(Session::get('admin_lang_file').'.ADMIN_DELETE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_DELETE'),['id' => 'delete_value','class' => 'btn btn-danger'])!!}	
										
									</p>
									
									
                                    <table class="table table-striped table-bordered table-hover" id="subadmin_table" style="text-align:center">
                                        <thead>
                                        <tr>
                                            <td></td>
                                            <td></td>
                                            <td colspan="2"><input type="text" id="subadmin_search_name_email" style="width:100%" class="form-control col-md-12" placeholder="{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_SEARCH_SUBADMIN')) ? trans(Session::get('admin_lang_file').'.ADMIN_SEARCH_SUBADMIN') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SEARCH_SUBADMIN')}}"/></td>
                                            <td></td>
                                            <td></td>
											<td></td>
											<td></td>
											<td></td>
											<td></td>
											<td></td>
                                            
                                        </tr>
                                         <tr>
												<th class="sorting_no_need">{!! Form::checkbox('chkone[]','','',['id' => 'check_all','onchange' =>'checkAll(this)'])!!}</th>
												<th>{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_S_NO')) ? trans(Session::get('admin_lang_file').'.ADMIN_S_NO') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_S_NO')}}</th>
												<th>{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_NAME')) ? trans(Session::get('admin_lang_file').'.ADMIN_NAME') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_NAME')}}</th>
												<th>{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_EMAIL')) ? trans(Session::get('admin_lang_file').'.ADMIN_EMAIL') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_EMAIL')}}</th>
												<th>{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_MOBILENO')) ? trans(Session::get('admin_lang_file').'.ADMIN_MOBILENO') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_MOBILENO')}}</th>
												<th>{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_LAST_LOGIN_DATE')) ? trans(Session::get('admin_lang_file').'.ADMIN_LAST_LOGIN_DATE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_LAST_LOGIN_DATE')}}</th>
												<th>{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_LAST_LOGOUT_DATE')) ? trans(Session::get('admin_lang_file').'.ADMIN_LAST_LOGOUT_DATE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_LAST_LOGOUT_DATE')}}</th>
												<th>{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_LAST_LOGIN_IP')) ? trans(Session::get('admin_lang_file').'.ADMIN_LAST_LOGIN_IP') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_LAST_LOGIN_IP')}}</th>
												<th>{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_EDIT')) ? trans(Session::get('admin_lang_file').'.ADMIN_EDIT') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_EDIT')}}</th>
												<th>{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_STATUS')) ? trans(Session::get('admin_lang_file').'.ADMIN_STATUS') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_STATUS')}}</th>
												<th>{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_DELETE')) ? trans(Session::get('admin_lang_file').'.ADMIN_DELETE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_DELETE')}}</th>
										</tr>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                        <tfoot>
                                        </tfoot>
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
    </div>
@section('script')
   <script type="text/javascript">

	$(document).ready(function(){

		var dataTable = $('#subadmin_table').DataTable({
            "processing"  : true,
             "responsive"   : true,
            "serverSide"  : true,
            "bLengthChange": true,
            "searching"   : false,
            "order"	 	  :[0,'desc'],
            "ajax"        :{

			            	"url"   	 :"{{ url('subadmin_list')}}",
			            	"dataType"   :"json",
			            	"type"		 :"POST",
			            	"data"       : { _token : "{{csrf_token()}}",'subadmin_search_name_email' :function(){
			            		return $("#subadmin_search_name_email").val();
			            	       }},  
			            	"error" : function(data){ }
                           },
            "columnDefs"  : [{
            	            "targets" : [0,1,7,8,9],
            	            "orderable" : false
                           }],

            "columns"      :[
                             {"data" : "checkBox",sWidth: '8%' },
                             {"data" : "SNo" , sWidth: '8%'},
                             {"data" : "sub_name" , sWidth: '20%'},
                             {"data" : "sub_email" , sWidth: '26%'},
                             {"data" : "sub_mobile" , sWidth: '10%'},
                             {"data" : "sub_login" , sWidth: '10%'},
                             {"data" : "sub_logout" , sWidth: '10%'},
                             {"data" : "sub_login_ip" , sWidth: '10%'},
                             {"data" : "edit" , sWidth: '10%'},
                             {"data" : "status" , sWidth: '5%'},
                             {"data" : "delete" , sWidth: '5%'},

            	],
		});

		$('#subadmin_search_name_email').keyup( function() {
			dataTable.draw();
		});
            
	});

	/******* ALL CHECKED BOX ********/  
		
               function checkAll(ele) {               
               
                 var checkboxes = document.getElementsByTagName('input');
                 if (ele.checked) {
                     for (var i = 0; i < checkboxes.length; i++) {
                         if (checkboxes[i].type == 'checkbox') {
                             checkboxes[i].checked = true;
                         }
                     }
                 } else {
                     for (var i = 0; i < checkboxes.length; i++) {
                         // console.log(i)
                         if (checkboxes[i].type == 'checkbox') {
                             checkboxes[i].checked = false;
                         }
                     }
                 }
               }
			   
			   
		/******* BLOCK MULTI CUSTOMER ********/  
 
                  $('#block_value').click(function(){

                    $(".rec-select").css({"display" : "none"});
                    var val = [];
						$(':checkbox:checked').each(function(i){
						  val[i] = $(this).val();
						});   
               
                     if(val=='')
						 {               
							$(".rec-select").css({"display" : "block"});                 
							return false;
						 }
             
						$.ajax({ 
							headers: {
								'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
							},
							type:'GET',
							url :"{{ url('change_multi_subadmin_status') }}",
							data:{val:val,status:0},
							success:function(response){
								location.reload(); 
							}
						});
					
                 });
               
			/******* UNBLOCK MULTI CUSTOMER ********/  

                 $('#unBlock_value').click(function(){

                    $(".rec-select").css({"display" : "none"});
                    var val = [];
						$(':checkbox:checked').each(function(i){
						  val[i] = $(this).val();
						}); 
               
                     if(val=='')
						 {               
							$(".rec-select").css({"display" : "block"});                 
							return false;
						 }              
               
                    $.ajax({    
						  headers: {
							'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
						  },
						  type:'GET',
						  url :"{{ url('change_multi_subadmin_status') }}",
						  data:{val:val,status:1},
						  success:function(response){
							location.reload();  
						  }
                    });
                 });

			/******* DELETE MULTI CUSTOMER ********/  

                 $('#delete_value').click(function(){
                 
                     $(".rec-select").css({"display" : "none"});
                    var val = [];
						$(':checkbox:checked').each(function(i){
						val[i] = $(this).val();
                    }); 
            
                     if(val=='')
                     {               
						 $(".rec-select").css({"display" : "block"});                 
						 return false;
                     }              
               
                    $.ajax({   
					  headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					  },						
                      type:'GET',
                      url :"{{ url('change_multi_subadmin_status') }}",
                      data:{val:val,status:2},
						  success:function(response){
							location.reload();  
						  }
                    });
                 });

</script>
@endsection
@stop