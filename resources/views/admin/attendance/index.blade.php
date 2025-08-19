@extends('admin.app')
@php
    $title = 'الحضور والغياب';
    $sub_title = 'الحضور والغياب';
@endphp
@section('title', $title)

@section('css')


    <style>
        /* Print styles */
        @media print {
            body * {
                visibility: hidden;
                overflow: hidden;
            }

            .print-content,
            .print-content * {
                visibility: visible;
            }

            .print-content {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                text-align: center;
                padding: 20px;
            }

            .barcode-container {
                margin: 20px 0;
            }

            .student-info {
                font-size: 18px;
                margin: 10px 0;
                font-weight: bold;
            }
        }

        .print-content {
            display: none;
        }

        .print-barcode-btn {
            background-color: #17a2b8;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
            margin-left: 5px;
        }

        .print-barcode-btn:hover {
            background-color: #138496;
        }
    </style>

@endsection

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

                            <h5 class="mb-1"> الرسوم الشهريه:
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
                                        @php
                                            $dueAmount =
                                                $student->total_fees > 0
                                                    ? $student->total_fees - $student->total_paid
                                                    : 0;
                                        @endphp
                                        <tr data-due-amount="{{ $dueAmount }}" data-student-id="{{ $student->id }}"
                                            data-student-code="{{ $student->student_code }}"
                                            data-student-name="{{ $student->name }}"
                                            data-discount="{{ $student->discount }}"
                                            data-status="{{ $attendance->status }}"
                                            data-barcode="{{ $student->barcode }}">

                                            <td>
                                                <a href="{{ route('admin.students.show', $student->id) }}">
                                                    #{{ $student->student_code }}
                                                </a>
                                                <button class="print-barcode-btn" onclick="printStudentBarcode(this)"
                                                    title="طباعة Barcode">
                                                    🖨️
                                                </button>
                                            </td>
                                            <td>{{ $student->name }}</td>
                                            <td>{{ $student->discount }}%</td>
                                            <td class="Fees">{{ $dueAmount }}</td>
                                            <td>
                                                <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal"
                                                    data-bs-target="#paymentModal"
                                                    onclick="document.getElementById('dueAmount').value='{{ $dueAmount }}';document.getElementById('studentId').value='{{ $student->id }}'">
                                                    دفع
                                                </button>
                                            </td>
                                            <td>{{ $student->phone }}</td>
                                            <td>{{ $student->parent_phone }}</td>
                                            <td>{{ $attendance->time ? \Carbon\Carbon::parse($attendance->time)->format('h:i A') : ' لم يحضر ' }}
                                            </td>
                                            <td>{{ \Carbon\Carbon::parse($attendance->date)->format('Y-m-d') }}</td>

                                            <td class="formStatus">
                                                @if ($attendance)
                                                    <span
                                                        class="badge {{ $attendance->status ? 'bg-success' : 'bg-danger' }}">
                                                        {{ $attendance->status ? 'حاضر' : 'غائب' }}
                                                    </span>
                                                @else
                                                    <span class="badge bg-secondary">غير محدد</span>
                                                @endif
                                            </td>
                                            <td class="formAttendance">
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
                                        <td colspan="11" class="text-center text-muted py-4">
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

    <!-- محتوى الطباعة المخفي -->
    <div class="print-content" id="printContent">
        <div class="student-info" id="studentInfo"></div>
        <div class="barcode-container">
            <canvas id="barcode"></canvas>
        </div>
        <div class="barcode-text" style="display: none" id="barcodeText"></div>
    </div>

    <!-- Payment Modal -->
    <div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('admin.payments.store') }}" onsubmit="return validatePayment()">
                @csrf
                <input type="hidden" name="student_id" id="studentId">

                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="paymentModalLabel">إضافة دفعة جديدة</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">المبلغ المستحق:</label>
                            <input type="text" class="form-control" readonly id="dueAmount">
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jsbarcode/3.11.5/JsBarcode.all.min.js"></script>

    <script>
        // استبدل دالة printStudentBarcode بهذا الكود

        function printStudentBarcode(button) {
            const row = button.closest('tr');
            let studentBarcode = row.dataset.barcode;
            const studentName = row.dataset.studentName;
            const studentCode = row.dataset.studentCode;

            console.log('Student Barcode:', studentBarcode);

            if (!studentBarcode && !studentCode) {
                alert('لا يوجد barcode أو كود للطالب');
                return;
            }

            // فحص إذا كان barcode عبارة عن صورة base64
            if (studentBarcode.startsWith('iVBORw0KGgo') || studentBarcode.startsWith('data:image') || !studentBarcode) {
                // استخدام كود الطالب بدلاً من الصورة
                studentBarcode = studentCode;
                console.log('Using student code as barcode:', studentBarcode);
            }

            // إظهار محتوى الطباعة مؤقتاً
            const printContent = document.getElementById('printContent');
            printContent.style.display = 'block';
            printContent.style.position = 'fixed';
            printContent.style.top = '0';
            printContent.style.left = '0';
            printContent.style.width = '100%';
            printContent.style.height = 'auto';
            printContent.style.background = 'white';
            printContent.style.zIndex = '9999';
            printContent.style.padding = '20px';
            printContent.style.textAlign = 'center';



            document.getElementById('barcodeText').innerHTML =
                `<div style="font-size: 14px; margin-top: 10px;">${studentBarcode}</div>`;

            try {
                // تنظيف الـ canvas
                const canvas = document.getElementById('barcode');
                const ctx = canvas.getContext('2d');
                ctx.clearRect(0, 0, canvas.width, canvas.height);

                // إنشاء barcode للطباعة
                JsBarcode("#barcode", studentBarcode, {
                    format: "CODE128",
                    width: 3,
                    height: 80,
                    displayValue: true,
                    fontSize: 16,
                    margin: 10,
                    background: "#ffffff",
                    lineColor: "#000000"
                });

                // انتظار قليل ثم طباعة
                setTimeout(() => {
                    window.print();

                    // إخفاء المحتوى مرة أخرى بعد الطباعة
                    setTimeout(() => {
                        printContent.style.display = 'none';
                        printContent.style.position = 'fixed';
                        printContent.style.top = '-9999px';
                        printContent.style.left = '-9999px';
                    }, 1000);
                }, 800);

            } catch (error) {
                console.error('Error generating barcode:', error);
                alert('حدث خطأ في إنشاء الباركود. تأكد من صحة البيانات.');
                // إخفاء المحتوى في حالة الخطأ
                printContent.style.display = 'none';
                printContent.style.position = 'fixed';
                printContent.style.top = '-9999px';
                printContent.style.left = '-9999px';
            }
        }

        // نفس التعديل لدالة printStudentBarcodeFromRow
        function printStudentBarcodeFromRow(row) {
            let studentBarcode = row.dataset.barcode;
            const studentName = row.dataset.studentName;
            const studentCode = row.dataset.studentCode;

            console.log('Auto printing barcode for:', studentName);

            // فحص إذا كان barcode عبارة عن صورة base64
            if (studentBarcode.startsWith('iVBORw0KGgo') || studentBarcode.startsWith('data:image') || !studentBarcode) {
                studentBarcode = studentCode;
            }

            // إظهار محتوى الطباعة مؤقتاً
            const printContent = document.getElementById('printContent');
            printContent.style.display = 'block';
            printContent.style.position = 'fixed';
            printContent.style.top = '0';
            printContent.style.left = '0';
            printContent.style.width = '100%';
            printContent.style.height = 'auto';
            printContent.style.background = 'white';
            printContent.style.zIndex = '9999';
            printContent.style.padding = '20px';
            printContent.style.textAlign = 'center';



            document.getElementById('barcodeText').innerHTML =
                `<div style="font-size: 14px; margin-top: 10px;">${studentBarcode}</div>`;

            try {
                // تنظيف الـ canvas
                const canvas = document.getElementById('barcode');
                const ctx = canvas.getContext('2d');
                ctx.clearRect(0, 0, canvas.width, canvas.height);

                JsBarcode("#barcode", studentBarcode, {
                    format: "CODE128",
                    width: 3,
                    height: 80,
                    displayValue: true,
                    fontSize: 16,
                    margin: 10,
                    background: "#ffffff",
                    lineColor: "#000000"
                });

                setTimeout(() => {
                    window.print();

                    // إخفاء المحتوى مرة أخرى بعد الطباعة
                    setTimeout(() => {
                        printContent.style.display = 'none';
                        printContent.style.position = 'fixed';
                        printContent.style.top = '-9999px';
                        printContent.style.left = '-9999px';
                    }, 1000);
                }, 800);

            } catch (error) {
                console.error('Error generating barcode:', error);
                // إخفاء المحتوى في حالة الخطأ
                printContent.style.display = 'none';
                printContent.style.position = 'fixed';
                printContent.style.top = '-9999px';
                printContent.style.left = '-9999px';
            }
        }
    </script>
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

        function attendanceAbsentForm(studentId) {
            return `
                    <form
                        action="/admin/attendance/mark/${studentId}/0"
                        method="POST" class="d-inline">
                        @csrf
                        <button class="btn btn-danger btn-sm">تسجيل غياب</button>
                    </form>
            `;
        }

        function attendancePresentForm(studentId) {
            return `
                    <form
                        action="/admin/attendance/mark/${studentId}/1"
                        method="POST" class="d-inline">
                        @csrf
                        <button class="btn btn-success btn-sm">تسجيل حضور</button>
                    </form>
            `;
        }

        // دالة الحضور 
        function markAttendance(code) {
            const input = document.getElementById('barcode-input');
            const result = document.getElementById('barcode-result');
            let row = document.querySelector(`tr[data-student-code="${code}"]`);

            if (!row) {
                alert('لا يوجد طالب بهذا الكود');
                input.disabled = false;
                input.focus();
                return;
            }

            let AttendanceStatus = parseInt(row.dataset.status);

            if (AttendanceStatus == 1) {
                alert('تم تحضير هذا الطالب من قبل');
                input.disabled = false;
                input.focus();
                return;
            }

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
                        let studentId = row.dataset.studentId;
                        let formAttendanceCell = row.querySelector('td.formAttendance');
                        let formStatusCell = row.querySelector('td.formStatus');

                        formStatusCell.innerHTML = `<span class="badge bg-success">حاضر</span>`;
                        formAttendanceCell.innerHTML = attendanceAbsentForm(studentId);

                        // طباعة barcode تلقائياً عند الحضور (بدون سؤال)
                        console.log('طباعة تلقائية للطالب:', row.dataset.studentName);
                        printStudentBarcodeFromRow(row);
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

        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('barcode-form');
            const input = document.getElementById('barcode-input');

            input.focus();
            // منع الـ form من عمل reload
            form.addEventListener('submit', function(e) {
                e.preventDefault();
            });

            // تنفيذ فقط عند الضغط على Enter
            input.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault(); // منع الريلود

                    // استنى شوية قبل ما تقرأ القيمة
                    setTimeout(() => {
                        const code = input.value.trim();
                        if (code) {
                            markAttendance(code);
                        }
                    }, 100);
                }
            });
        });
    </script>

@endsection
