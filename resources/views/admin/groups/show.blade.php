@extends('admin.app')
@php
    $title = "مجموعه : $group->name";
    $sub_title = 'الصلاحيات';
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
                        <li class="breadcrumb-item text-muted">التفاصيل</li>
                    </ul>
                </div>
                <div class="d-flex align-items-center gap-2 gap-lg-3">
                    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addStudentModal">إضافة
                        طالب</button>
                        <a href="{{ route('admin.groups.export', $group->id) }}" class="btn btn-success mb-3">
   تحميل اكسيل
</a>

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
                                    <th>كود الطالب</th>
                                    <th>الاسم</th>
                                    <th>رقم القومي</th>
                                    <th>تلفون</th>
                                    <th>تلفون ولي الامر</th>
                                    <th>صف دراسي</th>
                                    <th>الإجراء</th>

                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($group->students as $student)
                                    <tr>
                                        <td><a
                                                href="{{ route('admin.students.show', $student->id) }}">{{ $student->student_code }}</a>
                                        </td>
                                        <td class="student-name">{{ $student->name }}</td>
                                        <td>{{ $student->national_id }}</td>
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
                                        <td class="d-flex align-items-center justify-content-center gap-2">
                                            <form action="{{ route('admin.students.destroy', $student->id) }}"
                                                method="post" onsubmit="return confirm('هل أنت متأكد أنك تريد الحذف؟');">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-danger">حذف</button>
                                            </form>
                                                         <button
                                                    class="btn btn-sm {{ $student->blocked ? 'btn-success' : 'btn-info' }} toggle-block-btn"
                                                    data-id="{{ $student->id }}"
                                                    data-blocked="{{ $student->blocked ? 1 : 0 }}">
                                                    {{ $student->blocked ? 'إلغاء الحظر' : 'حظر' }}
                                                </button>
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
    <!-- مودال الإضافة -->
    <div class="modal fade" id="addStudentModal" tabindex="-1">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('admin.students.store') }}" class="modal-content">
                <input type="hidden" name="grade_level" value="{{ $group->grade_level }}">
                <input type="hidden" name="group_id" value="{{ $group->id }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">إضافة طالب</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
      
                    <div class="mb-3">
                        <label for="name" class="form-label">اسم الطالب</label>
                        <input type="text" name="name" id="name" class="form-control" placeholder="اسم الطالب"
                            required>
                    </div>

                    <div class="mb-3">
                        <label for="phone" class="form-label">تليفون الطالب</label>
                        <input type="text" name="phone" id="phone" class="form-control" placeholder="تليفون الطالب"
                            pattern="\d{11}" maxlength="11" minlength="11"
                            oninput="this.value = this.value.replace(/[^0-9]/g, '')" required
                            title="رقم الهاتف يجب أن يتكون من 11 رقم">
                    </div>

                    <div class="mb-3">
                        <label for="parent_phone" class="form-label">تليفون ولي الأمر</label>
                        <input type="text" name="parent_phone" id="parent_phone" class="form-control"
                            placeholder="تليفون ولي الأمر" pattern="\d{11}" maxlength="11" minlength="11"
                            oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                            title="رقم الهاتف يجب أن يتكون من 11 رقم" required>
                    </div>

                    <div class="mb-3">
                        <label for="national_id" class="form-label">الرقم القومي</label>
                        <input type="text" name="national_id" id="national_id" class="form-control"
                            placeholder="الرقم القومي" pattern="\d{14}" maxlength="14" minlength="14"
                            oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                            title="رقم قومي يجب أن يتكون من 14 رقم" required>
                    </div>

                    <div class="mb-3">
                        <label for="address" class="form-label">العنوان</label>
                        <textarea name="address" id="address" class="form-control" placeholder="العنوان" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="discount" class="form-label">الخصم</label>
                        <input type="number" name="discount" id="discount" class="form-control" placeholder="الخصم"
                            min="0" max="100"
                            oninput="this.value = this.value.replace(/[^0-9]/g, ''); 
                                                        if (this.value > 100) this.value = 100; 
                                                        if (this.value < 0) this.value = 0;">
                    </div>
                    <div class="mb-3">
                        <label for="discount_reason" class="form-label">سبب الخصم</label>
                        <textarea name="discount_reason" id="discount_reason" class="form-control" placeholder="سبب الخصم"></textarea>
                    </div>

                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary">حفظ</button>
                </div>
            </form>
        </div>
    </div>
               <!-- Modal: سبب الحظر -->
                        <div class="modal fade" id="blockModal" tabindex="-1" aria-labelledby="blockModalLabel"
                            aria-hidden="true">
                            <div class="modal-dialog">
                                <form id="blockForm" method="POST" action="{{ route('admin.students.block') }}">
                                    @csrf
                                    <input type="hidden" name="student_id" id="modal_student_id">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">سبب الحظر</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <textarea name="reason" id="block_reason" class="form-control" placeholder="اكتب سبب الحظر..." required></textarea>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-danger">تأكيد الحظر</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
@endsection
@section('js')
    <script>
        document.getElementById('studentSearch').addEventListener('keyup', function() {
            var searchValue = this.value.toLowerCase();
            var rows = document.querySelectorAll('#studentsTable tbody tr');

            rows.forEach(function(row) {
                var nameCell = row.querySelector('.student-name');
                var name = nameCell.textContent.toLowerCase();
                row.style.display = name.includes(searchValue) ? '' : 'none';
            });
        });
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
