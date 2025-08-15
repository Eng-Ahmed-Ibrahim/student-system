@extends('admin.app')
@php
    $title = 'المحظورين';
    $sub_title = 'الطلاب';
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
                <div class="d-flex align-items-center gap-2 gap-lg-3">

                    <a href="#" class="btn btn-sm fw-bold btn-primary" data-bs-toggle="modal"
                        data-bs-target="#kt_modal_create_app">Create</a>
                </div>
            </div>
        </div>
        <div id="kt_app_content" class="app-content flex-column-fluid">
            <div id="kt_app_content_container" class="app-container container-xxl">
                <div class="card">
                    <div class="card-body p-lg-17">



                        <div class=" mt-4">
                            <h2 class="mb-4">🚫 قائمة الطلاب المحظورين</h2>

                            @if ($blockedStudents->isEmpty())
                                <div class="alert alert-info text-center">
                                    لا يوجد طلاب محظورين حالياً
                                </div>
                            @else
                                <table class="table table-bordered ">
                                    <thead>
                                        <tr>
                                            <th>الكود</th>
                                            <th>الاسم</th>
                                            <th>مجموعه</th>
                                            <th>التليفون</th>
                                            <th>تليفون ولي الأمر</th>
                                            <th>سبب الحظر</th>
                                            <th>الغاء الحظر</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($blockedStudents as $student)
                                            <tr>
                                                <td><a
                                                        href="{{ route('admin.students.show', $student->id) }}">{{ $student->student_code }}</a>
                                                </td>
                                 

                                                <td>
                                                    <div style="width:120px">{{ $student->name }}</div>
                                                </td>
                                                <td>
                                                    <div style="width:120px">{{ $student->group->name }}</div>
                                                </td>
                                                <td>{{ $student->phone }}</td>
                                                <td>
                                                    <div style="width:120px">{{ $student->parent_phone }}</div>
                                                </td>
                                                <td>{{ $student->block_reason ?? 'غير محدد' }}</td>
                                                <td>

                                                    <button
                                                        class="btn btn-sm {{ $student->blocked ? 'btn-success' : 'btn-danger' }} toggle-block-btn"
                                                        data-id="{{ $student->id }}"
                                                        data-blocked="{{ $student->blocked ? 1 : 0 }}">
                                                        {{ $student->blocked ? 'إلغاء الحظر' : 'حظر' }}
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @endif
                        </div>


                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('js')
    <script>
        $(document).on('click', '.toggle-block-btn', function() {
            const studentId = $(this).data('id');
            const isBlocked = $(this).data('blocked');

            if (isBlocked) {
                // إلغاء الحظر مباشرة
                $.ajax({
                    url: '{{ route('admin.students.unblock') }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        student_id: studentId
                    },
                    success: function() {
                        location.reload();
                    }
                });
            } else {
                // فتح المودال لإدخال سبب الحظر
                $('#modal_student_id').val(studentId);
                $('#block_reason').val('');
                const blockModal = new bootstrap.Modal(document.getElementById('blockModal'));
                blockModal.show();
            }
        });
    </script>
@endsection
