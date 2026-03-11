@extends('layouts.app')
@section('title','Wishlist')
@section('content')
<div class="max-w-5xl mx-auto px-4 py-10">
    <h1 class="text-2xl font-extrabold text-slate-900 mb-8" style="font-family:'Syne',sans-serif;">My Wishlist</h1>
    @if($items->isEmpty())
    <div class="text-center py-20 bg-white rounded-2xl border border-gray-100">
        <div class="text-5xl mb-4">❤️</div>
        <h3 class="text-lg font-bold text-slate-800 mb-2">Your wishlist is empty</h3>
        <a href="{{ route('shop') }}" class="inline-block mt-4 px-6 py-3 bg-orange-500 text-white font-semibold rounded-xl">Browse Products</a>
    </div>
    @else
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
        @foreach($items as $item)
            @include('components.product-card', ['product' => $item->product])
        @endforeach
    </div>
    @endif
</div>
@endsection
