@extends('vendor.layout.app')

@section('meta_tags')
@endsection


@section('title')
@endsection


@section('css')
@endsection

@section('main_content')
    {{-- <div class="row">


    <form action="{{route('twilio.sendsms')}}" method="POST">
        @csrf
        <div class="col-sm-4">
            <label for="to">Reciever Phone Number: (with country code)</label><br>
            <input type="text" name="phone" id="phone">
        </div>
        <div class="col-sm-4">
            <label for="to">Message: </label><br>
            <textarea type="text" name="message" id="message"></textarea>
        </div><br>
        <div class="d-flex">
            <button type="submit" class="btn btn-primary">Send SMS</button>
        </div>
    </form>
</div> --}}

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Send Messages</li>
        </ol>
    </nav>

    <div class="card p-4 mt-3">
        <form id="send-sms">
            @csrf
            <div class="row">
                <div class="col-12">
                    <label for="">Phone Number <span class="text-danger">*</span> </label><br>
                    <input type="text" class="form-control" name="phone">
                </div>

            </div><br><br>

            <div>
                <label for="content">Message<span class="text-danger">*</span></label>
                <textarea id="content" name="message" class="form-control" style="height: 180px"></textarea>
            </div>
            <div class="d-flex">
                <button type="submit" class="btn btn-primary mx-auto mt-4"> Send</button>
            </div>
        </form>

    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            $('#send-sms').on('submit', function(e) {
                e.preventDefault();
                var formData = $(this).serialize();
                console.log(formData);
                $.ajax({
                    url: "{{ route('twilio.sendsms') }}",
                    type: "POST",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    dataType: "json",
                    data: formData,
                    cache: false,
                    processData: false,
                    success: function(data) {
                        if (data.status == true) {
                            toastr.success(data.msg);
                            $("#send-sms")[0].reset();
                        } else {
                            if (data.msg.message) {
                                toastr.error(data.msg.message);

                            }
                            if (data.msg.phone) {
                                toastr.error(data.msg.phone);
                            }

                            if (data.type) {
                                toastr.error(data.msg);
                            }
                        }
                    },
                    error: function(data) {
                        console.log(data);
                        toastr.error('something went wrong');
                    }
                })
            })
        })
    </script>
@endsection
