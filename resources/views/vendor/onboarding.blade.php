@extends('layouts.app')
@section('title','Become a Vendor')
@section('content')
<div class="max-w-2xl mx-auto px-4 py-16">
    <div class="text-center mb-10">
        <h1 class="text-3xl font-extrabold text-slate-900 mb-3" style="font-family:'Syne',sans-serif;">Open your store today</h1>
        <p class="text-slate-500">Fill in your store details below. Our team will review and approve your application.</p>
    </div>
    <div class="bg-white rounded-2xl border border-gray-100 p-8">
        <form method="POST" action="{{ route('vendor.apply.store') }}">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Store Name <span class="text-red-500">*</span></label>
                    <input type="text" name="store_name" value="{{ old('store_name') }}" required
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 @error('store_name') border-red-300 @enderror">
                    @error('store_name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Store Description</label>
                    <textarea name="description" rows="4" placeholder="Tell customers about your store..."
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 resize-none">{{ old('description') }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Phone Number</label>
                    <input type="text" name="phone" value="{{ old('phone') }}"
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-orange-500">
                </div>
            </div>
            <button type="submit" class="mt-6 w-full py-3.5 bg-orange-500 hover:bg-orange-600 text-white font-bold rounded-xl transition-colors">
                Submit Application
            </button>
        </form>
    </div>
</div>
@endsection
