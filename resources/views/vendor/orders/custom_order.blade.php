@extends('vendor.layout.app')

@section('meta_tags')
@endsection


@section('title')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
@endsection


@section('css')
    <style>
        .container {
            display: flex;
            align-items: center;
        }

        blockquote {
            margin: 20px 0 30px;
            padding-left: 20px;
            border-left: 5px solid #1371b8;
        }

        .card-padding {
            padding: 0.5rem 1.5rem;
        }

        .customer-card {
            padding: 0.5rem 0.5rem;
        }

        #product-card-header {
            padding: 0px;
        }

        .label-info {
            background-color: #00c0ef !important;
            color: white;
            padding: 0px 4px;
        }

        .label-outline {
            background-color: transparent;
            border: 1px solid #d2d6de;
            padding: 0px 4px;
        }

        .img-circle {
            border-radius: 50%;
            max-width: 30px;
            max-height: 30px;
        }

        .nopadding {
            padding: 0;
            margin: 0;
        }

        .input-lg {
            height: 48px !important;
            border-radius: 0px;
        }

        .select2-container .select2-selection--single {
            height: 48px;
        }

        .select2-container .select2-selection--single .select2-selection__rendered {
            line-height: 45px;
        }

        .table>tbody {
            vertical-align: unset;
        }

        .quantity-input {
            width: 46px;
            border: none;
        }

        .input_value {
            width: 46px;
            border: none;
        }

        .pull-right {
            float: right;
        }

        hr {
            margin: 0.5rem 0;
        }


 

        .iti--allow-dropdown {
            width: 100%;
        }

        .input_flag {
            padding: 0px 0 0 54px !important;
            height: 40px;
        }

        .center {
            text-align: center;
        }
    </style>
@endsection

