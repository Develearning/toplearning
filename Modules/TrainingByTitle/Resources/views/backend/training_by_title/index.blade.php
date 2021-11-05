{{-- @extends('layouts.backend')

@section('page_title', 'Lộ trình đào tạo')

@section('breadcrumb')
    <div class="ibox-content forum-container">
        <h2 class="st_title">
            <i class="uil uil-apps"></i>
            {{ trans('backend.training') }} 
            <i class="uil uil-angle-right"></i>
            <span class="font-weight-bold">Lộ trình đào tạo</span>
        </h2>
    </div>
@endsection
@section('content') --}}
    <div role="main">
        @if(isset($errors))
            @foreach($errors as $error)
                <div class="alert alert-danger">{!! $error !!}</div>
            @endforeach
        @endif
        <div class="row">
            <div class="col-md-12 mb-2">
                <form class="form-inline" id="form-search">
                    <div class="w-25">
                        <select name="title" class="form-control load-title" data-placeholder="-- {{ trans('backend.title') }} --"></select>
                    </div>
                    <div class="w-25">
                        <select name="unit_type" class="form-control select2">
                            <option value="" disabled selected>Chọn loại đơn vị</option>
                            @foreach ($unit_types as $unit_type)
                                <option value="{{ $unit_type->id }}">{{ $unit_type->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="w-25">
                        <select name="title_rank" class="select2 form-control" id="" data-placeholder="--Cấp bậc chức danh--">
                            <option value=""></option>
                            @foreach ($titles_rank as $title_rank)
                                <option value="{{ $title_rank->id }}">{{ $title_rank->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i>&nbsp;{{ trans('backend.search') }}</button>
                </form>
            </div>
            <div class="col-md-12 text-right">
                <div class="pull-right">
                    <button class="btn btn-warning copy">
                        <i class="fa fa-copy"></i> &nbsp;{{trans("backend.copy")}}
                    </button>
                    <div class="btn-group">
                        <a class="btn btn-info" href="{{ route('module.training_by_title.export') }}" id="export-excel">
                            <i class="fa fa-download"></i> Export
                        </a>
                    </div>
                    @can('training-by-title-detail-create')
                        <div class="btn-group">
                            <a class="btn btn-info" href="{{ download_template('mau_import_lo_trinh_dao_tao.xlsx') }}">
                                <i class="fa fa-download"></i> {{trans('backend.import_template')}}
                            </a>
                            <button class="btn btn-info" id="import-plan" type="submit" name="task" value="import">
                                <i class="fa fa-upload"></i> Import
                            </button>
                        </div>
                    @endcan
                </div>
            </div>
        </div>
        <br>
        <table class="tDefault table table-hover bootstrap-table">
            <thead>
                <tr>
                    <th data-field="index" data-formatter="index_formatter" data-align="center" data-width="2%">#</th>
                    <th data-field="code">{{trans('backend.title_code')}}</th>
                    <th data-field="name" data-formatter="name_formatter">{{trans('backend.title_name')}}</th>
                    <th data-field="unit_type_name">Loại đơn vị</th>
                    <th data-field="num_training_by_title_category">Số lượng giai đoạn</th>
                </tr>
            </thead>
        </table>
    </div>

    <div class="modal fade" id="modal-copy" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{ trans("backend.copy") }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group row">
                        <div class="col-3">Chức danh nguồn</div>
                        <div class="col-9">
                            @php
                                $titles = \App\Models\Categories\Titles::query()
                                    ->whereIn('id', function ($sub){
                                        $sub->select(['title_id'])
                                              ->from('el_training_by_title_category')
                                              ->pluck('title_id')
                                              ->toArray();
                                    })->get();
                            @endphp
                            <select name="title_old" id="title_old" class="form-control select2" data-placeholder="Chức danh nguồn">
                                <option value=""></option>
                                @foreach($titles as $title)
                                    <option value="{{ $title->id }}">{{ $title->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-3">Chức danh đích</div>
                        <div class="col-9">
                            <select name="title_new" id="title_new" class="form-control load-title" data-placeholder="Chức danh đích">
                                <option value=""></option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ trans('backend.close') }}</button>
                    <button type="button" class="btn btn-primary" id="copyTitle">{{ trans("backend.copy") }}</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-import" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form action="{{ route('module.training_by_title.import') }}" method="post" class="form-ajax">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">IMPORT {{trans('backend.trainingroadmap')}}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="file" name="import_file" id="import_file" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ trans('backend.close') }}</button>
                        <button type="submit" class="btn btn-primary">Import</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script type="text/javascript">

        function index_formatter(value, row, index) {
            return (index+1);
        }
        function name_formatter(value, row, index) {
            return '<a href="'+ row.title_url +'"> '+row.name+' </a>';
        }
        var table = new LoadBootstrapTable({
            locale: '{{ \App::getLocale() }}',
            url: '{{ route('module.training_by_title.getdata') }}',
        });

        $('#import-plan').on('click', function() {
            $('#modal-import').modal();
        });

        $('.copy').on('click', function() {
            $('#modal-copy').modal();
        });

        $('#copyTitle').on('click', function () {
            var title_old = $('#title_old option:selected').val();
            var title_new = $('#title_new option:selected').val();

            $.ajax({
                url: '{{ route('module.training_by_title.ajax_check_training_by_title') }}',
                type: 'post',
                data: {
                    title_old: title_old,
                    title_new: title_new
                }
            }).done(function(data) {
                if (data.status == 'error'){
                    show_message(data.message, data.status);
                    window.location = '';
                    return false;
                }else {
                    Swal.fire({
                        title: '',
                        text: data.message,
                        type: data.status,
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Đồng ý!',
                        cancelButtonText: 'Hủy!',
                    }).then((result) => {
                        if (result.value) {
                            $.ajax({
                                url: '{{ route('module.training_by_title.ajax_copy') }}',
                                type: 'post',
                                data: {
                                    title_old: title_old,
                                    title_new: title_new
                                }
                            }).done(function(data) {
                                show_message(data.message, data.status);
                                window.location = '';
                                return false;
                            }).fail(function(data) {
                                show_message('Lỗi hệ thống', 'error');
                                return false;
                            });
                        }
                    });
                }
            }).fail(function(data) {
                show_message('Lỗi hệ thống', 'error');
                return false;
            });
        });
    </script>
{{-- @endsection --}}
