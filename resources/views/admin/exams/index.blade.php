@extends('admin.app')
@php
    $title = 'الامتحانات';
    $sub_title = 'الصفحات';
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
                        data-bs-target="#addExamModal">اضافه امتحان</a>
                </div>
            </div>
        </div>
        <div id="kt_app_content" class="app-content flex-column-fluid">
            <div id="kt_app_content_container" class="app-container container-xxl">
                <div class="card">
                    <div class="card-body p-lg-17">



                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>المجموعة</th>
                                    <th>اسم الامتحان</th>
                                    <th>تاريخ الامتحان</th>
                                    <th>الدرجة الكاملة</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($exams as $exam)
                                    <tr>
                                        <td><a href="{{ route('admin.exams.show',$exam->id) }}">#{{ $exam->id }}</a></td>
                                        <td>{{ $exam->group->name }}</td>
                                        <td>{{ $exam->name ?? '-' }}</td>
                                        <td>{{ $exam->exam_date }}</td>
                                        <td>{{ $exam->total_score }}</td>
                                        <td>
                                            <button class="btn btn-sm btn-warning edit-btn" data-id="{{ $exam->id }}"
                                                data-grade="{{ $exam->grade_level }}" data-group="{{ $exam->group_id }}"
                                                data-name="{{ $exam->name }}" data-date="{{ $exam->exam_date }}"
                                                data-score="{{ $exam->total_score }}" data-bs-toggle="modal"
                                                data-bs-target="#editExamModal">
                                                تعديل
                                            </button>
                                            <form action="{{ route('admin.exams.destroy', $exam->id) }}" method="POST"
                                                class="d-inline">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-sm btn-danger"
                                                    onclick="return confirm('هل أنت متأكد؟')">حذف</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        {{-- إضافة امتحان --}}
                        <div class="modal fade" id="addExamModal" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="{{ route('admin.exams.store') }}" method="POST">
                                        @csrf
                                        <div class="modal-header">
                                            <h5 class="modal-title">إضافة امتحان</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            @include('admin.exams.form')
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-success">حفظ</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        {{-- تعديل امتحان --}}
                        <div class="modal fade" id="editExamModal" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form id="editExamForm" method="POST">
                                        @csrf @method('PUT')
                                        <div class="modal-header">
                                            <h5 class="modal-title">تعديل امتحان</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            @include('admin.exams.form')
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-success">تحديث</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>


                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // فلترة المجموعات حسب الصف
        document.querySelectorAll('#grade_level').forEach(select => {
            select.addEventListener('change', function() {
                let gradeId = this.value;
                let groupSelect = this.closest('.modal-body').querySelector('#group_id');
                groupSelect.querySelectorAll('option').forEach(option => {
                    option.hidden = option.dataset.grade != gradeId && option.value !=
                        '';
                });
            });
        });

        // تعبئة بيانات التعديل
        document.querySelectorAll('.edit-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                let form = document.getElementById('editExamForm');
                form.action = `/admin/exams/${this.dataset.id}`;
                form.querySelector('#grade_level').value = this.dataset.grade;
                form.querySelector('#group_id').value = this.dataset.group;
                form.querySelector('#name').value = this.dataset.name;
                form.querySelector('#exam_date').value = this.dataset.date;
                form.querySelector('#total_score').value = this.dataset.score;
            });
        });
    });
</script>
@endsection
