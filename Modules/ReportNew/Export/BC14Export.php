<?php
namespace Modules\ReportNew\Export;

use App\Config;
use App\LogoModel;
use App\Models\Categories\Absent;
use App\Models\Categories\AbsentReason;
use App\Models\Categories\Discipline;
use App\Models\Categories\LevelSubject;
use App\Models\Categories\Position;
use App\Models\Categories\StudentCost;
use App\Models\Categories\TrainingObject;
use App\Models\Categories\TrainingType;
use App\Models\Categories\UnitManager;
use App\Scopes\CompanyScope;
use Illuminate\Support\Facades\Request;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Modules\Certificate\Entities\Certificate;
use Modules\Offline\Entities\OfflineCourseCost;
use Modules\Offline\Entities\OfflineRegister;
use Modules\Offline\Entities\OfflineSchedule;

use Modules\Offline\Entities\OfflineStudentCost;
use Modules\ReportNew\Entities\BC14;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class BC14Export implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize,  WithEvents, WithStartRow, WithDrawings
{
    use Exportable;
    protected $index = 0;
    protected $count = 0;
    protected $count_title = 0;

    public function __construct($param)
    {
        $this->name_obj = $param->name_obj;
    }

    public function query()
    {
        $query = BC14::{$this->name_obj}()->orderBy('id', 'asc');

        $this->count = $query->count();
        return $query;
    }
    public function map($row): array
    {
        $obj = $this->{'map'.$this->name_obj}($row);

        return $obj;
    }

    public function headings(): array
    {
        $title_arr = $this->{'headings'.$this->name_obj}();

        return [
            [],
            [],
            [],
            [],
            [],
            ['EXPORT DANH M???C'],
            [],
            $title_arr
        ];
    }
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $title = [
                    'font' => [
                        'size'      =>  12,
                        'name' => 'Arial',
                        'bold'      =>  true,
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                    ],
                ];

                $arr_char = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'];
                if ($this->count_title > 26){
                    $num = floor($this->count_title/26);
                    $num_1 = $this->count_title - ($num * 26);

                    $char = $arr_char[($num - 1)] . $arr_char[($num_1 - 1)];
                }else{
                    $char = $arr_char[($this->count_title - 1)];
                }

                $event->sheet->getDelegate()->mergeCells('A6:'.$char.'6')
                ->getStyle('A6')
                ->applyFromArray($title);

                $event->sheet->getDelegate()->getStyle('A8:'.$char.'8')
                    ->applyFromArray($title)
                    ->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('FFFF00');

                $event->sheet->getDelegate()->getStyle('A8:'.$char.(8 + $this->index))
                    ->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ],
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                        ],
                    ]);
            },

        ];
    }
    public function startRow(): int
    {
        return 9;
    }

    public function drawings()
    {
        $storage = \Storage::disk('upload');

        LogoModel::addGlobalScope(new CompanyScope());
        $logo = LogoModel::where('status',1)->first();
        if ($logo) {
            $path = $storage->path($logo->image);
        }else{
            $path = './images/image_default.jpg';
        }

        $drawing = new Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('This is my logo');
        $drawing->setPath($path);
        $drawing->setHeight(100);
        $drawing->setCoordinates('A1');

        return $drawing;
    }

    public function headingsUnit(){
        $this->count_title = 6;
        return [
            'STT',
            'M?? ????n v???',
            '????n v???',
            '????n v??? qu???n l??',
            'Lo???i ????n v???',
            'Ng?????i qu???n l??',
        ];
    }
    public function mapUnit($row){
        $this->index++;
        $unit_manager = UnitManager::query()
            ->select([
                \DB::raw('CONCAT(user_code ,\' - \', lastname, \' \', firstname) as fullname')
            ])
            ->from('el_unit_manager as a')
            ->leftJoin('el_profile as b', 'b.code', '=', 'a.user_code')
            ->where('a.unit_code', '=', $row->code)
            ->pluck('fullname')->toArray();

        return [
            $this->index,
            $row->code,
            $row->name,
            $row->parent_name,
            $row->type_name,
            implode('; ', $unit_manager),
        ];
    }

    public function headingsArea(){
        $this->count_title = 4;
        return [
            'STT',
            'M??',
            'T??n',
            '?????a ??i???m qu???n l??',
        ];
    }
    public function mapArea($row){
        $this->index++;
        return [
            $this->index,
            $row->code,
            $row->name,
            $row->parent_name,
        ];
    }

    public function headingsUnitType(){
        $this->count_title = 2;
        return [
            'STT',
            'Lo???i ????n v???'
        ];
    }
    public function mapUnitType($row){
        $this->index++;
        return [
            $this->index,
            $row->name,
        ];
    }

    public function headingsTitles(){
        $this->count_title = 5;
        return [
            'STT',
            'M?? ch???c danh',
            'T??n ch???c danh',
            'Nh??m ch???c danh',
            'Tr???ng th??i'
        ];
    }
    public function mapTitles($row){
        $this->index++;
        switch ($row->group) {
            case 'CH': $row->group = 'C???a h??ng'; break;
            case 'CNT': $row->group = 'Chi nh??nh t???nh'; break;
            case 'VP': $row->group = 'V??n Ph??ng'; break;
            case 'NM': $row->group = 'C??ng ty con nh?? m??y'; break;
            default: $row->group = ''; break;
        }

        return [
            $this->index,
            $row->code,
            $row->name,
            $row->group,
            ($row->status == 1) ? 'B???t' : 'T???t',
        ];
    }

    public function headingsCert(){
        $this->count_title = 3;
        return [
            'STT',
            'M?? tr??nh ?????',
            'T??n tr??nh ?????',
        ];
    }
    public function mapCert($row){
        $this->index++;

        return [
            $this->index,
            $row->code,
            $row->name,
        ];
    }

    public function headingsPosition(){
        $this->count_title = 4;
        return [
            'STT',
            'M?? ch???c v???',
            'T??n ch???c v???',
            'Tr???ng th??i'
        ];
    }
    public function mapPosition($row){
        $this->index++;
        return [
            $this->index,
            $row->code,
            $row->name,
            ($row->status == 1) ? 'B???t' : 'T???t',
        ];
    }

    public function headingsTrainingProgram(){
        $this->count_title = 4;
        return [
            'STT',
            'M?? ch??? ?????',
            'Ch??? ?????',
            'Tr???ng th??i'
        ];
    }
    public function mapTrainingProgram($row){
        $this->index++;
        return [
            $this->index,
            $row->code,
            $row->name,
            ($row->status == 1) ? 'B???t' : 'T???t',
        ];
    }

    public function headingsLevelSubject(){
        $this->count_title = 4;
        return [
            'STT',
            'M??',
            'M???ng nghi???p v???',
            'Tr???ng th??i'
        ];
    }
    public function mapLevelSubject($row){
        $this->index++;
        return [
            $this->index,
            $row->code,
            $row->name,
            ($row->status == 1) ? 'B???t' : 'T???t',
        ];
    }

    public function headingsSubject(){
        $this->count_title = 6;
        return [
            'STT',
            'M?? chuy??n ?????',
            'T??n chuy??n ?????',
            'M???ng nghi???p v???',
            'Ch??? ?????',
            'Tr???ng th??i'
        ];
    }
    public function mapSubject($row){
        $this->index++;
        return [
            $this->index,
            $row->code,
            $row->name,
            $row->level_subject_name,
            $row->parent_name,
            ($row->status == 1) ? 'B???t' : 'T???t',
        ];
    }

    public function headingsTrainingLocation(){
        $this->count_title = 6;
        return [
            'STT',
            'M?? ?????a ??i???m ????o t???o',
            'T??n ?????a ??i???m ????o t???o',
            'T???nh th??nh',
            'Qu???n huy???n',
            'Tr???ng th??i'
        ];
    }
    public function mapTrainingLocation($row){
        $this->index++;
        return [
            $this->index,
            $row->code,
            $row->name,
            $row->province,
            $row->district,
            ($row->status == 1) ? 'B???t' : 'T???t',
        ];
    }

    public function headingsTrainingForm(){
        $this->count_title = 3;
        return [
            'STT',
            'M??',
            'T??n',
        ];
    }
    public function mapTrainingForm($row){
        $this->index++;
        return [
            $this->index,
            $row->code,
            $row->name,
        ];
    }

    public function headingsTrainingType(){
        $this->count_title = 4;
        return [
            'STT',
            'M?? h??nh th???c ????o t???o',
            'T??n h??nh th???c ????o t???o',
            'Tr???ng th??i'
        ];
    }
    public function mapTrainingType($row){
        $this->index++;
        return [
            $this->index,
            $row->code,
            $row->name,
            ($row->status == 1) ? 'B???t' : 'T???t',
        ];
    }

    public function headingsTrainingObject(){
        $this->count_title = 4;
        return [
            'STT',
            'M?? ?????i t?????ng ????o t???o',
            'T??n ?????i t?????ng ????o t???o',
            'Tr???ng th??i'
        ];
    }
    public function mapTrainingObject($row){
        $this->index++;
        return [
            $this->index,
            $row->code,
            $row->name,
            ($row->status == 1) ? 'B???t' : 'T???t',
        ];
    }

    public function headingsAbsent(){
        $this->count_title = 4;
        return [
            'STT',
            'M?? lo???i ngh???',
            'T??n lo???i ngh???',
            'Tr???ng th??i'
        ];
    }
    public function mapAbsent($row){
        $this->index++;
        return [
            $this->index,
            $row->code,
            $row->name,
            ($row->status == 1) ? 'B???t' : 'T???t',
        ];
    }

    public function headingsDiscipline(){
        $this->count_title = 4;
        return [
            'STT',
            'M?? vi ph???m',
            'T??n vi ph???m',
            'Tr???ng th??i'
        ];
    }
    public function mapDiscipline($row){
        $this->index++;
        return [
            $this->index,
            $row->code,
            $row->name,
            ($row->status == 1) ? 'B???t' : 'T???t',
        ];
    }

    public function headingsAbsentReason(){
        $this->count_title = 4;
        return [
            'STT',
            'M?? l?? do v???ng m???t',
            'T??n l?? do v???ng m???t',
            'Tr???ng th??i'
        ];
    }
    public function mapAbsentReason($row){
        $this->index++;
        return [
            $this->index,
            $row->code,
            $row->name,
            ($row->status == 1) ? 'B???t' : 'T???t',
        ];
    }

    public function headingsQuizType(){
        $this->count_title = 2;
        return [
            'STT',
            'T??n',
        ];
    }
    public function mapQuizType($row){
        $this->index++;
        return [
            $this->index,
            $row->name,
        ];
    }

    public function headingsTrainingCost(){
        $this->count_title = 3;
        return [
            'STT',
            'T??n chi ph?? ????o t???o',
            'Lo???i chi ph??',
        ];
    }
    public function mapTrainingCost($row){
        $this->index++;
        switch ($row->type){
            case 1: $row->type = 'Chi ph?? t??? ch???c'; break;
            case 2: $row->type = 'Chi ph?? ph??ng ????o t???o'; break;
            case 3: $row->type = 'Chi ph?? ????o t???o b??n ngo??i'; break;
            case 4: $row->type = 'Chi ph?? gi???ng vi??n'; break;
        }

        return [
            $this->index,
            $row->name,
            $row->type,
        ];
    }

    public function headingsStudentCost(){
        $this->count_title = 3;
        return [
            'STT',
            'T??n chi ph?? h???c vi??n',
            'Tr???ng th??i',
        ];
    }
    public function mapStudentCost($row){
        $this->index++;
        return [
            $this->index,
            $row->name,
            ($row->status == 1) ? 'B???t' : 'T???t',
        ];
    }

    public function headingsCommitMonth(){
        $this->count_title = 4;
        return [
            'STT',
            'T???',
            '?????n',
            'Th??ng',
        ];
    }
    public function mapCommitMonth($row){
        $this->index++;
        return [
            $this->index,
            $row->min_cost,
            $row->max_cost,
            $row->month,
        ];
    }

    public function headingsTrainingTeacher(){
        $this->count_title = 5;
        return [
            'STT',
            'T??n gi???ng vi??n',
            'Email gi???ng vi??n',
            'S??? ??i???n tho???i',
            'Tr???ng th??i',
        ];
    }
    public function mapTrainingTeacher($row){
        $this->index++;
        return [
            $this->index,
            $row->name,
            $row->email,
            $row->phone,
            ($row->status == 1) ? 'B???t' : 'T???t',
        ];
    }

    public function headingsTeacherType(){
        $this->count_title = 3;
        return [
            'STT',
            'M?? lo???i gi???ng vi??n',
            'T??n lo???i gi???ng vi??n',
        ];
    }
    public function mapTeacherType($row){
        $this->index++;
        return [
            $this->index,
            $row->code,
            $row->name,
        ];
    }

    public function headingsTrainingPartner(){
        $this->count_title = 7;
        return [
            'STT',
            'M??',
            'T??n ?????i t??c',
            'Ng?????i li??n h???',
            '?????a ch???',
            'Email',
            'S??? ??i???n tho???i',
        ];
    }
    public function mapTrainingPartner($row){
        $this->index++;
        return [
            $this->index,
            $row->code,
            $row->name,
            $row->people,
            $row->address,
            $row->email,
            $row->phone,
        ];
    }

    public function headingsProvince(){
        $this->count_title = 3;
        return [
            'STT',
            'M??',
            'T??n t???nh th??nh',
        ];
    }
    public function mapProvince($row){
        $this->index++;
        return [
            $this->index,
            $row->id,
            $row->name,
        ];
    }

    public function headingsDistrict(){
        $this->count_title = 4;
        return [
            'STT',
            'M?? qu???n huy???n',
            'T??n qu???n huy???n',
            'T???nh th??nh',
        ];
    }
    public function mapDistrict($row){
        $this->index++;
        return [
            $this->index,
            $row->id,
            $row->name,
            $row->province,
        ];
    }
}
