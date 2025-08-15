@extends('admin.app')
@php
    $grades = ['الصف الاول الثانوي', 'الصف الثاني الثانوي', 'الصف الثالث الثانوي'];

    $title = 'الحضور والغياب';
    $sub_title = 'التقارير';
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

                    <a href="{{ request()->fullUrlWithQuery(['download' => 'excel']) }}" class="btn btn-success mb-3">
                        تحميل Excel 📥
                    </a>

                </div>
            </div>
        </div>
        <div id="kt_app_content" class="app-content flex-column-fluid">
            <div id="kt_app_content_container" class="app-container container-xxl">
                <div class="card">
                    <div class="card-body p-lg-17">



                        <div class=" mt-4">
                            <h2>📋 تقرير الحضور والغياب</h2>

                            <form method="GET" action="{{ route('admin.reports.attendance') }}" class="row g-3 mb-4">
                                <div class="col-md-3">

                                    <label class="form-label">الطالب</label>
                                    <select name="student_id" id="studentSelect" class="form-select">
                                        <option value="all">الكل</option>
                                        @foreach ($students as $student)
                                            <option value="{{ $student->id }}"
                                                {{ request('student_id') == $student->id ? 'selected' : '' }}>
                                                {{ $student->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label>المجموعه</label>

                                    <select style="    border-top-left-radius: 0;border-bottom-left-radius: 0;"
                                        name="group_id" onchange="document.getElementById('FilterForm').submit()"
                                        name="group_id" id="grade_level" class="form-select">

                                        <option value="" disabled>
                                            اختر مجموعه
                                        </option>
                                        <option value="all">الكل</option>
                                        @foreach ($groups as $group)
                                            <option {{ request('group_id') == $group->id ? 'selected' : ' ' }}
                                                value="{{ $group->id }}">{{ $group->name }} --
                                                {{ $grades[$group->grade_level - 1] }}</option>
                                        @endforeach

                                    </select>
                                </div>

                                <div class="col-md-3">
                                    <label>نوع التقرير</label>
                                    <select name="type" class="form-select" onchange="toggleCustomDates(this.value)">
                                        <option value="">اختر</option>
                                        <option value="daily" {{ request('type') == 'daily' ? 'selected' : '' }}>يومي
                                        </option>
                                        <option value="monthly" {{ request('type') == 'monthly' ? 'selected' : '' }}>شهري
                                        </option>
                                        <option value="yearly" {{ request('type') == 'yearly' ? 'selected' : '' }}>سنوي
                                        </option>
                                        <option value="custom" {{ request('type') == 'custom' ? 'selected' : '' }}>من - إلى
                                        </option>
                                    </select>
                                </div>

                                <div class="col-md-3 custom-dates" style="display: none;">
                                    <label>من</label>
                                    <input type="date" name="from" value="{{ request('from') }}"
                                        class="form-control">
                                </div>

                                <div class="col-md-3 custom-dates" style="display: none;">
                                    <label>إلى</label>
                                    <input type="date" name="to" value="{{ request('to') }}" class="form-control">
                                </div>

                                <div class="col-md-3 align-self-end">
                                    <button type="submit" class="btn btn-primary w-100">عرض التقرير</button>
                                </div>
                            </form>

                            <div class="alert alert-info">
                                <strong>عدد أيام الحضور: </strong>{{ $presentCount }} |
                                <strong>عدد أيام الغياب: </strong>{{ $absentCount }}
                            </div>

                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>كود</th>
                                        <th>الطالب</th>
                                        <th>الصف</th>
                                        <th>المجموعه</th>
                                        <th>رقم التلفون</th>
                                        <th>رقم التلفون ولي الامر</th>
                                        <th>التاريخ</th>
                                        <th>الحالة</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($attendances as $attendance)
                                        <tr>
                                            @php $student=$attendance->student; @endphp
                                            <td> <a
                                                    href="{{ route('admin.students.show', $student->id) }}">{{ $student->student_code ?? '---' }}</a>
                                            </td>
                                            <td>{{ $student->name ?? '---' }}</td>
                                            <td>{{ $grades[$student->grade_level - 1] ?? '---' }}</td>
                                            <td>{{ $student->group->name ?? '---' }}</td>
                                            <td>{{ $student->phone ?? '---' }}</td>
                                            <td>{{ $student->parent_phone ?? '---' }}</td>
                                            <td>{{ $attendance->date }}</td>
                                            <td>
                                                @if ($attendance->status == 1)
                                                    <span class="badge bg-success">حاضر</span>
                                                @else
                                                    <span class="badge bg-danger">غائب</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center">لا توجد بيانات</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                            {{ $attendances->links('vendor.pagination.custom') }}

                        </div>




                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('js')
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <!-- JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        function toggleCustomDates(type) {
            document.querySelectorAll('.custom-dates').forEach(el => {
                el.style.display = (type === 'custom') ? 'block' : 'none';
            });
        }
        toggleCustomDates("{{ request('type') }}");
    </script>
    <script>
        $(document).ready(function() {
            $('#studentSelect').select2({
                placeholder: "اختر الطالب",
                allowClear: true,
                width: '100%'
            });
        });
    </script>
@endsection
