@extends('admin.app')
@php
    $title = 'Edit Profile';
    $sub_title = 'Profile';
@endphp
@section('title', $title)
@section('content')
    <div class="d-flex flex-column flex-column-fluid">

        <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
            <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                    <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                        {{ $title }}</h1>
                    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                        <li class="breadcrumb-item text-muted">
                            <a class="text-muted text-hover-primary">{{ $sub_title }}</a>
                        </li>
                        <li class="breadcrumb-item">
                            <span class="bullet bg-gray-400 w-5px h-2px"></span>
                        </li>
                        <li class="breadcrumb-item text-muted">{{ $title }}</li>
                    </ul>
                </div>
        
            </div>
        </div>
        <div id="kt_app_content" class="app-content flex-column-fluid">
            <div id="kt_app_content_container" class="app-container container-xxl">
                <div class="card">
                    <div class="card-body p-lg-17">


                        <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            {{-- Name --}}
                            <div class="mb-3">
                                <label for="name" class="form-label">الاسم</label>
                                <input type="text" name="name" class="form-control" id="name"
                                    value="{{ old('name', $user->name) }}" required>
                            </div>

                            {{-- Email --}}
                            <div class="mb-3">
                                <label for="email" class="form-label">الايميل</label>
                                <input type="email" name="email" class="form-control" id="email"
                                    value="{{ old('email', $user->email) }}" required>
                            </div>

                            {{-- Password --}}
                            <div class="mb-3">
                                <label for="password" class="form-label">كلمة المرور الجديدة</label>
                                <input type="password" name="password" class="form-control" id="password"
                                    placeholder="Leave empty if you don't want to change">
                                <div class="form-text">اتركه فارغًا إذا كنت لا تريد تغيير كلمة المرور.</div>
                            </div>

                            {{-- Avatar --}}
                            <div class="mb-3">
                                <label for="avatar" class="form-label">صورة الملف الشخصي</label>
                                <input type="file" name="avatar" class="form-control" id="avatar" accept="image/*">
                                @if ($user->avatar)
                                    <div class="mt-2">
                                        <img src="{{ asset('storage/' . $user->avatar) }}" alt="Avatar"
                                            class="rounded-circle" width="80" height="80">
                                    </div>
                                @endif
                            </div>

                            <button type="submit" class="btn btn-primary">تحديث الملف الشخصي</button>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
