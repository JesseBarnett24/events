@extends('layouts.master')

@section('Title')
  Item list
@endsection

@section('content')
@if ($items)
  @foreach($items as $item)
    <p><a href="{{url("item_detail/$item->id")}}">{{$item->summary}}</a></p>
  @endforeach
@else 
  No item found
@endif
@endsection


