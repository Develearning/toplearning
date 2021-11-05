<?php

namespace Modules\Libraries\Http\Controllers;

use App\Models\Categories\Titles;
use App\Scopes\DraftScope;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Profile;
use Modules\Libraries\Entities\Libraries;
use Modules\Libraries\Entities\LibrariesCategory;
use Illuminate\Support\Facades\Auth;
use Modules\Libraries\Entities\LibrariesObject;
use Modules\Libraries\Entities\LibrariesMoreVideo;
use Modules\Libraries\Imports\ProfileImport;
use App\LibrariesStatistic;
use App\Exports\ExportLibraries;

class VideoController extends Controller
{
    public function index()
    {
        $get_name_url = explode('/',url()->current());
        $get_menu_child = get_menu_child($get_name_url[5]);

        $categories = LibrariesCategory::where('type', '=', 4)->get();
        return view('libraries::backend.libraries.video.index', [
            'categories' => $categories,
            'get_menu_child' => $get_menu_child,
            'name_url' => $get_name_url[5],
        ]);
    }

    public function getData(Request $request)
    {
        $search = $request->input('search');
        $category_id = $request->input('category_id');
        $sort = $request->input('sort','id');
        $order = $request->input('order','desc');
        $offset = $request->input('offset',0);
        $limit = $request->input('limit',20);
        Libraries::addGlobalScope(new DraftScope());
        $query = Libraries::query();
        $query->select(['el_libraries.*', 'b.name AS category_name']); 
        $query->leftJoin('el_libraries_category AS b', 'b.id', '=', 'el_libraries.category_id' );
        $query->where('el_libraries.type', '=', 4);
        if($search){
            $query->where(function($sub_query) use ($search){
                $sub_query->orWhere('el_libraries.name','like','%' . $search . '%');
                $sub_query->orWhere('el_libraries.name_author','like','%' . $search . '%');
            });
        }
        if ($category_id){
            $query->where('el_libraries.category_id', '=', $category_id);
        }

        $count = $query ->count();
        $query -> orderBy('el_libraries.'.$sort,$order);
        $query ->offset($offset);
        $query->limit($limit);

        $rows = $query ->get();
        foreach ($rows as $row) {
            $profile = Profile::where('user_id', '=', $row->updated_by)->first();
            $row->user_name = $profile->lastname . ' ' . $profile->firstname;
            $row->edit_url = route('module.libraries.video.edit', ['id' => $row->id]);
            $row->updated_at2 = get_date($row->updated_at, 'H:i d/m/Y');
        }

        json_result(['total' => $count, 'rows' => $rows]);
    }

    public function form($id = 0) {
        $get_name_url = explode('/',url()->current());
        $get_menu_child = get_menu_child($get_name_url[5]);

        $errors = session()->get('errors');
        \Session::forget('errors');
        $categories = LibrariesCategory::where('type','=',4)->get();

        if ($id) {
            $model = Libraries::find($id);
            $videos = LibrariesMoreVideo::where('libraries_id',$id)->get();
            $page_title = $model->name;
            $titles = Titles::where('status', '=', 1)->get();

            return view('libraries::backend.libraries.video.form', [
                'model' => $model,
                'page_title' => $page_title,
                'videos' => $videos,
                'categories' => $categories,
                'titles' => $titles,
                'get_menu_child' => $get_menu_child,
                'name_url' => $get_name_url[5],
            ]);
        }

        $model = new Libraries();
        $page_title = trans('backend.add_new') ;

        return view('libraries::backend.libraries.video.form', [
            'model' => $model,
            'page_title' => $page_title,
            'categories' => $categories,
            'get_menu_child' => $get_menu_child,
            'name_url' => $get_name_url[5],
        ]);
    }

