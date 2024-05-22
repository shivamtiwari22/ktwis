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
        <li class="breadcrumb-item"><a href="{{route('vendor.shipping.rates')}}">Shipping Rates</a></li>
        <li class="breadcrumb-item active" aria-current="page">Edit Rate</li>
    </ol>
</nav>
<div class="card p-4 mt-4">
    <div class="d-flex justify-content-end mb-2">
        <a href="{{route('vendor.shipping.rates')}}" class="btn btn-primary">View Shipping Rates</a>
    </div>
    <form id="rate_form">
        <div class="row">
            <div class="mb-3 col-lg-6">
                <label for="simpleinput" class="form-label">Name<span class="text-danger">*</span></label>
                <input type="hidden" class="form-control" name="id" value="{{$rates->id}}" id="id">
                <input type="text" class="form-control" name="name" value="{{$rates->name}}" id="name">
            </div>
            <div class="mb-3 col-lg-6">
                <label for="example-select" class="form-label">Shipping Carrier<span class="text-danger">*</span></label>
                <select name="carrier_id" id="carrier_id" class="form-control">
                    <option value="">-- Select Carrier --</option>
                    @foreach ($carriers as $carrier)
                    <option value="{{$carrier->id}}" {{ $rates->carrier_id == $carrier->id ? 'selected' : '' }}>{{$carrier->name}}</option>
                    @endforeach
                </select>
            </div>

        </div>
        <div class="row">
            <div class="mb-3 col-lg-4">
                <label for="simpleinput" class="form-label"> Delivery Takes<span class="text-danger">*</span></label>
                <input type="number" class="form-control" id="delivery_days" value="{{$rates->delivery_time}}" name="delivery_days" placeholder="2 to 5 days">
            </div>
            <div class="mb-3 col-lg-4">
                <label for="example-select" class="form-label">Minimum Order Weight<span class="text-danger">*</span></label>
                <input type="number" class="form-control" value="{{$rates->minimum_order_weight}}" name="minimum" value="0">
            </div>
            <div class="mb-3 col-lg-4">
                <label class="form-label">Maximum Order Weight<span class="text-danger">*</span></label>
                <input type="number" class="form-control" value="{{$rates->max_order_weight}}" name="maximum" value="100">
            </div>
        </div>
        <div class="row">
            <div class="mb-3 col-lg-4">
                <label for="simpleinput" class="form-label">Status<span class="text-danger">*</span></label>
                <select name="status_rate" id="status_rate" class="form-control">
                    <option value="">-- Select Status --</option>
                    <option value="active" {{ $rates->status == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ $rates->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div class="mb-3 col-lg-4">
                <label class="form-label">Rate<span class="text-danger">*</span></label>
                <input type="number" class="form-control" name="rate" value="{{$rates->rate}}" id="rate">
            </div>
            <div class="mb-3 col-lg-4">
                <label class="form-label">Free Shipping<span class="text-danger">*</span></label>
                <div class="mb-3">
                    <input type="hidden" name="free_shipping" id="free_shipping" value="{{ $rates->is_free ? 1 : 0 }}">
                    <input type="checkbox" id="switch2" data-switch="primary" value="1" {{ $rates->is_free ? 'checked' : '' }} onclick="updateCheckboxValue(this)">
                    <label for="switch2" data-on-label="Free" data-off-label="Paid"></label>
                </div>
            </div>
        </div>
        <div>
            <button type="submit" class="btn btn-success">Update</button>
        </div>
    </form>
</div>
@endsection


@section('script')
<script>
    $(function() {
        $('#rate_form').on('submit', function(e) {
            e.preventDefault();

            if (validateForm_edit()) {

                let fd = new FormData(this);
                fd.append('_token', "{{ csrf_token() }}");
                $.ajax({
                    url: "{{ route('vendor.shipping.rates.update') }}",
                    type: "POST",
                    data: fd,
                    dataType: 'json',
                    processData: false,
                    contentType: false,
                    success: function(result) {
                        console.log(result.location);
                        if (result.status) {
                            $.NotificationApp.send("Success", result.msg, "top-right", "rgba(0,0,0,0.2)", "success")
                            setTimeout(function() {
                                window.location.href = result.location;
                            }, 1000);
                        } else {
                            $.NotificationApp.send("Error", result.msg, "top-right", "rgba(0,0,0,0.2)", "error")
                        }
                    },
                });
            }
        });

        function validateForm_edit() {
            var isValid = true;

            $('.error-message').remove();

            $('#name, #carrier_id ,#delivery_days ,#minimum ,#maximum ,#status_rate ,#rate, #free_shipping').each(function() {
                if ($.trim($(this).val()) === '') {
                    var errorMessage = 'This field is required';
                    $(this).addClass('is-invalid');
                    $(this).after('<span class="error-message" style="color:red;">' + errorMessage + '</span>');
                    isValid = false;
                } else {
                    $(this).removeClass('is-invalid');
                }
            });

            $('#name, #carrier_id ,#delivery_days ,#minimum ,#maximum ,#status_rate ,#rate, #free_shipping').on('input change', function() {
                $(this).removeClass('is-invalid');
                $(this).next('.error-message').remove();
            });

            return isValid;
        }
    });
</script>
<script>
    $(document).ready(function() {
        updateCheckboxValue($('#switch2').is(':checked'));

        $('#switch2').change(function() {
            updateCheckboxValue($(this).is(':checked'));
        });

        function updateCheckboxValue(checkbox) {
            var hiddenInput = $('input[name="free_shipping"][type="hidden"]');
            hiddenInput.val(checkbox ? 1 : 0);

            var rateInput = $('#rate');
            if (checkbox) {
                rateInput.val('0');
                rateInput.prop('readonly', true);
            } else {
                rateInput.prop('readonly', false);
            }
        }
    });
</script>

@endsection