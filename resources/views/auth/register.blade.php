@extends('layouts.one-column')
@section('content')
    <form class="form-signin form-register" method="POST" action="/register">
        {!! csrf_field() !!}
        <h2 class="form-signin-heading">Register</h2>
        <label for="name" class="sr-only">Name</label>
        <input class="form-control" id="name" type="text" name="name" value="{{ old('name') }}" placeholder="Name"/>
        <label for="inputEmail" class="sr-only">Email address</label>
        <input type="email" name="email" id="inputEmail" class="form-control" placeholder="Email address" required
               autofocus>
        <label for="inputPassword" class="sr-only">Password</label>
        <input type="password" name="password" id="password" class="form-control" placeholder="Password" required>
        <label for="inputPasswordConfirm" class="sr-only">Confirm Password</label>
        <input type="password" name="password_confirmation" id="inputPasswordConfirm" class="form-control" placeholder="Retype above password" required>

        <button class="btn btn-lg btn-primary btn-block" type="submit">Register</button>
    </form>
@endsection