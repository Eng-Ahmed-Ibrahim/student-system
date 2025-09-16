@extends('admin.app')
@php
    $monthNames = [
        'يناير',
        'فبراير',
        'مارس',
        'أبريل',
        'مايو',
        'يونيو',
        'يوليو',
        'أغسطس',
        'سبتمبر',
        'أكتوبر',
        'نوفمبر',
        'ديسمبر',
    ];
    $grades = ['الصف الاول الثانوي', 'الصف الثاني الثانوي', 'الصف الثالث الثانوي'];

    $title = 'الماليات';
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

                    @can('download financial reports')
                        <a href="{{ request()->fullUrlWithQuery(['download' => 'excel']) }}" class="btn btn-success mb-3">
                            تحميل Excel 📥
                        </a>
                    @endcan
                </div>
            </div>
        </div>
        <div id="kt_app_content" class="app-content flex-column-fluid">
            <div id="kt_app_content_container" class="app-container container-xxl">
                <div class="card">
                    <div class="card-body p-lg-17">


                        <div class="container mt-4">
                            <h2 class="mb-4">💵 تقارير المدفوعات والمستحقات</h2>

                            <form method="GET" action="{{ route('admin.reports.financial') }}" class="row g-3 mb-4">
                                <div class="col-md-3">
                                    <label class="form-label">الطالب</label>
                                    <select name="student_id" id="studentSelect" class="form-select">
                                        <option value="">الكل</option>
                                        @foreach ($students as $student)
                                            <option value="{{ $student->id }}"
                                                {{ request('student_id') == $student->id ? 'selected' : '' }}>
                                                {{ $student->name }}
                                            </option>
                                        @endforeach
                                    </select>

                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">نوع التقرير</label>
                                    <select name="type" class="form-select" onchange="toggleCustomDates(this.value)">
                                        <option value="">اختر</option>
                                        <option value="daily" {{ request('type') == 'daily' ? 'selected' : '' }}>يومي
                                        </option>
                                        <option value="weekly" {{ request('type') == 'weekly' ? 'selected' : '' }}>أسبوعي
                                        </option>
                                        <option value="monthly" {{ request('type') == 'monthly' ? 'selected' : '' }}>شهري
                                        </option>
                                        <option value="yearly" {{ request('type') == 'yearly' ? 'selected' : '' }}>سنوي
                                        </option>
                                        <option value="custom" {{ request('type') == 'custom' ? 'selected' : '' }}>من - إلى
                                        </option>
                                    </select>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label"> الصف </label>
                                    <select name="grade_level" class="form-select" onchange="toggleCustomDates(this.value)">
                                        <option value="">اختر</option>
                                        <option value="1" {{ request('grade_level') == 1 ? 'selected' : '' }}>الاول
                                            الثانوي
                                        <option value="2" {{ request('grade_level') == 2 ? 'selected' : '' }}>الثاني
                                            الثانوي
                                        <option value="3" {{ request('grade_level') == 3 ? 'selected' : '' }}>الثالث
                                            الثانوي
                                        </option>

                                    </select>
                                </div>

                                <div class="col-md-3 custom-dates" style="display: none;">
                                    <label class="form-label">من</label>
                                    <input type="date" name="from" value="{{ request('from') }}"
                                        class="form-control">
                                </div>

                                <div class="col-md-3 custom-dates" style="display: none;">
                                    <label class="form-label">إلى</label>
                                    <input type="date" name="to" value="{{ request('to') }}" class="form-control">
                                </div>

                                <div class="col-md-3 align-self-end">
                                    <button type="submit" class="btn btn-primary w-100">عرض التقرير</button>
                                </div>
                            </form>

                            <div class="alert alert-success">
                                <strong>إجمالي المدفوعات: </strong>{{ number_format($totalPayments, 2) }} |
                                <strong>إجمالي المستحقات: </strong>{{ number_format($totalFees, 2) }}
                            </div>

                            <h4>📌 المدفوعات</h4>
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>كود</th>
                                        <th>اسم الطالب</th>
                                        <th>الصف</th>
                                        <th>تاريخ الدفع</th>
                                        <th>المبلغ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($payments as $payment)
                                        <tr>
                                            <td> <a
                                                    href="{{ route('admin.students.show', $payment->student->id) }}">{{ $payment->student->student_code ?? '---' }}</a>
                                            </td>
                                            <td>{{ $payment->student->name }}</td>
                                            <td>{{ $grades[$payment->grade_level - 1] }}</td>
                                            <td>{{ $payment->payment_date }}</td>
                                            <td>{{ number_format($payment->amount, 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center">لا توجد مدفوعات</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                            {{ $payments->links('vendor.pagination.custom', ['pageName' => 'payments_page']) }}

                            <h4>📌 المستحقات</h4>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>كود الطالب</th>
                                        <th>اسم الطالب</th>
                                        <th>رقم التلفون</th>
                                        <th>رقم التلفون ولي الامر</th>
                                        <th>الصف</th>

                                        <th>الشهر</th>
                                        <th>المبلغ المستحق</th>
                                        <th>المبلغ المدفوع</th>
                                        <th>المبلغ المتبقي</th>
                                        <th>الحالة</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($studentFees as $fee)
                                        <tr>
                                            <td> <a
                                                    href="{{ route('admin.students.show', $fee->student->id) }}">{{ $fee->student->student_code ?? '---' }}</a>
                                            </td>
                                            <td>{{ $fee->student->name }}</td>
                                            <td>{{ $fee->student->phone }}</td>
                                            <td>{{ $fee->student->parent_phone }}</td>
                                            <td>{{ $grades[$fee->grade_level - 1] }}</td>

                                            <td>{{ $monthNames[$fee->month - 1] }}</td>
                                            <td>{{ number_format($fee->final_amount, 2) }}</td>
                                            <td>{{ number_format($fee->payments_sum_amount, 2) }}</td>
                                            @php $remain= $fee->final_amount - $fee->payments_sum_amount; @endphp
                                            <td>{{ number_format($remain, 2) }}</td>
                                            <td>{{ $fee->status }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center">لا توجد مستحقات</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                            {{ $studentFees->links('vendor.pagination.custom', ['pageName' => 'fees_page']) }}

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
