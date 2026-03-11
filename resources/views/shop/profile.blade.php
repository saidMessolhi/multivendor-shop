@extends('layouts.app')
@section('title','My Profile')
@section('content')
<div class="max-w-3xl mx-auto px-4 py-10">
    <h1 class="text-2xl font-extrabold text-slate-900 mb-8" style="font-family:'Syne',sans-serif;">My Profile</h1>
    <div class="bg-white rounded-2xl border border-gray-100 p-8 mb-6">
        <h2 class="font-bold text-slate-800 mb-5">Personal Information</h2>
        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="grid grid-cols-1 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Name</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-orange-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Email</label>
                    <input type="email" value="{{ $user->email }}" disabled
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm bg-gray-50 text-slate-400">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Phone</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-orange-500">
                </div>
            </div>
            <button type="submit" class="mt-5 px-6 py-2.5 bg-orange-500 hover:bg-orange-600 text-white font-semibold rounded-xl text-sm transition-colors">
                Save Changes
            </button>
        </form>
    </div>
    <div class="bg-white rounded-2xl border border-gray-100 p-8">
        <h2 class="font-bold text-slate-800 mb-5">Change Password</h2>
        <form method="POST" action="{{ route('profile.password') }}">
            @csrf @method('PUT')
            <div class="grid grid-cols-1 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Current Password</label>
                    <input type="password" name="current_password" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-orange-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">New Password</label>
                    <input type="password" name="password" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-orange-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Confirm New Password</label>
                    <input type="password" name="password_confirmation" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-orange-500">
                </div>
            </div>
            <button type="submit" class="mt-5 px-6 py-2.5 bg-slate-800 hover:bg-slate-900 text-white font-semibold rounded-xl text-sm transition-colors">
                Update Password
            </button>
        </form>
    </div>
</div>
@endsection
