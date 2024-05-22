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
    </style>
@endsection

@section('main_content')
 
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page"><a href="{{route('admin.refund.refund_datas')}}"> Refund </a></li>
            <li class="breadcrumb-item active" aria-current="page">View</li>
        </ol>
    </nav>
    {{-- <div class="card p-4 mt-4">
        <div class="box-widget widget-user">Customer Name : {{$customername->name}} </div>
        <p>Active</p>
    <div class="row">
        <div class="col">{{$totalAmount}}
            <p>Spent</p></div>
        <div class="col">  <span class="account-user-avatar">
            @if($customername->profile_pic === 'null')
            <img src="{{ asset('public/admin/profile_pic/' . $customername->profile_pic) }}" alt="user-image" class="rounded-circle">
        @else
            <img src="{{ asset('public/assets/images/users/avatar-1.jpg') }}" alt="user-image" class="rounded-circle">
        @endif
        
        </span></div>

        <div class="col">
            <p>Order</p>
             <p>{{$orderCount}}</p></div>
    </div>
</div> --}}
<div class="card p-4 mt-4">

    <div class="box-widget widget-user"><strong>Basic Information</strong>  </div>
    <div class="row">
    <div class="col-md-6">
        <div class="row">Full Name :  {{$customername->name}}</div>
        <div class="row">Email : {{$customername->email}}</div>
        <div class="row">Date Of Birth: {{ \Carbon\Carbon::parse($customername->dob)->format('F j, Y') }}</div>
    </div>
   
        <div class="col-md-6">  <span class="account-user-avatar">
            @if($customername->profile_pic != 'null')
            <img src="{{ asset('public/customer/profile/' . $customername->profile_pic) }}" alt="user-image" class="rounded-circle">
        @else
            <img src="{{ asset('public/assets/images/users/avatar-1.jpg') }}" alt="user-image" class="rounded-circle">
        @endif
        
        </span></div>
    </div>
</div>


<div class="card p-4 mt-4">
    <div class="box-widget widget-user"><strong> Address </strong></div>

    <div>

        <p class="order-text">PRIMARY</p>
                <div class="row">{{$useraddress->floor_apartment}}</div>
                <div class="row">{{$useraddress->address}}</div>
                <div class="row">{{$useraddress->city}},{{$useraddress->state	}}</div>
                <div class="row">{{$useraddress->country	}}</div>
                <div class="row">{{$useraddress->zip_code	}}</div>
       
    </div>
</div>
<div class="card p-4 mt-4">

    <div class="box-widget widget-user"><strong>Latest Order </strong> </div>
        <thead>
            <table border="3" cellpadding="10" cellspacing="0">
                {{-- <caption>Grocery Items Bill</caption> --}}
                <thead>
                   <colgroup>
                      <col width="20%">
                      <col width="20%">
                      <col width="20%" span="1" style="background-color:#f1f1f1;">
                   </colgroup>
                   <tr>
                      <th align="center" class="col-item-name">order Id</th>
                      <th align="center" class="col-quantity">Grand toatal</th>
                      <th align="center" class="col-price">Payment</th>
                      <th align="center" class="col-price">Status</th>
                      <th align="center" class="col-price">Order date</th>
                   </tr>
                </thead>
                <tbody>
                    @foreach ($orders as $item)
                        
                    <tr>
                        <td>{{$item->order_number}}</td>
                        <td align="">{{$item->total_amount}}</td>
                        <td align="">{{$item->payment->status == 'success' ? 'Paid' : 'Unpaid' }}</td>
                        <td align="">{{$item->status}}</td>

                        <td align="">{{ \Carbon\Carbon::parse($item->created_at)->format('F j, Y') }}</td>
                    </tr>
                    @endforeach
                  
                </tbody>
             
             </table>
</div>
   
@endsection


@section('script')


@endsection
