<?php
namespace Modules\ReportNew\Export;

use App\Config;
use App\LogoModel;
use App\Models\Categories\Titles;
use App\Models\Categories\Unit;
use App\Models\Categories\UnitType;
use App\Profile;
use App\ProfileStatus;
use App\Scopes\CompanyScope;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Modules\ReportNew\Entities\BC15;
use Modules\TrainingRoadmap\Entities\TrainingRoadmap;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class BC15Export implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize,  WithEvents, WithStartRow, WithDrawings
{
    use Exportable;
    protected $index = 0;
    protected $count = 0;
    protected $column = 1;
    public function __construct($param)
    {
        $this->from_date = $param->from_date;
        $this->to_date = $param->to_date;
        $this->status_id = $param->status_id;
        $this->title_id = $param->title_id;
    }

    public function query()
    {
        $query = BC15::sql($this->title_id, $this->status_id, $this->from_date, $this->to_date)->orderBy('full_name', 'asc');

        $this->count = $query->count();
        return $query;
    }
    public function map($row): array
    {
        $unit = Unit::whereCode($row->unit1_code)->first();
        $unit_type = UnitType::find(@$unit->type);

        $obj = [];
        $this->index++;
        $subjects =json_decode($row->subject,true);
            $obj[] = $this->index;
            $obj[]=$row->profile_code;
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
            $obj[] = @$unit_type->name;
            $obj[]=$row->position;
            $obj[]=$row->title;
            $obj[]=get_date($row->join_company);

        $profile = Profile::find($row->user_id);
        switch ($profile->status){
            case 0:
                $status = trans('backend.inactivity'); break;
            case 1:
                $status = trans('backend.doing'); break;
            case 2:
                $status = trans('backend.probationary'); break;
            case 3:
                $status = trans('backend.pause'); break;
        }

        $row->status = $status;

            $obj[]=$row->status;
        $this->column=13;
            foreach ($subjects as $index => $subject) {
                $obj[]= $subject['type'];
                $this->column++;
            }
        return $obj;
    }

    public function headings(): array
    {
        $subjects = \DB::table('el_trainingroadmap as a')->join('el_subject as b','a.subject_id','=','b.id')->select('b.code','b.name')
            ->where(['a.title_id'=>$this->title_id])->orderBy('a.order')->get();
        $title = Titles::findOrFail($this->title_id)->name;
        $status = $this->status_id>0? ProfileStatus::findOrFail($this->status_id)->name:'';
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
            'Lo???i ??V',
            'Ch???c v???',
            'Ch???c danh',
            'Ng??y v??o l??m',
            'T??nh tr???ng',

        ];
        foreach ($subjects as $index => $subject) {
            array_push($colHeader,$subject->name.PHP_EOL.'('.$subject->code.')');
        }
//        $condition =[];
//        $condition[] = 'Th???i gian :'. (($this->from_date && $this->to_date) ? getdate($this->from_date).' - '.getdate($this->to_date):'');
//        $condition[] = '';
//        $condition[] = '';
//        $condition[] = '';
//        dd( strtotime(str_replace('/','-',$this->to_date)));
        return [
            [],
            [],
            [],
            [],
            [],
            ['B??O C??O T???NG H???P K???T QU??? THEO TH??P ????O T???O'],
            ['Th???i gian : '. (($this->from_date && $this->to_date) ? get_date($this->from_date).' - '.get_date($this->to_date):'')],
            ['Ch???c danh : '.$title],
            ['Tr???ng th??i : '.$status],
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

                $columnName = $event->sheet->getDelegate()->getColumnDimensionByColumn($this->column);
                $event->sheet->getDelegate()->getStyle('A11:'.$columnName->getColumnIndex().'11')
                    ->applyFromArray($title)
                    ->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('FFFF00');

                $event->sheet->getDelegate()->getStyle('A11:'.$columnName->getColumnIndex().(11 + $this->index))
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
}
