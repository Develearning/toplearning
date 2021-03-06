<?php

namespace App\Models\Categories;

use App\BaseModel;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Categories\TrainingTeacher
 *
 * @property int $id
 * @property int|null $user_id
 * @property int|null $teacher_type_id
 * @property string $name
 * @property string $email
 * @property string $phone
 * @property int $status
 * @property int $type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Categories\TrainingTeacher newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Categories\TrainingTeacher newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Categories\TrainingTeacher query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Categories\TrainingTeacher whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Categories\TrainingTeacher whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Categories\TrainingTeacher whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Categories\TrainingTeacher whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Categories\TrainingTeacher wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Categories\TrainingTeacher whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Categories\TrainingTeacher whereTeacherTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Categories\TrainingTeacher whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Categories\TrainingTeacher whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Categories\TrainingTeacher whereUserId($value)
 * @mixin \Eloquent
 * @property int|null $training_partner_id
 * @property string $code
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Categories\TrainingTeacher whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Categories\TrainingTeacher whereTrainingPartnerId($value)
 */
class TrainingTeacher extends BaseModel
{
    protected $table = 'el_training_teacher';
    protected $fillable = [
        'user_id',
        'teacher_type_id',
        'training_partner_id',
        'code',
        'name',
        'email',
        'phone',
        'status',
        'type',
        'account_number',
    ];
    protected $primaryKey = 'id';

    public static function getAttributeName() {
        return [
            'user_id' => 'Gi???ng vi??n n???i b???',
            'teacher_type_id' => 'Lo???i gi???ng vi??n',
            'code' => 'M?? gi???ng vi??n',
            'name' => 'T??n gi???ng vi??n',
            'email' => 'Email gi???ng vi??n',
            'phone' => 'S??? ??i???n tho???i gi???ng vi??n',
            'status' => 'Tr???ng th??i',
            'type' => 'H??nh th???c'
        ];
    }

    public static function checkExists($name)
    {
        $query = self::query();
        $query->where('name', '=', $name);
        return $query->exists();
    }

    public static function getTeacherSelect2($request)
    {
        $search = $request->search;
        $query = self::where('status','=',1);
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
