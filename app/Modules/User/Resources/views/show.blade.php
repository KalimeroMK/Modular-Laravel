@extends('user::layouts.master')

@section('content')
<div class="container">
<h1>Show User</h1>

<div class="form-group">
    <label for="name">Name</label>
    <input type="text" name="name" id="name" class="form-control" value="{{ $user->name }}" readonly>
</div>
</div>
@endsection
