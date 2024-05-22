<html>

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Title</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <style>
        * {
            padding: 0;
            margin: 0;

        }
        
    
    </style>


</head>

<body>
<div style="margin: 0; padding: 0;  background-color:#d9d6d0;">

    <!-- logo side -->
    <div style="padding-top:20px; background-color: #fff;">
        {{-- <p style="
       
        font-size:16px;">Subject Line: Order Confirmation</p> --}}
    </div>

    <!-- logo image -->
    <div style="padding-top:20px; background-color: #fff;">
        <div
            style="  margin-top:20px;  border-radius: 2px; width: 105px; color: #fff; padding: 10px; width:140px;">
            {{-- @if($data['logo'])      <img src="{{$data['logo']}}" alt="" width="50px" height="60px"> @else  <p>Company Logo</p>  @endif --}}
           <p> <img src="{{ asset('public/assets/images/asset_9.png') }}" alt=""
            height="18"></p> 
        </div>

        <!-- user name -->
        <div style="margin-top:20px;">
            <p style="
           
            font-size:16px;
            ">Hi</p>
            <p style="
          
            font-size:16px;
        ">{{$data['name']}},</p>
            <p style="
             font: bold;
            margin-top: 20px;
            font-size:20px;">Sit Back and relax, your order

                <br>Is confirmed on {{date('D, j M')}}"
            </p>

            <p style="
           
            margin-top: 20px;
            font-size: 16px;
        "> We know you can’t wait to get your hands on it, <br>
                so we have begun prepping for it right away.</p>

        </div>

        <!-- view button  -->
        <div style="padding-top: 20px; padding-bottom: 20px;">
            {{-- <button style="background-color:#B72E25;
        border: none;
        color: white;
        padding: 12px 9px;
        width:200px;
        text-decoration: none;
        display: inline-block;
        font-size:16px;
        
        cursor: pointer;
        border-radius:5px;">View Order Details
            </button> --}}
        </div>
    </div>


    <!-- Product Detail -->

    <div style="margin-top: 30px;">


        <div style="background-color: #fff; background-color: #fff; padding-bottom: 20px ; padding-left: 20px;">
            <div style="padding-top: 20px;">
                <p style="font-size: 18px; margin-top: 20px;font-weight: bold;">Quick Details</p>
               
            </div>

 
            <table style="border-collapse: collapse; width: 100%; padding: 10px; border: 1px solid #ddd; margin-top: 20px;">

                <tr style="padding: 12px;">
                    
                  <th style="border: 1px solid #ddd; background-color: #f2f2f2;padding: 12px; "></th>
                  <th style="border: 1px solid #ddd; background-color: #f2f2f2; padding: 12px;">Product</th>
                  <th style="border: 1px solid #ddd; background-color: #f2f2f2; padding: 12px;">Quantity</th>
                  <th style="border: 1px solid #ddd; background-color: #f2f2f2; padding: 12px;">Price</th>
                  <th style="border: 1px solid #ddd; background-color: #f2f2f2; padding: 12px;">Amount</th>
                </tr>

                @foreach($data['order_items'] as $item)
                <tr style="text-align: center; ">
                    <td style="border: 1px solid #ddd; padding: 12px;">{{$loop->iteration}}</td>
                  <td style="border: 1px solid #ddd; padding: 12px;">{{$item->name}}</td>
                  <td style="border: 1px solid #ddd; padding: 12px;">{{$item->quantity}}</td>
                  <td style="border: 1px solid #ddd; padding: 12px;">{{$item->price}}</td>
                  <td style="border: 1px solid #ddd; padding: 12px;">{{$item->purchase_price}}</td>
                </tr>
                @endforeach
              </table>
              
            
        </div>


        <!-- Price Detail -->
        <div style="background-color: #fff;  padding-bottom: 20px; padding-left: 20px;">


            <div style=" background-color: #fff; padding-bottom: 20px;">
                <h3 style="   margin-top: 30px; margin-bottom: 5px; font-size:16px; padding-top: 20px;">Price Detail
                </h3>

                <table width="100%" style="font-size: 16px ;">

                    <tr>
                        <td width="50%" style=" vertical-align: top;" style="text-align: left">
                            <div>
                                <p>MRP</p>
                                <p>Discount on MRP </p>
                                <div style="margin-top: 20px;">


                                    <p>Shipping Charges</p>

                                    <p>Coupon Discount</p>
                                    {{-- <p> Taxes
                                        Worldwide 0%
                                    <p> --}}
                                        <p>
                                            Guarantee Charge
                                        </p>

                                </div>

                            </div>


                        </td>
                        <td width="50%" style="vertical-align: top;">
                            <div style="float: right; margin-right: 20px;">
                                <p>USD {{$data['order_summary']->total_amount}}</p>
                                <p>USD {{$data['order_summary']->discount_amount}}</p>

                                <div style="margin-top: 20px;">


                                    <p>USD {{$data['order_summary']->shipping_charges}}</p>

                                    <p>USD {{$data['order_summary']->coupon_discount}}</p>
                                    {{-- <p>0 </p> --}}
                                    <p> USD {{$data['order_summary']->guarantee_charge}} </p>

                                </div>
                            </div>
                        </td>

                    </tr>



                </table>
                <hr style="margin-top: 20px; width: 98%;">
                <table width="100%">


                    <tr style="padding: 10px 0px; font-size: 16px;">
                        <td>
                            <p>Total Amount</p>
                        </td>
                        <td width="50%" style="vertical-align: top;">
                            <div style="float: right; margin-right: 20px;">
                                <p> USD {{$data['order_summary']->grand_total}}</p>
                            </div>
                        </td>

                    </tr>



                </table>

                <hr style="margin-top: 20px; width: 98%;">
                <table width="100%">


                    <tr style="padding: 10px 0px; font-size: 16px;">
                        <td>
                            <p>Net Paid</p>
                        </td>
                        <td width="50%" style="vertical-align: top;">
                            <div style="float: right; margin-right: 20px;">
                                <p>USD {{$data['order_summary']->grand_total}}</p>
                            </div>
                        </td>

                    </tr>



                </table>

                <div style="margin-top: 20px; font-size: 16px;">
                    {{-- <p>You saved on this order.</p> --}}
                </div>


            </div>
        </div>

        <!--Product Delivery section -->
        <div style="background-color: #fff; background-color: #fff;  padding-bottom: 20px;  padding-left: 20px;">
            <h3 style="   margin-top: 30px; margin-bottom: 5px; font-size:16px; padding-top: 20px;">Delivering To</h3>

            <div style="font-size: 16px;">
                <p>{{$data['shipping_address']->contact_person}}, <br>

                    {{$data['shipping_address']->floor_apartment}} ,  {{$data['shipping_address']->address}}.<br>
                   {{$data['shipping_address']->city}} ,  {{$data['shipping_address']->state}},  {{$data['shipping_address']->country}}, Zip Code -   {{$data['shipping_address']->zip_code}}</p>
            </div>


        </div>

    </div>




    <!-- {# Footer top content #} -->
    <div style="background-color: #fff;">


        <div style="text-align: center; margin: 0% 10%;  font-size: 16px; padding: 30px 0px; color: #B72E25;">
            <p>For any assistance or question, feel free to reach out to our customer care center on</p>

            <div style="margin-top: 30px; ">
                <h3>7857489574308</h3>
                <p>from Monday to Friday, between 10 am to 6pm or.</p>
            </div>


            <p style="margin-top: 30px;">email us at <b>info@Ktwis.com</b><br />and well be in touch as soon as
                possible</p>

            <p style="margin-top: 30px; font-weight: bold;">With Gratitude, <br>Team Ktwis</p>
        </div>
    </div>


    <!-- footer section  -->
    <div style=" background-color:#B72E25; padding-top: 2px; padding-bottom: 2px; text-align: center;">
        <table style="margin: auto;">

            <tr>
                <td>

                    <p style=" font-size: 12px; color: #fff;">Follow us </p>

                </td>
                <td>


                    <div style="margin-top: 3px;">


                        <img src="https://api.arecharnutra.com/static/image/facebook.png" alt="Icon"
                            style="width: 13px; cursor: pointer;" />





                        <img src="https://api.arecharnutra.com/static/image/twitter.png" alt="Icon"
                            style="width: 13px; " />


                        <img src="https://api.arecharnutra.com/static/image/insta.png" alt="Icon"
                            style="width: 13px; " />




                    </div>
                </td>
                <td>
                    <div>
                        <p style=" font-size: 12px;color: #fff;">
                            | @ 2023, Ktwis, All Rights Reserved</p>
                    </div>
                </td>

            </tr>

        </table>
    </div>
</div>
</body>

</html>