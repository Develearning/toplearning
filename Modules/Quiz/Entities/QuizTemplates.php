<?php

namespace Modules\Quiz\Entities;

use App\BaseModel;
use App\Profile;
use App\Traits\ChangeLogs;
use Illuminate\Database\Eloquent\Model;

/**
 * Modules\Quiz\Entities\Quiz
 *
 * @property int $id
 * @property string $code
 * @property string $name
 * @property int|null $unit_id
 * @property int $limit_time
 * @property int $view_result
 * @property int $shuffle_answers
 * @property int $shuffle_question
 * @property int $paper_exam
 * @property int $questions_perpage
 * @property float $pass_score
 * @property float $max_score
 * @property string|null $description
 * @property int $max_attempts
 * @property int $grade_methor
 * @property int $review
 * @property int $is_open
 * @property int $status
 * @property int $course_id
 * @property int $course_type
 * @property int $quiz_type
 * @property int $created_by
 * @property int $updated_by
 * @property \Modules\Quiz\Entities\QuizQuestion[]|null $questions
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Quiz\Entities\Quiz newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Quiz\Entities\Quiz newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Quiz\Entities\Quiz query()
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Quiz\Entities\Quiz whereAttempts($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Quiz\Entities\Quiz whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Quiz\Entities\Quiz whereCourseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Quiz\Entities\Quiz whereCourseType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Quiz\Entities\Quiz whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Quiz\Entities\Quiz whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Quiz\Entities\Quiz whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Quiz\Entities\Quiz whereGradeMethor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Quiz\Entities\Quiz whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Quiz\Entities\Quiz whereIsOpen($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Quiz\Entities\Quiz whereLimitTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Quiz\Entities\Quiz whereMaxScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Quiz\Entities\Quiz whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Quiz\Entities\Quiz wherePaperExam($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Quiz\Entities\Quiz wherePassScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Quiz\Entities\Quiz whereQuestionsPerpage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Quiz\Entities\Quiz whereQuizType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Quiz\Entities\Quiz whereReview($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Quiz\Entities\Quiz whereShuffleAnswers($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Quiz\Entities\Quiz whereShuffleQuestion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Quiz\Entities\Quiz whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Quiz\Entities\Quiz whereUnitId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Quiz\Entities\Quiz whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Quiz\Entities\Quiz whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Quiz\Entities\Quiz whereViewResult($value)
 * @mixin \Eloquent
 * @property int|null $type_id
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Quiz\Entities\Quiz whereTypeId($value)
 * @property string|null $start_date
 * @property string|null $end_date
 * @method static \Illuminate\Database\Eloquent\Builder|Quiz whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Quiz whereStartDate($value)
 * @property string|null $img
 * @method static \Illuminate\Database\Eloquent\Builder|Quiz whereImg($value)
 * @property-read \Illuminate\Database\Eloquent\Collection|\Modules\Quiz\Entities\QuizPart[] $parts
 * @property-read int|null $parts_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Modules\Quiz\Entities\QuizTeacher[] $teachers
 * @property-read int|null $teachers_count
 * @method static \Illuminate\Database\Eloquent\Builder|Quiz active()
 * @method static \Illuminate\Database\Eloquent\Builder|Quiz hasEndPart()
 * @property int $webcam_require
 * @property int $question_require
 * @property int $times_shooting_webcam
 * @property int $times_shooting_question
 * @property-read \Illuminate\Database\Eloquent\Collection|\Modules\Quiz\Entities\QuizAttempts[] $attempts
 * @property-read int|null $attempts_count
 * @property-read int|null $questions_count
 * @property-read \Modules\Quiz\Entities\QuizType|null $type
 * @method static \Illuminate\Database\Eloquent\Builder|Quiz whereMaxAttempts($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Quiz whereQuestionRequire($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Quiz whereTimesShootingQuestion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Quiz whereTimesShootingWebcam($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Quiz whereWebcamRequire($value)
 */
class QuizTemplates extends BaseModel
{
    use ChangeLogs;
    protected $table = 'el_quiz_templates';
    protected $primaryKey = 'id';
    protected $fillable = [
        'code',
        'name',
        'unit_id',
        'start_date',
        'end_date',
        'limit_time',
        'view_result',
        'shuffle_answers',
        'shuffle_question',
        'paper_exam',
        'questions_perpage',
        'is_open',
        'status',
        'pass_score',
        'max_score',
        'description',
        'max_attempts',
        'grade_methor',
        'created_by',
        'updated_by',
        'course_id',
        'course_type',
        'quiz_type',
        'type_id',
        'img',
        'webcam_require',
        'question_require',
        'times_shooting_webcam',
        'times_shooting_question',
        'approved_step',
    ];


    public function questions() {
        return $this->hasMany('Modules\Quiz\Entities\QuizQuestion', 'quiz_id', 'id');
    }

    public function type()
    {
        return $this->belongsTo(QuizType::class,'type_id');
    }
    /**
     * Scope a query to only quiz active.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query) {
        return $query->where('status', '=', 1);
    }

    /**
     * Scope a query to only quiz Finished (Enddate < now()).
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeHasEndPart($query) {
        return $query->whereHas('parts', function ($subquery) {
            $subquery->where(function ($where) {
                    $where->orWhere('end_date', '<', now());
                    $where->orWhereNull('end_date');
                });
        });
    }

    public static function getAttributeName() {
        return [
            'code' => 'M?? k??? thi',
            'name' => 'T??n k??? thi',
            'start_date' => 'Th???i gian b???t ?????u',
            'end_date' => 'Th???i gian k???t th??c',
            'limit_time' => 'Th???i gian l??m b??i',
            'view_result' => 'Xem k???t qu???',
            'shuffle_answers' => 'X??o tr???n ????p ??n',
            'shuffle_question' => 'X??o tr???n c??u h???i',
            'paper_exam' => 'Thi gi???y',
            'questions_perpage' => 'S??? c??u h???i 1 trang',
            'is_open' => trans('lacore.open'),
            'status' => 'Tr???ng th??i',
            'pass_score' => '??i???m chu???n',
            'max_score' => '??i???m t???i ??a',
            'description' => 'N???i dung',
            'max_attempts' => 'S??? l???n l??m b??i',
            'grade_methor' => 'C??ch t??nh ??i???m',
            'created_by' => trans('lageneral.creator'),
            'updated_by' => trans('lageneral.editor'),
            'course_id' => trans('lacourse.course_code'),
            'course_type' => 'Lo???i kh??a h???c',
            'quiz_type' => 'Lo???i k??? thi',
        ];
    }


    public static function getUserType() {
        if (\Auth::check()) {
            return 1;
        }

        if (\Auth::guard('secondary')->check()) {
            return 2;
        }

        return null;
    }

    public static function getUserId() {
        if (\Auth::check()) {
            return \Auth::id();
        }

        if (\Auth::guard('secondary')->check()) {
            return \Auth::guard('secondary')->id();
        }

        return null;
    }

    public static function countQuiz() {
        return self::where('quiz_type','=',3)->count();
    }

    public static function getLastestQuiz($limit = 5){
        $query = self::query();
        $query->orderBy('created_at', 'DESC');
        $query->limit($limit);
        return $query->get();
    }

}
