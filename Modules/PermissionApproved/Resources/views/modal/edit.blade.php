<div class="modal fade"  id="modal-permission-approved" >
    <div class="modal-dialog modal-lg" >
        <div class="modal-content">
            <form id="frm-save-approve" class="form-horizontal" method="post" action="{{route('backend.permission.approved.update',['id'=>$id])}}" role="form" enctype="multipart/form-data">
                <div class="modal-header">
                    <h4 class="modal-title">Chỉnh sửa phê duyệt cấp {{$level}}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="idapproved" id="idapproved" value="">
                    <div>
                        <div class="form-group">
                            <label>Cấp duyệt</label>
                            <select id="objectlevel" name='objectlevel' class="form-control">
                                <option value="0">Chọn cấp duyệt</option>
                                @foreach ($objects as $object)
                                    <option value="{{$object->id}}" {{$object->id==$object->object_id?'selected':''}}>{{$object->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group" id="form-employees">
                            <label>Nhân viên</label>
                            <select name="employees" id="employees" multiple class="form-control load-user" data-placeholder="Nhập mã hoặc tên nhân viên">
                                @foreach ($users as $user)
                                    <option value="{{$user->id}}" selected>{{$user->full_name}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group" id="form-titles">
                            <label>Chức danh</label>
                            <select name="titles" id="titles" class="form-control load-title" multiple data-placeholder="chức danh">
                                @foreach ($titles as $title)
                                    <option value="{{$title->id}}" selected>{{$title->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{{trans('backend.close')}}</button>

                    <button id="update-approved" class="btn btn-primary"><i class="fa fa-save"></i> {{trans('backend.save')}}</button>
                </div>
            </form>
        </div>
    </div>
</div>
