@extends('admin.app')
@php
    $title = 'المجموعات';
    $sub_title = 'المجموعات';
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

                    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addGroupModal">Add
                        Group</button>

                </div>
            </div>
        </div>
        <div id="kt_app_content" class="app-content flex-column-fluid">
            <div id="kt_app_content_container" class="app-container container-xxl">
                <div class="card">
                    <div class="card-body p-lg-17">




                        <table class="table table-bordered text-center">
                            <thead class="table-light">
                                <tr>
                                    <th>الكود</th>
                                    <th>الاسم</th>
                                    <th>الصف</th>
                                    <th>الحد الأقصى</th>
                                    <th>الأيام</th>
                                    <th>الوقت</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $daysAr = [
                                        'Saturday' => 'السبت',
                                        'Sunday' => 'الأحد',
                                        'Monday' => 'الاثنين',
                                        'Tuesday' => 'الثلاثاء',
                                        'Wednesday' => 'الأربعاء',
                                        'Thursday' => 'الخميس',
                                        'Friday' => 'الجمعة',
                                    ];
                                @endphp

                                @php
                                $grades=[
                                    "1"=>"الصف الاول الثانوي",
                                    "2"=>"الصف الثاني الثانوي",
                                    "3"=>"الصف الثالث الثانوي"
                                ]
                                @endphp
                                @foreach ($groups as $group)
                                    <tr>
                                        <td>{{ $group->code }}</td>
                                        <td>{{ $group->name }}</td>
                                        <td>{{ $grades[$group->grade_level] }}</td>
                                        <td>{{ $group->limit }}</td>

                                        {{-- عرض الأيام بالعربية --}}
                                        <td>
                                            @foreach ($group->days as $day)
                                                {{ $daysAr[$day] ?? $day }}{{ !$loop->last ? '، ' : '' }}
                                            @endforeach
                                        </td>

                                        {{-- الوقت بصيغة AM/PM --}}
                                        <td>{{ \Carbon\Carbon::createFromFormat('H:i:s', $group->time)->format('h:i A') }}
                                        </td>
                                        </td>

                                        <td>
                                            
                                            <div class="d-flex gap-1">

                                                <button class="btn btn-sm btn-warning edit-btn" data-id="{{ $group->id }}"
                                                    data-name="{{ $group->name }}" data-code="{{ $group->code }}"
                                                    data-limit="{{ $group->limit }}" data-days='@json($group->days)'
                                                    data-time="{{ $group->time }}"
                                                    data-grade_level="{{ $group->grade_level }}" data-bs-toggle="modal"
                                                    data-bs-target="#editGroupModal">
                                                    تعديل
                                                </button>
                                                <a class="btn btn-sm btn-primary" href="{{ route('admin.attendance.index',['group'=>$group->id]) }}">الحضور والغياب</a>
                                            </div>
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
    <!-- مودال إضافة مجموعة -->
    <div class="modal fade" id="addGroupModal" tabindex="-1" role="dialog" aria-labelledby="addGroupModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <form action="{{ route('admin.groups.store') }}" method="POST" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">إضافة مجموعة</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label for="grade_level" class="form-label">الصف الدراسي</label>
                        <select name="grade_level" id="grade_level" class="form-select" required>
                            <option value="" disabled selected>اختر الصف الدراسي</option>
                            <option value="1"
                                {{ old('grade_level', $student->grade_level ?? '') == 1 ? 'selected' : '' }}>الصف الأول
                                الثانوي</option>
                            <option value="2"
                                {{ old('grade_level', $student->grade_level ?? '') == 2 ? 'selected' : '' }}>الصف الثاني
                                الثانوي</option>
                            <option value="3"
                                {{ old('grade_level', $student->grade_level ?? '') == 3 ? 'selected' : '' }}>الصف الثالث
                                الثانوي</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <input type="text" name="name" class="form-control" placeholder="اسم المجموعة" required>
                    </div>

                    <div class="mb-3">
                        <input type="text" name="code" class="form-control" placeholder="كود المجموعة" required>
                    </div>

                    <div class="mb-3">
                        <input type="number" name="limit" class="form-control" placeholder="الحد الأقصى للطلاب" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">أيام الحضور:</label>
                        <div>
                            @foreach (['Saturday' => 'السبت', 'Sunday' => 'الأحد', 'Monday' => 'الاثنين', 'Tuesday' => 'الثلاثاء', 'Wednesday' => 'الأربعاء', 'Thursday' => 'الخميس', 'Friday' => 'الجمعة'] as $day => $label)
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="days[]"
                                        value="{{ $day }}" id="day_{{ $day }}">
                                    <label class="form-check-label"
                                        for="day_{{ $day }}">{{ $label }}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="mb-3">
                        <input type="time" name="time" class="form-control" placeholder="وقت المجموعة" required>
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-success">إضافة</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                </div>
            </form>
        </div>
    </div>


    <!-- مودال تعديل مجموعة -->
    <div class="modal fade" id="editGroupModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form id="editGroupForm" method="POST" class="modal-content">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">تعديل المجموعة</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="editGradeLevel" class="form-label">الصف الدراسي</label>
                        <select name="grade_level" id="editGradeLevel" class="form-select" required>
                            <option value="1">الصف الأول الثانوي</option>
                            <option value="2">الصف الثاني الثانوي</option>
                            <option value="3">الصف الثالث الثانوي</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <input type="text" name="name" class="form-control" id="editName"
                            placeholder="اسم المجموعة" required>
                    </div>

                    <div class="mb-3">
                        <input type="text" name="code" class="form-control" id="editCode"
                            placeholder="كود المجموعة" required>
                    </div>

                    <div class="mb-3">
                        <input type="number" name="limit" class="form-control" id="editLimit"
                            placeholder="الحد الأقصى" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">أيام الحضور:</label>
                        <div id="editDaysContainer">
                            @foreach (['Saturday' => 'السبت', 'Sunday' => 'الأحد', 'Monday' => 'الاثنين', 'Tuesday' => 'الثلاثاء', 'Wednesday' => 'الأربعاء', 'Thursday' => 'الخميس', 'Friday' => 'الجمعة'] as $day => $label)
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input edit-day" type="checkbox" name="days[]"
                                        value="{{ $day }}" id="edit_day_{{ $day }}">
                                    <label class="form-check-label"
                                        for="edit_day_{{ $day }}">{{ $label }}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="mb-3">
                        <input type="time" name="time" class="form-control" id="editTime" required>
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-primary">تحديث</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                </div>
            </form>
        </div>
    </div>


@endsection

@section('js')
    <script>
        document.querySelectorAll('.edit-btn').forEach(button => {
            button.addEventListener('click', function() {
                const form = document.getElementById('editGroupForm');
                const id = this.dataset.id;
                form.action = `/admin/groups/${id}`;

                // تعبئة القيم
                document.getElementById('editName').value = this.dataset.name;
                document.getElementById('editCode').value = this.dataset.code;
                document.getElementById('editLimit').value = this.dataset.limit;
                document.getElementById('editTime').value = this.dataset.time;
                document.getElementById('editGradeLevel').value = this.dataset.grade_level;

                // معالجة الأيام
                const selectedDays = JSON.parse(this.dataset.days);
                document.querySelectorAll('.edit-day').forEach(chk => {
                    chk.checked = selectedDays.includes(chk.value);
                });
            });
        });
    </script>

@endsection
