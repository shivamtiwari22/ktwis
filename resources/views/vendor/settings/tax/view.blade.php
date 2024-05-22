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
        <li class="breadcrumb-item"><a href="{{route('vendor.settings.tax.index')}}">Tax</a></li>
        <li class="breadcrumb-item active" aria-current="page">View Tax</li>
    </ol>
</nav>
<h2>View <span class="badge badge-success-lighten">Tax</span> :: {{$tax->tax_name}}</h2>
<hr>
<div class="card p-4 mt-4">
    <div class="d-flex justify-content-end mb-2">
        <a href="{{route('vendor.settings.tax.index')}}" class="btn btn-primary">View All Taxes</a>
    </div>
    <form id="coupon_form">
        <div class="row">
            <div class="mb-3 col-lg-6">
                <label for="tax_name">{{ __('Tax Name') }}</label>
                <input type="hidden" name="id" id="id" value="{{$tax->id}}">
                <input id="tax_name" type="text" class="form-control @error('tax_name') is-invalid @enderror" name="tax_name" value="{{ $tax->tax_name }}" placeholder="Tax Name" autofocus>
            </div>
            <div class="mb-3 col-lg-6">
                <label for="tax_rate">{{ __('Tax Rate') }} (%)</label>
                <input id="tax_rate" type="number" class="form-control @error('tax_rate') is-invalid @enderror" name="tax_rate" placeholder="Tax Rate %" value="{{ $tax->tax_rate }}">
            </div>
        </div>
        <div class="row">
            <div class="mb-3 col-lg-4">
                <label for="status">{{ __('Status') }}</label>
                <select id="tax_status" class="form-control @error('status') is-invalid @enderror" name="status">
                    <option value="">--Select Status--</option>
                    <option value="active" {{ $tax->status == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ $tax->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div class="mb-3 col-lg-4">
                <label for="country_id">{{ __('Country') }}</label>
                <select id="country_id" class="form-control @error('country_id') is-invalid @enderror" name="country_id">
                    <option value="">--Select Country--</option>
                    @foreach ($countries as $country)
                    <option value="{{ $country->id }}" {{ $tax->country_id == $country->id ? 'selected' : '' }}>{{ $country->country_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3 col-lg-4">
                <label for="state_id">{{ __('State') }}</label>
                <select id="state_id" class="form-control @error('state_id') is-invalid @enderror" name="state_id">
                    <option value="">--Select State--</option>
                    @foreach ($states as $state)
                    <option value="{{ $state->id }}" {{ $tax->state_id == $state->id ? 'selected' : '' }}>{{ $state->state_name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

    </form>
</div>
@endsection


@section('script')
<script>
    $(document).ready(function() {
        $('#country_id').change(function() {
            var countryId = $(this).val()

            $('#state_id').empty().append('<option value="">--Select State--</option>');

            $.ajax({
                url: "{{ route('vendor.settings.tax.get_states', '') }}/" + countryId,
                type: 'GET',
                success: function(data) {
                    if (data.states.length > 0) {
                        $.each(data.states, function(index, state) {
                            $('#state_id').append('<option value="' + state.id + '">' + state.state_name + '</option>');
                        });
                    }
                }
            });
        });
    });
</script>

<script>
    $(function() {
        $('#coupon_form').on('submit', function(e) {
            e.preventDefault();

            if (validateForm()) {
                let fd = new FormData(this);
                fd.append('_token', "{{ csrf_token() }}");
                $.ajax({
                    url: "{{ route('vendor.settings.tax.update') }}",
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
        })

        function validateForm() {
            var isValid = true;

            $('.error-message').remove();

            $('#tax_name, #tax_rate, #tax_status, #country_id ,#state_id').each(function() {
                if ($.trim($(this).val()) === '') {
                    var errorMessage = 'This field is required';
                    $(this).addClass('is-invalid');
                    $(this).after('<span class="error-message" style="color:red;">' + errorMessage + '</span>');
                    isValid = false;
                } else {
                    $(this).removeClass('is-invalid');
                }
            });

            $('#tax_name, #tax_rate, #tax_status, #country_id ,#state_id').on('input change', function() {
                $(this).removeClass('is-invalid');
                $(this).next('.error-message').remove();
            });

            return isValid;
        }
    });
</script>
@endsection