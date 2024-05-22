<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complete Your Order</title>
</head>
<body>
    <div style="text-align: center; padding: 20px;">
        <h1>Your Order is Ready to Be Completed!</h1>
        <p>Thank you for choosing our service. Please review your order details below and proceed with the payment.</p>

        <!-- Display order details -->
        <table style="width: 80%; margin: 0 auto;">
            <tr>
                <th>Product</th>
                <th>Quantity</th>
                <th>Price</th>
            </tr>
            @foreach($data['order_items'] as $item)

            <tr>
                <td>{{$item->name}}</td>
                <td>{{$item->quantity}}</td>
                <td> ${{$item->total_amount}}</td>
            </tr>
            @endforeach
            <!-- Add more rows for additional products if needed -->
        </table>

        <p>Total Amount: $ {{$data['order']['total_amount']}}</p>

        <!-- Payment Button -->    
            <a href="{{route('payment-invoice',['id'=>$data['order']['id'] , 'link'=> $data['code']])  }}">  <button type="submit" style="padding: 10px 20px; background-color: #007bff; color: #fff; border: none; cursor: pointer;">
                Pay Now
            </button> </a>
    
        <p>If you have any questions or need assistance, please contact our support team.</p>
    </div>
</body>
</html>