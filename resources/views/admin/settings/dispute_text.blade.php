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
            <li class="breadcrumb-item"><a aria-current="page">Dispute Text</a></li>
        </ol>
    </nav>
    <div class="card p-4 mt-4">

        <form id="system_form">
            <div class="row">
                <div class="mb-3 col-lg-12">
                    <label for="simpleinput" class="form-label">Dispute Right Text<span class="text-danger">*</span></label>
                    <textarea id="content_show" name="dispute_right_text" class="form-control note">{!! $dispute->dispute_right_text ?? '' !!}</textarea>
                    <span id="error" style="color: red"></span>

                </div>
            </div>


            <div class="row">
                <div class="mb-3 col-lg-12">
                    <label for="simpleinput" class="form-label">Dispute Left Text<span class="text-danger">*</span></label>
                    <textarea id="content" name="dispute_left_text" class="form-control summernote">{!! $dispute->dispute_left_text ?? '' !!}</textarea>
                    <span id="note-error" style="color: red"></span>

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
            $('#system_form').validate({
                rules: {
                    dispute_text: {
                        required: true,
                    },
                    refund_request: {
                        required: true,
                    },
                    return_goods: {
                        required: true,
                    }
                },
            });
        })
    </script>



    <script>
        $(function() {
            $(document).on('submit', '#system_form', function(e) {
                e.preventDefault();
                let fd = new FormData(this);
                fd.append('_token', "{{ csrf_token() }}");
             
                    $.ajax({
                        url: "{{ route('admin.update_dispute_text') }}",
                        type: "POST",
                        data: fd,
                        dataType: 'json',
                        processData: false,
                        contentType: false,
                        success: function(result) {
                            console.log(result);
                            if (result.status) {
                                $.NotificationApp.send("Success", result.message, "top-right",
                                    "rgba(0,0,0,0.2)", "success")
                                setTimeout(function() {
                                    window.location.reload();
                                }, 1000);
                            } else {
                                $.NotificationApp.send("Error", result.message, "top-right",
                                    "rgba(0,0,0,0.2)", "error")
                            }
                        },
                    });
                
            })
        });


        function validateForm_edit() {
            var isValid = true;

            $('.error-message').remove();

            var contentValue = $('.summernote').summernote('code');

            if ($.trim(contentValue) === '' || contentValue === '<p><br></p>') {
                var errorMessage = 'Please enter the text';
                // $('#content').addClass('is-invalid');
                // $('#content').after('<span class="error-message" style="color:red;">' + errorMessage +
                //     '</span>');
                $('#note-error').text(errorMessage);
                isValid = false;
                console.log('yes');
            } else {
                $('#content').removeClass('is-invalid');
                $('#note-error').text('');
                console.log('no');

            }



            var Value = $('.note').summernote('code');

            if ($.trim(Value) === '' || Value === '<p><br></p>') {
                var errorMessage = 'Please enter the text';
                // $('#content').addClass('is-invalid');
                // $('#content').after('<span class="error-message" style="color:red;">' + errorMessage +
                //     '</span>');
                $('#error').text(errorMessage);
                isValid = false;
                console.log('yes');
            } else {
                $('#content_show').removeClass('is-invalid');
                $('#error').text('');
                console.log('no');

            }







            return isValid;
        }
    </script>
    <script>
        $('.summernote').summernote({
            height: 80
        });

        $('.note').summernote({
            height: 80
        });
    </script>
@endsection
