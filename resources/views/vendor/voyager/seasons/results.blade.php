@extends('voyager::master')

@section('page_title', __('voyager::generic.viewing').' '.$season->title)

@section('page_header')
    <div class="container-fluid">
        <h1 class="page-title">
            {{$season->title}}
        </h1>
    </div>
@stop

@section('content')
    <div class="page-content browse container-fluid">
        @include('voyager::alerts')
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered">
                    <div class="panel-body">
                       SEASON RESULTS
                    </div>
                </div>
            </div>
        </div>
    </div>

@stop

@section('css')

@stop

@section('javascript')

@stop
