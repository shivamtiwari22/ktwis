@extends('admin.layout.app')

@section('meta_tags')
@endsection


@section('title')

@endsection


@section('css')
    <style>
        .modal-header {
            background: black;
            color: #ffc233;
            display: flex;
            /* align-items: center; */
            /* justify-content: center; */
            flex-flow: row-reverse;
            text-align: center;
            padding: 0 20px;
            padding-bottom: 5px;

            .close {
                color: white;
                opacity: 1;
                display: flex;
                // align-self: flex-start;
                padding-top: 5px;
            }
        }

        // modal title text logo
        h3 {
            &#myModalLabel {
                flex: 1;
                text-transform: uppercase;

                span {
                    color: white;
                }
            }
        }

        a.back,
        a.next,
        a.back:focus,
        a.next:focus,
        a.back:active,
        a.next:active {
            color: white;
            background: #ffc233;
            padding: 10px 60px;
            margin: 0;
            font-weight: bold;
        }

        a.back:hover,
        a.next:hover {
            color: #333;
            background-color: #e6e6e6;
            border-color: #adadad;
        }

        .modal-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .left-footer {
            display: flex;
        }

        .right-footer {
            flex: 1;
        }

        // prevent nav tabs from collapsing on mobile
        // using flex to fill the modal width
        // otherwise use table-cell and float
        .nav-justified {
            display: flex;

            li {
                flex: 1;
                // display: table-cell;
                // float: left;
            }
        }
        .box-widget {
    border: none;
    position: relative;
}
.row{
    margin-left:1%;
}
.order-text {
    position: absolute;
    top: 0px; /* Adjust the top position to move the text vertically */
    right: 10px; /* Adjust the right position to move the text horizontally */
    font-size: 16px;
}
.myvalue{
    width: -webkit-fill-available;
}
    </style>
@endsection

@section('main_content')
 
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Refund Response</li>
        </ol>
    </nav>
    <div class="card p-4 mt-4">
        <div class="box-widget widget-user">Response 
            <hr>
        </div>
        <div class="row">
            <div class="col">


                <p><b>Customer</b></p>
                @if($customername->profile_pic)
                <img src="{{ asset('public/customer/profile/' . $customername->profile_pic) }}" alt="user-image" class="rounded-circle"  width="100px" height="100px">
                @else
                    <img src="{{ asset('public/assets/images/users/avatar-1.jpg') }}" alt="user-image" class="rounded-circle">
                    @endif

                    <br>
                    <div class="row">  Customer Name : {{$customername->name}} </div>
                    <div class="row">    Customer Email : {{$customername->email}}  </div>
                    <div class="row">Date Of Birth: {{ \Carbon\Carbon::parse($customername->dob)->format('F j, Y') }}</div>
            <div>
                {{-- <label class="mt-5"><b>Product Details</b><hr></label> --}}
               <p> </p>
                </div>
            </div>
        <div class="col">
            @foreach ($orders as $item)

            <p><strong>#Order:</strong> 	{{$item->order_number}}</p>
            <p><strong> Refund amount: </strong>		{{$item->total_refund_amount}}</p>
            <p><strong>Order amount:</strong>	{{$item->total_amount}}</p>
            <p><strong>Order status: </strong> {{$item->status == "initiate_refund" ?   "Initiate Refund" : ucwords($item->order->status)}}</p>
            {{-- <p><strong>#Order received:</strong>	{{$item->good_received === 1 ? 'yes' : 'no'}}</p> --}}
            {{-- <p><strong>#Return goods:</strong>		{{$item->good_received === 1 ? 'yes' : 'no'}}</p> --}}
            {{-- <p><strong>#Refund Payment Status: </strong> {{$item->refund_payment_status}}</p> --}}
            <p><strong>#Guarantee Charge: </strong> {{$item->orderSummary->guarantee_charge > 0 ? "Yes" : "No"}}</p>
            <p><strong>#Order date:</strong>	{{ \Carbon\Carbon::parse($item->created_at)->format('F j, Y') }}</p>
            @endforeach
        </div>
        <hr>
        <div class="row">
            @foreach ($orders as $item)
            @if($item->status == "initiate_refund")
            <div class="col">
                <form id="reply_form">
                  <input type="hidden" name="datashow" value="{{$dispute->id}}">
                  <input type="hidden" name="myapprove" value="approve">

                  <button type="submit" class="btn btn-danger mx-auto myvalue"> Approve</button>
                </form>

            </div>
            <div class="col">
                <form id="reply_formes">
                    <input type="hidden" name="datashow" value="{{$dispute->id}}">
                  <input type="hidden" name="myapprove" value="decline">
                  <button type="submit" class="btn btn-secondary mx-auto myvalue"> Decline</button>
                </form>
             
            </div>
            @endif
            @endforeach
        </div>
        
        <div id="result"></div>
        
    </div>
</div>




   
@endsection


@section('script')
<script>
      $(document).ready(function() {
        $(document).on('submit', '#reply_form', function(e) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            e.preventDefault();
            var form = $(this);
            var formData = new FormData(form[0]);
          
            $.ajax({
                url: "{{ route('admin.refund.payment_approve') }}",
                method: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    console.log(response);
                    if (response.status) {
                       
                        $.NotificationApp.send("Success", response.message, "top-right", "rgba(0,0,0,0.2)", "success");
                        setTimeout(function() {
                            window.location.href = response.location;
                        }, 1000);
                    } else {
                        $.NotificationApp.send("Error", response.message, "top-right", "rgba(0,0,0,0.2)", "error");
                    }
                },
                error: function(xhr, status, error) {
                    $.NotificationApp.send("Error", xhr.responseText, "top-right", "rgba(0,0,0,0.2)", "error");
                }
            });
        });
    });
      $(document).ready(function() {
        $(document).on('submit', '#reply_formes', function(e) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            e.preventDefault();
            var form = $(this);
            var formData = new FormData(form[0]);
          
            $.ajax({
                url: "{{ route('admin.refund.payment_approve') }}",
                method: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    console.log(response);
                    if (response.status) {
                       
                        $.NotificationApp.send("Success", response.message, "top-right", "rgba(0,0,0,0.2)", "success");
                        setTimeout(function() {
                            window.location.href = response.location;
                        }, 1000);
                    } else {
                        $.NotificationApp.send("Error", response.message, "top-right", "rgba(0,0,0,0.2)", "error");
                    }
                },
                error: function(xhr, status, error) {
                    $.NotificationApp.send("Error", xhr.responseText, "top-right", "rgba(0,0,0,0.2)", "error");
                }
            });
        });
    });
</script>


@endsection
