@extends('layouts.one-column')
@push('scripts')
<script src="/build/dist/bootstrap-table.min.js"></script>
@endpush
@push('styles')
<link rel="stylesheet" href="/build/dist/bootstrap-table.min.css"/>
@endpush
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">Dashboard</div>

                <div class="panel-body">
                    <table id="table"
                           data-toggle="table"
                           data-url="{{url('/tickets')}}"
                           data-height="400"
                           data-side-pagination="server"
                           data-pagination="true"
                           data-page-list="[5, 10, 20, 50, 100, 200]"
                           data-search="true">
                        <thead>
                        <tr>
                            <th data-field="state" data-checkbox="true"></th>
                            <th data-field="id" data-sortable="true">ID</th>
                            <th data-field="subject">Item Name</th>
                            <th data-field="status">Status</th>
                            <th data-field="importance">Importance</th>
                            <th data-field="date">Date</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
