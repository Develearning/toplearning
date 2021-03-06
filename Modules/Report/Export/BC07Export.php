<?php
namespace Modules\Report\Export;
use App\Config;
use App\Models\Categories\Unit;
use App\Profile;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Modules\Report\Entities\BC07;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class BC07Export implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize,  WithEvents, WithStartRow, WithDrawings
{
    use Exportable;
    protected $index = 0;
    private $course;
    public function __construct($param)
    {
        $this->course = $param->course;
    }

    public function query()
    {
        $query = BC07::sql($this->course)->orderBy('id');
        return $query;
    }

    public function map($report): array
    {
        $profile = Profile::find($report->user_id);
        $arr_unit = Unit::getTreeParentUnit($profile->unit_code);

        $this->index++;
        return [
            $this->index,
            $report->code,
            $report->full_name,
            $report->title_name,
            $arr_unit ? $arr_unit[$profile->unit->level]->name : '',
            $arr_unit ? $arr_unit[$profile->unit->level - 1]->name : '',
            $arr_unit ? $arr_unit[2]->name : '',
            $report->email,
            $report->score ? number_format($report->score,2) : null,
            $report->pass,
            $report->fail,
            $report->commit_date,
            $report->note,
        ];
    }

    public function headings(): array
    {
        $course = BC07::getCourseInfo($this->course);

        $course_time = preg_replace("/[^0-9]./", '', $course->course_time);
        $course_time_unit = preg_replace("/[^a-z]/", '', $course->course_time);
        switch ($course_time_unit){
            case 'day': $time_unit = 'Ng??y'; break;
            case 'session': $time_unit = 'Bu???i'; break;
            default : $time_unit = 'Gi???'; break;
        }

        return [
            [],
            [],
            [],
            [],
            [],
            ['B??O C??O K???T QU??? KH??A H???C T???P TRUNG'],
            [trans('lacourse.course_code').': ', $course->code],
            [trans('lacourse.course_name').': ', $course->name],
            ['H??nh th???c: ', 'T???p trung'],
            ['Th???i l?????ng: ', $course_time ? ($course_time . ' ' . $time_unit) : ''],
            ['Th???i gian: ', get_date($course->start_date).' - '.get_date($course->end_date)],
            ['?????a ??i???m: ', $course->training_location],
            ['Chi ph??: ', $course->cost_class],
            [
            'STT',
            'M?? HV',
            'H??? v?? t??n',
            'Ch???c danh',
            '????n v??? tr???c ti???p',
            '????n v??? gi??n ti???p 1',
            'C??ng ty',
            'Email',
            '??i???m',
            '?????t',
            'Kh??ng ?????t',
            'S??? ng??y cam k???t',
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
                        'outline' => [
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
                    ->getStyle("A".($this->startRow()-1).":M".($this->startRow()-1))
                    ->applyFromArray($header)
                    ->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('FFFF00');

                $event->sheet->getDelegate()->mergeCells('A6:M6')->getStyle('A6')->applyFromArray($title);
                $event->sheet->getDelegate()->getStyle('A14:M'.(14 + $this->index))
                    ->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                'color' => ['argb' => '000000'],
                            ],

                        ],
                        'font' => [
                            'name' => 'Arial',
                            'size' =>  12,
                        ],
                    ]);
            },

        ];
    }

    public function startRow(): int
    {
        return 15;
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
