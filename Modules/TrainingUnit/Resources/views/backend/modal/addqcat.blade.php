<div class="modal fade" id="myModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('module.training_unit.questionlib.save_category') }}" method="post" class="form-ajax" data-success="success_submit">
            <input type="hidden" name="id" value="{{ $model->id }}">
            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">@if($model->name) {{ $model->name }} @else {{trans('backend.add_new')}} @endif</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <!-- Modal body -->
            <div class="modal-body">
                <div class="form-group">
                    <label>{{ trans('backend.category_name') }}</label>
                    <input name="name" type="text" class="form-control" value="{{ $model->name }}">
                </div>

                <div class="form-group">
                    <label>{{ trans('backend.parent_category') }}</label>
                    <select name="parent_id" class="form-control">
                        <option value="">-- {{ trans('backend.parent_category') }} --</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" {{ $category->id == $model->parent_id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Modal footer -->
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> {{ trans('backend.save') }}</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Đóng</button>
            </div>
            </form>
        </div>
    </div>
</div>
