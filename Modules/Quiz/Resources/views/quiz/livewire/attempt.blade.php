<div>
    <div class="row" id="quiz-content" xmlns:wire="http://www.w3.org/1999/xhtml">

        <div class="col-md-3">
            {{--@include('quiz::quiz.component.sidebar')--}}
        </div>

        <div class="col-md-9 quiz-{{ $attempt->quiz_id }}">
            @if($current_page <= $total_page)
            <form id="form-question" wire:submit.prevent="submit">
                <div class="card">
                    <div class="card-header">
                        <div class="text-center mb-1 button-page">
                            <button type="submit" class="btn btn-info" @if($current_page - 1 <= 0) disabled @endif><i class="fa fa-mail-reply"></i> {{ trans('backend.back') }}</button> |
                            <button type="submit" class="btn btn-info">{{ trans('backend.next') }} <i class="fa fa-mail-forward"></i></button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="questions">
                            @foreach($questions as $question)
                                @livewire('quiz::livewire.attempt.question', ['question' => $question], key($question['id']))
                            @endforeach
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="text-center mt-1 button-page">
                            <button type="button" class="btn btn-info" @if($current_page - 1 <= 0) disabled @endif><i class="fa fa-mail-reply"></i> {{ trans('backend.back') }}</button> |
                            <button type="button" class="btn btn-info">{{ trans('backend.next') }} <i class="fa fa-mail-forward"></i></button>
                        </div>
                    </div>

                    <div id="loading"></div>
                </div>
            </form>
            @endif
            @if($current_page > $total_page)
            <form action="" method="post" class="form-ajax text-center">
                <div class="card">
                    <div class="card-header">
                        Ch??c m???ng b???n ???? ho??n th??nh k??? thi <b>{{ $quiz->name }}</b>
                    </div>
                    <div class="card-body">
                        @if(!$attempt_finish)
                            <p>????? n???p b??i vui l??ng nh???n n??t <b>N???p b??i thi</b></p>
                            <p>????? xem l???i b??i thi vui l??ng nh???n n??t <b>Xem l???i b??i</b></p>
                        @else
                            <p>B??i thi c???a b???n ???? ???????c n???p, nh???n n??t <b>Xem l???i b??i</b> ????? xem l???i b??i l??m c???a m??nh</p>
                        @endif
                        <p></p>
                        <button type="button" class="btn btn-info" wire:click="backPage"><i class="fa fa-mail-reply"></i> Xem l???i b??i</button>
                        @if($attempt_finish)
                            <a href="{{ route('module.quiz.doquiz.index', [ 'quiz_id' => $quiz->id, 'part_id' => $part->id]) }}" class="btn btn-info"><i class="fa fa-mail-reply"></i> Tr??? v??? m??n h??nh k??? thi</a>
                        @endif
                        @if(!$attempt_finish)
                            <button class="btn btn-primary" wire:click="submit"><i class="fa fa-send-o"></i> N???p b??i thi</button>
                        @endif
                    </div>
                </div>
            </form>
            @endif
        </div>
    </div>
</div>