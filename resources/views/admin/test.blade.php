@extends('admin.app')
@php
    $title = 'الصلاحيات';
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

                        <div id="reader" style="width:300px;"></div>
                        <input type="text" id="barcode_input" placeholder="Scanned result" />
              



                        {{-- <div id="reader" style="width: 300px;"></div>
                        <p>Scanned Code: <span id="scanned-result"></span></p>
                        <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
                        <script>
                            function onScanSuccess(decodedText, decodedResult) {
                                // Show result
                                document.getElementById('scanned-result').innerText = decodedText;

                                // لو حبيت تبعته بفورم
                                // document.getElementById('barcode_input').value = decodedText;
                                // document.getElementById('form').submit();

                                // توقف عن القراءة لو عايز
                                html5QrcodeScanner.clear();
                            }

                            let html5QrcodeScanner = new Html5QrcodeScanner(
                                "reader", {
                                    fps: 10,
                                    qrbox: 250
                                }
                            );
                            html5QrcodeScanner.render(onScanSuccess);
                        </script> --}}


                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
