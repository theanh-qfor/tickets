@extends('layouts.one-column')
@push('scripts')
<script src="{{url('/build/dist/bootstrap-table.min.js')}}"></script>
@endpush
@push('styles')
<link rel="stylesheet" href="{{url('/build/dist/bootstrap-table.min.css')}}"/>
@endpush
@section('title')
    Tickets
@endsection
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="panel panel-default">
                    <div class="panel-heading">Dashboard</div>

                    <div class="panel-body">
                        <table id="list-tickets-table"
                               data-toolbar="#toolbar"
                               data-url="{{url('/tickets')}}"
                               data-height="400"
                               data-side-pagination="server"
                               data-pagination="true"
                               data-page-list="[5, 10, 20, 50, 100, 200]"
                               data-search="true">

                        </table>
                        <div type="text/template" class="container-fluid" id="toolbar">
                            <div class="row">
                                <div class="col-xs-5">
                                    <select class="form-control" id="status">
                                        <option value="">Status...</option>
                                        @forelse($statuses as $status)
                                            <option {{$status}}>{{$status}}</option>
                                            @empty
                                        @endforelse
                                    </select>
                                </div>
                                <div class="col-xs-5">
                                    <select class="form-control" id="importance">
                                        <option>Importance...</option>
                                        @forelse($importances as $importance)
                                            <option {{$importance}}>{{$importance}}</option>
                                            @empty
                                        @endforelse
                                    </select>
                                </div>
                                <div class="col-xs-2">
                                    <button id="remove" class="btn btn-danger" disabled>
                                        <i class="glyphicon glyphicon-remove"></i> Delete
                                    </button>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
