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
       
        font-size:16px;">Subject Line: Order Dispatched</p> --}}
    </div>

    <!-- logo image -->
    <div style="padding-top:20px; background-color: #fff;">
        <div
            style="  margin-top:20px;  border-radius: 2px; width: 105px; color: #fff; padding: 10px; width:140px;">
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
        ">{{$data['user']->name}},</p>
            <p style="
             font: bold;
            margin-top: 20px;
            font-size:20px;">We have dispatched your
order on {{date('D, j M')}} .
            </p>

            <p style="
           
            margin-top: 20px;
            font-size: 16px;
        "> We know you can t wait to get your
hands on it, you can track your order
below.</p>

        </div>

        <!-- view button  -->
        {{-- <div style="padding-top: 20px; padding-bottom: 20px;">
            <button style="background-color:#B72E25;
        border: none;
        color: white;
        padding: 12px 9px;
        width:180px;
        text-decoration: none;
        display: inline-block;
        font-size:16px;
        
        cursor: pointer;
        border-radius:5px;">Track My Order
            </button>
        </div>
    </div> --}}







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