<?php
namespace Modules\Report\Export;

use App\Config;
use App\Models\Categories\Titles;
use App\Models\Categories\Unit;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Modules\Report\Entities\BC24;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class BC24Export implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize,  WithEvents, WithStartRow, WithDrawings
{
    use Exportable;
    protected $index = 0;
    protected $count = 0;
    public function __construct($param)
    {
        $this->from_date = $param->from_date;
        $this->to_date = $param->to_date;
        $this->unit_id = $param->unit_id;
    }

    public function query()
    {
        $query = BC24::sql($this->unit_id, $this->from_date, $this->to_date)->orderBy('id', 'ASC');

        $this->count = $query->count();
        return $query;
    }
    public function map($report): array
    {
        $this->index++;

        $titles = array_values(json_decode($report->title,true));
        $arr_title = [];
        foreach ($titles as $item){
            $title = Titles::find($item);
            $arr_title[] = $title->name;
        }
        $title = implode(', ', $arr_title);

        return [
            $this->index,
            $report->unit_code,
            $report->unit_name,
            $report->subject_name,
            get_date($report->start_date, 'm/Y'),
            $report->content,
            $report->purpose,
            $report->duration,
            $title,
            $report->amount,
            $report->teacher,
            $report->attach ? 'x' : '',
            $report->note,
        ];
    }
    public function headings(): array
    {
        return [
            [],
            [],
            [],
            [],
            [],
            ['K??? HO???CH ????O T???O ????N V???'],
            ['T???: ' . $this->from_date .' - '. $this->to_date],
            [],
            [$this->unit_id ? ('????n v???: ' . Unit::find($this->unit_id)->name) : ''],
            [
                'STT',
                'M?? ????n v???',
                '????n v???',
                'Chuy??n ?????',
                'Th??ng',
                'N???i dung chuy??n ?????',
                'M???c ti??u ????o t???o',
                'Th???i l?????ng (Bu???i)',
                '?????i t?????ng',
                'S??? l?????ng',
                'Gi???ng vi??n',
                'T??i li???u',
                '????? ngh??? h??? tr??? t??? H???i s??? (n???u c??)'
            ]
        ];
    }
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $title = [
                    'font' => [
                        'size'      =>  12,
                        'bold'      =>  true,
                        'name' => 'Arial',
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                    ],
                ];
                $event->sheet->getDelegate()->mergeCells('A6:M6')->getStyle('A6')->applyFromArray($title);
                $event->sheet->getDelegate()->mergeCells('A7:M7')->getStyle('A7')->applyFromArray($title);
                $event->sheet->getDelegate()->mergeCells('A9:M9')->getStyle('A9')->applyFromArray($title);

                $event->sheet->getDelegate()->getStyle('A10:M10')
                    ->applyFromArray($title)
                    ->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('FFFF00');

                $event->sheet->getDelegate()->getStyle('A10:M'.(10 + $this->count))->applyFromArray([
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
        return 11;
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
