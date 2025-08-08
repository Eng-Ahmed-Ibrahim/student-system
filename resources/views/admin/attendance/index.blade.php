@extends('admin.app')
@php
    $title = 'الحضور والغياب';
    $sub_title = 'الحضور والغياب';
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
                        {{-- معلومات المجموعة والحضور --}}
                        <div class="mb-4 d-flex gap-2">
                            <h4 class="mb-2">المجموعة:
                                <span class="badge bg-primary">{{ $group->name }}</span>
                            </h4>

                            <h5 class="mb-1">عدد الحضور:
                                <span class="badge bg-success total-present">{{ $presentCount }}</span>
                            </h5>

                            <h5>عدد الغياب:
                                <span class="badge bg-danger total-absent">{{ $absentCount }}</span>
                            </h5>
                            <h5>تبدأ الحصة:
                                <span
                                    class="badge bg-light text-dark">{{ \Carbon\Carbon::parse($group->time)->format('h:i A') }}</span>
                            </h5>

                            <h5 class="mb-1">سعر الحصه:
                                <span class="badge bg-success total-present">{{ $group->monthly_fee }} جنيه</span>
                            </h5>
                        </div>

                        <form id="barcode-form">
                            @csrf
                            <input type="text" id="barcode-input" name="code" class="form-control mb-3"
                                placeholder="كود الطالب ، دوس انتر بعد كتابه الكود لتحضير الطالب" autofocus>
                        </form>
                        <div id="barcode-result" class="alert d-none mt-2"></div>

                        <table class="table table-bordered">

                            <thead class="thead-dark">
                                <tr>
                                    <th>الكود</th>
                                    <th>الاسم</th>
                                    <th> الخصم </th>
                                    <th> المستحقات </th>
                                    <th> دفع </th>
                                    <th>رقم التلفون</th>
                                    <th>رقم تلفون ولي الامر</th>
                                    <th>وقت الحضور</th>
                                    <th>تاريخ الحضور</th>
                                    <th>الحالة</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (count($students) > 0)
                                    @foreach ($students as $student)
                                        @php
                                            $attendance = $student->attendance->first();
                                        @endphp
                                        <tr>

                                            <td><a
                                                    href="{{ route('admin.students.show', $student->id) }}">#{{ $student->student_code }}</a>
                                            </td>
                                            <td>{{ $student->name }}</td>
                                            <td>{{ $student->discount }}%</td>
                                            @php 
                                            $dueAmount= $student->total_fees > 0 ? $student->total_fees - $student->total_paid : 0;
                                            @endphp
                                            <td>{{ $dueAmount }}
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal"
                                                    data-bs-target="#paymentModal" onclick="document.getElementById('dueAmount').value='{{ $dueAmount }}';document.getElementById('studentId').value='{{ $student->id }}'">
                                                    دفع 
                                                </button>
                                            </td>
                                            <td>{{ $student->phone }}</td>
                                            <td>{{ $student->parent_phone }}</td>
                                            <td>{{ $attendance->time ? \Carbon\Carbon::parse($attendance->time)->format('h:i A') : ' لم يحضر ' }}
                                            </td>
                                            <td>{{ \Carbon\Carbon::parse($attendance->date)->format('Y-m-d') }}</td>

                                            <td>
                                                @if ($attendance)
                                                    <span
                                                        class="badge {{ $attendance->status ? 'bg-success' : 'bg-danger' }}">
                                                        {{ $attendance->status ? 'حاضر' : 'غائب' }}
                                                    </span>
                                                @else
                                                    <span class="badge bg-secondary">غير محدد</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if (!$attendance || !$attendance->status)
                                                    <form
                                                        action="{{ route('admin.attendance.mark', ['student' => $student->id, 'status' => 1]) }}"
                                                        method="POST" class="d-inline">
                                                        @csrf
                                                        <button class="btn btn-success btn-sm">تسجيل حضور</button>
                                                    </form>
                                                @endif

                                                @if (!$attendance || $attendance->status)
                                                    <form
                                                        action="{{ route('admin.attendance.mark', ['student' => $student->id, 'status' => 0]) }}"
                                                        method="POST" class="d-inline">
                                                        @csrf
                                                        <button class="btn btn-danger btn-sm">تسجيل غياب</button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-4">
                                            <div class="alert alert-info mb-0" role="alert">
                                                لا يوجد حصه اليوم
                                            </div>
                                        </td>
                                    </tr>

                                @endif
                            </tbody>
                        </table>




                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Modal -->
    <div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('admin.payments.store') }}" onsubmit="return validatePayment()">
                @csrf
                <input type="hidden" name="student_id" id="studentId">
                {{-- <input type="hidden" name="month" value="{{ $month }}">
                <input type="hidden" name="year" value="{{ now()->year }}"> --}}

                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="paymentModalLabel">إضافة دفعة جديدة</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">المبلغ المستحق:</label>
                            <input type="text" class="form-control"
                                 readonly id="dueAmount">
                        </div>
                        <div class="mb-3">
                            <label for="amount" class="form-label">المبلغ المدفوع:</label>
                            <input type="number" name="amount" id="paidAmount" class="form-control" required
                                min="1">
                            <small id="errorMsg" class="text-danger d-none">المبلغ أكبر من المستحق!</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">تأكيد الدفع</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('js')
    <script src="https://unpkg.com/html5-qrcode"></script>
    <script>
        function validatePayment() {
            const due = parseFloat(document.getElementById('dueAmount').value);
            const paid = parseFloat(document.getElementById('paidAmount').value);
            const errorMsg = document.getElementById('errorMsg');

            if (paid > due) {
                errorMsg.classList.remove('d-none');
                return false;
            }

            errorMsg.classList.add('d-none');
            return true;
        }
    </script>
    <script>
        // دالة الحضور (نفس اللي كتبته بالضبط)
        function markAttendance(code) {
            const input = document.getElementById('barcode-input');
            const result = document.getElementById('barcode-result');

            if (!code) return;

            input.disabled = true;
            input.value = code;

            fetch("{{ route('admin.attendance.mark-barcode') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
                        code: code
                    })
                })
                .then(res => res.json())
                .then(data => {
                    result.className = 'alert mt-2 ' + (data.success ? 'alert-success' : 'alert-danger');
                    result.textContent = data.message;
                    result.classList.remove('d-none');
                    input.value = '';

                    if (data.success) {
                        const rows = document.querySelectorAll("tbody tr");

                        rows.forEach(row => {
                            const studentCode = row.cells[0].textContent.trim();

                            if (studentCode === code) {
                                // تحديث الحالة
                                row.cells[6].innerHTML = '<span class="badge bg-success">حاضر</span>';

                                // تحديث الوقت
                                const now = new Date();
                                const hours = now.getHours() % 12 || 12;
                                const minutes = now.getMinutes().toString().padStart(2, '0');
                                const ampm = now.getHours() >= 12 ? 'PM' : 'AM';
                                row.cells[4].textContent = `${hours}:${minutes} ${ampm}`;

                                // تحديث زر الإجراء
                                row.cells[7].innerHTML = `
                                <form action="/admin/attendance/mark/${code}/0" method="POST" class="d-inline">
                                    @csrf
                                    <button class="btn btn-danger btn-sm">تسجيل غياب</button>
                                </form>
                            `;
                            }
                        });





                    }
                })
                .catch(() => {
                    result.className = 'alert alert-danger mt-2';
                    result.textContent = 'حدث خطأ ما';
                    result.classList.remove('d-none');
                })
                .finally(() => {
                    input.disabled = false;
                    input.focus();
                });
        }

        // عند تحميل الصفحة
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('barcode-form');
            const input = document.getElementById('barcode-input');

            // تشغيل الفوكس تلقائيًا
            input.focus();

            // عند إرسال الفورم (Enter من ماسح الباركود)
            form.addEventListener('submit', function(e) {
                e.preventDefault(); // منع إعادة تحميل الصفحة
                const code = input.value.trim();
                markAttendance(code);
            });
        });
    </script>



    <script>
        const form = document.getElementById('barcode-form');
        const input = document.getElementById('barcode-input');

        form.addEventListener('submit', function(e) {
            e.preventDefault(); // يمنع إرسال الفورم بالشكل التقليدي
            const code = input.value.trim();

            if (code !== '') {
                markAttendance(code);
            }
        });
    </script>

@endsection
