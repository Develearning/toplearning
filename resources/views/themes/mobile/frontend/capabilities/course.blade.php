@extends('themes.mobile.layouts.app')

@section('page_title', trans('app.competence'))

@section('header')
    <link rel="stylesheet" href="{{ asset('styles/module/capabilities/css/capabilities.css') }}">
@endsection

@section('content')
    <div class="container" id="course-review">
        @php
            $review_last = \Modules\Capabilities\Entities\CapabilitiesResult::getLastReviewUser($user->user_id);
            $course_standard = \Modules\Capabilities\Entities\CapabilitiesResult::getCourseStandard($user->user_id);
            $course_need_add = \Modules\Capabilities\Entities\CapabilitiesResult::getCourseNeedAdditional($user->user_id);
            $percent =  \Modules\Capabilities\Entities\CapabilitiesResult::getPercent($user->user_id);
        @endphp
        <div class="row">
            <div class="col-md-12">
                <h6 class="text-center pb-2">{{trans('backend.training_program')}}</h6>
                <div class="card shadow border-0 mb-3">
                    <div class="card-body">
                        <div class="form-group row">
                            <label class="control-label col-4"><b>{{trans('backend.fullname')}}</b></label>
                            <div class="col-8">
                                {{ $user->lastname .' '. $user->firstname .' ('. $user->code .')' }}
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label col-4"><b>{{ trans('backend.unit') }}</b></label>
                            <div class="col-8">
                                @if(isset($unit->name)) {{ $unit->name }} @endif
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label col-4"><b>{{ trans('backend.title') }}</b></label>
                            <div class="col-8">
                                @if(isset($title->name)) {{ $title->name }} @endif
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label col-4"><b>Tr???ng th??i</b></label>
                            <div class="col-8">
                                @if($percent == 0)
                                    ??ang l??n l??? tr??nh c???i thi???n
                                @elseif($percent == 100)
                                    ???? ????p ???ng y??u c???u
                                @else
                                    ??ang ph??t tri???n
                                @endif
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label col-4"><b>Ng??y c???p nh???t ????nh gi??</b></label>
                            <div class="col-8">
                                {{ get_date($review_last->updated_at, 'd/m/Y') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <p class="border-bottom">TI???N TR??NH ????O T???O THEO N??NG L???C</p>
                <div class="progress progress2">
                    <div class="progress-bar w-{{$percent}}" role="progressbar" style="width: {{ $percent }}%" aria-valuenow="{{ $percent }}"
                         aria-valuemin="0" aria-valuemax="100">
                        {{ $percent }}%
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <p class="border-bottom mt-3">BI???U ????? PH??T TRI???N N??NG L???C D???A THEO KH??A H???C</p>
                <div id="chart-capability"></div>
            </div>

            <div class="col-md-12">
                <h6 class="text-center mt-3">CHI TI???T C??C KH??A H???C THEO N??NG L???C C???A B???N</h6>

                <p>CHI TI???T C??C KH??A H???C THEO N??NG L???C C???A CH???C DANH HI???N T???I</p>
                <table class="tDefault table table-bordered table-review">
                    <thead>
                    <tr>
                        <th></th>
                        <th>T??n n??ng l???c y??u c???u</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if($course_standard)
                        @foreach($course_standard as $key => $item)
                            @php
                                $result = \Modules\Capabilities\Entities\CapabilitiesResult::getCourseComplete($user->user_id, $item->course_id, $user->user_id);
                            @endphp
                            <tr>
                                <td>@if($result) <i class="fa fa-check text-success"></i> @endif</td>
                                <td class="text-left">
                                    {{ $item->capabilities_name .' - C???p ????? '. $item->standard_level .' ('. $item->capabilities_code . ')'  }} <br>
                                    <span class="text-info">NH??M: {{ $item->group_name }}</span> <br>
                                    <span class="text-danger">KH??A: {{ $item->course_name }}</span>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                    </tbody>
                </table>

                <p>CHI TI???T C??C KH??A H???C C???N B??? SUNG CHO N??NG L???C HI???N T???I</p>
                <table class="tDefault table table-bordered table-review">
                    <thead>
                    <tr>
                        <th></th>
                        <th>T??n n??ng l???c y??u c???u</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if($course_need_add)
                        @foreach($course_need_add as $key => $item)
                            @php
                                $result = \Modules\Capabilities\Entities\CapabilitiesResult::getCourseComplete($user->user_id, $item->course_id, $user->user_id);
                            @endphp
                            <tr>
                                <td>@if($result) <i class="fa fa-check text-success"></i> @endif</td>
                                <td class="text-left">
                                    {{ $item->capabilities_name .' - C???p ????? '. $item->standard_level .' ('. $item->capabilities_code . ')'  }} <br>
                                    <span class="text-info">NH??M: {{ $item->group_name }}</span> <br>
                                    <span class="text-danger">KH??A: {{ $item->course_name }}</span>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
@section('footer')
    <script type="text/javascript">
        /* charts */
        google.charts.load('current', {'packages': ['corechart']});
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {
            var jsonData = $.ajax({
                type: "POST",
                url: "{{ route('module.capabilities.review.user.chart_course', ['user_id' => $user->user_id]) }}",
                dataType: "json",
                async: false,
                data: {
                    "_token": "{{ csrf_token() }}",
                },
            }).responseText;

            jsonData = JSON.parse(jsonData);
            var data = google.visualization.arrayToDataTable(jsonData);

            var options = {
                title: '',
                curveType: 'function',
                legend: {position: 'top'}
            };

            var chart = new google.visualization.LineChart(document.getElementById('chart-capability'));

            chart.draw(data, options);
        }
    </script>
@endsection
