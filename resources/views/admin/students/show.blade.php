@extends('admin.app')
@php
    $title = "الطالب : $student->name";
    $sub_title = 'الطلاب';
@endphp
@section('title', $title)
@section('content')
    {{-- Block section --}}
    @if ($student->blocked)
        <div class="container">
            <div class="alert alert-primary " role="alert">
                <h4 class="alert-heading">
                    <svg style="height: 50px;" xmlns="http://www.w3.org/2000/svg"
                        class="bi bi-exclamation-triangle-fill flex-shrink-0 me-2" viewBox="0 0 16 16" role="img"
                        aria-label="Warning:">
                        <path
                            d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z">
                        </path>
                    </svg>
                    هذا الطالب محظور
                </h4>
                <p class="mb-0">{{ $student->block_reason }}</p>
            </div>
        </div>
    @endif

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
                        <li class="breadcrumb-item text-muted">تفاصيل الطالب</li>
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

                        <div class="card-body pt-9 pb-0">
                            <!--begin::Details-->
                            <div class="d-flex flex-wrap flex-sm-nowrap">
                                <!--begin: Pic-->
                                <div class="me-7 mb-4">
                                    <div class="symbol symbol-100px symbol-lg-160px symbol-fixed position-relative">
                                        <img src="{{ asset('static/student_avatar.png') }}" alt="image">

                                    </div>
                                </div>
                                <!--end::Pic-->
                                <!--begin::Info-->
                                <div class="flex-grow-1">
                                    <!--begin::Title-->
                                    <div class="d-flex justify-content-between align-items-start flex-wrap mb-2">
                                        <!--begin::User-->
                                        <div class="d-flex flex-column">
                                            <!--begin::Name-->
                                            <div class="d-flex align-items-center mb-2">
                                                <a href="#"
                                                    class="text-gray-900 text-hover-primary fs-2 fw-bold me-1">{{ $student->name }}</a>
                                                <a href="#">
                                                    <i class="ki-duotone ki-verify fs-1 text-primary">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                    </i>
                                                </a>
                                            </div>
                                            <!--end::Name-->
                                            <!--begin::Info-->
                                            <div class="d-flex flex-wrap fw-semibold fs-6 mb-4 pe-2">
                                                <a href="#"
                                                    class="d-flex align-items-center text-gray-400 text-hover-primary me-5 mb-2">
                                                    <i class="ki-duotone ki-profile-circle fs-4 me-1">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                        <span class="path3"></span>
                                                    </i>{{ $student->group->name }}</a>
                                                <a href="#"
                                                    class="d-flex align-items-center text-gray-400 text-hover-primary me-5 mb-2">
                                                    <i class="ki-duotone ki-geolocation fs-4 me-1">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                    </i>{{ $student->phone }}</a>
                                                <a href="#"
                                                    class="d-flex align-items-center text-gray-400 text-hover-primary mb-2">
                                                    <i class="ki-duotone ki-sms fs-4 me-1">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                    </i>رقم ولي الامر : {{ $student->parent_phone }}</a>
                                            </div>
                                            <!--end::Info-->
                                            <!-- زر فتح المودال -->
                                            <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal"
                                                data-bs-target="#paymentModal">
                                                دفع جديد
                                            </button>
                                        </div>

                                    </div>
                                    <!--end::Title-->
                                    <!--begin::Stats-->
                                    <div class="d-flex flex-wrap flex-stack">
                                        <!--begin::Wrapper-->
                                        <div class="d-flex flex-column flex-grow-1 pe-8">
                                            <!--begin::Stats-->
                                            <div class="d-flex flex-wrap">

                                                <div
                                                    class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                                                    <div class="d-flex align-items-center">
                                                        <div class="fs-2 fw-bold counted" data-kt-countup="true"
                                                            data-kt-countup-value="{{ $student->total_fees - $student->total_paid }}"
                                                            data-kt-countup-prefix=" EGP " data-kt-initialized="1">
                                                            {{ $student->total_fees - $student->total_paid }}</div>
                                                    </div>
                                                    <div class="fw-semibold fs-6 text-gray-400">
                                                        المستحقات</div>
                                                </div>

                                                <div
                                                    class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                                                    <div class="d-flex align-items-center">
                                                        <div class="fs-2 fw-bold counted" data-kt-countup="true"
                                                            data-kt-countup-value="{{ $student->total_paid ?? 0 }}"
                                                            data-kt-initialized="1">{{ $student->total_paid ?? 0 }}</div>
                                                    </div>
                                                    <div class="fw-semibold fs-6 text-gray-400">مجموع المدفوعات
                                                    </div>

                                                </div>



                                            </div>
                                            <div class="d-flex flex-wrap">
                                                <div
                                                    class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                                                    <div class="d-flex align-items-center">
                                                        <div class="fs-2 fw-bold counted" data-kt-countup="true"
                                                            data-kt-countup-value="0" data-kt-initialized="1">
                                                            {{ $student->total_present }}</div>
                                                    </div>
                                                    <div class="fw-semibold fs-6 text-gray-400">
                                                        مجموع الحضور الكلي</div>
                                                </div>
                                                <div
                                                    class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                                                    <div class="d-flex align-items-center">
                                                        <div class="fs-2 fw-bold counted" data-kt-countup="true"
                                                            data-kt-countup-value="0" data-kt-initialized="1">
                                                            {{ $student->total_absent }}</div>
                                                    </div>
                                                    <div class="fw-semibold fs-6 text-gray-400">
                                                        مجموع الغياب الكلي</div>
                                                </div>
                                            </div>
                                            <!--end::Stats-->
                                        </div>
                                    </div>
                                    <!--end::Stats-->
                                </div>
                                <!--end::Info-->
                            </div>
                            <!--end::Details-->

                        </div>


                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-body p-lg-17">
                        <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link {{ $tab==1 ?"active" : ' ' }}" id="pills-home-tab" data-bs-toggle="pill"
                                    data-bs-target="#pills-home" type="button" role="tab"
                                    aria-controls="pills-home" aria-selected="true">الحضور والغياب</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link {{ $tab==2 ?"active" : ' ' }}" id="pills-profile-tab" data-bs-toggle="pill"
                                    data-bs-target="#pills-profile" type="button" role="tab"
                                    aria-controls="pills-profile" aria-selected="false">المدفوعات</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link {{ $tab==3 ?"active" : ' ' }} " id="pills-contact-tab" data-bs-toggle="pill"
                                    data-bs-target="#pills-contact" type="button" role="tab"
                                    aria-controls="pills-contact" aria-selected="false">الدرجات</button>
                            </li>

                        </ul>
                        <div class="tab-content" id="pills-tabContent">
                            <div class="tab-pane fade {{ $tab==1 ?" show active" : ' ' }}" id="pills-home" role="tabpanel"
                                aria-labelledby="pills-home-tab" tabindex="0">

                                @php
                                    $arabicMonths = [
                                        1 => 'يناير',
                                        2 => 'فبراير',
                                        3 => 'مارس',
                                        4 => 'أبريل',
                                        5 => 'مايو',
                                        6 => 'يونيو',
                                        7 => 'يوليو',
                                        8 => 'أغسطس',
                                        9 => 'سبتمبر',
                                        10 => 'أكتوبر',
                                        11 => 'نوفمبر',
                                        12 => 'ديسمبر',
                                    ];
                                @endphp

                                <!-- فلتر الشهر -->
                                <form method="GET" action="">
                                    <input type="hidden" name="tab" value="1">

                                    <div class="mb-3">
                                        <label for="month">اختر الشهر:</label>
                                        <select name="month" id="month" class="form-select"
                                            onchange="this.form.submit()">
                                            @foreach ($arabicMonths as $key => $name)
                                                <option value="{{ $key }}"
                                                    {{ request('month', now()->month) == $key ? 'selected' : '' }}>
                                                    {{ $name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </form>


                                <!-- إحصائيات -->
                                <div class="mb-3">
                                    <span class="badge bg-success">عدد الحضور: {{ $presentCount }}</span>
                                    <span class="badge bg-danger">عدد الغياب: {{ $absentCount }}</span>
                                </div>

                                <!-- جدول الحضور / الغياب -->
                                <div class="tab-pane fade show active" id="pills-home" role="tabpanel"
                                    aria-labelledby="pills-home-tab" tabindex="0">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>التاريخ</th>
                                                <th> وقت تسجيل الحضور </th>
                                                <th>موعد الحصة</th>
                                                <th>الحالة</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($attendances as $attendance)
                                                <tr>
                                                    <td>{{ $attendance->date }}</td>
                                                    <td>{{ $attendance->time ? \Carbon\Carbon::parse($attendance->time)->format('h:i A') : ' لم يحضر ' }}
                                                    <td>{{ \Carbon\Carbon::parse($attendance->class_start_at)->format('h:i A') }}
                                                    </td>
                                                    <td>
                                                        @if ($attendance->status)
                                                            <span class="badge bg-success">حضور</span>
                                                        @else
                                                            <span class="badge bg-danger">غياب</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="3" class="text-center">لا توجد بيانات حضور في هذا
                                                        الشهر.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>

                            </div>

                            <div class="tab-pane fade {{ $tab==2 ?" show active" : ' ' }}" id="pills-profile" role="tabpanel"
                                aria-labelledby="pills-profile-tab" tabindex="0">


                                <!-- فلتر الشهر -->
                                <form method="GET" action="">
                                    <input type="hidden" name="tab" value="2">
                                    <div class="mb-3">
                                        <label for="month">اختر الشهر:</label>
                                        <select name="month" id="month" class="form-select"
                                            onchange="this.form.submit()">
                                            @php
                                                $arabicMonths = [
                                                    1 => 'يناير',
                                                    2 => 'فبراير',
                                                    3 => 'مارس',
                                                    4 => 'أبريل',
                                                    5 => 'مايو',
                                                    6 => 'يونيو',
                                                    7 => 'يوليو',
                                                    8 => 'أغسطس',
                                                    9 => 'سبتمبر',
                                                    10 => 'أكتوبر',
                                                    11 => 'نوفمبر',
                                                    12 => 'ديسمبر',
                                                ];
                                            @endphp
                                            @foreach ($availableMonths as $m)
                                                <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                                                    {{ $arabicMonths[$m] }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </form>



                                <!-- جدول المدفوعات -->
                                <div class="tab-pane fade show active" id="pills-profile" role="tabpanel"
                                    aria-labelledby="pills-profile-tab" tabindex="0">
                                    <h6>تفاصيل المدفوعات:</h6>
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>التاريخ</th>
                                                <th>وقت الدفع</th>
                                                <th>المبلغ</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($student->payments as $payment)
                                                <tr>
                                                    <td>{{ \Carbon\Carbon::parse($payment->payment_date)->format('Y-m-d') }}
                                                    </td>
                                                    <td>{{ $payment->time ? \Carbon\Carbon::parse($payment->time)->format('h:i A') : ' ' }}

                                                    <td>{{ $payment->amount }} جنيه</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="2" class="text-center">لا توجد مدفوعات في هذا الشهر.
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>

                            </div>
                            <div class="tab-pane fade  {{ $tab==3 ?" show active" : ' ' }}" id="pills-contact" role="tabpanel"
                                aria-labelledby="pills-contact-tab" tabindex="0">...</div>
                        
                        </div>
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
                <input type="hidden" name="student_id" value="{{ $student->id }}">
                <input type="hidden" name="month" value="{{ $month }}">
                <input type="hidden" name="year" value="{{ now()->year }}">

                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="paymentModalLabel">إضافة دفعة جديدة</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">المبلغ المستحق:</label>
                            <input type="text" class="form-control"
                                value="{{ $student->total_fees - $student->total_paid }}" readonly id="dueAmount">
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

@endsection
