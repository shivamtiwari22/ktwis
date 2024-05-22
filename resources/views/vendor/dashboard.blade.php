@extends('vendor.layout.app')

@section('meta_tags')
@endsection


@section('title')
@endsection


@section('css')
@endsection


@section('main_content')
    <div class="container-fluid">
        {{-- @if (Session::has('message'))
            <p class="alert alert-info">{{ Session::get('message') }}</p>
        @endif --}}
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        {{-- <form class="d-flex">
                            <div class="input-group">
                                <input type="text" class="form-control form-control-light" id="dash-daterange" disabled>
                                <span class="input-group-text bg-primary border-primary text-white">
                                    <i class="mdi mdi-calendar-range font-13"></i>
                                </span>
                            </div>
                        </form> --}}
                    </div>
                    <h4 class="page-title">{{__('messages.dashboard')}}</h4>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-3">
                <div class="card widget-flat">
                    <div class="card-body">
                        <div class="float-end">
                            <i class="mdi mdi-account-multiple widget-icon"></i>
                        </div>
                        <h5 class="text-muted fw-normal mt-0" title="Number of Customers">{{__('messages.customer')}}</h5>
                        <h3 class="mt-3 mb-3">{{ $userCount }}</h3>
                        <p class="mb-0 text-muted">
                            @if ($currentCount > $previousCount)
                                <span class="text-success me-2"><i class="mdi mdi-arrow-up-bold"></i>
                                    {{ number_format($percentageIncreaseUser, 2) }}%</span> <br>
                                <span class="text-nowrap">{{__('messages.since_last_month')}}</span>
                            @else
                                <span class="text-danger me-2"><i class="mdi mdi-arrow-down-bold"></i>
                                    {{  number_format($percentageDecreaseUser, 2)   }}%</span> <br>
                                <span class="text-nowrap">{{__('messages.since_last_month')}}</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-lg-3">
                <div class="card widget-flat">
                    <div class="card-body">
                        <div class="float-end">
                            <i class="mdi mdi-cart-plus widget-icon"></i>
                        </div>
                        <h5 class="text-muted fw-normal mt-0" title="Number of Orders">{{__('messages.Orders')}}</h5>
                        <h3 class="mt-3 mb-3">{{ $orderCount }}</h3>
                        <p class="mb-0 text-muted">
                            @if ($currentOrderCount > $previousOrderCount)
                                <span class="text-success me-2"><i
                                        class="mdi mdi-arrow-up-bold">{{number_format( $percentageIncreaseOrder, 2) }}%</i></span>  <br>
                                <span class="text-nowrap">{{__('messages.since_last_month')}}</span>
                            @else
                                <span class="text-danger me-2"><i class="mdi mdi-arrow-down-bold"></i>
                                    {{ number_format($percentageDecreaseOrder,2) }}%</span> <br>
                                <span class="text-nowrap">{{__('messages.since_last_month')}}</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-lg-3">
                <div class="card widget-flat">
                    <div class="card-body">
                        <div class="float-end">
                            <i class="mdi mdi-clipboard widget-icon"></i>
                        </div>
                        <h5 class="text-muted fw-normal mt-0" title="Number of Products">{{__('messages.products')}}</h5>
                        <h3 class="mt-3 mb-3">{{ $productCount }}</h3>
                        <p class="mb-0 text-muted">
                            @if ($currentProductCount > $previousProductCount)
                                <span class="text-success me-2"><i
                                        class="mdi mdi-arrow-up-bold">{{ number_format($percentageIncreaseProduct ,2) }}%</i></span><br>
                                <span class="text-nowrap">{{__('messages.since_last_month')}}</span> 
                            @else
                                <span class="text-danger me-2"><i class="mdi mdi-arrow-down-bold"></i>
                                    {{ number_format($percentageDecreaseProduct,2) }}%</span><br>
                                <span class="text-nowrap">{{__('messages.since_last_month')}}</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-lg-3">
                <div class="card widget-flat">
                    <div class="card-body">
                        <div class="float-end">
                            <i class="mdi mdi-sale widget-icon"></i>
                        </div>
                        <h5 class="text-muted fw-normal mt-0" title="Number of Orders">{{__('messages.coupons')}}</h5>
                        <h3 class="mt-3 mb-3">{{ $couponCount }}</h3>
                        <p class="mb-0 text-muted">
                            @if ($currentCouponCount > $previousCouponCount)
                                <span class="text-success me-2"><i
                                        class="mdi mdi-arrow-up-bold">{{ number_format($percentageIncreaseCoupon,2) }}%</i></span><br>
                                <span class="text-nowrap">{{__('messages.since_last_month')}}</span>
                            @else
                                <span class="text-danger me-2"><i class="mdi mdi-arrow-down-bold"></i>
                                    {{ number_format($percentageDecreaseCoupon ,2) }}%</span><br>
                                <span class="text-nowrap">{{__('messages.since_last_month')}}</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6">
                <div class="card widget-flat">
                    <div class="card-body">
                        <div class="float-end">
                            <i class="mdi mdi-currency-usd widget-icon"></i>
                        </div>
                        <h5 class="text-muted fw-normal mt-0" title="Average Revenue">{{__('messages.revenue')}}</h5>
                        <h3 class="mt-3 mb-3">${{ $revenueAmount }}</h3>
                        <p class="mb-0 text-muted">
                            @if ($currentrevenueAmount > $previousrevenueAmount)
                                <span class="text-success me-2"><i
                                        class="mdi mdi-arrow-up-bold">{{ $percentageIncreaseRevenue }}%</i></span>
                                <span class="text-nowrap">{{__('messages.since_last_month')}}</span>
                            @else
                                <span class="text-danger me-2"><i class="mdi mdi-arrow-down-bold"></i>
                                    {{ $percentageDecreaseRevenue }}%</span>
                                <span class="text-nowrap">{{__('messages.since_last_month')}}</span>
                            @endif
                        </p>
                    </div> <!-- end card-body-->
                </div> <!-- end card-->
            </div> <!-- end col-->

            <div class="col-lg-6">
                <div class="card widget-flat">
                    <div class="card-body">
                        <div class="float-end">
                            <i class="mdi mdi-pulse widget-icon"></i>
                        </div>
                        <h5 class="text-muted fw-normal mt-0" title="Growth">{{__('messages.growth')}}</h5>
                        <h3 class="mt-3 mb-3">+ 30.56%</h3>
                        <p class="mb-0 text-muted">
                            <span class="text-success me-2"><i class="mdi mdi-arrow-up-bold"></i> 4.87%</span>
                            <span class="text-nowrap">{{__('messages.since_last_month')}}</span>
                        </p>
                    </div> <!-- end card-body-->
                </div> <!-- end card-->
            </div> <!-- end col-->
        </div> <!-- end row -->

        <div class="row">
            <div class="col-lg-7">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title mb-3">{{__('messages.revenue')}}</h4>
                        <div class="chart-content-bg">
                            <div class="row text-center">
                                <div class="col-md-6">
                                    <p class="text-muted mb-0 mt-3">{{__('messages.current_week')}}</p>
                                    <h2 class="fw-normal mb-3">
                                        <small
                                            class="mdi mdi-checkbox-blank-circle text-primary align-middle me-1"></small>
                                        <span>${{ $currentweekrevenueAmount }}</span>
                                    </h2>
                                </div>
                                <div class="col-md-6">
                                    <p class="text-muted mb-0 mt-3">{{__('messages.previous_week')}}</p>
                                    <h2 class="fw-normal mb-3">
                                        <small
                                            class="mdi mdi-checkbox-blank-circle text-success align-middle me-1"></small>
                                        <span>${{ $previousweekrevenueAmount }}</span>
                                    </h2>
                                </div>
                            </div>
                        </div>

                        <div class="dash-item-overlay d-none d-md-block" dir="ltr">
                            <h5>Today's Earning: ${{ $dayRevenueAmount }}</h5>
                            <p class="text-muted font-13 mb-3 mt-2">Etiam ultricies nisi vel augue. Curabitur ullamcorper
                                ultricies nisi. Nam eget dui.
                                Etiam rhoncus...</p>
                            {{-- <a href="javascript: void(0);" class="btn btn-outline-primary">View Statements
                                <i class="mdi mdi-arrow-right ms-2"></i> --}}
                            </a>
                        </div>
                        <div dir="ltr">
                            <div id="revenue-chart" class="apex-charts mt-3" data-colors="#727cf5,#0acf97"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title mt-2 mb-3">{{__('messages.top_selling_products')}}</h4>
                        <div class="table-responsive">
                            <table class="table table-centered table-nowrap table-hover mb-0">
                                @foreach ($topSellingProducts as $selling)
                                    <tbody>
                                        <tr>
                                            <td>
                                                <h5 class="font-14 my-1 fw-normal">{{ $selling->product->name ?? '' }}
                                                </h5>
                                                <span
                                                    class="text-muted font-13">{{ $selling->product ? $selling->product->created_at->format('d F Y') : '' }}</span>
                                            </td>
                                            <td>
                                                @if ($selling->product)
                                                    @if ($selling->product->inventory)
                                                        <h5 class="font-14 my-1 fw-normal">
                                                            ${{ $selling->product ? $selling->product->inventory->offer_price : '' }}
                                                        </h5>
                                                    @elseif($selling->product->inventoryVariants)
                                                        @foreach ($selling->product->inventoryVariants as $variant)
                                                            @foreach ($variant->variants as $vary)
                                                                @if ($vary->id == $selling->variant_id)
                                                                    <h5 class="font-14 my-1 fw-normal">
                                                                        ${{ $vary->offer_price }}</h5>
                                                                @endif
                                                            @endforeach
                                                        @endforeach
                                                    @endif
                                                @endif
                                                <span class="text-muted font-13">Price</span>
                                            </td>
                                            <td>
                                                <h5 class="font-14 my-1 fw-normal">{{ $selling->quantity }}</h5>
                                                <span class="text-muted font-13">Quantity</span>
                                            </td>
                                            <td>
                                                <h5 class="font-14 my-1 fw-normal">${{ $selling->total_sub_total }}</h5>
                                                <span class="text-muted font-13">Amount</span>
                                            </td>
                                        </tr>
                                    </tbody>
                                @endforeach
                            </table>
                        </div> <!-- end table-responsive-->
                    </div> <!-- end card-body-->
                </div>
            </div>
        </div>
    </div>

@endsection


@section('script')
    <script src="{{ asset('public/assets/js/vendor/apexcharts.min.js') }}"></script>
    {{-- <script src="{{ asset('public/assets/js/app.min.js') }}"></script> --}}
    <script src="{{ asset('public/assets/js/vendor/apexcharts.min.js') }}"></script>
    <script src="{{ asset('public/assets/js/vendor/jquery-jvectormap-1.2.2.min.js') }}"></script>
    <script src="{{ asset('public/assets/js/vendor/jquery-jvectormap-world-mill-en.js') }}"></script>
    <link href="{{ asset('public/assets/css/vendor/jquery-jvectormap-1.2.2.css') }}" rel="stylesheet" type="text/css">
    <script src="{{ asset('public/assets/js/pages/demo.dashboard.js') }}"></script>


    {{-- @if (Session::has('message'))
        <script>
            $(document).ready(function() {
                toastr.success('{{ Session::get('message') }}');    
            });
        </script>
    @endif --}}
@endsection