@section('main_content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('vendor.dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('vendor.order.index') }}">Orders</a></li>
            <li class="breadcrumb-item active" aria-current="page">Create Order</li>


            <div class="d-flex justify-content-end mb-2" style="position: absolute;  right:3%">
                <a href="{{ route('vendor.order.index') }}" class="btn btn-secondary">Cancel</a>
            </div>


        </ol>



    </nav>


    <div class="row">
        <img src="https://www.google.com/url?sa=i&url=https%3A%2F%2Flaraget.com%2Fblog%2Fhow-to-create-an-ajax-pagination-using-laravel&psig=AOvVaw1TCuO_ZK5xFhN2YMU-Aoik&ust=1695467843776000&source=images&cd=vfe&ved=0CBAQjRxqFwoTCMjttoaMvoEDFQAAAAAdAAAAABAW"
            alt="">

        <div class=" mb-2">
            <h3>New Invoice Number ({{$invoice}})</h3>
        </div>
        <div class="col-sm-9">
            <div class="card ">
              
                    <div class="container">
                        <h5 class=""><strong>Bill  to</strong> </h5>
                    </div>
            
                <form id="order-place">
                    <div class="card-body card-padding">
                        <div class="row mb-4">
                            <div class="col-md-12 ">
                                <input type="email" class="form-control input-lg" id="customer-email" name="email" placeholder="enter user email "  required value="{{$customer->email ?? null}}">
                         
                            </div>
                        </div>
                        
                            <div class="row" id="item-container">
                                <h5>Items</h5>
                                <div class="col-md-6">
                                    <span class="spacer10"></span>
                                    <input type="text" class="form-control input-lg" required placeholder="Item Name" name="item_name[]" >
                                </div>
                                <div class="col-md-3">
                                    <span class="spacer10"></span>
                                    <input type="number" class="form-control input-lg" required placeholder="Quantity" min="1" name="quantity[]">
                                </div>
                                <div class="col-md-3">
                                    <span class="spacer10"></span>
                                    <input type="number" class="form-control input-lg item-price" required  placeholder="Price" min="1" name="price[]" id="price">
                                </div>

                        
                                <div class="col-md-12 mt-2">
                                    <span class="spacer10"></span>
                                    <textarea name="description[]" id="" cols="10" rows="3" class="form-control" placeholder="Description(optional)"></textarea>
                                </div>

                                <div class="col-md-12" style="display: flex;justify-content: end;">
                                    <span class="spacer10"></span>
                                    <h5 style="font-size:small" class="item-amount">Amount : $ <span id="amount">0</span></h5>
                                </div>

                            </div>

                            <div class="col-md-12">
                                <span class="spacer10"></span>
                                <h5 style="color: #0082FF"><a href="javascript:void(0)" class="add-item"> <i class="fa fa-plus"></i> Add Item </a></h5>
                            </div>
                        


                       
                            <div class="col-md-12">
                                <div class="form-group mt-3">
                                  <h5>Message For Customer</h5>
                                    <textarea id="" placeholder="Seller to customer" name="seller_to_customer" cols="10" rows="3"  class="form-control"></textarea>
                                </div>

                                <div class="form-group mt-2">
        
                                      <textarea id="" placeholder="Seller's Terms & Conditions" name="terms_condition" cols="10" rows="3"  class="form-control"></textarea>
                                  </div>


                            </div>

                            <div class="col-md-6 mt-2">
                                <textarea id="" placeholder="Reference" name="reference" cols="10" rows="3"  class="form-control"></textarea>

                            </div>

                            <div class="mt-3 mb-2">
                                <h3>More Options</h3>

                                <div class="col-md-6">
                                    <label for="">Attachments</label>
                                    <input type="file" name="attachments" class="form-control">
                                    <label style="font-size: 12px">JPG GIF PNG PDF | Up to 5 files, 4MB per file</label>
                                </div>
                            </div>

                            <div class="mt-2" style="text-align: end">
                                <button class="btn btn-primary" style="border-radius: 16px" type="submit">Send</button>
                            </div>
                         
           

            </div>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="card ">
          
            <div class="card-body customer-card mt-3">
                <input type="text" class="form-control input-lg" placeholder="Invoice number" name="invoice_number" readonly value="{{$invoice}}">

                <div class="col-md-6 mt-2" >
                <input type="date" class="form-control input-lg" placeholder="Invocie date" name="date">
            </div>
             

            <hr class="mt-3">

            <div class="row">
                <div class="col-md-6 center">
                    <h5>Subtotal</h5>
                </div>
                <div class="col-md-6 center"  >
                    <h5 class="subtotal" id="sub-total">$ 0</h5>
                    <input type="hidden" id="subTotal" name="sub_total" value="0">
                </div>


                <div class="col-md-6 center">
                    <h5>Other Discount</h5>
                </div>
                <div class="col-md-6 center"  >
                    <h5><input type="number" class="form-control center" style="border: none" value="0" id="discount" min="0" name="discount"></h5>
                </div>

                <div class="col-md-6 center">
                    <h5>Shipping</h5>
                </div>
                <div class="col-md-6 center" >
                    <h5><input type="number" class="form-control center" value="0" style="border: none" id="shipping" min="0" name="shipping"></h5>
                </div>

                <hr class="mt-2">

                <div class="col-md-6 center">
                    <h5>Total Amount</h5>
                </div>
                <div class="col-md-6 center"  >
                    <h5 class="totalamount" id="total-amount">$ 0</h5>
                    <input type="hidden" name="total_amount" id="total">
                </div>

            </div>

      
            </div>
        </div>
    </form>
    </div>


@endsection


