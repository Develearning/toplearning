<?php

namespace Modules\ReportNew\Http\Controllers;

use App\Models\Categories\Titles;
use App\Models\Categories\TrainingTeacher;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Excel;
use Modules\Online\Entities\OnlineCourse;
use Modules\Offline\Entities\OfflineCourse;
use Modules\ReportNew\Entities\HistoryExport;
use Modules\TrainingRoadmap\Entities\TrainingRoadmap;

class ReportNewController extends Controller
{
    public function index()
    {
        $get_name_url = explode('/',url()->current());
        $get_menu_child = get_menu_child($get_name_url[4]);
        
        $reports = $this->reportList();
        return view('reportnew::index', [
            'reports' => $reports,
            'get_menu_child' => $get_menu_child,
            'name_url' => $get_name_url[4],
        ]);
    }

    public function review(Request $request, $report) {
        $class_name = 'Modules\ReportNew\Http\Controllers\\'. strtoupper($report). 'Controller';
        if (class_exists($class_name)) {
            $controller = new $class_name();
            return $controller->review($request, $report);
        }
        abort(404);
    }

    public function getData(Request $request) {
        $report = $request->report;
        if (!$report) return;
        $class_name = "Modules\ReportNew\Http\Controllers\\". $report . 'Controller';
        if (class_exists($class_name)) {
            $controller = new $class_name();
            return $controller->getData($request);
        }
        abort(404);
    }

    public function export(Request $request)
    {
        $list_report = $this->reportList();

        $rpt = $request->report;
        $name_report = $list_report[$rpt];

        $class_name = "Modules\ReportNew\Export\\". $rpt . 'Export';
        if (class_exists($class_name)){
            $report = new $class_name($request);
            return $report->download(Str::slug($name_report, '_') .'_'. date('d_m_Y') .'.xlsx', Excel::XLSX);

            /*\DB::table('el_history_export_new')->insert([
                'class_name' => $rpt,
                'report_name' => @$this->reportList()[$rpt],
                'request' => json_encode($request->all()),
                'user_id' => \Auth::id(),
                'status' => 2,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            return redirect()->route('module.report_new.history_export');*/
        }
        abort(404);
    }

    public function dataChart(Request $request) {
        $report = $request->report;
        if (!$report) {
            return false;
        }

        $class_name = "Modules\ReportNew\Http\Controllers\\". $report . 'Controller';
        if (class_exists($class_name)) {
            $controller = new $class_name();
            return $controller->dataChart($request);
        }

        abort(404);
    }

    public function filter(Request $request)
    {
        $search = $request->search;
        if ($request->type=='course') {
            $from_date = $request->from_date;
            $to_date = $request->to_date;

            if ($request->course_type==1) {
                $query = OnlineCourse::where('status','=',1);
            }elseif($request->course_type==2) {
                $query = OfflineCourse::where('status','=',1);
            }else{
                return null;
            }
            if ($search) {
                $query->where(function ($join) use ($search){
                    $join->where('name', 'like', '%'. $search .'%');
                    $join->orWhere('code', 'like', '%'. $search .'%');
                });
            }
            if ($from_date && $to_date){
                $query->where('start_date', '>=', date_convert($from_date));
                $query->where('start_date', '<=', date_convert($to_date, '23:59:59'));
            }

            $paginate = $query->paginate(10);
            $data['results'] = $query->get(\DB::raw('id, CONCAT(code, \' - \', name) AS text'));
            if ($paginate->nextPageUrl()) {
                $data['pagination'] = ['more' => true];
            }
            return json_result($data);
        }elseif ($request->type=='teacher'){
            return TrainingTeacher::getTeacherSelect2($request);
        }elseif ($request->type=='SubjectByTitle'){
            return TrainingRoadmap::getSubjectByTitle($request);
        }elseif ($request->type == 'titleAll'){
            $query = Titles::query();
            $query->where('status', '=', 1);

            if ($search) {
                $query->where('name', 'like', '%'. $search .'%');
            }

            $paginate = $query->paginate(10);
            $data['results'] = $query->get(['id', 'name AS text']);
            if ($paginate->nextPageUrl()) {
                $data['pagination'] = ['more' => true];
            }

            return json_result($data);
        }
    }

