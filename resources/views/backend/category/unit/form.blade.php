@extends('layouts.backend')

@section('page_title', $page_title)

@section('breadcrumb')
    <div class="forum-container">
        @include('layouts.backend.breadcum')
    </div>
@endsection

@section('content')
<div class="forum-container">
    <h2 class="st_title"><i class="uil uil-apps"></i>
        {{ trans('backend.management') }} <i class="uil uil-angle-right"></i>
        <a href="{{ route('backend.category') }}">{{ trans('backend.category') }}</a>
        <i class="uil uil-angle-right"></i>
        <a href="{{ route('backend.category.unit', ['level' => $level]) }}">{{ data_locale($name->name, $name->name_en) }}</a>
        <i class="uil uil-angle-right"></i>
        <span class="">{{ $page_title }}</span>
    </h2>
</div>
<div role="main">
    <form method="post" action="{{ route('backend.category.unit.save', ['level' => $level]) }}" class="form-validate form-ajax " role="form" enctype="multipart/form-data">
        <input type="hidden" name="id" value="{{ $model->id }}">
        <input type="hidden" name="level" value="{{ $level }}">
        <div class="row">
            <div class="col-md-8"></div>
            <div class="col-md-4 text-right">
                <div class="btn-group act-btns">
                    @canany(['category-unit-create', 'category-unit-edit'])
                        <button type="submit" class="btn btn-primary" data-must-checked="false"><i class="fa fa-save"></i> &nbsp;{{ trans('backend.save') }}</button>
                    @endcanany
                    <a href="{{ route('backend.category.unit', ['level' => $level]) }}" class="btn btn-warning"><i class="fa fa-times-circle"></i> {{ trans('backend.cancel') }}</a>
                </div>
            </div>
        </div>

        <div class="clear"></div>

        <br>
        <div class="tPanel">
            <ul class="nav nav-pills mb-4" role="tablist" id="mTab">
                <li class="nav-item">
                    <a href="#base" class="nav-link active" role="tab" data-toggle="tab">{{ trans('backend.info') }}</a>
                </li>
                <li class="nav-item">
                    <a href="#object" class="nav-link" data-toggle="tab">{{ trans('backend.management') }}</a>
                </li>
            </ul>
            <div class="tab-content">
                <div id="base" class="tab-pane active">
                    @include('backend.category.unit.form.info')
                </div>

                <div id="object" class="tab-pane">
                    @include('backend.category.unit.form.manager')
                </div>
            </div>
        </div>
    </form>
</div>

@stop
