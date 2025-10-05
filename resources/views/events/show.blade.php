@extends('layouts.master')

@section('Title')
  Item list
@endsection

@section('content')
    <h1>{{$product->name}}</h1>
    <img src="{{ url($product->image) }}" alt="product image" style="width:300px;height:300px;">
    <p>{{$product->price}}</p>
    <p>{{$product->manufacturer->name}}</p>
    <p><a href=' {{url("product/$product->id/edit")}}'>Edit</a></p>
    <p>
        <form method="POST" action= '{{url("product/$product->id")}}'>
            {{csrf_field()}}
            {{ method_field('DELETE') }}
            <input type="submit" value="Delete">
        </form>
    </p>
@endsection


