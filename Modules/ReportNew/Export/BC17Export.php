<?php
namespace Modules\ReportNew\Export;

use App\Config;
use App\LogoModel;
use App\Models\Categories\Area;
use App\Models\Categories\Titles;
use App\Models\Categories\TrainingLocation;
use App\Models\Categories\TrainingType;
use App\Models\Categories\Unit;
use App\Models\Categories\TrainingPartner;

use App\Scopes\CompanyScope;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Modules\Offline\Entities\OfflineCourse;
use Modules\Offline\Entities\OfflineSchedule;
use Modules\ReportNew\Entities\BC17;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class BC17Export implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize,  WithEvents, WithStartRow, WithDrawings
{
    use Exportable;
    protected $index = 0;
    protected $count = 0;
    protected $column = 1;
    public function __construct($param)
    {
        $this->from_date = $param->from_date;
        $this->to_date = $param->to_date;
        $this->area_id = $param->area_id;
        $this->title_id = $param->title_id;
        $this->unit_id = $param->unit_id;
        $this->training_type_id = $param->training_type_id;
    }

    public function query()
    {
        $query = BC17::sql($this->title_id, $this->unit_id,$this->area_id,$this->training_type_id, $this->from_date, $this->to_date)->orderBy('full_name', 'asc');

        $this->count = $query->count();
        return $query;
    }
    public function map($row): array
    {
        $offline = OfflineCourse::find($row->course_id);
        $unit_name = [];
        !empty($offline->training_unit) ? $training_unit = json_decode($offline->training_unit) : $training_unit = [];
        if($offline->training_unit_type == 0 && !empty($training_unit)) {
            $units = Unit::where('status', '=', 1)->get();
            foreach ($units as $key => $unit) {
                if(in_array($unit->id, $training_unit)) {
                    $unit_name[] = $unit->name;
                }
            }
        } else if ($offline->training_unit_type == 1 && !empty($training_unit)) {
            $training_partners = TrainingPartner::get();
            foreach ($training_partners as $key => $training_partner) {
                if(in_array($training_partner->id, $training_unit)) {
                    $unit_name[] = $training_partner->name;
                }
            }
        }
        $row->training_unit = !empty($unit_name) ? implode(',',$unit_name) : '';

        $offline = OfflineCourse::find($row->course_id);
        $row->course_time = @$offline->course_time;
        $row->training_address = @TrainingLocation::find(@$offline->training_location_id)->name;

        $schedules = OfflineSchedule::query()
            ->select(['a.end_time', 'a.lesson_date'])
            ->from('el_offline_schedule as a')
            ->where('a.course_id', '=', $row->course_id)
            ->get();

        foreach ($schedules as $schedule){
            if (get_date($schedule->end_time, 'H:i:s') <= '12:00:00'){
                $row->time_schedule .= 'S??ng '. get_date($schedule->lesson_date) .'; ';
            }else{
                $row->time_schedule .= 'Chi???u '. get_date($schedule->lesson_date) .'; ';
            }
        }

        $obj = [];
        $this->index++;
        $obj[] = $this->index;
        $obj[]=$row->user_code;
        $obj[]=$row->full_name;
        $obj[]=$row->email;
        $obj[]=$row->phone;
        $obj[]=$row->area;
        // $obj[]=$row->unit1_code;
        $obj[]=$row->unit1_name;
        // $obj[]=$row->unit2_code;
        $obj[]=$row->unit2_name;
        // $obj[]=$row->unit3_code;
        // $obj[]=$row->unit3_name;
        $obj[]=$row->position_name;
        $obj[]=$row->titles_name;
        $obj[]=$row->training_program_name;
        $obj[]=$row->subject_name;
        $obj[]=$row->course_code;
        $obj[]=$row->course_name;
        $obj[]=$row->training_unit;
        $obj[]=$row->training_type;
        $obj[]=$row->training_address;
        $obj[]=$row->course_time;
        $obj[]=get_date($row->start_date);
        $obj[]=get_date($row->end_date);
        $obj[]=$row->time_schedule;
        $obj[]= number_format($row->cost_held, 2);
        $obj[]= number_format($row->cost_training, 2);
        $obj[]= number_format($row->cost_external, 2);
        $obj[]= number_format($row->cost_teacher, 2);
        $obj[]= number_format($row->cost_student, 2);
        $obj[]= number_format($row->cost_total, 2);
        $obj[]=$row->time_commit;
        $obj[]=get_date($row->from_time_commit).' - '.get_date($row->to_time_commit);
        $obj[]=$row->time_rest;
        $obj[]= number_format($row->cost_refund, 2);
        return $obj;
    }

    public function headings(): array
    {
        $title = Titles::find($this->title_id);
        $area = Area::find($this->area_id);
        $unit = Unit::find($this->unit_id);
        $trainingType = TrainingType::find($this->training_type_id);
        $colHeader= [
            'STT',
            'M?? nh??n vi??n',
            'H??? v?? t??n',
            'Email',
            '??i???n tho???i',
            'Khu v???c',
            // 'M?? ????n v??? c???p 1',
            '????n v??? tr???c ti???p',
            // 'M?? ????n v??? c???p 2',
            '????n v??? qu???n l??',
            // 'M?? ????n v??? c???p 3',
            // '????n v??? c???p 3',
            'Ch??c v???',
            'Ch???c danh',
            'T??n ch????ng tr??nh',
            'T??n chuy??n ?????',
            trans('lacourse.course_code'),
            trans('lacourse.course_name'),
            '????n v??? ????o t???o',
            'H??nh th???c ????o t???o',
            '?????a ??i???m ????o t???o',
            'Th???i l?????ng kh??a',
            'T??? ng??y',
            '?????n ng??y',
            'Th???i gian',
            'B??nh qu??n chi ph?? t??? ch???c',
            'B??nh qu??n chi ph?? ph??ng ????o t???o',
            'B??nh qu??n chi ph?? b??n ngo??i',
            'B??nh qu??n chi ph?? gi???ng vi??n',
            'B??nh qu??n chi ph?? h???c vi??n',
            'T???ng chi ph??',
            'S??? ng??y cam k???t',
            'Th???i gian cam k???t',
            'Th???i h???n c??n',
            'Chi ph?? b???i ho??n (?????ng)'
        ];
        return [
            [],
            [],
            [],
            [],
            [],
            ['DANH S??CH X??C NH???N B???I HO??N CHI PH?? ????O T???O ?????I V???I CBNV T??N TUY???N'],
            ['Th???i gian : '. (($this->from_date && $this->to_date) ? get_date($this->from_date).' - '.get_date($this->to_date):'')],
            ['Ch???c danh : '.@$title->name],
            ['V??ng : '.@$area->name],
            ['????n v??? : '.@$unit->name],
            ['Lo???i h??nh ????o t???o : '.@$trainingType->name],
            [],
            $colHeader
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

                $event->sheet->getDelegate()->mergeCells('A6:Z6')
                ->getStyle('A6')
                ->applyFromArray($title);

                $event->sheet->getDelegate()->getStyle('A13:AE13')
                    ->applyFromArray($title)
                    ->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('FFFF00');

                $event->sheet->getDelegate()->getStyle('A13:AE'.(13 + $this->index))
                    ->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ],
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                        ],
                    ])->getAlignment()->setWrapText(true);
            },

        ];
    }
    public function startRow(): int
    {
        return 12;
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
}
