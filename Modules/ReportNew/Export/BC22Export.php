<?php
namespace Modules\ReportNew\Export;

use App\Config;
use App\LogoModel;
use App\Scopes\CompanyScope;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Modules\ReportNew\Entities\BC22;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class BC22Export implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize,  WithEvents, WithStartRow, WithDrawings
{
    use Exportable;
    protected $index = 0;
    protected $count = 0;
    protected $column = 1;
    public function __construct($param)
    {
        $this->from_date = $param->from_date;
        $this->to_date = $param->to_date;
        $this->type = $param->type;
    }

    public function query()
    {
        $query = BC22::sql($this->type,$this->from_date,$this->to_date);
        $this->count = $query->count();

        return $query;
    }
    public function map($row): array
    {

        $obj = [];
        $this->index++;
        $obj[] = $this->index;
        $obj[]=$row->subject_merge_split_code;
        $obj[]=$row->subject_merge_split_name;
        $obj[]=$row->subject_merges_splits;
        $obj[]=get_date($row->date_action);
        $obj[]=$row->user_code;
        $obj[]=$row->created_user;
        $obj[]=$row->email;
        $obj[]=$row->phone;
        $obj[]=$row->area_name;
//        $obj[]=$row->unit1_code;
        $obj[]=$row->unit1_name;
//        $obj[]=$row->unit2_code;
        $obj[]=$row->unit2_name;
//        $obj[]=$row->unit3_code;
//        $obj[]=$row->unit3_name;
        $obj[]=$row->position;
        $obj[]=$row->title;
        $obj[]=$row->note;
        return $obj;
    }

    public function headings(): array
    {
        if ($this->type=='1'){
            $title='DANH S??CH G???P CHUY??N ?????';
            $nameType='G???p chuy??n ?????';
        }
        else{
            $title ='DANH S??CH T??CH CHUY??N ?????';
            $nameType='T??ch chuy??n ?????';
        }

        $colHeader= [
            'STT',
            'M??',
            $this->type=='1'?'T??n chuy??n ????? m???i':'T??n chuy??n ????? c???n t??ch',
            $this->type=='1'?'Chuy??n ????? c???n g???p':'Chuy??n ????? m???i',
            'Ng??y g???p',
            'M?? nh??n vi??n',
            'H??? v?? t??n',
            'Email',
            '??i???n tho???i',
            'Khu v???c',
            // 'M?? ????n v??? 1',
            '????n v??? tr???c ti???p',
            // 'M?? ????n v??? 2',
            '????n v??? qu???n l??',
            // 'M?? ????n v??? 3',
            // '????n v??? 3',
            'Ch???c v???',
            'Ch???c danh',
            'Ghi ch??',
        ];
        return [
            [],
            [],
            [],
            [],
            [],
            [$title],
            [],
            ['T??? ng??y: '.get_date($this->from_date).' - '.get_date($this->to_date)],
            ['Lo???i: '.$nameType],
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

                $event->sheet->getDelegate()->mergeCells('A6:F6')
                ->getStyle('A6')
                ->applyFromArray($title);

                $event->sheet->getDelegate()->getStyle('A11:L11')
                    ->applyFromArray($title)
                    ->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('FFFF00');

                $event->sheet->getDelegate()->getStyle('A11:L11')
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
        return 11;
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
