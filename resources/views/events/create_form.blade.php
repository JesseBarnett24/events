@extends('layouts.master')

@section('Title')
  Add Item
@endsection


@section('content')
<h1>Create Product</h1>
  <form method="post" action="{{ url('product') }}" enctype="multipart/form-data">
    {{csrf_field()}}
    <p>
      <label>Name: </label>
      <input type="text" name="name">
    </p>    
    <p>
      <label>Price: </label>
      <input type="text" name="price">
    </p>
    <p>
      <select name="manufacturer">
        @foreach ($manufacturers as $manufacturer)
          <option value="{{$manufacturer->id}}">{{$manufacturer->name}}</option>
        @endforeach
      </select>
    </p>
    <p><input type="file" name="image"></p>
    <input type="submit" value="Create">
  </form>
@endsection
