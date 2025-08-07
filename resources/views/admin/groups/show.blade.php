@extends('admin.app')
@php
$title="مجموعه : $group->name";
$sub_title="الصلاحيات";
@endphp
@section('title',$title)
@section('content')
<div class="d-flex flex-column flex-column-fluid">

	<div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
		<div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
			<div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
				<h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">{{ $title }}</h1>
				<ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
					<li class="breadcrumb-item text-muted">
						<a class="text-muted text-hover-primary">{{ $sub_title }}</a>
					</li>
					<li class="breadcrumb-item">
						<span class="bullet bg-gray-400 w-5px h-2px"></span>
					</li>
					<li class="breadcrumb-item text-muted">التفاصيل</li>
				</ul>
			</div>
			<div class="d-flex align-items-center gap-2 gap-lg-3">

				<a href="#" class="btn btn-sm fw-bold btn-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_create_app">Create</a>
			</div>
		</div>
	</div>
	<div id="kt_app_content" class="app-content flex-column-fluid">
		<div id="kt_app_content_container" class="app-container container-xxl">
			<div class="card">
				<div class="card-body p-lg-17">


<div class="mb-5">
    <h3>📊 إحصائيات المجموعة</h3>
    <p>عدد الطلاب الحالي: <strong>{{ count($group->students) }}</strong></p>
    <p>عدد الأماكن المتبقية: <strong>{{ $group->limit - count($group->students) }}</strong></p>
</div>
<div class="mb-5">
    <input type="text" id="studentSearch" class="form-control" placeholder="ابحث باسم الطالب...">
</div>
<table class="table table-bordered table-striped" id="studentsTable">
    <thead>
        <tr>
            <th>الرقم</th>
            <th>الاسم</th>
            <th>الكود</th>
            <th>تلفون</th>
            <th>تلفون ولي الامر</th>
            <th>صف دراسي</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($group->students as $student)
            <tr>
                <td><a href="{{ route('admin.students.show',$student->id) }}">{{ $student->student_code }}</a></td>
                <td class="student-name">{{ $student->name }}</td>
                <td>{{ $student->student_code }}</td>
                <td>{{ $student->phone }}</td>
                <td>{{ $student->parent_phone }}</td>
                <td>
                    @switch($student->grade_level)
                        @case(1)
                            الصف الأول الثانوي
                            @break
                        @case(2)
                            الصف الثاني الثانوي
                            @break
                        @case(3)
                            الصف الثالث الثانوي
                            @break
                        @default
                            غير معروف
                    @endswitch
                </td>
            </tr>
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
    document.getElementById('studentSearch').addEventListener('keyup', function () {
        var searchValue = this.value.toLowerCase();
        var rows = document.querySelectorAll('#studentsTable tbody tr');

        rows.forEach(function(row) {
            var nameCell = row.querySelector('.student-name');
            var name = nameCell.textContent.toLowerCase();
            row.style.display = name.includes(searchValue) ? '' : 'none';
        });
    });
</script>
@endsection
