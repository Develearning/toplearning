<?php

namespace Modules\Report\Http\Controllers;

use App\Permission;
use App\Models\Categories\TrainingTeacher;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Maatwebsite\Excel\Excel;
use Modules\Online\Entities\OnlineCourse;
use Modules\Offline\Entities\OfflineCourse;
use Modules\Report\Entities\HistoryExport;

class ReportController extends Controller
{
    public function index()
    {
        $reports = $this->reportList();
        return view('report::index', [
            'reports' => $reports
        ]);
    }

    public function review(Request $request, $report) {
        $class_name = 'Modules\Report\Http\Controllers\\'. strtoupper($report). 'Controller';
        if (class_exists($class_name)) {
            $controller = new $class_name();
            return $controller->review($request, $report);
        }
        abort(404);
    }

    public function getData(Request $request) {
        $report = $request->report;
        if (!$report) return;
        $class_name = "Modules\Report\Http\Controllers\\". $report . 'Controller';
        if (class_exists($class_name)) {
            $controller = new $class_name();
            return $controller->getData($request);
        }
        abort(404);
    }

    public function export(Request $request)
    {
        $rpt = $request->report;
        $class_name = "Modules\Report\Export\\". $rpt . 'Export';
        if (class_exists($class_name)){
            $report = new $class_name($request);
            return $report->download('report_'.$rpt.'_'. date('d_m_Y') .'.xlsx', Excel::XLSX);

            // \DB::table('el_history_export')->insert([
            //     'class_name' => $rpt,
            //     'report_name' => @$this->reportList()[$rpt],
            //     'request' => json_encode($request->all()),
            //     'user_id' => \Auth::id(),
            //     'status' => 2,
            //     'created_at' => date('Y-m-d H:i:s'),
            //     'updated_at' => date('Y-m-d H:i:s'),
            // ]);

            // return redirect()->route('module.report.history_export');
        }
        abort(404);
    }

    public function dataChart(Request $request) {
        $report = $request->report;
        if (!$report) {
            return false;
        }

        $class_name = "Modules\Report\Http\Controllers\\". $report . 'Controller';
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
        }
    }

    public function reportList() {
        return [
            'BC01' => 'Danh s??ch k?? cam k???t b???i ho??n',
            'BC02' => 'Danh s??ch h???c vi??n tham gia c??c kh??a ????o t???o',
            'BC03' => 'Danh s??ch kh??a ????o t???o c?? chi ph??',
            /*'BC04' => 'Danh s??ch kh??a ????o t???o ',*/
            'BC05' => 'Danh s??ch h???c vi??n tham gia kh??a ????o t???o',
            'BC06' => 'B??o c??o gi??o v???',
            'BC07' => 'B??o c??o k???t qu??? kh??a h???c t???p trung',
            'BC08' => 'B??o c??o k???t qu??? kh??a h???c Elearning',
            'BC09' => 'B??o c??o ????nh gi?? sau kh??a h???c',
            'BC10' => 'B??o c??o danh s??ch gi???ng vi??n',
            'BC11' => 'B??o c??o ????nh gi??',
            'BC12' => 'Th???ng k?? ????ng k?? tham gi?? kh??a h???c',
            'BC13' => 'Danh s??ch vi ph???m',
            'BC14' => 'Th???ng k?? k???t qu??? ????o t???o',
            'BC15' => 'Qu?? tr??nh ????o t???o',
            'BC16' => 'B??o c??o chi ti???t k???t qu??? k??? thi',
            'BC17' => 'B??o c??o s??? l???n thi theo nh??m c??u h???i',
            'BC18' => 'B??o c??o l???n truy c???p',
            /*'BC19' => 'B??o c??o t???ng h???p k???t qu??? ????nh gi?? n??ng l???c',
            'BC20' => 'B??o c??o t???ng h???p nhu c???u ????o t???o',*/
            'BC21' => 'Th???ng k?? th?? sinh trong k??? thi theo ch???c danh',
            'BC22' => 'Th???ng k?? t??? l??? x???p lo???i trong k??? thi theo ch???c danh',
            /*'BC23' => 'Th???ng k?? k???t qu??? kh??a h???c theo ch????ng tr??nh ????o t???o',*/
            'BC24' => 'K??? ho???ch t??? ????o t???o',
            'BC25' => 'T???ng h???p b??o c??o ????o t???o n???i b???',
            'BC26' => 'B??o c??o chi ti???t th???c hi???n ????o t???o n???i b???',
            /*'BC27' => 'B??o c??o k???t qu??? thi theo nh??m c??u h???i',*/
            'BC28' => 'B??o c??o th???ng k?? k???t qu??? kh???o s??t',
            /*'BC29' => 'B??o c??o th???ng k?? l??????t truy c????p',
            'BC30' => 'B??o c??o th???ng k?? chi ti????t l??????t truy c????p',
            'BC31' => 'B??o c??o th???ng k?? th????i l??????ng truy c????p',*/
//            'BC32' => 'B??o c??o th???ng k?? qua?? tri??nh ??a??o ta??o',
//            'BC33' => 'B??o c??o th???ng k?? s???? l??????ng kho??a ho??c',
            /*'BC34' => 'B??o c??o th???ng k?? s???? l??????ng ng??????i du??ng',*/
            //'BC35' => 'B??o c??o k????t qua?? ho??c t????p GG',
            /*'BC36' => 'Ba??o ca??o ti??nh hi??nh t???? ch????c ky?? thi',
            'BC37' => 'Ba??o ca??o th???ng k?? s??? l?????ng video c???a ????n v??? xu???t b???n trong th??ng',
            'BC38' => 'Ba??o ca??o th???ng k?? s??? l?????ng ng?????i xem video c???a ????n v??? c?? video xu???t b???n trong th??ng',
            'BC39' => 'Ba??o ca??o th???ng k?? s??? l?????ng xem video t???ng ng??y trong th??ng',*/
            'BC40' => 'Ba??o ca??o t??nh h??nh ????o t???o theo k??nh ph??n ph???i',
            'BC41' => 'Th???ng k?? k???t qu??? ????o t???o theo ch???c danh',
            'BC42' => 'B??o c??o ????o t???o',
            'BC43' => 'B??o c??o hi???u qu??? sau ????o t???o',

            //??em qua module ReportNew chuy???n th??nh BC01 -> BC04
            /*'BC44' => 'B??o c??o s??? li???u c??ng t??c kh???o thi',
            'BC45' => 'B??o c??o s??? li???u ??i???m thi chi ti???t',
            'BC46' => 'B??o c??o c?? c???u ????? thi',
            'BC47' => 'B??o c??o t??? l??? tr??? l???i ????ng t???ng c??u h???i trong ng??n h??ng c??u h???i',*/
        ];
    }

    public function history(){
        return view('report::export');
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

            $row->download = route('module.report.download', ['history_id' => $row->id]);
        }

        return \response()->json([
            'total' => $count,
            'rows' => $rows
        ]);
    }
}
