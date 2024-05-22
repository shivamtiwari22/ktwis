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
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.disputes.index') }}">Disputes</a></li>
            <li class="breadcrumb-item active" aria-current="page">Disputes Reply</li>
        </ol>
    </nav>
    <form id="reply_form">
        <input type="hidden" name="id" id="id" value="{{ $dispute->id }}">
        <div class="card p-4 mt-4">
           
            <div class="row">
                <div class="col-sm-6">
                    <label for="">STATUS </label><br>
                    <select name="reply_status" id="reply_status" class="form-control">
                    <option value="" selected disabled>Select Status</option>
                    <option value="New" {{ $dispute->status == 'New' ? 'selected' : '' }}>NEW</option>
                    <option value="Open" {{ $dispute->status == 'Open' ? 'selected' : '' }}>OPEN</option>
                    <option value="Pending" {{ $dispute->status == 'Pending' ? 'selected' : '' }}>PENDING</option>
                    <option value="Solved" {{ $dispute->status == 'Solved' ? 'selected' : '' }}>SOLVED</option>
                    <option value="Closed" {{ $dispute->status == 'Closed' ? 'selected' : '' }}>CLOSED</option>
                    <option value="Span" {{ $dispute->status == 'Span' ? 'selected' : '' }}>SPAM</option>
                    </select>
                </div>
                <div class="col-sm-6">
                    <label for="">PRIORITY* </label><br>
                    <select name="reply_priority" id="reply_status" class="form-control">
                            <option value="" selected disabled>Select Status</option>
                            <option value="Low" {{ $dispute->priority == 'Low' ? 'selected' : '' }}>Low</option>
                            <option value="Normal" {{ $dispute->priority == 'Normal' ? 'selected' : '' }}>Normal</option>
                            <option value="High" {{ $dispute->priority == 'High' ? 'selected' : '' }}>High</option>
                            <option value="Critical" {{ $dispute->priority == 'Critical' ? 'selected' : '' }}>Critical</option>
                            </select>
                    </select>
                </div>
            </div><br><br>

            <div>
                <label for="content">Body:<span class="red">*</span></label>
                <textarea id="contents " name="content" class="form-control summernote"></textarea>
            </div>
                 <div class="col-sm-12">
                    <label for="">Upload Attachment: </label><br>
                    <input type="file" name="attachment" id="attachment" class="form-control"  />
                </div>
            <div class="d-flex mt-2">
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
                var form = $(this);
                var formData = new FormData(form[0]);
                $.ajax({
                    url: "{{ route('admin.disputes.update_reply') }}",
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
                                "rgba(0,0,0,0.2)", "error")
                        }
                    },
                    error: function(xhr, status, error) {
                        // console.log(xhr.responseText);
                        $.NotificationApp.send("Error", xhr.responseText, "top-right",
                            "rgba(0,0,0,0.2)", "error");

                    }
                });
            });
        });
    </script>
@endsection
