<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Your Title</title>
        <link rel="stylesheet"
            href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

        <style>
        * {
            padding: 0;
            margin: 0;

        }
    </style>
    </head>
    <body style="text-align: center;">
        <div style="margin: 0; padding: 0;">
            <div
                style="padding-top: 20px; margin: auto; padding-bottom:20px;">
                <h4>{{$data['vendor_name']}} sent you an invoice for $
                    {{$data['order']['total_amount']}} USD Due on receipt</h4>
            </div>

            <div
                style="background-color:#EBECEA; border-radius:2px; margin: auto; padding:10px;">
                <h5 style="color:#113A85">Invoice details</h5>

                <div style="margin-top: 20px;">
                    <h5>Amount requested</h5>
                    <h5>$ {{$data['order']['total_amount']}} USD</h5>
                </div>

                <div style="margin-top: 20px;">
                    <h5>Note from seller</h5>
                    <h5>{{$data['order']['seller_to_customer']}}</h5>
                </div>

                <div style="margin-top: 20px;">
                    <p>Invoice number</p>
                    <p>{{$data['order']['invoice_number']}}</p>
                </div>

            </div>

            <!-- <div
                style="padding-top: 20px; padding-bottom: 20px; margin: auto;">
                <a
                    href="{{route('custom-payment-invoice',['id'=>$data['order']['id'] , 'link'=> $data['code']])  }}">
                    <button style="background-color:#B72E25;
border: none;
color: white;
padding: 12px 9px;
text-decoration: none;
display: inline-block;
font-size:16px;

cursor: pointer;
border-radius:20px;">View and Pay Invoice
                    </button> </a>
            </div> -->
<!-- <br>
            <div style="margin: auto;">
                <h3>Don't recognise this invoice?</h3>
                <h5 style="color: #0d6efd;;">Report this invoice</h5>
                <h5>Before Paying, make sure you recognise this invoice. if you don't, report it. <span
                        style="color: #0d6efd;">Learn more</span> </h5><br>
            </div>
            <div
                style="background-color:#EBECEA; border-radius:2px; margin: auto; padding:10px;">

                <div style="margin-top: 20px;">
                    <h5>Not for seller</h5>
                    <h5>{{$data['order']['seller_to_customer']}}</h5>
                </div>

                <div style="margin-top: 20px;">
                    <p>Invoice number</p>
                    <p>{{$data['order']['invoice_number']}}</p>
                </div>

            </div> -->
            <div
                style="padding-top: 20px; padding-bottom: 20px; margin: auto;">
                <a
                    href="{{route('custom-payment-invoice',['id'=>$data['order']['id'] , 'link'=> $data['code']])  }}">
                    <button style="background-color:#B72E25;
border: none;
color: white;
padding: 12px 9px;
text-decoration: none;
display: inline-block;
font-size:16px;

cursor: pointer;
border-radius:20px;">View and Pay Invoice
                    </button> </a>
            </div>
            <div style=" margin: auto;">
                <h3>Don't recognise this invoice?</h3>
                <h5 style="color: #0d6efd;;">Report this invoice</h5>
                <h5>Before Paying, make sure you recognise this invoice. if you
                    don't, report it. <span
                        style="color: #0d6efd;">Learn more</span> </h5>
                <h5>about common security threats and how to spot them. For
                    example ,Paypal would never use an invoice or a payment
                    requested to ask you for your account credentials.</h5>
            </div>

            <!-- {# Footer top content #} -->
            <div style="background-color: #fff;">

                <div
                    style=" font-size: 16px; padding: 30px 0px; color: #B72E25;">
                    <p>For any assistance or question, feel free to reach out to
                        our customer care center on</p>

                    <div style="margin-top: 30px; ">
                        <h3>7857489574308</h3>
                        <p>from Monday to Friday, between 10 am to 6pm or.</p>
                    </div>

                    <p style="margin-top: 30px;">email us at
                        <b>info@Ktwis.com</b><br />and well be in touch as soon
                        as
                        possible</p>

                    <p style="margin-top: 30px; font-weight: bold;">With
                        Gratitude, <br>Team Ktwis</p>
                </div>
            </div>

            <!-- footer section  -->
            <div
                style=" background-color:#B72E25; padding-top: 2px; padding-bottom: 2px; text-align: center;">
                <table style="margin: auto;">

                    <tr>
                        <td>

                            <p style=" font-size: 12px; color: #fff;">Follow us
                            </p>

                        </td>
                        <td>

                            <div style="margin-top: 3px;">

                                <img
                                    src="https://api.arecharnutra.com/static/image/facebook.png"
                                    alt="Icon"
                                    style="width: 13px; cursor: pointer;" />

                                <img
                                    src="https://api.arecharnutra.com/static/image/twitter.png"
                                    alt="Icon"
                                    style="width: 13px; " />

                                <img
                                    src="https://api.arecharnutra.com/static/image/insta.png"
                                    alt="Icon"
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