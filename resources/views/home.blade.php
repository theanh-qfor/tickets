@extends('layouts.one-column')
@push('scripts')
<script src="{{url('/build/dist/bootstrap-table.min.js')}}"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.2/js/select2.min.js"></script>

@endpush
@push('styles')
<link rel="stylesheet" href="{{url('/build/dist/bootstrap-table.min.css')}}"/>
<link href="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.2/css/select2.min.css" rel="stylesheet" />
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
                               data-search="true"
                               data-is-engineer="{{Auth::user()->role=='engineer'}}"
                               data-ajax-url="{{url('/')}}"
                        >

                        </table>
                        <div type="text/template" class="container-fluid" id="toolbar">
                            <div class="row">
                                <div class="col-xs-5">
                                    <select class="form-control" id="status-filter">
                                        <option value="" disabled="disabled">Status...</option>
                                        <option value="">All</option>
                                        @forelse($statuses as $status)
                                            <option {{$status}}>{{$status}}</option>
                                            @empty
                                        @endforelse
                                    </select>
                                </div>
                                <div class="col-xs-5">
                                    <select class="form-control" id="importance-filter">
                                        <option value="" disabled="disabled">Importance...</option>
                                        <option value="">All</option>
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
    <button type="button" class="btn btn-info btn-lg{{{ (Auth::user()->role=='engineer') ? ' hide' : '' }}}" data-toggle="modal" data-target="#myModal" id="add-ticket">Add Ticket</button>

    <div id="myModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                {!! Form::open(['url' => '/add_tickets', 'class' => 'form-horizontal']) !!}
                <div class="modal-header">
                    <button type="button" id="close" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Add Ticket</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        {!! Form::label('id-label', 'ID:', ['class' => 'col-lg-2 control-label']) !!}
                        <div class="col-lg-4">
                            {!! Form::text('id', $value = null, ['class' => 'form-control id', 'disabled']) !!}
                        </div>
                        <input type="hidden" class="hidden-id" name="id" value="">
                        {!! Form::label('date-label', 'Date:', ['class' => 'col-lg-2 control-label']) !!}
                        <div class="col-lg-4">
                            {!! Form::text('date', $value = date('m/d/Y'), ['class' => 'form-control date', 'disabled']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('subject-label', 'Subject:', ['class' => 'col-lg-2 control-label can-be-cleared']) !!}
                        <div class="col-lg-10">
                            {!! Form::text('subject', $value = null, ['class' => 'form-control subject', 'placeholder' => 'Subject']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('description-label', 'Description', ['class' => 'col-lg-2 control-label']) !!}
                        <div class="col-lg-10">
                            {!! Form::textarea('description', $value = null, ['class' => 'form-control description', 'rows' => 4]) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('importance-label', 'Importance:', ['class' => 'col-lg-2 control-label']) !!}
                        <div class="col-lg-4">
                            {!! Form::select('importance', array('low' => 'Low', 'normal' => 'Normal', 'high' => 'High', 'urgent' => 'Urgent'), null, array('class' => 'form-control importance')) !!}
                        </div>
                        {!! Form::label('status-label', 'Status:', ['class' => 'col-lg-2 control-label']) !!}
                        <div class="col-lg-4">
                            {!! Form::select('status', array('new' => 'New', 'under review' => 'Under review', 'assigned' => 'Assigned', 'question' => 'Question', 'answer' => 'Answer', 'resolved' => 'Resolved', 'cancelled' => 'Cancelled', 'closed' => 'Closed'), null, array('class' => 'form-control status')) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        <a class="col-lg-4" id="browse" href="javascript:;" data-href="{{url('/upload')}}">Upload Files</a>
                        <br />
                        <div>
                            <ul id="filelist"></ul>
                            <div class="file-array"></div>
                        </div>
                    </div>

                    <div class="comment-container">
                        <h4 class="comment-title">Comment</h4>
                        <div class="comment-box col-lg-12">
                            {!! Form::textarea('comments', $value = null, ['class' => 'form-control comment', 'placeholder' => 'Leave Comments Here...', 'rows' => 4]) !!}
                            <button type="button" class="btn btn-default" id="comment-post" data-href="{{url('/post_comment')}}">Post</button>
                        </div>
                        <div class="more-comments">
                        <div class="comment-array"></div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    {!! Form::submit('Save', ['class' => 'btn btn-default'] ) !!}
                    <button type="button" class="btn btn-default" id="close" data-dismiss="modal">Close</button>
                </div>
                {!! Form::close()  !!}
            </div>

        </div>
    </div>
</div>
@endsection
