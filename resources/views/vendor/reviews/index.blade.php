@extends('layouts.vendor')
@section('title','Reviews')
@section('page-title','Reviews')
@section('content')
<div class="space-y-4">
    @forelse($reviews as $review)
    <div class="bg-white rounded-xl border border-gray-100 p-5">
        <div class="flex items-start justify-between mb-3">
            <div>
                <p class="font-semibold text-slate-800">{{ $review->product->name }}</p>
                <p class="text-sm text-slate-400">by {{ $review->user->name }} · {{ $review->created_at->format('M d, Y') }}</p>
            </div>
            <div class="flex">
                @for($i=1;$i<=5;$i++)
                <svg class="w-4 h-4 {{ $i<=$review->rating?'text-yellow-400':'text-gray-200' }}" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                </svg>
                @endfor
            </div>
        </div>
        @if($review->body)<p class="text-sm text-slate-600 mb-3">{{ $review->body }}</p>@endif
        @if(!$review->vendor_reply)
        <form method="POST" action="{{ route('vendor.reviews.reply', $review) }}" class="flex gap-2">
            @csrf
            <input type="text" name="reply" placeholder="Write a reply..." required
                class="flex-1 px-4 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-orange-500">
            <button type="submit" class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white font-semibold rounded-xl text-sm transition-colors">Reply</button>
        </form>
        @else
        <div class="pl-4 border-l-2 border-orange-200 bg-orange-50 rounded-r-lg p-3 text-sm text-slate-600">
            <p class="text-xs font-semibold text-orange-600 mb-1">Your reply</p>
            {{ $review->vendor_reply }}
        </div>
        @endif
    </div>
    @empty
    <div class="text-center py-16 bg-white rounded-xl border border-gray-100">
        <p class="text-slate-400">No reviews yet</p>
    </div>
    @endforelse
    <div>{{ $reviews->links() }}</div>
</div>
@endsection
