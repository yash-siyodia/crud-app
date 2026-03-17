@extends('layouts.app')

@section('content')
    <h2>Product Details</h2>
    <p><strong>Name:</strong> {{ $product->name }}</p>
    <p><strong>Price:</strong> {{ $product->price }}</p>
    <p><strong>Description:</strong> {{ $product->description }}</p>
@endsection
