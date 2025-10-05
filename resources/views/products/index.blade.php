@extends('layouts.master')

@section('Title')
  Item list
@endsection

@section('content')
<h1>Product List</h1>
<ul>
    @foreach ($products as $product)
        <li>
            <a href="{{ url('product/' . $product->id) }}">{{ $product->name }}</a>
        </li>
    @endforeach
</ul>

<!-- Create new product button -->
<p>
  <a href="{{ url('product/create') }}" class="btn btn-primary">Create New Product</a>
</p>

{{ $products ->links()}}

@endsection
