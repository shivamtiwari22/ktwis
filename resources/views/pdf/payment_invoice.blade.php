<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Document</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif
        }

        .clothing_invoice {
            width: 90%;
            margin: auto;
            margin-top: 20px;
            margin-bottom: 20px;
        }

        .data {
            padding: 10px 50px;
        }

        .container {
            border: 1px solid;
            background-color: #f9f9f9;
        }

        .invoice_heading h1 {
            margin-top: 30px;
            text-align: center;
        }

        .line_left {
            border-bottom: 10px solid #8f0606;
            padding-top: 15px;
            width: 50%;
        }

        .line_right {
            border-bottom: 10px solid #205d73;
            padding-top: 15px;
            margin-left: 45%;
        }

        .table_left {
            width: 50%;
        }

        .table_right {
            width: 50%;
        }

        .table_data {
            width: 100%;
            margin: auto;
            /* padding-top: 50px; */
            display: flex;
            flex-wrap: nowrap;
            justify-content: center;
        }

        .table_data td {
            padding: 10px;
        }

        .table_data p {
            margin-bottom: 20px;
        }

        .clothing_table {
            border-collapse: collapse;
            margin: auto;
        }

    
    
        .clothing_table th,
        .clothing_table td,
        .clothing_table tr {
            border: 1px solid gray;
            padding: 10px 30px;

        }

        @media only screen and (max-width: 1050px) {

            .clothing_table th,
            td,
            tr {
                padding: 10px 15px;

            }
        }

        .clothing_table th {
            background-color: #d9daeb;
        }

        .custom_class {
            background-color: #d9daeb;
        }

        .cloth_pro_head {
            margin: auto;
            width: fit-content;
            text-align: center ;
            margin-bottom: 20px;
        }

        .transaction-id-container {
            width: 200px; /* Adjust the width as needed */
            word-wrap: break-word;
        }
    </style>
</head>

<body>
    <section class="clothing_invoice">
        <div class="container">
            <div class="invoice_heading">
                <h1>Order Invoice</h1>
                <div class="line_left"></div>
                <div class="line_right"></div>
            </div>

            <div class="data">
                <div class="table_data">
                    <table>
                        <tr>
                            <td>
                                <b>Name</b><br />
                                {{ $user->name ?? null }}
                            </td>
                            <td>
                                <b>Invoice No.</b><br />
                              {{ sprintf("%05d",$order->invoice_number )}}
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <b>Phone Number</b><br />
                                {{ $address->contact_no ?? null }}
                            </td>
                            <td>
                                <b>Invoice Date</b><br />
                                {{ date('M d,Y') }}</p>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <b>Email</b><br />
                                {{ $user->email ?? null }}
                            </td>
                            <td>
                                @if($payment->status != "success" || $payment->status != "paid" )
                                <b>Due Date</b><br />
                                {{  date('M d,Y', strtotime($order->created_at)); }}

                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <b>Address</b><br />
                                {{ $address->address ?? null }},
                                {{ $address->city ?? null }},
                                {{ $address->state ?? null }},
                                {{ $address->country ?? null }},
                                {{ $address->zip_code ?? null }}
                            </td>
                            <td></td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="cloth_pro">
                <h2 class="cloth_pro_head"> Products</h2>
                <table class="clothing_table">
                    <thead>
                        <tr>
                            <th class="custom_class">SR.No</th>
                            <th>Product Name</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Discount</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order_item as $item)
                        <tr>
                            <td class="custom_class">{{$loop->iteration}}</td>
                            <td>{{ $item->name  ?? null}}</td>
                            <td>{{ $item->quantity ?? null }}</td>
                            <td>{{ $item->price ?? null}}</td>
                            <td>{{$item->offer_price ?? null}}</td>
                            <td>{{ $item->total_amount ?? null }}</td>
                        </tr>
                        @endforeach
                       
                    </tbody>
                </table>
            </div>


            <div class="data" >
                <div class="table_data"  style="padding-left: 20%">
                    <table>
                        <tbody>

                        <tr>
                            <td>
                                <b>Payment Method</b><br />
                                {{ $payment->payment_method ?? null}}
                            </td>
                            <td>
                                <b>Shipping Charge </b><br />
                                {{ $order->shipping_amount ?? null}}
                            </td>
                        </tr>
                        <tr>
                            <td>    
                                <b>Transaction Id</b><br />
                                <p class="transaction-id-container">{{$payment->transaction_id ?? null}}</p>
                                 
                            </td>
                            <td>
                                <b>Coupon Discount </b><br />
                                {{ $order->coupon_discount ?? 0}}
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <b>Sub Total </b><br />
                                {{ $order->sub_total ?? null}}
                            </td>
                            <td>
                                <b>Total Amount</b><br />
                                {{ $order->total_amount ?? null}}
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <b>Guarantee Charge</b><br />
                                {{ $orderSummary->guarantee_charge == 0 ? "No":"Yes"}}  
                            </td>
                            <td>
                             
                            </td>

                        </tr>
                    </tbody>

                    </table>
                </div>
            </div>

            <div class="invoice_heading">
                <div class="line_left"></div>
                <div class="line_right"></div>
            </div>
        </div>
    </section>


</body>

</html>
