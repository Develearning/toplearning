<?php

namespace Modules\News\Entities;

use App\BaseModel;
use Illuminate\Database\Eloquent\Model;

/**
 * Modules\News\Entities\NewsCategory
 *
 * @property int $id
 * @property string $name
 * @property int|null $parent_id
 * @property int $created_by
 * @property int $updated_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\News\Entities\NewsCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\News\Entities\NewsCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\News\Entities\NewsCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\News\Entities\NewsCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\News\Entities\NewsCategory whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\News\Entities\NewsCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\News\Entities\NewsCategory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\News\Entities\NewsCategory whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\News\Entities\NewsCategory whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\News\Entities\NewsCategory whereUpdatedBy($value)
 * @mixin \Eloquent
 */
class NewsCategory extends BaseModel
{
    protected $table = 'el_news_category';
    protected $fillable = [
        'name',
        'parent_id',
        'sort',
        'status',
        'stt_sort',
        'stt_sort_parent',
        'created_by',
        'updated_by'
    ];
    protected $primaryKey = 'id';
    public static function getCourseCategoriesParent($exclude_id = 0, $parent_id = null, $type = 0, $prefix = '', &$result = []) {
        $query = self::query();
        $query->where('parent_id', '=', $parent_id);
        if ($type) {
            $query->where('type', '=', $type);
        }

        $rows = $query->get();
        foreach ($rows as $row) {
            if ($row->id == $exclude_id) continue;
            $result[] = ['id' => $row->id, 'name' => $prefix.' '. $row->name];

            self::getCourseCategoriesParent($exclude_id, $row->id, $type, $prefix.'--', $result);
        }

        return $result;
    }
    public static function getAttributeName() {
        return [
            'name' => 'T??n danh m???c',
            'parent_id'=>'Danh m???c cha',
            'created_by'=>'Ng??y t???o',
            'updated_by'=>'Ng??y s???a',
            'sort'=>'S???p x???p',
            'stt_sort'=>'S??? th??? t??? s???p x???p',
            'stt_sort_parent'=>'S??? th??? t??? s???p x???p cha'
        ];
    }

    public function child(){
        return $this->hasMany(NewsCategory::class, 'parent_id', 'id');
    }
}
