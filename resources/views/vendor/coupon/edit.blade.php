@extends('vendor.layout.app')

@section('meta_tags')
@endsection


@section('title')
@endsection


@section('css')
@endsection

@section('main_content')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{route('vendor.dashboard')}}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{route('vendor.coupon.list')}}">Coupon</a></li>
        <li class="breadcrumb-item active" aria-current="page">Edit</li>
    </ol>
</nav>
    <div class="card p-4 mt-4">
        <div class="d-flex justify-content-end mb-2">
            <a href="{{route('vendor.coupon.list')}}" class="btn btn-primary">View All Coupons</a>
        </div>
        <form id="coupon_form">
            <input type="hidden" value="{{$coupon->id}}" name="coupon_id">
            <div class="row">
                <div class="mb-3 col-lg-6">
                    <label for="simpleinput" class="form-label">Coupon Code<span
                        class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="code" value="{{$coupon->code}}">
                </div>
                <div class="mb-3 col-lg-6">
                    <label for="example-select" class="form-label">Coupon Type <span
                        class="text-danger">*</span></label>
                    <select class="form-select" data-id="{{ $coupon->id }}" name="coupon_type">
                        <option value="fixed" {{ $coupon->coupon_type == "fixed" ? "selected" : "" }}>Fixed</option>
                        <option value="Percentage" {{ $coupon->coupon_type == "Percentage" ? "selected" : "" }}>Percentage</option>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="mb-3 col-lg-4">
                    <label for="simpleinput" class="form-label">Amount <span id="coupon_type_sapce"> (in USD)</span><span
                        class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="amount"  value="{{$coupon->amount}}">
                </div>
                <div class="mb-3 col-lg-4">
                    <label for="example-select" class="form-label">No. of Coupons<span
                        class="text-danger">*</span></label>
                    <input type="number" class="form-control" name="no_of_coupons" min="1" value="{{$coupon->no_of_coupons}}">
                </div>
                <div class="mb-3 col-lg-4">
                    <label class="form-label">Expiry Date <span
                        class="text-danger">*</span></label>
                    <input type="date" class="form-control date" id="birthdatepicker" name="expiry_date" value="{{$coupon->expiry_date}}" min="{{ date('Y-m-d') }}">
                </div>
            </div>
            <div class="row">
                <div class="mb-3 col-lg-4">
                <label for="example-select" class="form-label">Coupon Status <span
                    class="text-danger">*</span></label>
                <select class="change_status form-select" data-id="{{ $coupon->id }}" name="status">
                    <option value="pending" {{ $coupon->status == "pending" ? "selected" : "" }}>Pending</option>
                    <option value="published" {{ $coupon->status == "published" ? "selected" : "" }}>Publish</option>
                </select>
            </div>
        </div>
            <div class="mt-4 text-center">
                <button type="submit" class="btn btn-warning text-white">Update</button>
            </div>
        </form>
    </div>
@endsection


@section('script')
<script>
      $(document).ready(function() {
            $('#coupon_form').validate({
                rules: {
                    code: {
                        required: true,
                        maxlength: 46,
                    },
                    coupon_type: {
                        required: true,

                    },
                    amount: {
                        required: true,
                        maxlength: 30

                    },
                    no_of_coupons: {
                        required: true,
                        number: true,
                        maxlength: 30
                    },
                    expiry_date :{
                        required: true,
                    }

                },
            })
        })
</script>
<script>
    $(function () {
        $(document).on('submit','#coupon_form',function(e) {
            e.preventDefault();
            let fd = new FormData(this);
            fd.append('_token',"{{ csrf_token() }}");
            $.ajax({
                url: "{{ route('vendor.coupon.update') }}",
                type:"POST",
                data: fd,
                dataType: 'json',
                processData: false,
                contentType: false,
                success:function(result){
                    console.log(result.location);


                    if(result.status)
                    {
                        $.NotificationApp.send("Success",result.message,"top-right","rgba(0,0,0,0.2)","success")
                        setTimeout(function(){
                            window.location.href = result.location;
                        }, 1000);
                    }
                    else
                    {
                        $.NotificationApp.send("Error",result.message,"top-right","rgba(0,0,0,0.2)","error")
                    }
                },
            });
        })
    });
</script>
@endsection
