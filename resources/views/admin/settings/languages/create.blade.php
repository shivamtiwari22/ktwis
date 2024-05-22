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
        <li class="breadcrumb-item"><a aria-current="page" href="{{route('languages.list')}}">Languages</a></li>
        <li class="breadcrumb-item"><a aria-current="page">Add Language</a></li>
    </ol>
</nav>
    <div class="card p-4 mt-4">
        <div class="d-flex justify-content-end mb-2">
            <a href="{{route('languages.list')}}" class="btn btn-primary">View All Languages</a>
        </div>
        <form id="business_form">
            <div class="row">
                <div class="mb-3 col-lg-6">
                    <label for="simpleinput" class="form-label">Language<span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="language">
                    
                </div>
                <div class="mb-3 col-lg-6">
                    <label for="simpleinput" class="form-label">Order</label><span tabindex="0" data-bs-toggle="popover" data-bs-trigger="hover" data-bs-placement="top" data-bs-content="The position you want to show this language on the language option. The smallest number will display first.">
                        <i class="dripicons-question"></i></span>
                    <input type="number" class="form-control" name="order">
                </div>
            </div>
            <div class="row">
                <div class="mb-3 col-lg-4">
                    <label for="simpleinput" class="form-label">Code<span class="text-danger">*</span></label><span tabindex="0" data-bs-toggle="popover" data-bs-trigger="hover" data-bs-placement="top" data-bs-content="The locale code, the code must have the same name as the language folder.">
                        <i class="dripicons-question"></i></span>
                    <input type="text" class="form-control" name="code">
                    
                </div>
                <div class="mb-3 col-lg-4">
                    <label for="simpleinput" class="form-label">Flag <span class="fw-light">(png/jpg/svg)</sapn></label>
                    <input type="file" class="form-control" name="flag" accept=".png, .jpg, .svg">
                </div>
                <div class="mb-3 col-lg-4">
                    <label for="simpleinput" class="form-label">PHP LOCALE CODE<span class="text-danger">*</span></label><span tabindex="0" data-bs-toggle="popover" data-bs-trigger="hover" data-bs-content="The PHP locale code for system use like translating date, time etc. Please find the full list of the PHP locale code on the documentation.">
                        <i class="dripicons-question"></i>
                    </span>
                    <input type="text" class="form-control" name="php_locale_code">
                </div>
            </div>
            <div class="row">
                <label for="simpleinput" class="form-label">Status<span class="text-danger">*</span></label>
                <div class="mb-3">
                    <input type="hidden" name="status" value="1">
                    <input type="checkbox" id="switch2" checked data-switch="primary"value="1" onclick="updateCheckboxValue(this)">
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
    $(function () {
        $('#business_form').on('submit', function(e){
            e.preventDefault();
            let fd = new FormData(this);
            fd.append('_token',"{{ csrf_token() }}");
            $.ajax({
                url: "{{ route('language.store') }}",
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
@endsection
