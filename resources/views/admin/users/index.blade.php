@extends('admin.app')
@php
    $title = 'المستخدمين';
    $sub_title = 'المستخدمين';
    $current_user = Auth::user();
@endphp
@section('title', $title)
@section('content')
    <div class="d-flex flex-column flex-column-fluid">

        <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
            <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                    <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                        {{ $title }}</h1>
                </div>
                @can('create users')
                    <div class="d-flex align-items-center gap-2 gap-lg-3">
                        <!-- زرار إضافة مستخدم -->
                        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addUserModal">
                            إضافة مستخدم
                        </button>
                    </div>
                @endcan
            </div>
        </div>

        <div id="kt_app_content" class="app-content flex-column-fluid">
            <div id="kt_app_content_container" class="app-container container-xxl">
                <div class="card">
                    <div class="card-body p-lg-17">

                        <!-- رسائل -->
                        @if (session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif
                        @if (session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif

                        <!-- جدول المستخدمين -->
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>الاسم</th>
                                    <th>البريد الإلكتروني</th>
                                    <th>الدور</th>
                                    @if ($current_user->can('edit users') || $current_user->can('delete users'))
                                        <th>الإجراءات</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $u)
                                    <tr>
                                        <td>{{ $u->id }}</td>
                                        <td>{{ $u->name }}</td>
                                        <td>{{ $u->email }}</td>
                                        <td>{{ $u->roles->pluck('name')->join(', ') }}</td>
                                        @if ($current_user->can('edit users') || $current_user->can('delete users'))
                                            <td class="d-flex align-items-center gap-2 justify-content-center">
                                                @can('edit users')
                                                    <button class="btn btn-sm btn-warning editBtn" data-id="{{ $u->id }}"
                                                        data-name="{{ $u->name }}" data-email="{{ $u->email }}"
                                                        data-role="{{ $u->roles->first()->name ?? '' }}" data-bs-toggle="modal"
                                                        data-bs-target="#editUserModal">تعديل</button>
                                                @endcan
                                                @can('delete users')
                                                    <form action="{{ route('admin.users.destroy', $u->id) }}" method="post">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button class="btn btn-sm btn-danger deleteBtn">حذف</button>
                                                    </form>
                                                @endcan
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>

                        </table>
                    </div>

                    <!-- مودال إضافة -->
                    <div class="modal fade" id="addUserModal" tabindex="-1">
                        <div class="modal-dialog">
                            <form method="POST" action="{{ route('admin.users.store') }}">
                                @csrf
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5>إضافة مستخدم</h5>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label>الاسم</label>
                                            <input type="text" name="name" class="form-control" required>
                                        </div>
                                        <div class="mb-3">
                                            <label>البريد الإلكتروني</label>
                                            <input type="email" name="email" class="form-control" required>
                                        </div>
                                        <div class="mb-3">
                                            <label>الدور</label>
                                            <select name="role" class="form-control" required>
                                                <option value="">اختر الدور</option>
                                                @foreach ($roles as $role)
                                                    <option value="{{ $role->name }}">{{ $role->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label>كلمة المرور</label>
                                            <input type="password" name="password" class="form-control" required>
                                        </div>
                                        <div class="mb-3">
                                            <label>تأكيد كلمة المرور</label>
                                            <input type="password" name="password_confirmation" class="form-control"
                                                required>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">إلغاء</button>
                                        <button type="submit" class="btn btn-success">إضافة</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- مودال تعديل -->
                    <div class="modal fade" id="editUserModal" tabindex="-1">
                        <div class="modal-dialog">
                            <form method="POST" id="editUserForm">
                                @csrf
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5>تعديل مستخدم</h5>
                                    </div>
                                    <div class="modal-body">
                                        <input type="hidden" name="id" id="editUserId">
                                        <div class="mb-3">
                                            <label>الاسم</label>
                                            <input type="text" name="name" id="editUserName" class="form-control"
                                                required>
                                        </div>
                                        <div class="mb-3">
                                            <label>البريد الإلكتروني</label>
                                            <input type="email" name="email" id="editUserEmail" class="form-control"
                                                required>
                                        </div>
                                        <div class="mb-3">
                                            <label>الدور</label>
                                            <select name="role" id="editUserRole" class="form-control" required>
                                                <option value="">اختر الدور</option>
                                                @foreach ($roles as $role)
                                                    <option value="{{ $role->name }}">{{ $role->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label>كلمة المرور (اتركها فارغة إذا لم تتغير)</label>
                                            <input type="password" name="password" class="form-control">
                                        </div>
                                        <div class="mb-3">
                                            <label>تأكيد كلمة المرور</label>
                                            <input type="password" name="password_confirmation" class="form-control">
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">إلغاء</button>
                                        <button type="submit" class="btn btn-warning">تحديث</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- مودال حذف -->
                    <div class="modal fade" id="deleteUserModal" tabindex="-1">
                        <div class="modal-dialog">
                            <form method="POST" id="deleteUserForm">
                                @csrf
                                @method('DELETE')
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5>تأكيد الحذف</h5>
                                    </div>
                                    <div class="modal-body">
                                        <p>هل أنت متأكد أنك تريد حذف هذا المستخدم؟</p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">إلغاء</button>
                                        <button type="submit" class="btn btn-danger">حذف</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        // تمرير البيانات للمودال تعديل
        document.querySelectorAll('.editBtn').forEach(btn => {
            btn.addEventListener('click', function() {
                let id = this.dataset.id;
                let name = this.dataset.name;
                let email = this.dataset.email;
                let role = this.dataset.role;

                document.getElementById('editUserId').value = id;
                document.getElementById('editUserName').value = name;
                document.getElementById('editUserEmail').value = email;

                // حدد الدور الحالي
                let roleSelect = document.getElementById('editUserRole');
                roleSelect.value = role;

                let form = document.getElementById('editUserForm');
                form.action = `/admin/users/${id}`;
            });
        });
    </script>
@endsection
