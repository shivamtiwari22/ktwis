@extends('vendor.layout.app')

@section('meta_tags')
@endsection


@section('title')
@endsection


@section('css')
<style>
    .red
    {
        color:red !important;
    }

    .note-insert {
            display: none;
        }

        .note-view {
            display: none;
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
<form id="reply_form">
    <input type="hidden" name="id" id="id" value="{{$dispute->id}}">
    <div class="card p-4 mt-4">
        <div class="d-flex justify-content-end mb-2">
            <a href="{{route('vendor.disputes.index')}}" class="btn btn-primary">View All Disputes</a>
        </div>
        <div class="row">
            <div class="col-sm-6">
                <label for="">Status: </label><br>
                <select name="reply_status" id="reply_status" class="form-select">
                    <option value="new" {{ $dispute->status == 'new' ? 'selected' : '' }}>NEW</option>
                    <option value="open" {{ $dispute->status == 'open' ? 'selected' : '' }}>OPEN</option>
                    <option value="waiting" {{ $dispute->status == 'waiting' ? 'selected' : '' }}>WAITING</option>
                    <option value="solved" {{ $dispute->status == 'solved' ? 'selected' : '' }}>SOLVED</option>
                    <option value="closed" {{ $dispute->status == 'closed' ? 'selected' : '' }}>CLOSED</option>
                </select>
            </div>
            <div class="col-sm-6">
                <label for="">Upload Attachment: </label><br>
                <input type="file" name="attachment" id="attachment" class="form-control" />
            </div>
        </div><br><br>

        <div>
            <label for="content">Body:<span class="red">*</span></label>
            <textarea id="content" name="content" class="form-control summernote"></textarea>
        </div>
        <div class="d-flex">
            <button type="submit" class="btn btn-success mx-auto"> Submit</button>
        </div>
    </div>
</form>
@endsection


@section('script')
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('.summernote').summernote({
            height: 200
        });
    });
</script>
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
            if (validateForm_edit()) {
                var form = $(this);
                var formData = new FormData(form[0]);
                $('.mx-auto').prop('disabled', true);
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
                                "rgba(0,0,0,0.2)", "error");
                $('.mx-auto').prop('disabled', false);

                        }
                    },
                    error: function(xhr, status, error) {
                        console.log(xhr.responseText);
                        var response = JSON.parse(xhr.responseText);
                        var message = response.message;
                        console.log(message);
                        $.NotificationApp.send("Error", message, "top-right",
                            "rgba(0,0,0,0.2)", "error");
                            $('.mx-auto').prop('disabled', false);

                    }
                });
            }
        });

        function validateForm_edit() {
            var isValid = true;

            $('.error-message').remove();

            $('#content').each(function() {
                if ($.trim($(this).val()) === '') {
                    var errorMessage = 'The Body is required.';
                    $(this).addClass('is-invalid');
                    $(this).after('<span class="error-message" style="color:red;">' + errorMessage + '</span>');
                    isValid = false;
                } else {
                    $(this).removeClass('is-invalid');
                }
            });

            $('#content').on('input change', function() {
                $(this).removeClass('is-invalid');
                $(this).next('.error-message').remove();
            });

            return isValid;
        }
    });
</script>
@endsection