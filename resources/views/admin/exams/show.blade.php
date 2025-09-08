@extends('admin.app')

@php

    $title = $exam->name;
    $sub_title = 'الامتحانات';
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





                        <h2>العنوان: {{ $exam->name }} (Group: {{ $exam->group->name }})</h2>
                        <p> التارخ: {{ $exam->exam_date }}</p>
                        <p> درجع الامتحان: {{ $exam->total_score }}</p>
                        <div class="input-group my-3" style="max-width: 300px;">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input id="searchInput" type="text" class="form-control"
                                placeholder="البحث بالاسم او كود الطالب">
                        </div>
                        <table id="studentsTable" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>كود الطالب</th>
                                    <th>اسم الطالب</th>
                                    <th>الدرجه</th>
                                    <th>من</th>
                                    @can('add scores for students')
                                        <th>تعديل الدرجه</th>
                                    @endcan
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($exam->results as $index => $result)
                                    @if ($result->student)
                                        <tr>
                                            <td># {{ $index + 1 }}</td>
                                            <td class="student-code"><a
                                                    href="{{ route('admin.students.show', $result->student->id) }}">{{ $result->student->student_code }}</a>
                                            </td>
                                            <td class="student-name">{{ $result->student->name }}</td>
                                            <td>
                                                {{ $result->score }}
                                            </td>
                                            <td>{{ $exam->total_score }}</td>
                                            @can('add scores for students')
                                                <td>
                                                    <form method="POST"
                                                        action="{{ route('admin.exams.update_student_score', $result->id) }}"
                                                        class="d-flex align-items-center gap-2">
                                                        @csrf
                                                        @method('PATCH')
                                                        <input type="number" name="score" step="0.01" min="0"
                                                            max="{{ $exam->total_score }}" value="{{ $result->score }}"
                                                            class="form-control" style="width: 100px;" required>
                                                        <button type="submit" class="btn btn-primary btn-sm ml-2">حفظ</button>
                                                    </form>
                                                </td>
                                            @endcan
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('js')

    <script>
        // عند تحميل الصفحة

        function filterFunction(filter) {
            let rows = document.querySelectorAll('#studentsTable tbody tr');

            rows.forEach(row => {
                let code = row.querySelector('.student-code')?.textContent.toLowerCase() || '';
                let name = row.querySelector('.student-name')?.textContent.toLowerCase() || '';

                if (code.includes(filter) || name.includes(filter)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }
        document.addEventListener('DOMContentLoaded', function() {
            let filter = document.getElementById('searchInput').value.trim().toLowerCase();

            input.focus();

            filterFunction(filter)

        });
        document.getElementById('searchInput').addEventListener('input', function() {
            let filter = this.value.trim().toLowerCase();
            filterFunction(filter)
        });
    </script>
@endsection
