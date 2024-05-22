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
        <li class="breadcrumb-item active" aria-current="page">Add Tax</li>
    </ol>
</nav>
<h2>Add <span class="badge badge-success-lighten">Tax</span></h2>
<hr>
<div class="card p-4 mt-4">
    <div class="d-flex justify-content-end mb-2">
        <a href="{{route('vendor.settings.tax.index')}}" class="btn btn-primary">View All Taxes</a>
    </div>
    <form id="coupon_form">
        <div class="row">
            <div class="mb-3 col-lg-6">
                <label for="tax_name">{{ __('Tax Name') }} <span class="text-danger">*</span></label>
                <input id="tax_name" type="text" class="form-control @error('tax_name') is-invalid @enderror" name="tax_name" value="{{ old('tax_name') }}" placeholder="Tax Name" autofocus>
                @error('tax_name')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="mb-3 col-lg-6">
                <label for="tax_rate">{{ __('Tax Rate') }} (%)  <span class="text-danger">*</span></label>
                    <input id="tax_rate" type="number" class="form-control @error('tax_rate') is-invalid @enderror" name="tax_rate" placeholder="Tax Rate %" min="1" value="{{ old('tax_rate') }}">
                @error('tax_rate')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
        </div>
        <div class="row">
            <div class="mb-3 col-lg-4">
                <label for="status">{{ __('Status') }}  <span class="text-danger">*</span></label>
                <select id="tax_status" class="form-select @error('status') is-invalid @enderror" name="status">
                    <option value="">--Select Status--</option>
                    <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
                @error('status')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="mb-3 col-lg-4">
                <label for="country_id">{{ __('Country') }}  <span class="text-danger">*</span></label>
                <select id="country_id" class="form-select @error('country_id') is-invalid @enderror" name="country_id">
                    <option value="">--Select Country--</option>
                    @foreach ($countries as $country)
                    <option value="{{ $country->id }}" {{ old('country_id') == $country->id ? 'selected' : '' }}>{{ $country->country_name }}</option>
                    @endforeach
                </select>
                @error('country_id')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="mb-3 col-lg-4">
                <label for="state_id">{{ __('State') }}  <span class="text-danger">*</span></label>
                <select id="state_id" class="form-select @error('state_id') is-invalid @enderror" name="state_id">
                    <option value="">--Select State--</option>
                </select>

                @error('state_id')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
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
                    url: "{{ route('vendor.settings.tax.store') }}",
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
                  var input = $(this).attr('id');
                if ($.trim($(this).val()) === '') {
                    var errorMessage = 'This field is required';
                    $(this).addClass('is-invalid');
                    $(this).after('<span class="error-message" style="color:red;">' + errorMessage + '</span>');
                    isValid = false;
                }
                else if(input == "tax_name"){
                        if($.trim($(this).val()).length < 4 || $.trim($(this).val()).length > 26){
                            var errorMessage = 'Tax name must be between 4 and 26 characters.';
                    $(this).addClass('is-invalid');
                    $(this).after('<span class="error-message" style="color:red;">' + errorMessage + '</span>');
                    isValid = false;
                        }
                } 
                else if (input == "tax_rate"  && parseFloat($.trim($(this).val())) < 0) {
                        var errorMessage = 'Value cannot be negative';
                        $input.addClass('is-invalid');
                        $input.after('<span class="error-message" style="color:red;">' + errorMessage +
                            '</span>');
                        isValid = false;
                    } 
                else {
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