<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\PlanAppItem
 *
 * @property int $id
 * @property string $name
 * @property string|null $criteria_1
 * @property string|null $criteria_2
 * @property string|null $criteria_3
 * @property string|null $result
 * @property string|null $finish
 * @property int|null $sort
 * @property int $user_id
 * @property int $cate_id
 * @property int $plan_app_id
 * @property int $course_id
 * @property int $course_type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PlanAppItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PlanAppItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PlanAppItem query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PlanAppItem whereCateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PlanAppItem whereCourseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PlanAppItem whereCourseType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PlanAppItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PlanAppItem whereCriteria1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PlanAppItem whereCriteria2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PlanAppItem whereCriteria3($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PlanAppItem whereFinish($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PlanAppItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PlanAppItem whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PlanAppItem wherePlanAppId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PlanAppItem whereResult($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PlanAppItem whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PlanAppItem whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PlanAppItem whereUserId($value)
 * @mixin \Eloquent
 */
class PlanAppItem extends Model
{
    protected $table = 'el_plan_app_item';
    protected $fillable = [
        'name',
    ];
    protected $primaryKey = 'id';

    public static function getAttributeName() {
        return [
            'name' => 'T??n m???c ti??u',
            'criteria_1' => 'Ti??u ch?? 1',
            'criteria_2' => 'Ti??u ch?? 2',
            'criteria_3' => 'Ti??u ch?? 3',
            'result' => 'K???t qu??? ?????t ???????c',
            'finish' => 't??? l??? ho??n th??nh',
            'sort' => 'th??? t???',
            'user_id' => 'M?? user_id',
            'cate_id' => 'M?? nh??m ????? m???c',
            'plan_app_id' => 'M?? template',
            'course_id' => 'M?? kh??a h???c',
            'course_type' => 'Lo???i kh??a h???c'
        ];
    }
}
