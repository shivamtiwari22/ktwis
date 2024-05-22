@extends('vendor.layout.app')

@section('meta_tags')
@endsection


@section('title')
@endsection


@section('css')
<style>
    /* Basic CSS styling for the notification area */
    .notification-area {
        width: 100%;
        border: 1px solid #ccc;
        padding: 10px;
        margin: 20px;
        background-color: white;
    }

    /* Styling for individual notifications */
    .notification {
        margin-bottom: 10px;
        padding: 5px;
        border: 1px solid #ddd;
        background-color: #fff;
    }

    .bg-red {
        background-color: red;
    }

    .bg-color {
        background-color: beige;
    }
</style>
@endsection

@section('main_content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">All Notifications</li>
        </ol>
    </nav>

    <div class="container">
        <div class="notification-area">
            <h2 class="mb-2">Notifications</h2>
                   

            @foreach (auth()->user()->notifications as $notification)

              @php      @endphp
            <div class="notification pt-2 {{$notification->read_at  ? '' : 'bg-color'}}" style="display: flex;
            justify-content: space-between;">
                <p style=" word-break: break-all ; margin-right: 2rem" ><strong class="ms-2">  <i class="mdi mdi-comment-account-outline"></i></strong> {{ $notification->data['message'] }}
                </p>

                @if($notification->read_at == null)
                 <a href="{{url('vendor/mark-as-read/'.$notification->id)}}"><button class="btn btn-danger ">Mark&nbsp;as&nbsp;Read</button> </a> 
                 @endif
            </div>
            @endforeach
           
        </div>

    </div>
@endsection

@section('script')
@endsection
