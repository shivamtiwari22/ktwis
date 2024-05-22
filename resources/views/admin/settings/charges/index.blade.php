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
            <li class="breadcrumb-item"><a aria-current="page">Settings</a></li>
            <li class="breadcrumb-item"><a aria-current="page">Set Charges</a></li>
        </ol>
    </nav>
    <div class="card p-4 mt-4">
      
        <form id="charges_form" enctype="multipart/form-data">
              <p><strong class=""> Guarantee Charge</strong></p>
            <div class="row">
                <div class="mb-3 col-lg-6">
                     <input type="hidden" name="id" value="{{$charge ? $charge->id : ''}}">
                    <label for="simpleinput" class="form-label"> Amount (in USD)<span class="text-danger">*</span></label>
                    <input type="number" class="form-control" required name="guarantee_amount" value="{{$charge ? $charge->amount : ''}}" id="platform" min="0">

                </div>
                {{-- <div class="mb-3 col-lg-6">
                    <label for="simpleinput" class="form-label">Transaction Charges<span
                            class="text-danger">*</span></label>
                    <input type="number" required class="form-control" name="transaction_amount" id="transaction">

                </div> --}}
            </div>
            {{-- <div class="row">

                <div class="mb-3 col-lg-6">
                    <label for="simpleinput" class="form-label">Total Amount</label>
                    <input type="number" required class="form-control" id="amount" readonly name="total_amount">
                </div>
            </div> --}}
            <div class="row">
                <div class="mb-3 col-lg-6">
            <p><strong>Commission Charge</strong></p>
            <label for="simpleinput" class="form-label"> Amount (in %)<span class="text-danger">*</span></label>
            <input type="number" class="form-control" required name="commission_amount" value="{{$commission ? $commission->amount : ''}}" id="platform" min="0">
                </div>
            </div>
            <div>
                <button type="submit" class="btn btn-success">Submit</button>
            </div>
        </form>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            $('#charges_form').on('submit', function(e) {
                e.preventDefault();
                var formData = $(this).serialize();
                console.log(formData);
                $.ajax({
                    url: "{{ route('admin.setting.setCharges') }}",
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
                            setTimeout(function() {
                                window.location.reload();
                            }, 1000);
                    
                        } else {
                            toastr.error(data.msg);
                            console.log(data);
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


    <script>
        $(function() {
            $('#platform, #transaction').keyup(function() {
                var value1 = parseFloat($('#platform').val()) || 0;
                var value2 = parseFloat($('#transaction').val()) || 0;
                $('#amount').val(value1 + value2);
            });
        });
    </script>
@endsection
