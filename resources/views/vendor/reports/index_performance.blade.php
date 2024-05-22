@extends('vendor.layout.app')

@section('meta_tags')
@endsection


@section('title')
@endsection


@section('css')
@endsection


@section('main_content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <form class="d-flex">
                        <div class="input-group">
                            <input type="text" class="form-control form-control-light" id="dash-daterange" disabled>
                            <span class="input-group-text bg-primary border-primary text-white">
                                <i class="mdi mdi-calendar-range font-13"></i>
                            </span>
                        </div>
                    </form>
                </div>
                <h4 class="page-title">Performance</h4>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-3">
            <div class="card widget-flat">
                <div class="card-body">
                    <div class="float-end">
                        <i class="mdi mdi-currency-usd widget-icon"></i>
                    </div>
                    <h5 class="text-muted fw-normal mt-0" title="Average Revenue">Revenue</h5>
                    <h3 class="mt-3 mb-3">${{$sumAmountperyear}}</h3>
                    <p class="mb-0 text-muted">
                        <span class="text-success me-2"><i class="mdi mdi-arrow-up-bold"></i></span>
                        <span class="text-nowrap">Last 12 month</span>
                    </p>
                </div> <!-- end card-body-->
            </div>
        </div>

        <div class="col-lg-3">
            <div class="card widget-flat">
                <div class="card-body">
                    <div class="float-end">
                        <i class="mdi mdi-cart-plus widget-icon"></i>
                    </div>
                    <h5 class="text-muted fw-normal mt-0" title="Number of Orders">Avg Order Value</h5>
                    <h3 class="mt-3 mb-3">${{$avgAmountperyear}}</h3>
                    <p class="mb-0 text-muted">
                        <span class="text-success me-2"><i class="mdi mdi-arrow-up-bold"></i></span>
                        <span class="text-nowrap">Last 12 month</span>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mt-2 mb-3">Top Selling Products</h4>
                    <div class="table-responsive">
                        <table class="table table-centered table-nowrap table-hover mb-0">
                            @foreach ($topSellingProducts as $selling)
                            <tbody>
                                <tr>
                                    <td>
                                        <h5 class="font-14 my-1 fw-normal">{{$selling->product->name}}</h5>
                                        <span class="text-muted font-13">{{ $selling->product->created_at->format('d F Y') }}</span>
                                    </td>
                                    <td>
                                        @if($selling->product->inventory)
                                        <h5 class="font-14 my-1 fw-normal">${{ $selling->product->inventory->offer_price }}</h5>
                                        @elseif($selling->product->inventoryVariants)
                                        @foreach ($selling->product->inventoryVariants as $variant)
                                        @foreach ($variant->variants as $vary )
                                        @if ($vary->id == $selling->variant_id)
                                        <h5 class="font-14 my-1 fw-normal">${{$vary->offer_price}}</h5>
                                        @endif
                                        @endforeach
                                        @endforeach
                                        @endif
                                        <span class="text-muted font-13">Price</span>
                                    </td>
                                    <td>
                                        <h5 class="font-14 my-1 fw-normal">{{$selling->quantity}}</h5>
                                        <span class="text-muted font-13">Quantity</span>
                                    </td>
                                    <td>
                                        <h5 class="font-14 my-1 fw-normal">${{$selling->total_sub_total}}</h5>
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

    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mt-2 mb-3">Top Customer</h4>
                    <div class="table-responsive">
                        @php
                        $totalGrandTotal = 0;
                        @endphp
                        @if (!empty($top_users))
                        <table class="table table-centered table-nowrap table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Orders</th>
                                    <th>Revenue</th>
                                </tr>
                            </thead>
                            @foreach ($top_users as $user)
                            <tbody>
                                <tr>
                                    <td>
                                        <h5 class="font-14 my-1 fw-normal">{{$user->name}}</h5>
                                        <span class="text-muted font-13">{{ $user->created_at->format('d F Y') }}</span>
                                    </td>
                                    <td>
                                        @if (!empty($user['order']))
                                        <?php $orderCount = count($user['order']->where('seller_id',Auth::user()->id)); ?>
                                        <h5 class="font-14 my-1 fw-normal">
                                            {{ $orderCount }}
                                        </h5>
                                        @endif
                                    </td>
                                    <td>
                                        @foreach ($user['order']->where('seller_id',Auth::user()->id) as $order)
                                        @php
                                        $totalGrandTotal += $order->total_amount;
                                        @endphp
                                        <h5 class="font-14 my-1 fw-normal">{{number_format($totalGrandTotal,2)}}</h5>
                                        @endforeach
                                    </td>
                                </tr>
                            </tbody>
                            @endforeach
                        </table>
                        @endif
                    </div> <!-- end table-responsive-->
                </div> <!-- end card-body-->
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mt-2 mb-3">Top Category</h4>
                    <div class="table-responsive">
                        <table class="table table-centered table-nowrap table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Orders</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            @foreach($categoryProducts as $categoryId => $products)
                            @php
                            $category = App\Models\Category::find($categoryId);

                            $categoryStatus = $category->status === '1' ? 'Active' : 'Inactive';
                            @endphp
                            <tbody>
                                <tr>
                                    <td>
                                        <h5 class="font-14 my-1 fw-normal">{{ $category->category_name}}</h5>
                                    </td>
                                    <td>
                                        <?php
                                        $order_count = 0;
                                        foreach ($products as $product) {
                                            $order_count += count($product->product->orderItems);
                                        }
                                        ?>
                                        <h5 class="font-14 my-1 fw-normal">
                                            {{ $order_count }}
                                        </h5>
                                    </td>
                                    <td>
                                        <h5 class="font-14 my-1 fw-normal">{{ $categoryStatus }}</h5>
                                    </td>
                                </tr>
                            </tbody>
                            @endforeach
                        </table>
                    </div> 
                </div>
            </div>
        </div>
    </div>

</div>

@endsection


@section('script')

<script src="{{asset('public/assets/js/vendor/apexcharts.min.js')}}"></script>
<script src="{{ asset('public/assets/js/app.min.js') }}"></script>
<script src="{{ asset('public/assets/js/vendor/apexcharts.min.js') }}"></script>
<script src="{{ asset('public/assets/js/vendor/jquery-jvectormap-1.2.2.min.js') }}"></script>
<script src="{{ asset('public/assets/js/vendor/jquery-jvectormap-world-mill-en.js') }}"></script>
<link href="{{ asset('public/assets/css/vendor/jquery-jvectormap-1.2.2.css') }}" rel="stylesheet" type="text/css">
<script src="{{ asset('public/assets/js/pages/demo.dashboard.js') }}"></script>
@endsection