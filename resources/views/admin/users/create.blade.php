@extends('layouts.admin')

@section('page-title', 'Add User')

@section('content')
    <div class="">
        <div class="mb-5">
            <a href="{{ route('admin.users.index') }}" class="text-sm text-slate-500 hover:text-slate-700">&larr; Back to
                Users</a>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 p-5">
            <form method="POST" action="{{ route('admin.users.store') }}">
                @include('admin.users._form')
                <div class="mt-6 flex justify-end gap-3">
                    <a href="{{ route('admin.users.index') }}"
                        class="rounded-lg border border-slate-300 text-sm font-medium px-5 py-2.5 text-slate-600 hover:bg-slate-50">Cancel</a>
                    <button type="submit"
                        class="rounded-lg bg-indigo-600 hover:bg-indigo-500 text-sm font-medium px-5 py-2.5 text-white shadow-sm">Create
                        User</button>
                </div>
            </form>
        </div>
    </div>
@endsection
