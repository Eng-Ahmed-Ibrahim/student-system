@extends('admin.app')

@php
    $title = 'لوحه التحكم';
    $sub_title = 'لوحه التحكم';
@endphp

@section('title', $title)

@section('content')
    <div class="d-flex flex-column flex-column-fluid">

        <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
            <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                    <h1 class="page-heading text-dark fw-bold fs-3 my-0">{{ $title }}</h1>
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
            </div>
        </div>

        <div id="kt_app_content" class="app-content flex-column-fluid">
            <div id="kt_app_content_container" class="app-container container-xxl">
                <div class="row">

                    <div class="card col-md-6 col-12 mb-5">
                        <div class="card-body  ">
                            <h3 class="mb-4">📊 عدد الطلاب في كل صف</h3>
                            <canvas id="gradeChart" height="100"></canvas>
                        </div>
                    </div>

                    <div class="card col-md-6 col-12 mb-5">
                        <div class="card-body py-2"  style="height: 150px;overflow-y: scroll;">
                            <h3 class="mb-4">👥 عدد الطلاب في كل مجموعة</h3>
                            <ul class="list-group">
                                @foreach ($groupsWithStudentCount as $group)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        {{ $group->name }}
                                        <span class="badge bg-primary rounded-pill">{{ $group->students_count }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h3 class="mb-4">📅 جدول الحصص اليوم ({{ \Carbon\Carbon::now()->translatedFormat('l') }})</h3>
                        <ul class="list-group">
                            @forelse($todayGroups as $group)
                                <li class="list-group-item">{{ $group->name }} - {{   \Carbon\Carbon::parse($group->time)->format('h:i A')}} </li>
                            @empty
                                <li class="list-group-item text-muted">لا يوجد مجموعات اليوم</li>
                            @endforelse
                        </ul>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@section('js')
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('gradeChart');

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($labels) !!},
                datasets: [{
                    label: 'عدد الطلاب',
                    data: {!! json_encode($data) !!},
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    },
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>

@endsection
