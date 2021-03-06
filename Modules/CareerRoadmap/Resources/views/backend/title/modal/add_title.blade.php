<div class="modal fade" id="add-title-modal" role="dialog" aria-labelledby="modal-title2" aria-hidden="true">
    <form action="{{ route('module.career_roadmap.title.add_title', [$title->id]) }}" method="post" class="form-ajax">
        <div class="modal-dialog" role="document">
            <div class="modal-content 1">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-title2">@lang('career.add_title')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <div class="form-group">
                        <label>@lang('career.title')</label>
                        <select name="title_id" id="title_id" class="form-control select2"></select>
                    </div>

                    <div class="form-group">
                        <label>@lang('career.parent_title')</label>
                        <select name="parent_title" id="parent_title" class="form-control select2" required></select>
                    </div>

                    <div class="form-group">
                        <label>Thâm niên (năm)</label>
                        <input type='number' step='0.1' placeholder='0.0'  name="seniority" id="seniority" class="form-control" required/>
                    </div>

                    <input type="hidden" name="id" value="">
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> @lang('app.save')</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times-circle"></i> @lang('app.close')</button>
                </div>
            </div>
        </div>
    </form>
</div>
