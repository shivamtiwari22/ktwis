@extends('admin.layout.app')

@section('meta_tags')
@endsection


@section('title')
@endsection


@section('css')
@endsection

@section('main_content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.vendor.applications') }}">Merchants</a></li>
            <li class="breadcrumb-item active" aria-current="page">View</li>
        </ol>
    </nav>
    <div class="card p-2 mt-1">


        <div class="row">

            <h4 class="text-center mt-2">Basic Info</h4>

            <div class="col-md-6 mt-2">
                <div class="mb-1">
                    <label for="fullname" class="form-label"> <strong>Shop Name: </strong></label> <span>{{$user->shops->shop_name}}</span>

                </div>
                <div class="mb-1">
                    <label for="emailaddress" class="form-label"><strong>Owner:</strong></label>
                    <span>{{$user->name}}</span>
                </div>
                <div class="mb-1">
                    <label for="tax" class="form-label"><strong>Status:</strong></label>
                    <span>{{  ucwords($user->shops->status)}}</span>
                </div>

                <div class="mb-1">
                    <label for="tax" class="form-label"><strong>Member since: </strong></label>
                    {{date('d-m-Y', strtotime($user->created_at))}}
                </div>


                <div class="mb-1">
                    <label for="tax" class="form-label"><strong>Last update:</strong></label>
                    {{date('d-m-Y h:i:s', strtotime($user->updated_at))}}
                </div>

            </div>

            <div class="col-md-6 mt-2 text-center">

                <img src="{{ asset('public/vendor/shop/brand/'.$user->shops->brand_logo) }}" alt="ShopImage"  width="80%" height="80%">
            </div>
        </div>


        <div class="row">
            <div class="col-md-4 mt-2">
                <div class="card mt-2">
                    <div class="card-header">
                       Shop Details
                    </div>
                    <div class="card-body card-padding">
                        <div class="mb-1">
                            <label for="fullname" class="form-label"> <strong>Legal name:</strong></label>
                            {{$user->shops->legal_name}}
                        </div>
                        <div class="mb-1">
                            <label for="emailaddress" class="form-label"><strong>Shop url:</strong></label>
                            {{$user->shops->shop_url}}
                        </div>
                        <div class="mb-1">
                            <label for="tax" class="form-label"><strong>Time zone:</strong></label>
                            {{$user->shops->timezone}}
                        </div>
    
                    </div>
                </div>
            </div>
            <div class="col-md-4 mt-2">
                <div class="card mt-2">
                    <div class="card-header">
                        Description
                    </div>
                    <div class="card-body card-padding">
                          <p>
                            {{$user->shops->description}}
                          </p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mt-2">
                <div class="card mt-2">
                    <div class="card-header">
                        Contact
                    </div>
                    <div class="card-body card-padding">
                        <div class="mb-1">
                            <label for="fullname" class="form-label"> <strong>Email:</strong></label>
                            {{$user->shops->email}}
                        </div>
                        <div class="mb-1">
                            <label for="emailaddress" class="form-label"><strong>Phone:</strong></label>
                            {{$user->shopAddress ?   $user->shopAddress->phone : ''}}
        
                        </div>
                        <div class="mb-1">
                            <label for="tax" class="form-label"><strong>Address:</strong></label>
                            {{$user->shopAddress ?  $user->shopAddress->address_line1 : ''}}   <br>
                            {{$user->shopAddress ?  $user->shopAddress->address_line2 : ''}}     <br>
                            {{$user->shopAddress ?  $user->shopAddress->city : ''}}     

                        
                        </div>
                    </div>
                </div>

            </div>
        </div>


    </div>
@endsection

@section('script')
@endsection
