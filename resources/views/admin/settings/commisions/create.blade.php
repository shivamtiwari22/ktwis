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
        <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Home</a></li>
        <li class="breadcrumb-item"><a aria-current="page">Settings</a></li>
        <li class="breadcrumb-item"><a aria-current="page" href="{{route('commision.list')}}">Commisions</a></li>
        <li class="breadcrumb-item"><a aria-current="page">Add</a></li>
    </ol>
</nav>
    <div class="card p-4 mt-4">
        <div class="d-flex justify-content-end mb-2">
            <a href="{{route('commision.list')}}" class="btn btn-primary">View All Commisions</a>
        </div>
        <form id="business_form">
            <div class="row">
                <div class="mb-3 col-lg-6">
                    <label for="simpleinput" class="form-label">Business Area<span class="text-danger">*</span></label>
                    <select class="form-select" id="example-select" name="business_area">
                        <option disabled selected>Select Business Area</option>
                        @foreach($business as $item)
                            <option value="{{ $item->id }}">
                                {{ $item->name }}
                            </option>
                        @endforeach
                    </select>
                    
                </div>
                <div class="mb-3 col-lg-6">
                    <label for="simpleinput" class="form-label">Countries<span class="text-danger">*</span></label>
                      <select name="countries[]" multiple id="country" class="select2 form-control select2-multiple" data-toggle="select2">
                        @foreach($countries as $item)
                        <option value="{{ $item->id }}"  >
                            {{ $item->country_name}}
                        </option>
                    @endforeach
                      </select>
                      <label for="country" id="country-error" class="error"></label>
                </div>
            </div>

            <div class="row">
                <div class="mb-3 col-lg-6">
                    <label for="simpleinput" class="form-label">Platform Charge <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" oninput="this.value = !!this.value && Math.abs(this.value) >=0 ? Math.abs(this.value) : null" name="platform_charges" id="platform">

                </div>
                <div class="mb-3 col-lg-6">
                    <label for="simpleinput" class="form-label">Transaction Charge  <span
                            class="text-danger">*</span></label>
                    <input type="text" required oninput="this.value = !!this.value && Math.abs(this.value) >=0 ? Math.abs(this.value) : null" class="form-control" name="transaction_charges" id="transaction">
                </div>
            </div>

            <div class="row">
                <div class="mb-3 col-lg-6">
                    <label for="simpleinput" class="form-label">Total Charge  </label>
                    <input type="text" required  class="form-control" id="amount" readonly name="total_charges">
                </div>
            </div>

            <div class="row">
                <label for="simpleinput" class="form-label">Status</label>
                <div class="mb-3">
                    <input type="hidden" name="status" value="0">
                    <input type="checkbox" id="switch2" checked data-switch="primary" name="status" value="1" onclick="updateCheckboxValue(this)">
                    <label for="switch2" data-on-label="On" data-off-label="Off"></label>          
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
        $('#business_form').validate({
            rules: {
                business_area: {
                    required: true,
                },
                platform_charges: {
                    required: true,
                    number: true,
                    maxlength:6

                },
                transaction_charges: {
                    required: true,
                    number: true,
                    maxlength:6
                },
                "countries[]" : {
                    required: true
                }

            },
        });
    })
</script>

<script>
    $(function () {
        $(document).on('submit','#business_form', function(e){
            e.preventDefault();
            let fd = new FormData(this);
            fd.append('_token',"{{ csrf_token() }}");
            $.ajax({
                url: "{{ route('commision.store') }}",
                type:"POST",
                data: fd,
                dataType: 'json',
                processData: false,
                contentType: false,
                success:function(result){
                    if(result.status)
                    {
                        $.NotificationApp.send("Success",result.msg,"top-right","rgba(0,0,0,0.2)","success")
                        setTimeout(function(){
                            window.location.href = result.location;
                        }, 1000);
                    }
                    else
                    {
                        $.NotificationApp.send("Error",result.msg,"top-right","rgba(0,0,0,0.2)","error")
                    }
                },
            });
        })
    });
</script>
<script>
    function updateCheckboxValue(checkbox) {
        var hiddenInput = document.querySelector('input[name="status"][type="hidden"]');
        hiddenInput.value = checkbox.checked ? 1 : 0;
    }
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
