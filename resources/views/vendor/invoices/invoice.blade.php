@extends('vendor.layout.app')

@section('meta_tags')
@endsection


@section('title')
@endsection


@section('css')
@endsection

@section('main_content')
<div class="container-fluid">
    <br>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <!-- Invoice Logo-->
                    <div class="clearfix">
                        <a href="javascript:window.print()" class="btn btn-primary"><i class="mdi mdi-printer"></i> Print</a>
                        <div class="float-start mb-3">
                            <img src="assets/images/logo-light.png" alt="" height="18">
                        </div>
                        <div class="float-end">
                            <h2 class="m-0 d-print-none">Invoice</h2>
                        </div>
                    </div>

                    <!-- Invoice Detail-->
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="float-end mt-3">
                                <h4><b>Hello, {{$invoice->order->user->name}}</b></h4>
                                <p class="text-muted font-13">Please find below a cost-breakdown for the recent work completed. Please make payment at your earliest convenience, and do not hesitate to contact me with any questions.</p>
                            </div>

                        </div><!-- end col -->
                        <div class="col-sm-4 offset-sm-2">
                            <div class="mt-3 float-sm-end">
                                <p class="font-13"><strong>Order Date: </strong> &nbsp;&nbsp;&nbsp; {{$invoice->order->created_at->format('d F Y')}}</p>
                                <p class="font-13"><strong>Order Status: </strong> <span class="badge bg-success float-end">{{$invoice->order->status}}</span></p>
                                <p class="font-13"><strong>Order ID: </strong> <span class="float-end">#{{$invoice->order->id}}</span></p>
                            </div>
                        </div><!-- end col -->
                    </div>
                    <!-- end row -->

                    <div class="row mt-4">
                        <div class="col-sm-4">
                            <h6>Billing Address</h6>
                            <address>
                                {{ $invoice->order->shippingAddress->contact_person }},<br>
                                {{ $invoice->order->shippingAddress->floor_apartment }},{{ $invoice->order->shippingAddress->address }},{{ $invoice->order->shippingAddress->city }},<br>
                                {{ $invoice->order->shippingAddress->states->state_name }},{{ $invoice->order->shippingAddress->states->country->country_name }},{{ $invoice->order->shippingAddress->zip_code }} <br>
                                Phone : {{ $invoice->order->shippingAddress->contact_no }}
                            </address>
                        </div> <!-- end col-->

                        <div class="col-sm-4">
                            <h6>Shipping Address</h6>
                            <address>
                                {{ $invoice->order->shippingAddress->contact_person }},<br>
                                {{ $invoice->order->shippingAddress->floor_apartment }},{{ $invoice->order->shippingAddress->address }},{{ $invoice->order->shippingAddress->city }},<br>
                                {{ $invoice->order->shippingAddress->states->state_name }},{{ $invoice->order->shippingAddress->states->country->country_name }},{{ $invoice->order->shippingAddress->zip_code }} <br>
                                Phone : {{ $invoice->order->shippingAddress->contact_no }}
                            </address>
                        </div> <!-- end col-->
                    </div>
                    <hr>
                    <!-- end row -->

                    <div class="row">
                        <div class="col-12">
                            <div class="table-responsive">
                                <table class="table mt-4">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Item</th>
                                            <th>Quantity</th>
                                            <th>Unit Cost</th>
                                            <th>Discount</th>
                                            <th>Tax</th>
                                            <th class="text-end">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($invoice->order->orderItems as $item)
                                        <tr>
                                            <td>{{$loop->iteration}}</td>
                                            <td>
                                                <b>{{ $item->product->name }}</b> <br>
                                                {{ $item->product->brand }} {{ $item->product->model_number }}
                                            </td>
                                            <td>{{ $item->quantity }}</td>
                                            @if ($item->product->has_variant == "0")
                                            <td>${{ $item->product->inventory->price }}</td>
                                            @else
                                            @foreach ($item->product->inventoryVariants as $inventoryVariant)
                                            @foreach ($inventoryVariant->variants as $variant)
                                            @if ($variant->id == $item->variant_id)
                                            <td>${{ $variant->price }}</td>
                                            @endif
                                            @endforeach
                                            @endforeach
                                            @endif
                                            <td>${{ $item->price_without_discount - $item->price_with_discount }} </td>
                                            <td>${{ $item->price_without_tax - $item->price_with_tax }} </td>
                                            <td class="text-end">${{ $item->sub_total }} </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <hr><br>
                    <!-- end row -->

                    <div class="row">
                        <div class="col-sm-6">
                            <div class="clearfix pt-3">
                                <h6 class="text-muted">Notes:</h6>
                                <small>
                                    All accounts are to be paid within 7 days from receipt of
                                    invoice. To be paid by cheque or credit card or direct payment
                                    online. If account is not paid within 7 days the credits details
                                    supplied as confirmation of work undertaken will be charged the
                                    agreed quoted fee noted above.
                                </small>
                            </div>
                        </div> <!-- end col -->
                        <div class="col-sm-6">
                            <div class="float-end mt-3 mt-sm-0">
                                <p><b>Sub-total:</b> <span class="float-end">${{$invoice->order->grand_total}}</span></p>
                                <!-- <p><b>VAT (12.5):</b> <span class="float-end">$515.00</span></p> -->
                                <h3>${{$invoice->order->grand_total}} USD</h3>
                            </div>
                            <div class="clearfix"></div>
                        </div> <!-- end col -->
                    </div>
                    <!-- end row-->
                    <!-- 
                    <div class="d-print-none mt-4">
                        <div class="text-end">
                            <a href="javascript:window.print()" class="btn btn-primary"><i class="mdi mdi-printer"></i> Print</a>
                        </div>
                    </div> -->
                    <!-- end buttons -->
                </div> <!-- end card-body-->
            </div> <!-- end card -->
        </div> <!-- end col-->
    </div>
</div>
@endsection