<?php
namespace Modules\Report\Export;
use App\Config;
use App\Models\Categories\Unit;
use App\Profile;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Modules\Offline\Entities\OfflineTeacher;
use Modules\Report\Entities\BC01;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class BC01Export implements FromQuery, WithHeadings, ShouldAutoSize, WithMapping, WithEvents, WithStartRow, WithDrawings
{
    use Exportable;
    protected $index = 0;

    public function __construct($param)
    {
        $this->from_date = $param->from_date;
        $this->to_date = $param->to_date;
        $this->user_id = $param->user_id;
    }
    public function map($report): array
    {
        $this->index++;
        $profile = Profile::find($report->user_id);
        $arr_unit = Unit::getTreeParentUnit($profile->unit_code);

        return [
            $this->index,
            $report->code,
            $report->lastname . ' ' .$report->firstname,
            $profile ? $profile->titles->name : '',
            $arr_unit ? $arr_unit[$profile->unit->level]->name : '',
            $arr_unit ? $arr_unit[$profile->unit->level - 1]->name : '',
            $arr_unit ? $arr_unit[2]->name : '',
            $report->course_code,
            $report->course_name,
            get_date($report->start_date, 'd/m/Y'),
            get_date($report->end_date, 'd/m/Y'),
            number_format($report->cost_commit,0,',','.'),
            number_format($report->cost_indemnify,0,',','.'),
            get_date($report->day_commit, 'd/m/Y'),
            $report->month_commit,
            $report->date_diff,
            $report->contract,
            get_date($report->day_off, 'd/m/Y'),
            '',
        ];
    }

    public function query()
    {
        $query = BC01::sql($this->user_id, $this->from_date, $this->to_date);
        $query->orderBy('c.id','asc');
        return $query;
    }
    public function headings(): array
    {
        return [
            [],
            [],
            ['DANH S??CH K?? CAM K???T B???I HO??N'],
            ['T??? '. $this->from_date. ' ?????n '. $this->to_date],
            [],
            [],
            [
                'STT',
                'M?? nh??n vi??n',
                'T??n nh??n vi??n',
                'Ch???c danh',
                '????n v??? tr???c ti???p',
                '????n v??? gi??n ti???p 1',
                'C??ng ty',
                trans('lacourse.course_code'),
                trans('lacourse.course_name'),
                'Ng??y b???t ?????u',
                'Ng??y k???t th??c',
                'S??? ti???n cam k???t',
                'S??? ti???n b???i ho??n',
                'Ng??y b???t ?????u cam k???t',
                'S??? th??ng cam k???t',
                'S??? th??ng c??n l???i',
                'S??? h???p ?????ng cam k???t',
                'Ng??y ngh???',
                'Ghi ch??',
            ]
        ];
    }
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $header = [
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],

                    ],
                    'font' => [
                        'size'      =>  12,
                        'name' => 'Arial',
                        'bold'      =>  true,
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                    ],
                ];
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
                $event->sheet->getDelegate()
                    ->getStyle("A7:S7")
                    ->applyFromArray($header)
                    ->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('FFFF00');

                $event->sheet->getDelegate()->mergeCells('A3:S3')->getStyle('A3')->applyFromArray($title);
                $event->sheet->getDelegate()->mergeCells('A4:S4')->getStyle('A4')->applyFromArray($title);
                $event->sheet->getDelegate()->getStyle('A7:S'.(7 + $this->index))
                    ->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['argb' => '000000'],
                            ],

                        ],
                        'font' => [
                            'size'      =>  12,
                            'name' => 'Arial',
                        ],
                    ]);
            },
        ];
    }
    public function startRow(): int
    {
        return 7;
    }

    public function drawings()
    {
        $storage = \Storage::disk('upload');
        if ($storage->exists(Config::getConfig('logo'))) {
            $path = $storage->path(Config::getConfig('logo'));
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
