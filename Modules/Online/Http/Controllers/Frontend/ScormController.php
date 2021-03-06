<?php

namespace Modules\Online\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Online\Entities\ActivityScormAttemptData;
use Modules\Online\Entities\ActivityScormScore;
use Modules\Online\Entities\OnlineCourse;
use Modules\Online\Entities\OnlineCourseActivityScorm;
use Modules\Online\Entities\OnlineCourseActivity;
use Modules\Online\Entities\OnlineCourseLesson;
use Modules\Quiz\Entities\Quiz;
use Modules\Quiz\Entities\QuizPart;

class ScormController extends Controller
{
    public function index($course_id, $activity_id,$lesson) {
        $course = OnlineCourse::findOrFail($course_id);
        $activity_scorm = OnlineCourseActivityScorm::findOrFail($activity_id);

        //$get_lesson_activities = OnlineCourseActivity::where('lesson_id',$lesson)->get();
        $item = OnlineCourse::find($course_id);
        $lessons_course = OnlineCourseLesson::where('course_id',$course_id)->get();

        $part = function ($subject_id){
            $user_type = Quiz::getUserType();
            $item = QuizPart::where('quiz_id', '=', $subject_id)
                ->whereIn('id', function ($subquery) use ($user_type, $subject_id) {
                    $subquery->select(['a.part_id'])
                        ->from('el_quiz_register AS a')
                        ->join('el_quiz_part AS b', 'b.id', '=', 'a.part_id')
                        ->where('a.quiz_id', '=', $subject_id)
                        ->where('a.user_id', '=', getUserId())
                        ->where('a.type', '=', $user_type)
                        ->where(function ($where){
                            $where->orWhere('b.end_date', '>', date('Y-m-d H:i:s'));
                            $where->orWhereNull('b.end_date');
                        });
                })->first();
            return $item;
        };

        if (url_mobile()){
            return view('themes.mobile.frontend.online_course.scorm.index', [
                'course' => $course,
                'activity' => $activity_scorm,
                'title' => $activity_scorm->course_activity->name,
            ]);
        }
        return view('online::scorm.index', [
            'course' => $course,
            'activity' => $activity_scorm,
            'title' => $activity_scorm->course_activity->name,
            'course_id' => $course_id,
            'item' => $item,
            //'get_lesson_activities' => $get_lesson_activities,
            'part' => $part,
            'lesson_course' => $lesson,
            'lessons_course' => $lessons_course
        ]);
    }

    public function getDataAttempt($activity_id, Request $request) {
        $activity = OnlineCourseActivityScorm::findOrFail($activity_id);

        $user_id = $request->user_id ? $request->user_id : getUserId();
        $user_type = $request->user_type ? $request->user_type : getUserType();
        //$search = $request->input('search');
        $sort = $request->input('sort', 'id');
        $order = $request->input('order', 'desc');
        $offset = $request->input('offset', 0);
        $limit = $request->input('limit', 20);

        $query = $activity->attempts()
            ->where('user_id', '=', $user_id);

        $count = $query->count();
        $query->orderBy($sort, $order);
        $query->offset($offset);
        $query->limit($limit);
        $rows = $query->get();

        foreach ($rows as $row){
            $score_scorm = ActivityScormScore::query()
                ->where('user_id', '=', $user_id)
                ->where('user_type', '=', $user_type)
                ->where('activity_id', '=', $activity_id)
                ->where('attempt_id', '=', $row->id)
                ->first();

            if ($score_scorm){
                $row->end_date = get_date($score_scorm->created_at, 'H:i:s d/m/Y');
                if (!is_null($score_scorm->score)) {
                    $row->grade = number_format($score_scorm->score, 2);
                }
                else {
                    $row->grade = null;
                }
            }

            $row->start_date = get_date($row->created_at, 'H:i:s d/m/Y');

        }

        return response()->json([
            'total' => $count,
            'rows' => $rows
        ]);
    }

