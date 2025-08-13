@extends('admin.app')
@php
    $title = 'الطلاب';
    $sub_title = 'الطلاب';
    $grade_level = request('grade_level');
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
                    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addStudentModal">إضافة
                        طالب</button>

                </div>
            </div>
        </div>
        <div id="kt_app_content" class="app-content flex-column-fluid">
            <div id="kt_app_content_container" class="app-container container-xxl">
                <div class="card">
                    <div class="card-body p-lg-17">



                        <!-- جدول الطلاب -->
                        <div class="table-responsive">

                            <div class="d-flex gap-2">
                                <button class="btn btn-primary mb-3" onclick="downloadAllBarcodes()">📥 تحميل كل
                                    الباركودات</button>
                                <form action="" id="FilterForm" class="d-flex gap-2" method="get">
                                    <input type="hidden" name="page" value="{{ request('page') }}">
                                    <input type="hidden" name="grade_level" value="{{ request('grade_level') }}">
                                    <!-- بحث بالاسم أو الهاتف أو الرقم القومي -->
                                    <div class="mb-2">
                                        <input type="text" name="search" value="{{ request('search') }}"
                                            class="form-control" placeholder="ابحث بالاسم أو الهاتف أو الرقم القومي">
                                    </div>


                                    <div class="d-flex">
                                        <select style="    border-top-left-radius: 0;border-bottom-left-radius: 0;"
                                            name="group_id" onchange="document.getElementById('FilterForm').submit()"
                                            name="grade_level" id="grade_level" class="form-select">

                                            <option value="" disabled>
                                                اختر مجموعه
                                            </option>
                                            @foreach ($groups as $group)
                                                @if ($group->grade_level == $grade_level)
                                                    <option value="{{ $group->id }}">{{ $group->name }}</option>
                                                @endif
                                            @endforeach

                                        </select>
                                        <button
                                            style="    border-top-right-radius: 0;
                                            border-bottom-right-radius: 0;"
                                            type="submit" class="btn btn-primary">
                                            بحث
                                        </button>
                                    </div>

                                </form>

                            </div>

                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>الكود</th>
                                        <th>الباركود</th>
                                        <th>الاسم</th>
                                        <th>مجموعه</th>
                                        <th>التليفون</th>
                                        <th>تليفون ولي الأمر</th>
                                        <th>الرقم القومي</th>
                                        <th>المستحقات </th>
                                        <th>المدفوعات</th>
                                        <th>تاريخ التسجيل</th>
                                        <th>محظور؟</th>
                                        <th>الإجراء</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($students as $student)
                                        <tr>
                                            <td><a
                                                    href="{{ route('admin.students.show', $student->id) }}">{{ $student->student_code }}</a>
                                            </td>
                                            <td>
                                                @if ($student->barcode)
                                                    <div class="barcode-wrapper text-center d-flex gap-1">
                                                        <img id="barcode-img-{{ $student->id }}"
                                                            src="data:image/png;base64,{{ $student->barcode }}"
                                                            alt="Barcode" style="max-height: 40px;">

                                                        <div class="mt-1 d-flex gap-1 justify-content-center">

                                                            <!-- زر تحميل -->
                                                            <a class="btn btn-sm btn-success"
                                                                href="data:image/png;base64,{{ $student->barcode }}"
                                                                download="barcode-{{ $student->student_code }}.png">💾</a>
                                                        </div>
                                                    </div>
                                                @else
                                                    لا يوجد
                                                @endif
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
                                            <td>{{ $student->national_id }}</td>
                                            <td>{{ $student->total_fees - $student->total_paid }}</td>
                                            <td>{{ $student->total_paid ?? 0 }}</td>
                                            <td>{{ $student->created_at->format('Y-m-d ') }}</td>
                                            <td>
                                                @if ($student->blocked)
                                                    نعم
                                                    @if ($student->block_reason)
                                                        -
                                                        {{ Str::limit($student->block_reason, 10) }}
                                                        <a href="#" class="view-reason"
                                                            data-reason="{{ $student->block_reason }}">عرض</a>
                                                    @endif
                                                @else
                                                    لا
                                                @endif
                                            </td>

                                            <td class="d-flex gap-1">

                                                <button
                                                    class="btn btn-sm {{ $student->blocked ? 'btn-success' : 'btn-danger' }} toggle-block-btn"
                                                    data-id="{{ $student->id }}"
                                                    data-blocked="{{ $student->blocked ? 1 : 0 }}">
                                                    {{ $student->blocked ? 'إلغاء الحظر' : 'حظر' }}
                                                </button>
                                                <form action="{{ route('admin.students.destroy', $student->id) }}"
                                                    method="post"
                                                    onsubmit="return confirm('هل أنت متأكد أنك تريد الحذف؟');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-sm btn-danger">حذف</button>
                                                </form>

                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            {{ $students->links('vendor.pagination.custom') }}

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
                        <!-- Modal: عرض سبب الحظر -->
                        <div class="modal fade" id="reasonModal" tabindex="-1" aria-labelledby="reasonModalLabel"
                            aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">سبب الحظر الكامل</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="إغلاق"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p id="full-reason-text"></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- مودال الإضافة -->
                        <div class="modal fade" id="addStudentModal" tabindex="-1">
                            <div class="modal-dialog">
                                <form method="POST" action="{{ route('admin.students.store') }}" class="modal-content">
                                    @csrf
                                    <div class="modal-header">
                                        <h5 class="modal-title">إضافة طالب</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label for="grade_level" class="form-label">الصف الدراسي</label>
                                            <select name="grade_level" id="grade_level" class="form-select" required>
                                                <option value="" disabled selected>اختر الصف الدراسي</option>
                                                <option value="1">الصف الأول الثانوي</option>
                                                <option value="2">الصف الثاني الثانوي</option>
                                                <option value="3">الصف الثالث الثانوي</option>
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label for="group_id" class="form-label">المجموعة</label>
                                            <select name="group_id" id="group_id" class="form-select" required>
                                                <option value="" disabled selected>اختر المجموعة</option>
                                                @foreach ($groups as $group)
                                                    <option value="{{ $group->id }}">{{ $group->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label for="name" class="form-label">اسم الطالب</label>
                                            <input type="text" name="name" id="name" class="form-control"
                                                placeholder="اسم الطالب" required>
                                        </div>

                                        <div class="mb-3">
                                            <label for="phone" class="form-label">تليفون الطالب</label>
                                            <input type="text" name="phone" id="phone" class="form-control"
                                                placeholder="تليفون الطالب" pattern="\d{11}" maxlength="11"
                                                minlength="11" oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                                required title="رقم الهاتف يجب أن يتكون من 11 رقم">
                                        </div>

                                        <div class="mb-3">
                                            <label for="parent_phone" class="form-label">تليفون ولي الأمر</label>
                                            <input type="text" name="parent_phone" id="parent_phone"
                                                class="form-control" placeholder="تليفون ولي الأمر" pattern="\d{11}"
                                                maxlength="11" minlength="11"
                                                oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                                title="رقم الهاتف يجب أن يتكون من 11 رقم" required>
                                        </div>

                                        <div class="mb-3">
                                            <label for="national_id" class="form-label">الرقم القومي</label>
                                            <input type="text" name="national_id" id="national_id"
                                                class="form-control" placeholder="الرقم القومي" pattern="\d{14}"
                                                maxlength="14" minlength="14"
                                                oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                                title="رقم قومي يجب أن يتكون من 14 رقم" required>
                                        </div>

                                        <div class="mb-3">
                                            <label for="address" class="form-label">العنوان</label>
                                            <textarea name="address" id="address" class="form-control" placeholder="العنوان" required></textarea>
                                        </div>

                                        <div class="mb-3">
                                            <label for="discount" class="form-label">الخصم</label>
                                            <input type="number" name="discount" id="discount" class="form-control"
                                                placeholder="الخصم" min="0" max="100"
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

                        <!-- مودال التعديل -->
                        <div class="modal fade" id="editStudentModal" tabindex="-1">
                            <div class="modal-dialog">
                                <form method="POST" id="editStudentForm" class="modal-content">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-header">
                                        <h5 class="modal-title">تعديل بيانات الطالب</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label for="grade_level" class="form-label">الصف الدراسي</label>
                                            <select name="grade_level" id="edit_grade_level" class="form-select"
                                                required>
                                                <option value="" disabled selected>اختر الصف الدراسي</option>
                                                <option value="1"
                                                    {{ old('grade_level', $student->grade_level ?? '') == 1 ? 'selected' : '' }}>
                                                    الصف الأول
                                                    الثانوي</option>
                                                <option value="2"
                                                    {{ old('grade_level', $student->grade_level ?? '') == 2 ? 'selected' : '' }}>
                                                    الصف الثاني
                                                    الثانوي</option>
                                                <option value="3"
                                                    {{ old('grade_level', $student->grade_level ?? '') == 3 ? 'selected' : '' }}>
                                                    الصف الثالث
                                                    الثانوي</option>
                                            </select>
                                        </div>
                                        <select name="group_id" class="form-control mb-2" id="editGroup" required>
                                            <option value="">اختر المجموعة</option>
                                            @foreach ($groups as $group)
                                                <option value="{{ $group->id }}">{{ $group->name }}</option>
                                            @endforeach
                                        </select>
                                        <input type="text" name="name" class="form-control mb-2" id="editName"
                                            required>
                                        <input type="text" name="phone" class="form-control mb-2" id="editPhone"
                                            required>
                                        <input type="text" name="parent_phone" class="form-control mb-2"
                                            id="editParentPhone" required>
                                        <input type="text" name="national_id" class="form-control mb-2"
                                            id="editNationalId" required>
                                        <textarea name="address" class="form-control mb-2" id="editAddress" required></textarea>

                                    </div>
                                    <div class="modal-footer">
                                        <button class="btn btn-primary">تحديث</button>
                                    </div>
                                </form>
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
        function downloadAllBarcodes() {
            // حدد كل روابط التحميل اللي تحتوي على base64 للباركود
            const links = document.querySelectorAll('a[href^="data:image/png;base64,"]');

            links.forEach((link, index) => {
                const downloadLink = document.createElement('a');
                downloadLink.href = link.href;
                downloadLink.download = link.download || `barcode-${index + 1}.png`;
                document.body.appendChild(downloadLink);
                downloadLink.click();
                document.body.removeChild(downloadLink);
            });
        }
    </script>



    <script>
        // كل بيانات الجروبات من السيرفر بصيغة JSON
        const allGroups = @json($groups);

        // عناصر الـ select
        const gradeSelects = document.querySelectorAll('select[name="grade_level"]');
        const groupSelects = document.querySelectorAll('select[name="group_id"]');

        // لما المستخدم يغير الصف
        gradeSelects.forEach((gradeSelect, index) => {
            gradeSelect.addEventListener('change', function() {
                const selectedGrade = this.value;

                const filteredGroups = allGroups.filter(group => group.grade_level == selectedGrade);

                // حدث قائمة الجروبات المقابلة لهذا الصف
                const groupSelect = groupSelects[index];
                groupSelect.innerHTML = `<option value="" disabled selected>اختر المجموعة</option>`;

                filteredGroups.forEach(group => {
                    groupSelect.innerHTML += `<option value="${group.id}">${group.name}</option>`;
                });
            });
        });
    </script>



    <script>
        document.querySelectorAll('.edit-btn').forEach(button => {
            button.addEventListener('click', function() {
                const student = JSON.parse(this.dataset.student);

                document.getElementById('edit_grade_level').value = student.grade_level;
                document.getElementById('editGroup').value = student.group_id;
                document.getElementById('editName').value = student.name;
                document.getElementById('editPhone').value = student.phone;
                document.getElementById('editParentPhone').value = student.parent_phone;
                document.getElementById('editNationalId').value = student.national_id;
                document.getElementById('editAddress').value = student.address;
                // document.getElementById('editBlocked').value = student.blocked ? 1 : 0;
                // document.getElementById('editBlockReason').value = student.block_reason ?? '';
                // document.getElementById('editDiscount').value = student.discount ?? '';
                // document.getElementById('editDiscountReason').value = student.discount_reason ?? '';

                document.getElementById('editStudentForm').action = `/admin/students/${student.id}`;
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
    <script>
        $(document).on('click', '.view-reason', function(e) {
            e.preventDefault();
            const reason = $(this).data('reason');
            $('#full-reason-text').text(reason);
            const reasonModal = new bootstrap.Modal(document.getElementById('reasonModal'));
            reasonModal.show();
        });
    </script>


@endsection
