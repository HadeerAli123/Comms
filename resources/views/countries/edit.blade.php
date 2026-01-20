@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Edit Country</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif
    <form action="{{ route('countries.update', $country) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label>الاسم</label>
            <input type="text" name="name" class="form-control" required value="{{ old('name', $country->name) }}">
        </div>
        <button class="btn btn-success">تحديث</button>
    </form>
</div>
@endsection
