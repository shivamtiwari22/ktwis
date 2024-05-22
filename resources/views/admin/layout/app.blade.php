<!DOCTYPE html>
<html lang="en">

<head>
    @yield('meta_tags')
    @yield('title')

    <!-- App favicon -->

    @php
        use App\Models\SystemSettings;
    
        $system = SystemSettings::where('user_id', Auth::user()->id)->first();
    @endphp

    <link rel="shortcut icon" href="   {{$system ? asset('public/admin/system/'. $system->icon) :  asset('public/assets/images/favicon.ico') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- App css -->
    <link href="{{ asset('public/assets/css/icons.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('public/assets/css/app.min.css') }}" rel="stylesheet" type="text/css" id="light-style">
    <link href="{{ asset('public/assets/css/app-dark.min.css') }}" rel="stylesheet" type="text/css" id="dark-style">
    <link href="{{ asset('public/assets/css/vendor/buttons.bootstrap5.css') }}" rel="stylesheet" type="text/css">


    <link href="{{ asset('public/assets/css/vendor/dataTables.bootstrap5.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('public/assets/css/vendor/responsive.bootstrap5.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('public/assets/css/vendor/buttons.bootstrap5.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('public/assets/css/vendor/select.bootstrap5.css') }}" rel="stylesheet" type="text/css">
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">


    @yield('css')

    <style>
        .error {
            color: red
        }

        .f-size {
            font-size: 15px !important;
        }
    </style>

</head>

<body class="loading"
    data-layout-config='{"leftSideBarTheme":"dark","layoutBoxed":false, "leftSidebarCondensed":false, "leftSidebarScrollable":false,"darkMode":false, "showRightSidebarOnStart": true}'>
    <!-- Begin page -->
    <div class="wrapper">
        <!-- ========== Left Sidebar Start ========== -->
        @include('admin.include.sidebar')
        <!-- Left Sidebar End -->

        <!-- ============================================================== -->
        <!-- Start Page Content here -->
        <!-- ============================================================== -->

        <div class="content-page">
            <div class="content">
                <!-- Topbar Start -->
                @include('admin.include.navbar')
                <!-- end Topbar -->

                <!-- Start Content-->
                <div class="container-fluid">
                    @yield('main_content')
                </div>
                <!--End content -->

            </div>

            @include('admin.include.footer')

        </div>
    </div>

    <!-- Right Sidebar -->
    @include('admin.include.settingbar')
    <div class="rightbar-overlay"></div>
    <!-- /End-bar -->

    <!-- bundle -->
    <script src="{{ asset('public/assets/js/vendor.min.js') }}"></script>
    <script src="{{ asset('public/assets/js/app.min.js') }}"></script>

    <!-- Apex js -->
    {{-- <script src="{{asset('public/assets/js/vendor/apexcharts.min.js')}}"></script> --}}

    <!-- Todo js -->
    <script src="{{ asset('public/assets/js/ui/component.todo.js') }}"></script>

    {{-- Toster js --}}
    <script src="{{ asset('public/assets/js/pages/demo.toastr.js') }}"></script>

    <!-- demo app -->
    {{-- <script src="{{asset('public/assets/js/pages/demo.dashboard-crm.js')}}"></script> --}}
    <!-- end demo js-->

    <!-- Datatables js -->
    {{-- <script src="{{asset('public/assets/js/vendor/dataTables.buttons.min.js')}}"></script>
    <script src="{{asset('public/assets/js/vendor/buttons.bootstrap5.min.js')}}"></script>
    <script src="{{asset('public/assets/js/vendor/buttons.html5.min.js')}}"></script>
    <script src="{{asset('public/assets/js/vendor/buttons.flash.min.js')}}"></script>
    <script src="{{asset('public/assets/js/vendor/buttons.print.min.js')}}"></script>
    <script src="{{asset('public/assets/js/vendor/dataTables.select.min.js')}}"></script>
    <script src="{{asset('public/assets/js/vendor/dataTables.bootstrap5.js')}}"></script> --}}


    {{-- <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script> --}}

    <!-- third party js -->
    <script src="{{ asset('public/assets/js/vendor/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('public/assets/js/vendor/dataTables.bootstrap5.js') }}"></script>
    <script src="{{ asset('public/assets/js/vendor/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('public/assets/js/vendor/responsive.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('public/assets/js/vendor/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('public/assets/js/vendor/buttons.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('public/assets/js/vendor/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('public/assets/js/vendor/buttons.flash.min.js') }}"></script>
    <script src="{{ asset('public/assets/js/vendor/buttons.print.min.js') }}"></script>
    <script src="{{ asset('public/assets/js/vendor/dataTables.keyTable.min.js') }}"></script>
    <script src="{{ asset('public/assets/js/vendor/dataTables.select.min.js') }}"></script>
    <!-- third party js ends -->

    <!-- demo app -->
    <script src="{{ asset('public/assets/js/pages/demo.datatable-init.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>


    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <!-- demo toastr -->

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/js/bootstrap-select.min.js"></script>
    <!-- end demo js-->

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
    

    <script>
        $('table').dataTable({
            responsive: false,
            "scrollX": true,
        });
    </script>
    <script type="module">
        // Import the functions you need from the SDKs you need
        import {
            initializeApp
        } from "https://www.gstatic.com/firebasejs/10.1.0/firebase-app.js";
        // TODO: Add SDKs for Firebase products that you want to use
        // https://firebase.google.com/docs/web/setup#available-libraries

        // Your web app's Firebase configuration
        const firebaseConfig = {
            apiKey: "AIzaSyBujCW1mClUvZ8iROVJlNFQokjFi9_HDfw",
            authDomain: "bytelogic-spark.firebaseapp.com",
            databaseURL: "https://bytelogic-spark-default-rtdb.firebaseio.com",
            projectId: "bytelogic-spark",
            storageBucket: "bytelogic-spark.appspot.com",
            messagingSenderId: "117694769619",
            appId: "1:117694769619:web:9874dbc7e6c35fdc6ee726",
            measurementId: "G-NXFJ9BCX4K"
        };

        // Initialize Firebase
        const app = initializeApp(firebaseConfig);
    </script>

    <script>
            $(document).ready(function(){
            $(".show").removeClass("end-bar-enabled")
            });
    </script>
    @yield('script')
</body>

</html>
