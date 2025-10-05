@extends('layouts.master')

@section('Title')
  Result
@endsection

@section('content')
  <p>
    Hello {{$user}}.
    Next year, you will be {{$age}} years old.
  </p>
  <hr>
@endsection