    public function reportList() {
        return [
            'BC01' => 'B??o c??o s??? li???u c??ng t??c kh???o thi',
            'BC02' => 'B??o c??o s??? li???u ??i???m thi chi ti???t',
            'BC03' => 'B??o c??o c?? c???u ????? thi',
            'BC04' => 'B??o c??o t??? l??? tr??? l???i ????ng t???ng c??u h???i trong ng??n h??ng c??u h???i',
            'BC05' => 'B??o c??o h???c vi??n tham gia kh??a h???c t???p trung / tr???c tuy???n',
            'BC06' => 'Danh s??ch h???c vi??n c???a ????n v??? theo chuy??n ?????',
            'BC07' => 'B??o c??o qu?? tr??nh ????o t???o c???a nh??n vi??n',
            'BC08' => 'T???ng h???p t??nh h??nh t??? ch???c c??c kh??a h???c n???i b??? v?? b??n ngo??i',
            'BC09' => 'Th???ng k?? t??nh h??nh ????o t???o nh??n vi??n t??n tuy???n',
            'BC10' => 'Danh s??ch CBNV kh??ng ch???p h??nh n???i quy ????o t???o',
            'BC11' => 'Th???ng k?? Gi???ng vi??n ????o t???o (N???i b??? & b??n ngo??i) theo Th??ng / Qu?? / N??m',
            'BC12' => 'Th???ng k?? chi ti???t h???c vi??n theo ????n v???',
            'BC13' => 'B??o c??o chi ph?? ????o t???o theo khu v???c',
            'BC14' => 'Export danh m???c',
            'BC15' => 'B??o c??o t???ng h???p k???t qu??? theo th??p ????o t???o',
            'BC17' => 'Danh s??ch x??c nh???n b???i ho??n chi ph?? ????o t???o ?????i v???i CBNV t??n tuy???n',
            'BC18' => 'Danh s??ch x??c nh???n b???i ho??n chi ph?? ????o t???o ?????i v???i CBNV c?? cam k???t',
            'BC21' => 'Danh s??ch c??c kh??a h???c tr???c tuy???n ??ang m???',
            'BC22' => 'Danh s??ch c??c chuy??n ????? g???p / t??ch',
            'BC23' => 'Th???ng k?? t??? l??? ho??n th??nh th??p ????o t???o theo ch???c danh',
            'BC24' => 'T???ng h???p t??nh h??nh tham gia ????o t???o c??c kh??a E-Learning theo ????n v???',
            'BC25' => 'T???ng h???p t??nh h??nh tham gia ????o t???o c??c kh??a E-Learning theo chuy??n ?????',
            'BC26' => 'B??o c??o th?? lao gi???ng vi??n',
            'BC27' => 'B??o c??o chi ph?? ????o t???o',
            'BC28' => 'B??o c??o k???t qu??? chi ti???t theo k??? thi',
            'BC29' => 'B??o c??o k???t qu??? th???c hi???n so v???i k??? ho???ch qu?? / n??m',
            'BC30' => 'B??o c??o k???t qu??? ????nh gi?? kh??a h???c',
        ];
    }

    public function history(){
        $get_name_url = explode('/',url()->current());
        $get_menu_child = get_menu_child($get_name_url[4]);

        return view('reportnew::export',[
            'get_menu_child' => $get_menu_child,
            'name_url' => $get_name_url[4],
        ]);
    }

    public function download($history_id)
    {
        $history = HistoryExport::find($history_id);
        $storage = \Storage::disk('local');
        $file_name = $storage->path($history->file_name);

        //$file_name = Config('app.datafile.dataroot'). '/uploads/'. $history->file_name;
        if (file_exists($file_name)) {
            return \Response::download($file_name);
        }

        return abort(404);
    }

    public function getDataHistoryExport(Request $request)
    {
        $offset = $request->input('offset', 0);
        $limit = $request->input('limit', 20);

        $query = HistoryExport::query();
        $count = $query->count();
        $query->orderBy('created_at', 'DESC');
        $query->offset($offset);
        $query->limit($limit);
        $rows = $query->get();

        foreach ($rows as $row){
            $row->created_at2 = get_date($row->created_at, 'H:i d/m/Y');
            $file_name = \Storage::disk('local')->path($row->file_name);
            //$file_name = Config('app.datafile.dataroot') . '/uploads/' . $row->file_name;

            $row->size = file_exists($file_name) ? round(filesize($file_name)/1024/1024, 2) : 0;

            $row->download = route('module.report_new.download', ['history_id' => $row->id]);
        }

        return \response()->json([
            'total' => $count,
            'rows' => $rows
        ]);
    }
}
