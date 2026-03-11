@extends('layouts.app')
@section('title', $vendor->store_name)
@section('content')
<div class="max-w-7xl mx-auto px-4 py-10">
    <div class="bg-white rounded-2xl border border-gray-100 p-8 mb-8 flex items-center gap-6">
        <img src="{{ $vendor->logo_url }}" class="w-20 h-20 rounded-2xl object-cover border border-gray-100">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900" style="font-family:'Syne',sans-serif;">{{ $vendor->store_name }}</h1>
            @if($vendor->description)<p class="text-slate-500 mt-1 max-w-xl">{{ $vendor->description }}</p>@endif
            <div class="flex items-center gap-4 mt-2 text-sm text-slate-400">
                <span>{{ $products->total() }} products</span>
                @if($vendor->average_rating > 0)<span>⭐ {{ number_format($vendor->average_rating, 1) }}</span>@endif
            </div>
        </div>
    </div>
    @if($products->isEmpty())
    <div class="text-center py-16 bg-white rounded-2xl border border-gray-100">
        <p class="text-slate-400">This vendor has no products yet.</p>
    </div>
    @else
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
        @foreach($products as $product)
            @include('components.product-card', ['product' => $product])
        @endforeach
    </div>
    <div class="mt-6">{{ $products->links() }}</div>
    @endif
</div>
@endsection
