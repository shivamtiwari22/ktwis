@extends('vendor.layout.app')

@section('meta_tags')
@endsection


@section('title')
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
</style>
@endsection

@section('main_content')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{route('vendor.dashboard')}}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{route('vendor.disputes.index')}}">Disputes</a></li>
        <li class="breadcrumb-item active" aria-current="page">Disputes Reply</li>
    </ol>
</nav>
<input type="hidden" name="id" id="id" value="{{$dispute->id}}">
<div class="card p-4 mt-4">

    <div class="row">
        <div class="d-flex justify-content-end mb-2">
            <a href="{{route('vendor.disputes.index')}}" class="btn btn-primary">View All Disputes</a>
        </div>
        <div class="col-sm-9">
            <div class="card mt-2">
                <div class="card-header">
                    <div class="container">
                        <h3 style="margin-right: 10px;">Dispute</h3>
                        @if ($dispute->status == "new") <span class="label" style="display: inline-block; padding: 4px 10px; border-radius: 3px; font-size: 12px; font-weight: bold; border: 1px solid #000; background-color: #fff;">NEW</span>
                        @elseif ($dispute->status == "open") <span class="label" style="display: inline-block; padding: 4px 10px; border-radius: 3px; font-size: 12px; font-weight: bold; background-color: #5bc0de; color: #fff; width: 60px; height: 22px;">OPEN</span>
                        @elseif ($dispute->status == "waiting") <span class="label" style="display: inline-block; padding: 4px 10px; border-radius: 3px; font-size: 12px; font-weight: bold; background-color: #00c0ef; color: #fff; width: 60px; height: 20px;">WAITING</span>
                        @endif
                    </div>
                </div>
                <br>
                <div class="card-body card-padding">
                    <div class="">
                        <span class="label" style="display: inline-block; padding: 4px 10px; border-radius: 3px; font-size: 12px; font-weight: bold; border: 1px solid #000; background-color: #fff;">#Order:&nbsp; #{{$dispute->order->order_number}}</span> <br><br>
                        <h4>{{$dispute->type}}</h4>
                    </div>
                    <textarea name="" id="" cols="10" rows="2" disabled class="form-control">{{$dispute->description}}</textarea><br><br>
                    <h4>Conversation</h4>
                    <hr>

                    {{-- <div class="parent" style="display: grid"> --}}
                
                    @foreach ($dispute->disputemessages as $disputes)


                    @if($disputes->response_by_id  == auth()->user()->id)
                    <div class="row">
                     
                        <div class="col-md-2 nopadding-right no-print"><br><br>
                            ME
                        </div>
                        <div class="col-md-8 nopadding">
                            <blockquote style="font-size: 1em;" class="">
                                <p>{!! $disputes->message !!}</p>
                                <footer>{{ \Carbon\Carbon::parse($disputes->created_at)->diffForHumans(null, true) }} ago</footer>
                            </blockquote>
                        </div>                       
                    </div>

                     @else
                    <div class="row new">
                        <div class="col-md-8 nopadding-left no-print" style="
                        text-align: right;
                    "><br><br>
                            {{$dispute->customer->name}}
                        </div>

                        
                        <div class="col-md-4 nopadding"  style="    display: flex;
                        justify-content: space-evenly;
                        align-items: center;
                    ">
                          
                            <blockquote style="font-size: 1em;  border-left: 5px solid #00c0ef" class="">
                                <p>{!! $disputes->message !!}</p>
                                <footer>{{ \Carbon\Carbon::parse($disputes->created_at)->diffForHumans(null, true) }} ago</footer>
                            </blockquote>
                            @if($disputes->attachment)
                            <a href="{{url('public/customer/dispute/'.$disputes->attachment)}}" class="anchor-deco"  download="file"> <i
                                class="fa fa-download"></i></a>
                                @endif
                        </div>
                       
                    </div>
                    @endif
                    @endforeach
                </div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="card mt-2">
                <div class="card-header">
                    Refund Request
                </div>
                <div class="card-body card-padding">
                    @if ($dispute->refund_requested == "1")
                    <h5 class="card-title"><strong>Amount:</strong> $ {{ $dispute->refund_amount }} </h5>
                    @endif
                </div>
            </div>
            <div class="card mt-2">
                <div class="card-header">
                    Customer
                </div>
                <div class="card-body card-padding"><br>
                    @if ($dispute->customer)
                    <h5 class="card-title"><strong>Name:</strong> {{ $dispute->customer->name }} </h5>
                    <h5 class="card-title"><strong>Total Disputes :</strong>&nbsp;&nbsp;&nbsp;&nbsp;<span class="label label-info"> {{ $totalDisputes }} </span></h5>
                    @php
                    $count_message = $dispute->disputemessages->count();
                    @endphp
                    <h5 class="card-title"><strong>Total Replies :</strong>&nbsp;&nbsp;&nbsp;&nbsp;<span class="label label-outline"> {{ $count_message }} </span></h5>
                    @endif
                    <hr>
                    <h5>Created At : {{ \Carbon\Carbon::parse($dispute->created_at)->diffForHumans(null, true) }}
                        ago </h5>
                    <h5>Last Updated At : {{ \Carbon\Carbon::parse($dispute->updated_at)->diffForHumans(null, true) }}
                        ago </h5>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection


@section('script')
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>

<script>
    $(document).ready(function() {
        $('#reply_form').validate({
            rules: {
                reply_status: {
                    required: true,
                },
                content: {
                    required: true,
                },
            },
        });
    })
</script>
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
                url: "{{ route('vendor.disputes.reply.store') }}",
                method: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    console.log(response);
                    if (response.status) {
                        $.NotificationApp.send("Success", response.message, "top-right",
                            "rgba(0,0,0,0.2)", "success")
                        setTimeout(function() {
                            window.location.href = response.location;
                        }, 1000);
                    } else {
                        $.NotificationApp.send("Error", response.message, "top-right",
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
    });
</script>
@endsection