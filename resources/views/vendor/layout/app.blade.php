<!DOCTYPE html>
<html lang="en">

<head>
    @yield('meta_tags')
    @yield('title')



    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('public/assets/images/asset_10.png') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
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
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/css/bootstrap-select.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">


    {{-- <link href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css"> --}}

    @yield('css')
    <style>
        .error {
            color: red;
        }

        .f-size {
            font-size: 15px !important;
        }

        .loader {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 9999;
        }

        .loader-wheel {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .loader-wheel .spinner {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border-top: 4px solid #f3f3f3;
            border-right: 4px solid #f3f3f3;
            border-bottom: 4px solid #f3f3f3;
            border-left: 4px solid #337ab7;
            animation: spin 1s infinite linear;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

    </style>
</head>

@php
    use App\Models\Shop;
    use App\Models\Wishlist;
    use App\Models\Cart;
    use App\Models\CancelOrderRequest;
    use App\Models\Message;
    use App\Models\Dispute;
    use App\Models\Product;
    use App\Models\Order;

    $shop = Shop::where('vendor_id', Auth::user()->id)->first();
    //    counts
    $orderCount = Order::where('seller_id', Auth::user()->id)->count();
    $wishlistCount = Wishlist::whereHas('product', function ($query) {
        $query->where('created_by', '=', Auth::user()->id);
    })->count();
    $cartCount = Cart::where('seller_id', Auth::user()->id)->count();
    $cancelCount = CancelOrderRequest::join('orders', 'cancel_order_requests.order_id', '=', 'orders.id')
        ->where('cancel_order_requests.status', 'NEW')
        ->where('orders.seller_id', Auth::user()->id)
        ->get()
        ->groupBy('order_id')
        ->count();

    $messagesCount = Message::where(function ($query) {
        $query->where('spam', '=', null)->orWhere('spam', '=', 0);
    })
        ->where('received_by', Auth::user()->id)
        ->where(function ($query) {
            $query->where('message', '!=', null);
        })
        ->where(function ($query) {
            $query->where('draft', '=', 0);
        })
        ->count();

    $disputeCount = Dispute::where('vendor_id', Auth::user()->id)->count();
    $reviewsCount = Product::with('reviews')
        ->whereHas('reviews')
        ->where('created_by', Auth::user()->id)
        ->count();

@endphp

<body class="loading"
    data-layout-config='{"leftSideBarTheme":"dark","layoutBoxed":false, "leftSidebarCondensed":false, "leftSidebarScrollable":false,"darkMode":false, "showRightSidebarOnStart": true}'>
    <!-- Begin page -->
   
    <div class="wrapper">
        <!-- ========== Left Sidebar Start ========== -->
        @include('vendor.include.sidebar')
        <!-- Left Sidebar End -->

        <!-- ============================================================== -->
        <!-- Start Page Content here -->
        <!-- ============================================================== -->

        <div class="content-page">
            <div class="content">
                <!-- Topbar Start -->
                @include('vendor.include.navbar')
                <!-- end Topbar -->

                <!-- Start Content-->
                <div class="container-fluid">

                    @if ($shop->email_is_verified == 0)
                        <div class="alert  alert-primary alert-dismissible fade show" role="alert">
                            <strong><i class="uil-comment-info"></i> Notice!</strong> Your email address is not
                            verified,
                            please verify to get full access. <u><a href="{{ url('vendor/send/verification-mail') }}">
                                    Resend verification link</a></u>
                            <button type="button" class="close" data-dismiss="alert" style="float: right"
                                aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if ($shop->status == 'inactive')
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong><i class="uil-shield-exclamation"></i> Alert!</strong> Your store is on hold! We
                            will
                            review and approve your store as soon as possible!
                            @if ($shop->maintenance_mode == 1)
                                <a href="{{ url('vendor/setting/shops/create') }}"><button
                                        class="btn-btn-sm secondary  ms-5">
                                        Take Action</button></a>
                            @endif
                            <button type="button" class="close" data-dismiss="alert" style="float: right"
                                aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif
                   
                    <div class="loader" id="loader">
                        <div class="loader-wheel">
                            <div class="spinner"></div>
                        </div>
                    </div>
                    @yield('main_content')
                </div>
                <!--End content -->

            </div>

            @include('vendor.include.footer')

        </div>
    </div>


    <!-- Right Sidebar -->
 
    {{-- <div class="rightbar-overlay"></div> --}}
    <!-- /End-bar -->

    <!-- bundle -->
    <script src="{{ asset('public/assets/js/vendor.min.js') }}"></script>
    <script src="{{ asset('public/assets/js/app.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
    <!-- Apex js -->
    {{-- <script src="{{asset('public/assets/js/vendor/apexcharts.min.js')}}"></script> --}}

    <!-- Todo js -->
    <script src="{{ asset('public/assets/js/ui/component.todo.js') }}"></script>

    {{-- Toster js --}}
    <script src="{{ asset('public/assets/js/pages/demo.toastr.js') }}"></script>

    <!-- demo app -->
    {{-- <script src="{{asset('public/assets/js/pages/demo.dashboard-crm.js')}}"></script> --}}
    <!-- end demo js-->
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
    @if (Session::has('message'))
        <script>
            $(document).ready(function() {
                toastr.success('{{ Session::get('message') }}');
            });
        </script>
    @endif


    <script>

$(window).on('load',function(){
    $('#loader').show();
	setTimeout(function(){ // allowing 3 secs to fade out loader
        $('#loader').hide();
	},500);
});


// hide side bar setting 
$(document).ready(function(){
            $(".show").removeClass("end-bar-enabled")
            });


        // $('table').dataTable();
        $(document).ready(function() {
            $('#myTable').DataTable({
                responsive: false
            });


        });
        // $('table').dataTable({
        //     "lengthChange": false
        // });

      
    </script>
    
    <!-- end demo js-->
    <!-- demo toastr -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <!-- demo toastr -->

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/js/bootstrap-select.min.js"></script>



    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
    
        <script src = "https://www.gstatic.com/firebasejs/8.3.2/firebase.js" ></script>

        

<script>
    var firebaseConfig = {
        apiKey: "AIzaSyDpzrHVoIgNR8Mf8bKvX7k1z-gfq-YRxL8",
        authDomain: "gspark-1bdae.firebaseapp.com",
        projectId: "gspark-1bdae",
        storageBucket: "gspark-1bdae.appspot.com",
        messagingSenderId: "231857528437",
        appId: "1:231857528437:web:ca6e08f9e7d06d0a43e23c",
        measurementId: "G-H2TM7V6RKX"
    };

    firebase.initializeApp(firebaseConfig);
    const messaging = firebase.messaging();

        messaging
            .requestPermission()
            .then(function() {
                return messaging.getToken({vapidKey:"BNHvrfeViDLJC3PI29sLmjbi5rC9mDLmjUAochbHPdzjsnB_uMgCY-U0GkH0p0rTcHWPgIgAfILAYq7c9ctj1NY"})
            })
            .then(function(response) {
                console.log(response);
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: '{{ route('vendor.fcmToken') }}',
                    type: 'POST',
                    data: {
                        token: response
                    },
                    dataType: 'JSON',
                    success: function(response) {
                        // alert('Token stored.');
                        console.log('Token Stored');
                    },
                    error: function(error) {
                        // alert(error);
                        console.log(error);
                    },
                });
            }).catch(function(error) {
                // alert(error);
                console.log(error);
            });

    
    messaging.onMessage(function(payload) {
        const title = payload.notification.title;
        const options = {
            body: payload.notification.body,
            icon: 'public/assets/images/notify_icon.jpeg',
        };
        new Notification(title, options);
    });
</script>



    @yield('script')
</body>

</html>
