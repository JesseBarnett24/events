@extends('layouts.master')

@section('Title')
  Edit Product
@endsection

@section('content')
<h1>Edit Product</h1>

<form method="POST" action='{{ url("product/$product->id") }}'>
    @csrf
    @method('PUT')

    <p>
      <label>Name:</label>
      <input type="text" name="name" value="{{ old('name', $product->name) }}">
    </p>

    <p>
      <label>Price:</label>
      <input type="text" name="price" value="{{ old('price', $product->price) }}">
    </p>

    <p>
      <label>Manufacturer:</label>
      <select name="manufacturer">
        @foreach ($manufacturers as $manufacturer)
            @if ($manufacturer->id === $product->manufacturer_id)
                <option value="{{ $manufacturer->id }}" selected>{{ $manufacturer->name }}</option>
            @else
                <option value="{{ $manufacturer->id }}">{{ $manufacturer->name }}</option>
            @endif
        @endforeach
      </select>
    </p>

    <input type="submit" value="Update">
</form>
@endsection
