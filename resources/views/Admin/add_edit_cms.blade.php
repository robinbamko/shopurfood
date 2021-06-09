@extends('Admin.layouts.default')
@section('PageTitle')
    @if(isset($pagetitle))
        {{$pagetitle}}
    @endif
@stop

@section('content')
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

                                {{-- Display error message--}}
                                @if ($errors->any())
                                    <div class="alert alert-warning alert-dismissible">
                                        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                                        <ul>
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                {{-- Add/Edit page starts--}}
                                <div class="box-body spaced">
                                    <div id="location_form" class="collapse in panel-body">
                                        {{--Edit page values--}}
                                        @php $page_title = $page_description = $page_id = '';
                                        @endphp
                                        @if($id != '' && empty($cms_detail) === false)
                                            @php
                                                $page_title = $cms_detail->page_title_en;
                                                $page_description = $cms_detail->description_en;
                                                $page_id = $cms_detail->id;
                                            @endphp
                                        @endif
                                        {{--Edit page values--}}
                                        <div class="">
                                            @if($id != '' && empty($cms_detail) === false)
                                                {!! Form::open(['method' => 'post','class' => 'form-auth-small','url' => 'add-update-cms','enctype'=>'multipart/form-data','id'=>'cms_form']) !!}
                                                {!! Form::hidden('page_id',$page_id)!!}
                                            @else
                                                {!! Form::open(['method' => 'post','class' => 'form-auth-small','url' => 'add-update-cms','enctype'=>'multipart/form-data','id'=>'cms_form']) !!}
                                                {!! Form::hidden('page_id',$page_id)!!}
                                            @endif
                                            <div class="row panel-heading">
                                                <label class="col-sm-2">
												<span class="">
													{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_PAGE_TITLE')) ? trans(Session::get('admin_lang_file').'.ADMIN_PAGE_TITLE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PAGE_TITLE')}}&nbsp;*
												</span>
                                                </label>
                                                <div class="col-sm-6">

                                                    {!! Form::text('page_title_en',$page_title,['class'=>'form-control','id' => 'cus_name','required']) !!}
                                                    <div id="suggesstion-box" class="cntry_box" ><span id="no_country"> </span></div>
                                                </div>
                                            </div>

                                                @if(count($Admin_Active_Language) > 0)
                                                    @foreach($Admin_Active_Language as $lang)
                                                        @php
                                                            $pagetitlang = '';
                                                            $pagetit_lang = 'page_title_'.$lang->lang_code;
                                                        @endphp
                                                        @if($id != '' && empty($cms_detail) === false)
                                                            @php
                                                                $pagetitlang = $cms_detail->$pagetit_lang;
                                                            @endphp
                                                        @endif

                                                        <div class="row panel-heading">
                                                            <label class="col-sm-2">
												<span class="">
													{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_PAGE_TITLE')) ? trans(Session::get('admin_lang_file').'.ADMIN_PAGE_TITLE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PAGE_TITLE')}} (In {{$lang->lang_name}})*
												</span>
                                                            </label>
                                                            <div class="col-sm-6">
                                                                {!! Form::text('page_title_'.$lang->lang_code.'',$pagetitlang,['class'=>'form-control','id' => 'page_title','required']) !!}
                                                                <div id="suggesstion-box" class="cntry_box" ><span id="no_country"> </span></div>
                                                            </div>
                                                        </div>

                                                    <!-- end -->
                                                    @endforeach
                                                @endif



                                            <div class="row panel-heading">
                                                <label class="col-sm-2">
												<span class="panel-title">
													{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_PAGE_DESCRIPTION')) ? trans(Session::get('admin_lang_file').'.ADMIN_PAGE_DESCRIPTION') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PAGE_DESCRIPTION')}}&nbsp;*
												</span>
                                                </label>
                                                <div class="col-sm-6">
                                                    {!! Form::textarea('description',$page_description,['class'=>'form-control summernote','required']) !!}
                                                </div>
                                            </div>


                                                @if(count($Admin_Active_Language) > 0)
                                                    @foreach($Admin_Active_Language as $lang)
                                                        @php
                                                            $page_descLang = '';
                                                            $pagedes_lang = 'description_'.$lang->lang_code;

                                                        @endphp
                                                        @if($id != '' && empty($cms_detail) === false)
                                                            @php
                                                             $page_descLang = $cms_detail->$pagedes_lang;
                                                            @endphp
                                                        @endif
                                                        <div class="row panel-heading">
                                                            <label class="col-sm-2">
												<span class="panel-title">
													{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_PAGE_DESCRIPTION')) ? trans(Session::get('admin_lang_file').'.ADMIN_PAGE_DESCRIPTION') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PAGE_DESCRIPTION')}} (In {{$lang->lang_name}})*
												</span>
                                                            </label>
                                                            <div class="col-sm-6">
                                                                {!! Form::textarea('description_'.$lang->lang_code.'',$page_descLang,['class'=>'form-control summernote','required']) !!}
                                                            </div>
                                                        </div>

                                                        <!-- end -->
                                                    @endforeach
                                                @endif

                                            <div class="row panel-heading">
                                                <div class="form-group">
                                                    <div class="col-sm-2"></div>
                                                    <div class="col-sm-6">
                                                        @if($id!='')
                                                            @php $saveBtn = (Lang::has(Session::get('admin_lang_file').'.ADMIN_UPDATE')) ? trans(Session::get('admin_lang_file').'.ADMIN_UPDATE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_UPDATE') @endphp
                                                        @else
                                                            @php $saveBtn=(Lang::has(Session::get('admin_lang_file').'.ADMIN_SAVE')) ? trans(Session::get('admin_lang_file').'.ADMIN_SAVE') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_SAVE') @endphp
                                                        @endif

                                                        {!! Form::submit($saveBtn,['class' => 'btn btn-success'])!!}
                                                        @if($id!='')
                                                            <input type="button" value="Cancel" class="btn btn-warning" onclick="javascript:window.location.href='<?php echo url('manage-cms'); ?>'">
                                                        @else
                                                            <input type="button" value="Cancel" class="btn btn-warning" onclick="javascript:window.location.href='<?php echo url('manage-cms'); ?>'">
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            {!! Form::close() !!}
                                        </div>
                                    </div>
                                </div>
                                {{-- Add page ends--}}

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
        function strip_tags(str) {
            str = str.toString();
            return str.replace(/<\/?[^>]+>/gi, '');
        }
        jQuery.validator.addMethod("noSpace", function(value, element) {
            return strip_tags(value).trim().length != 0;
        }, "No space please and don't leave it empty");
        $("#cms_form").validate({
            //onkeyup: true,
            onfocusout: function (element) {
                this.element(element);
            },
            //ignore: ":hidden:not(.summernote),.note-editable.panel-body",
            rules: {
                page_title_en: "required",
                description : { noSpace : true }
                //description: "required",


            },
            messages: {
                page_title_en: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_PAGE_TITLE_VAL')) ? trans(Session::get('admin_lang_file').'.ADMIN_PAGE_TITLE_VAL') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PAGE_TITLE_VAL')}}",
                description: "{{(Lang::has(Session::get('admin_lang_file').'.ADMIN_PAGE_DESCRIPTION_VAL')) ? trans(Session::get('admin_lang_file').'.ADMIN_PAGE_DESCRIPTION_VAL') : trans($ADMIN_OUR_LANGUAGE.'.ADMIN_PAGE_DESCRIPTION_VAL')}}",



            }
        });

    </script>
    <script src="{{url('')}}/public/admin/assets/scripts/summernote.js"></script>
    <script>

        $(document).ready(function() {
            $('.summernote').summernote();
            $('#cms_form').each(function () {
                if ($(this).data('validator'))
                    $(this).data('validator').settings.ignore = ".note-editor *";
            });
        });

    </script>
@endsection
@stop