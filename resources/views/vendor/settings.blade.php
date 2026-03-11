@extends('layouts.vendor')
@section('title','Store Settings')
@section('page-title','Store Settings')
@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-xl border border-gray-100 p-6">
        <form method="POST" action="{{ route('vendor.settings.update') }}" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Store Name</label>
                    <input type="text" name="store_name" value="{{ old('store_name', $vendor->store_name) }}"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-orange-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Description</label>
                    <textarea name="description" rows="4" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 resize-none">{{ old('description', $vendor->description) }}</textarea>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Phone</label>
                        <input type="text" name="phone" value="{{ old('phone', $vendor->phone) }}"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-orange-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">City</label>
                        <input type="text" name="city" value="{{ old('city', $vendor->city) }}"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-orange-500">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Logo</label>
                    <input type="file" name="logo" accept="image/*" class="text-sm text-slate-500">
                </div>
            </div>
            <button type="submit" class="mt-6 px-6 py-2.5 bg-orange-500 hover:bg-orange-600 text-white font-semibold rounded-xl text-sm transition-colors">
                Save Settings
            </button>
        </form>
    </div>
</div>
@endsection