    public function play($course_id, $activity_id) {
        OnlineCourse::findOrFail($course_id);
        $user_id = getUserId();
        $user_type = getUserType();

        $activity = OnlineCourseActivityScorm::findOrFail($activity_id);

        switch ($activity->new_attempt_required) {
            case 1:
                /*
                 * N????u cho??n khi co?? k????t qua??.
                 * */

                $attempt = $activity->attempts()
                    ->where('user_id', '=', $user_id)
                    ->where('user_type', '=', $user_type)
                    ->orderBy('attempt', 'DESC')
                    ->first(['id']);

                if (empty($attempt)) {
                    $attempt = $activity->attempts()
                        ->create([
                            'user_id' => $user_id,
                            'user_type' => $user_type,
                            'attempt' => 1,
                        ]);
                }
                else {
                    $score_exists = $activity->scores()
                        ->where('user_id', '=', $user_id)
                        ->where('user_type', '=', $user_type)
                        ->where('attempt_id', '=', $attempt->id)
                        ->where('score', '>', 0)
                        ->exists();

                    if ($score_exists) {
                        $count_attempt = $activity->attempts()
                            ->where('user_id', '=', $user_id)
                            ->where('user_type', '=', $user_type)
                            ->count('id');

                        $attempt = $activity->attempts()
                            ->create([
                                'user_id' => $user_id,
                                'user_type' => $user_type,
                                'attempt' => $count_attempt + 1,
                            ]);
                    }
                }

                break;
            case 2:
                /*
                * N????u lu??n lu??n ta??o l????n th???? m????i
                * */
                $count_attempt = $activity->attempts()
                    ->where('user_id', '=', $user_id)
                    ->where('user_type', '=', $user_type)
                    ->count('id');

                if ($activity->max_attempt > 0) {
                    if ($count_attempt > $activity->max_attempt) {
                        return response()->json([
                            'status' => 'error',
                            'message' => 'Ba??n ??a?? h????t l????n ho??c ba??i ho??c na??y.',
                        ]);
                        // return '<div class="out_of_scorm"><h3>Ba??n ??a?? h????t l????n ho??c ba??i ho??c na??y.</h3></div>';
                    }
                }

                $attempt = $activity->attempts()
                    ->create([
                        'user_id' => $user_id,
                        'user_type' => $user_type,
                        'attempt' => $count_attempt + 1,
                    ]);

                break;
            default:
                /**
                 * N????u cho??n kh??ng => Lu??n va??o l????n th???? ??????u ti??n
                 * */
                $attempt = $activity->attempts()
                    ->where('user_id', '=', $user_id)
                    ->where('user_type', '=', $user_type)
                    ->first(['id']);

                if (empty($attempt)) {
                    $attempt = $activity->attempts()
                        ->create([
                            'user_id' => $user_id,
                            'user_type' => $user_type,
                            'attempt' => 1,
                        ]);
                }

                break;
        }

        // dd($attempt->id);
        /*$course = OnlineCourse::findOrFail($course_id);
        $activitys = OnlineCourseActivityScorm::findOrFail($activity_id);
        $attempts = $activitys->attempts()
            ->where('id', $attempt->id)
            ->firstOrFail(['id', 'suspend_data']);*/

        if (url_mobile()){
            return response()->json([
                'status' => 'success',
                'redirect' =>  route('themes.mobile.frontend.online.view_scorm', [
                    $course_id,
                    $activity_id,
                    $attempt->id,
                    'title' => $activity->course_activity->name,
                ])
            ]);
        }

        // return view('online::scorm.player', [
        //     'course' => $course,
        //     'activity' => $activitys,
        //     'attempt' => $attempts,
        //     'user' => Auth::user(),
        //     'title' => $activitys->course_activity->name,
        // ]);

        return response()->json([
            'status' => 'success',
            'redirect' => route('module.online.scorm.player', [
                $course_id,
                $activity_id,
                $attempt->id,
            ]),
        ]);
    }

    public function player($course_id, $activity_id, $attempt_id) {
        $course = OnlineCourse::findOrFail($course_id);
        $activity = OnlineCourseActivityScorm::findOrFail($activity_id);
        $attempt = $activity->attempts()
            ->where('id', $attempt_id)
            ->firstOrFail(['id', 'suspend_data']);

        return view('online::scorm.player', [
            'course' => $course,
            'activity' => $activity,
            'attempt' => $attempt,
            'user' => Auth::user(),
            'title' => $activity->course_activity->name,
        ]);
    }

    public function redirect(Request $request) {
        $scoid = $request->get('scoid');
        $activity = OnlineCourseActivityScorm::findOrFail($scoid);

        /**
         * Check go??i scorm ??a?? unzip tha??nh c??ng ch??a?
         * */
        $scorm = $activity->scorm;
        if (empty($scorm->unzip_path)) {
            if ($scorm->error) {
                \Log::error($scorm->error);
            }

            return view('online::scorm.message', [
                'message' => 'Go??i Scorm ch??a s????n sa??ng. Vui lo??ng quay la??i sau!',
            ]);
        }

        /**
         * Get url scorm ?????? play
         * */
        $storage = \Storage::disk(config('app.datafile.upload_disk'));
        $scorm_url = $storage->url($scorm->unzip_path) . '/' . $scorm->index_file;

        /**
         * N????u giao di????n mobile => Thay ??????i url datafile la?? url mobile ?????? cho phe??p embed
         * */
        if (url_mobile()){
            $scorm_url = str_replace(config('app.url'), config('app.mobile_url'), $scorm_url);
        }

        return redirect()->to($scorm_url);
    }

