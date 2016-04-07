@extends('layouts.one-column')

@section('content')
<div class="container">
    <div class="col-md-10 col-md-offset-1">
        <div class="panel panel-default">
            <div class="panel-heading">Dashboard</div>

            <div class="panel-body">
                You are logged in!
            </div>
        </div>
    </div>
    <button type="button" class="btn btn-info btn-lg" data-toggle="modal" data-target="#myModal">Open Modal</button>

    <div id="myModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                {!! Form::open(['url' => '/processform', 'class' => 'form-horizontal']) !!}
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Modal Header</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        {!! Form::label('id-label', 'ID:', ['class' => 'col-lg-2 control-label']) !!}
                        <div class="col-lg-4">
                            {!! Form::text('id', $value = null, ['class' => 'form-control', 'disabled']) !!}
                        </div>
                        {!! Form::label('date-label', 'Date:', ['class' => 'col-lg-2 control-label']) !!}
                        <div class="col-lg-4">
                            {!! Form::text('date', $value = null, ['class' => 'form-control', 'disabled']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('subject-label', 'Subject:', ['class' => 'col-lg-2 control-label']) !!}
                        <div class="col-lg-10">
                            {!! Form::text('subject', $value = null, ['class' => 'form-control', 'placeholder' => 'Subject']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('description-label', 'Description', ['class' => 'col-lg-2 control-label']) !!}
                        <div class="col-lg-10">
                            {!! Form::textarea('description', $value = null, ['class' => 'form-control', 'rows' => 4]) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('importance-label', 'Importance:', ['class' => 'col-lg-2 control-label']) !!}
                        <div class="col-lg-4">
                            {!! Form::select('importance', array('low' => 'Low', 'normal' => 'Normal', 'high' => 'High', 'urgent' => 'Urgent')) !!}
                        </div>
                        {!! Form::label('status-label', 'Status:', ['class' => 'col-lg-2 control-label']) !!}
                        <div class="col-lg-4">
                            {!! Form::select('status', array('new' => 'New', 'under review' => 'Under review', 'assigned' => 'Assigned', 'question' => 'Question', 'answer' => 'Answer', 'resolved' => 'Resolved', 'cancelled' => 'Cancelled', 'closed' => 'Closed')) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('upload', 'Upload:', ['class' => 'col-lg-2 control-label']) !!}
                        <div class="col-lg-6">
                            {!! Form::file('file'); !!}
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    {!! Form::submit('Save', ['class' => 'btn btn-default'] ) !!}
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
                {!! Form::close()  !!}
            </div>

        </div>
    </div>
</div>
@endsection