@section('script')
   
    <script>
            $(document).ready(function() {
            
            var html = `    <div class="row lotData" id="" >
                <div class="col-md-11" id="" >
                    <div class="row lotData" id="" >
                            <div class="col-md-6">
                                <span class="spacer10"></span>
                                <input type="text" class="form-control input-lg"  placeholder="Item Name" name="item_name[]">
                            </div>
                            <div class="col-md-3">
                                <span class="spacer10"></span>
                                <input type="number" class="form-control input-lg"  placeholder="Quantity" min="1" name="quantity[]">
                            </div>
                            <div class="col-md-3">
                                <span class="spacer10"></span>
                                <input type="number" class="form-control input-lg item-price"  placeholder="Price" min="1" name="price[]" id="">
                            </div>

                          
                            <div class="col-md-12 mt-2">
                                <span class="spacer10"></span>
                                <textarea name="description[]" id="" cols="10" rows="3" class="form-control" placeholder="Description(optional)"></textarea>
                            </div>

                            <div class="col-md-12" style="display: flex;justify-content: end;">
                                <span class="spacer10"></span>
                                  <h5 style="font-size:small" class="item-amount" >Amount : $0</h5>
                            </div>

                        </div>
                        </div>

                        <div class="col-md-1" style="display: flex;
    justify-content: center;
    align-items: center;">
                                <a class="removeDangerBtn" type="button"><i class="fa fa-minus"></i></a>
                            </div>

                        </div>

                     
                    </div>`;
          

            $("body").on("click", ".add-item", function() {
             
            $("#item-container").append(html);
        });

            $("body").on("click", ".removeDangerBtn", function() {
        
                console.log('kok');
                $(this).parents(".lotData").remove();
            });

            $("body").on("input", ".item-price", function() {
        updateItemAmount($(this));
        updateTotalAmount();
    });

    $("body").on("input","#price",function(){
              $('#amount').text($(this).val());
    })

    function updateItemAmount(priceInput) {
        var quantity = priceInput.closest('.lotData').find('[name="quantity"]').val() || 1;
        var price = priceInput.val() || 0;
        var amount = price;
        priceInput.closest('.lotData').find('.item-amount').text('Amount: $' + amount);
    }

        });

        function updateTotalAmount() {
            var totalAmount = 0;
        $('.item-price').each(function() {
            var amount = parseFloat($(this).val()) || 0 ;
            totalAmount += amount ;
        });

        var shipping = $('#shipping').val() ?  $('#shipping').val() : 0;
        var discount = $('#discount').val() ?  $('#discount').val() : 0;
        var grandTotal = totalAmount + parseFloat(shipping);
        var final = grandTotal - parseFloat(discount);
        
        console.log(grandTotal);

        $("#sub-total").text('$ ' + totalAmount);
        $("#total-amount").text('$ ' + final);
        $('#total').val(final);
        $('#subTotal').val(totalAmount);
    }

    $("body").on("input","#shipping",function(){
               var sub = $('#subTotal').val() ;
               var dis = $('#discount').val() ?  $('#discount').val() : 0 ;
               var final_total = parseFloat(sub) + parseFloat($(this).val() ? $(this).val() : 0);
               var total = final_total - parseFloat(dis);
     
              $('#total-amount').text('$ ' + total);
        $('#total').val(total);
              console.log("ship:" + total);
    });

    $("body").on("input","#discount",function(){
        var sub = $('#subTotal').val() ;
        var ship = $('#shipping').val()  ? $('#shipping').val() : 0 ;
        console.log(ship);
         var total_amount = parseFloat(sub) -  parseFloat($(this).val() ? $(this).val() : 0);
         var final =   total_amount + parseFloat(ship);
      

              $('#total-amount').text('$ ' + final);
              $('#total').val(final);
    });

            $('#address').validate({
                    rules: {
                        phone: {
                            required: true,
                            number: true,
                            maxlength: 20,
                            minlength: 6
                        },
                     
                    },
                 
                })


            $(document).on('submit', '#address', function(e) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                e.preventDefault();
                var form = $(this);
                var formData = new FormData(form[0]);
                const countryData = phoneInput.getSelectedCountryData();
            const countryCode = countryData.dialCode;
            formData.append('country_code', countryCode);
                $.ajax({
                    url: "{{ route('vendor.order.customerAddress') }}",
                    method: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        console.log(response);
                        if (response.status) {
                            $.NotificationApp.send("Success", response.msg, "top-right",
                                "rgba(0,0,0,0.2)", "success")
                            setTimeout(function() {
                                window.location.reload();
                            }, 1000);
                        } else {
                            $.NotificationApp.send("Error", response.msg, "top-right",
                                "rgba(0,0,0,0.2)", "error")
                        }
                    },
                    error: function(xhr, status, error) {
                        console.log(xhr.responseText);
                        $.NotificationApp.send("Error", xhr.responseText, "top-right",
                            "rgba(0,0,0,0.2)", "error");

                    }
                });
            });
        


   
    </script>

  



    <script>
        $(document).on('click', '#getCart', function(e) {
            e.preventDefault();
            let product_id = $('#product_id').val();

            if ($('#offer-price_' + product_id).val()) {
                $.NotificationApp.send("Success", 'Item Already Added', "top-right",
                    "rgba(0,0,0,0.2)", "success");
            } else {
                $.ajax({
                    url: "{{ route('vendor.order.getProducts') }}",
                    method: "POST",
                    data: {
                        id: product_id
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        // console.log(response);
                        $('#product-table tbody').append(`
                         <tr data-id="${response.data.id}">
                        <td><img src="${response.data.feature_img_url}"
                                                width="50px;"></td>
                        <td>${response.data.name}         <input type="hidden" name="product[]" id="" value="${response.data.id}" class="product-id">  </td>
                        <td>${response.data.inventory.offer_price}
                            <input type="hidden" name="actual_price[]" id="actual-price_${response.data.id}" value="${response.data.inventory.offer_price}" class="actual_price">
                            </td>
                        <td>×</td>
                       <td><input type="number" class="quantity-input" name="quantity[]" value="1" min="1" id="${response.data.id}"></td>
                        <td> <span id="innner-total_${response.data.id}"> ${response.data.inventory.offer_price} </span>  
                                             <input type="hidden" name="offer_price[]" id="offer-price_${response.data.id}" value="${response.data.inventory.offer_price}" class="offer_price">   </td>
                 <td><i class="fa fa-trash text-muted delete-row"></i></td>
                          </tr>
    `);

                        var total = 0;

                        $('.offer_price').each(function() {
                            var offerPrice = parseFloat($(this).val());
                            if (!isNaN(offerPrice)) {
                                total += offerPrice;
                            }
                        });
                        // console.log(total);
                        $('#grand').val(total);
                        $('#table_grand').html(total);
                        $('#total').val(total);
                        $('#table_total').html(total);
                        toggleMessageRow();

                        $('#product_id').val('0');
                        $('#discount-amount').val(0);
                        $('#shipping-amount').val(0);

                    },
                    error: function(xhr, status, error) {
                        console.log(xhr.responseText);
                        $.NotificationApp.send("Error", xhr.responseText, "top-right",
                            "rgba(0,0,0,0.2)", "error");

                    }
                });
            }
        })


        $(document).on('submit', '#order-place', function(e) {
            e.preventDefault();
                var form = $(this);
                var formData = new FormData(form[0]);
                //  var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

                //  var email = $('#customer-email').val();
                //  // Test the email against the regular expression
                //  if(!emailRegex.test(email);){
                //     $.NotificationApp.send("Error",'Invalid email!', "top-right",
                //                 "rgba(0,0,0,0.2)", "error")
                //  }

                $('#submit-btn').prop('disabled', true);

                $.ajax({
                    url: "{{ route('vendor.customOrder.place') }}",
                    method: "POST",
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        console.log(response);
                        if (response.status) {
                            $.NotificationApp.send("Success", response.message, "top-right",
                                "rgba(0,0,0,0.2)", "success")
                            setTimeout(function() {
                                window.location.href = "{{route('vendor.order.custom')}}";
                            }, 1000);
                        } else {
                            $.NotificationApp.send("Error", response.msg, "top-right",
                                "rgba(0,0,0,0.2)", "error")
                        }
                        $('#submit-btn').prop('disabled', false);
                    },
                    error: function(xhr, status, error) {
                        console.log(xhr.responseText);
                        $.NotificationApp.send("Error", xhr.responseText, "top-right",
                            "rgba(0,0,0,0.2)", "error");
                        $('#submit-btn').prop('disabled', false);

                    }
                });
            
        });
    </script>
@endsection
