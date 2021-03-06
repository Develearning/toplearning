<a class="nav-item nav-link @if ($tabs == 'info')
    active
    @endif" id="nav-about-tab" href="{{ route('module.frontend.user.info') }}" >@lang('app.info')
</a>

<a class="nav-item nav-link @if ($tabs == 'roadmap')
    active
    @endif" id="nav-reviews-tab" href="{{ route('module.frontend.user.roadmap') }}" >@lang('backend.roadmap')
</a>

<a class="nav-item nav-link @if ($tabs == 'training-by-title')
    active
    @endif" id="nav-reviews-tab" href="{{ route('module.frontend.user.training_by_title') }}">Lộ trình đào tạo
</a>

<a class="nav-item nav-link @if ($tabs == 'trainingprocess')
    active
    @endif" id="nav-courses-tab" href="{{ route('module.frontend.user.trainingprocess') }}">@lang('backend.training_process')
</a>

<a class="nav-item nav-link @if ($tabs == 'quizresult')
    active
    @endif" id="nav-purchased-tab" href="{{ route('module.frontend.user.quizresult') }}">@lang('backend.quiz_result')
</a>

<a class="nav-item nav-link @if ($tabs == 'my-career-roadmap')
    active
    @endif" id="nav-reviews-tab" href="{{ route('module.frontend.user.my_career_roadmap') }}" >@lang('career.career_roadmap')
</a>

<a class="nav-item nav-link @if ($tabs == 'subjectregister')
    active
@endif" id="nav-reviews-tab" href="{{ route('module.frontend.user.subjectregister') }}" >@lang('app.subject_registered')</a>

<a class="nav-item nav-link @if ($tabs == 'student-cost')
    active
@endif" id="nav-reviews-tab" href="{{ route('module.frontend.user.student_cost') }}" >{{ trans('backend.student_cost') }}</a>

<a class="nav-item nav-link @if ($tabs == 'violate-rules')
    active
@endif" id="nav-reviews-tab" href="{{ route('module.frontend.user.violate_rules') }}" >{{ trans('app.violate_rules') }}</a>

<a class="nav-item nav-link @if ($tabs == 'my-promotion')
    active
@endif" id="nav-reviews-tab" href="{{ route('module.frontend.user.my_promotion') }}" >{{ trans('app.promotion') }}</a>

<a class="nav-item nav-link @if ($tabs == 'point-hist')
    active
@endif" id="nav-reviews-tab" href="{{ route('module.frontend.user.point_hist') }}" >{{ trans('app.history_point') }}</a>