    public function checkNet(Request $request) {
        $scoid = $request->input('scoid');
        $attempt_id = $request->input('attempt');
        $activity = OnlineCourseActivityScorm::findOrFail($scoid);
        $attempt = $activity->attempts()->where('id', '=', $attempt_id)
            ->firstOrFail(['id']);

        $user_activity = $activity->users()->firstOrNew([
            'user_id' => getUserId(),
            'user_type' => getUserType(),
            'attempt_id' => $attempt->id,
        ]);

        $user_activity->touch();
        $user_activity->save();

        return response()->json([
            'status' => true,
        ]);
    }

    public function save(Request $request) {
        $scoid = $request->post('scoid');
        $attempt_id = $request->post('attempt');
        $varname = $request->post('varname');
        $varvalue = $request->post('varvalue');

        $activity = OnlineCourseActivityScorm::findOrFail($scoid);
        $attempt = $activity->attempts()
            ->where('id', '=', $attempt_id)
            ->firstOrFail(['id', 'lesson_location']);

        /**
         * Update lesson_location va?? suspend_data
         * lesson_location la?? slider ng??????i du??ng ??a?? ho??c ??????n
         * suspend_data la?? d???? li????u ghi nh???? ng??????i ho??c ??a?? ho??c ??????n slider na??o?
         * Khi co?? suspend_data se?? cho phe??p ng??????i du??ng chuy????n h??????ng ??????n ph????n ba??i ho??c tr??????c ??o??
         * */
        if ($varname == 'cmi.core.lesson_location') {
            $attempt->update([
                'lesson_location' => $varvalue,
            ]);
        }

        if ($varname == 'cmi.suspend_data') {
            $attempt->update([
                'suspend_data' => $varvalue,
            ]);
        }

        /**
         * Update data score
         * */
        $data = [];

        if ($varname == 'cmi.core.score.raw' || $varname == 'cmi.score.raw') {
            $data['score_raw'] = $varvalue;
        }

        if ($varname == 'cmi.core.score.max' || $varname == 'cmi.score.max') {
            $data['score_max'] = $varvalue;
        }

        if ($varname == 'cmi.core.score.min' || $varname == 'cmi.score.min') {
            $data['score_min'] = $varvalue;
        }

        if ($varname == 'cmi.core.lesson_status') {
            $data['status'] = $varvalue;
        }

        if ($varname == 'cmi.completion_status' && $varname == 'cmi.success_status'){
            $data['status'] = $varvalue. ', ' .$varvalue;
        }

        $user = $activity->scores()->firstOrNew([
            'user_id' => getUserId(),
            'user_type' => getUserType(),
            'attempt_id' => $attempt->id,
        ]);

        if ($user && $varname == 'cmi.core.score.raw') {
            /*
             * If set score_max in attempt user
             * */
            if ($user->score_max > 0) {
                /*
                * If setup scorm max_score and score.raw > 0
                * */
                if ($varvalue > 0 && $activity->max_score > 0) {
                    $per_score = $activity->max_score / $user->score_max;
                    $data['score'] = round($varvalue * $per_score, 2);
                }

                /**
                 * M????c ??i??nh khi go??i scorm ch??a tra?? ra ??i????m (cmi.core.score.raw)
                 * */

                /*else {

                    if ($attempt->activity_scorm->score_required || $attempt->activity_scorm->min_score_required) {
                        $data['score'] = $varvalue;
                    }
                }*/
            }
        }

        $user->fill($data);
        $user->save();

        $activity->scores()
            ->whereUserId(getUserId())
            ->where('user_type','=', getUserType())
            ->whereAttemptId($attempt->id)
            ->whereNull('score')
            ->where(function ($sub){
                $sub->orWhere('status', 'like', '%completed%');
                $sub->orWhere('status', 'like', '%passed%');
            })
        ->update([
            'score' => $activity->max_score
        ]);
        /**
         * Save scorm data
         * $varname t??n gia?? tri?? nh????n ????????c
         * $varvalue gia?? tri?? t????ng ????ng cu??a $varname
         * */
        ActivityScormAttemptData::updateOrCreate([
            'attempt_id' => $attempt_id,
            'var_name' => $varname,
        ], [
            'var_value' => $varvalue
        ]);

        return response()->json([
            'status' => true,
        ]);
    }
}