    public function save(Request $request) {
        $this->validateRequest([
            'name' => 'required',
            'description' => 'required',
            'status'=>'required',
            'image' => 'nullable|string',
            'category_id' => 'nullable|exists:el_libraries_category,id',
        ], $request, Libraries::getAttributeName());
        
        $get_parents_cate_id = LibrariesCategory::getTreeParentUnit($request->category_id);
        foreach($get_parents_cate_id as $get_parent_cate_id) {
            $cate_parent[] =  $get_parent_cate_id->name;
        }

        $attachments = $request->attachments;
        $name_videos = $request->name_videos;
        $model = Libraries::firstOrNew(['id' => $request->id,'type'=>$request->type]);
        $model->fill($request->all());
        $model->attachment = path_upload($request->attachment);

        if ($request->image) {
            $sizes = config('image.sizes.library');
            $model->image = upload_image($sizes, $request->image);
        }
        
        
        $model->created_by = Auth::id();
        $model->updated_by = Auth::id();
        $model->category_parent = implode(',',$cate_parent);
        $save = $model->save();
        $id = $model->id;
        $res = LibrariesMoreVideo::where('libraries_id',$id)->delete();
        if (($attachments !== null && $name_videos !== null) || (!empty($attachments) && !empty($name_videos))) {
            foreach ($attachments as $key => $attachment) {
                $attachment = path_upload($attachment);
                $query = LibrariesMoreVideo::create();
                $query->libraries_id = $id;
                $query->name_video = $name_videos[$key];
                $query->attachment = $attachment;
                $query->save();
            }
        }
        if ($save) {
            json_result([
                'status' => 'success',
                'message' => trans('lageneral.successful_save'),
                'redirect' => route('module.libraries.video')
            ]);
        }

        json_message(trans('lageneral.save_error'), 'error');
    }

    public function remove(Request $request) {
        $ids = $request->input('ids', null);
        foreach ($ids as $id){
            $libraries = Libraries::find($id);
            $libraries->delete();
        }
        json_result([
            'status' => 'success',
            'message' => 'Xóa thành công',
        ]);
    }

    public function saveObject($libraries_id, Request $request){
        $this->validateRequest([
            'unit_id' => 'nullable|exists:el_unit,id',
            'parent_id' => 'nullable|exists:el_unit,id',
            'title_id' => 'nullable',
        ], $request);

        $title_id = explode(',', $request->input('title_id'));
        $unit_id = $request->input('unit_id');
        $parent_id = $request->input('parent_id');
        $status_unit = $request->input('status_unit');
        $status_title = $request->input('status_title');

        if ($parent_id && is_null($unit_id)){
            if (LibrariesObject::checkObjectUnit($libraries_id, $parent_id, 4)){

            }else{
                $model = new LibrariesObject();
                $model->libraries_id = $libraries_id;
                $model->unit_id = $parent_id;
                $model->type = 4;
                $model->status = 1;
                $model->save();
            }
            json_result([
                'status' => 'success',
                'message' => 'Thêm đơn vị thành công',
            ]);
        }
        if ($unit_id) {
            foreach ($unit_id as $item){
                if (LibrariesObject::checkObjectUnit($libraries_id, $item, 4)){
                    continue;
                }
                $model = new LibrariesObject();
                $model->libraries_id = $libraries_id;
                $model->unit_id = $item;
                $model->type = 4;
                $model->status = 1;
                $model->save();
            }
            json_result([
                'status' => 'success',
                'message' => 'Thêm đơn vị thành công',
            ]);
        }else{
            foreach ($title_id as $item){
                if (LibrariesObject::checkObjectTitle($libraries_id, $item, 4)){
                    continue;
                }
                $model = new LibrariesObject();
                $model->libraries_id = $libraries_id;
                $model->title_id = $item;
                $model->type = 4;
                $model->status = 1;
                $model->save();
            }
            json_result([
                'status' => 'success',
                'message' => 'Thêm chức danh thành công',
            ]);
        }
    }

    public function getUserObject($libraries_id, Request $request){
        $sort = $request->input('sort', 'id');
        $order = $request->input('order', 'desc');
        $offset = $request->input('offset', 0);
        $limit = $request->input('limit', 20);

        $query = LibrariesObject::query();
        $query->select([
            'a.*', 
            'b.code AS profile_code', 
            'b.lastname', 
            'b.firstname', 
            'b.email', 
            'c.name AS title_name', 
            'd.name AS unit_name', 
            'e.name AS parent_name'
        ]);
        $query->from('el_libraries_object AS a');
        $query->leftJoin('el_profile AS b', 'b.user_id', '=', 'a.user_id');
        $query->leftJoin('el_titles AS c', 'c.code', '=', 'b.title_code');
        $query->leftJoin('el_unit AS d', 'd.code', '=', 'b.unit_code');
        $query->leftJoin('el_unit AS e', 'e.code', '=', 'd.parent_code');
        $query->where('a.libraries_id', '=', $libraries_id);
        $query->where('a.type', '=', 4);
        $query->where('a.title_id', '=', null);
        $query->where('a.unit_id', '=', null);

        $count = $query->count();
        $query->orderBy('a.'.$sort, $order);
        $query->offset($offset);
        $query->limit($limit);

        $rows = $query->get();
        foreach ($rows as $row){
            $row->profile_name = $row->lastname . ' ' . $row->firstname;

            if (empty($row->parent_name)){
                $row->parent = $row->unit_name;
                $row->unit = '';
            }else{
                $row->parent = $row->parent_name;
                $row->unit = $row->unit_name;
            }
            $row->status = 'Xem';
        }

        json_result(['total' => $count, 'rows' => $rows]);
    }

    public function getObject($libraries_id, Request $request){
        $sort = $request->input('sort', 'id');
        $order = $request->input('order', 'desc');
        $offset = $request->input('offset', 0);
        $limit = $request->input('limit', 20);

        $query = LibrariesObject::query();
        $query->select(['a.*', 'b.name AS title_name', 'c.name AS unit_name', 'd.name AS parent_name']);
        $query->from('el_libraries_object AS a');
        $query->leftJoin('el_titles AS b', 'b.id', '=', 'a.title_id');
        $query->leftJoin('el_unit AS c', 'c.id', '=', 'a.unit_id');
        $query->leftJoin('el_unit AS d', 'd.code', '=', 'c.parent_code');
        $query->where('a.libraries_id', '=', $libraries_id);
        $query->where('a.type', '=', 4);
        $query->where('a.user_id', '=', null);

        $count = $query->count();
        $query->orderBy('a.'.$sort, $order);
        $query->offset($offset);
        $query->limit($limit);

        $rows = $query->get();
        foreach ($rows as $row){
            if (empty($row->parent_name)){
                $row->parent = $row->unit_name;
                $row->unit = '';
            }else{
                $row->parent = $row->parent_name;
                $row->unit = $row->unit_name;
            }

            $row->status = 'Xem';

        }

        json_result(['total' => $count, 'rows' => $rows]);
    }

    public function removeObject($libraries_id, Request $request){
        $this->validateRequest([
            'ids' => 'required',
        ], $request, [
            'ids' => 'Đối tượng',
        ]);

        $item = $request->input('ids');
        LibrariesObject::destroy($item);
        json_result([
            'status' => 'success',
            'message' => 'Xóa thành công',
        ]);
    }

    public function importObject($libraries_id, Request $request){
        $this->validateRequest([
            'import_file' => 'required|file'
        ], $request, ['import_file' => 'File import']);

        $import = new ProfileImport($libraries_id);
        \Excel::import($import, $request->file('import_file'));

        if ($import->errors) {
            session()->put('errors', $import->errors);
            session()->save();
        }
        json_result([
            'status' => 'success',
            'message' => 'Import thành công',
            'redirect' => route('module.libraries.video.edit', ['id' => $libraries_id]),
        ]);
    }

    public function export()
    {
        return (new ExportLibraries(4))->download('danh_sach_video_'. date('d_m_Y') .'.xlsx');
    }

    public function ajaxIsopenPublish(Request $request) {
        $this->validateRequest([
            'ids' => 'required',
            'status' => 'required|in:0,1'
        ], $request, [
            'ids' => 'Sách giấy',
        ]);

        $ids = $request->input('ids', null);
        $status = $request->input('status', 0);
        if(is_array($ids)) {
            foreach ($ids as $id) {
                $model = Libraries::findOrFail($id);
                $model->status = $status;
                $model->save();
            }
        } else {
            $model = Libraries::findOrFail($ids);
            $model->status = $status;
            $model->save();
        }

        json_result([
            'status' => 'success',
            'message' => trans('lageneral.successful_save'),
        ]);
    }
}
