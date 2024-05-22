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
            <li class="breadcrumb-item"><a aria-current="page" href="{{ route('slider.list') }}">Slider</a></li>
            <li class="breadcrumb-item"><a aria-current="page">Add Slider</a></li>
        </ol>
    </nav>
    <div class="card p-4 mt-4">
        <div class="d-flex justify-content-end mb-2">
            <a href="{{ route('slider.list') }}" class="btn btn-primary">View All Sliders</a>
        </div>
        <form id="currency_form">
            <div class="row">
                <div class="mb-3 col-lg-8">
                    <label for="simpleinput" class="form-label">Title</label><span tabindex="0" data-bs-toggle="popover"
                        data-bs-trigger="hover" data-bs-placement="top"
                        data-bs-content="This line will be highlighted over the slider. Leave it blank if you don't want to show the title.">
                        <i class="dripicons-question"></i></span>
                    <input type="text" class="form-control" name="title" value="{{ $slider->title }}">
                    <input type="hidden" class="form-control" name="id" value="{{ $slider->id }}">
                </div>
                <div class="mb-3 col-lg-4">
                    <label for="simpleinput" class="form-label">Title Color</label>
                    <input type="color" class="form-control" name="title_color" value="{{ $slider->title_color }}">
                </div>
            </div>
            {{-- <div class="row">
                <div class="mb-3 col-lg-8">
                    <label for="simpleinput" class="form-label">Subtitle</label><span tabindex="0"
                        data-bs-toggle="popover" data-bs-trigger="hover" data-bs-placement="top"
                        data-bs-content="The second line of the title. Leave it blank if you don't want to show this.">
                        <i class="dripicons-question"></i></span>
                    <input type="text" class="form-control" name="subtitle" value="{{ $slider->subtitle }}">
                </div>
                <div class="mb-3 col-lg-4">
                    <label for="simpleinput" class="form-label">Subtitle Color</label>
                    <input type="color" class="form-control" name="subtitle_color" value="{{ $slider->subtitle_color }}">
                </div>
            </div> --}}
            <div class="row">
                <div class="mb-3 col-lg-8">
                    <label for="simpleinput" class="form-label">Description</label><span tabindex="0"
                        data-bs-toggle="popover" data-bs-trigger="hover" data-bs-placement="top"
                        data-bs-content="Few more words about the slider. Leave it blank if you don't want to show the description.">
                        <i class="dripicons-question"></i></span>
                    <input type="text" class="form-control" name="description" value="{{ $slider->description }}">
                </div>
                <div class="mb-3 col-lg-4">
                    <label for="simpleinput" class="form-label">Description Color</label>
                    <input type="color" class="form-control" name="description_color"
                        value="{{ $slider->description_color }}">
                </div>
            </div>
            <div class="row">
                <div class="mb-3 col-lg-6">
                    <label for="simpleinput" class="form-label">Link</label><span tabindex="0" data-bs-toggle="popover"
                        data-bs-trigger="hover" data-bs-placement="top" data-bs-content="Users will redirect to this link.">
                        <i class="dripicons-question"></i></span>
                    <input type="text" class="form-control" name="link" value="{{ $slider->link }}">
                </div>
                <div class="mb-3 col-lg-6">
                    <label for="simpleinput" class="form-label">Order</label><span tabindex="0" data-bs-toggle="popover"
                        data-bs-trigger="hover" data-bs-placement="top"
                        data-bs-content="The slider will be  arranged by this order.">
                        <i class="dripicons-question"></i></span>
                    <input type="text" class="form-control" name="order" value="{{ $slider->order }}"  oninput="this.value = 
                    !!this.value && Math.abs(this.value) >= 0 ? Math.abs(this.value) : null">
                </div>
            </div>
            <div class="row">
                <div class="mb-3 col-lg-6">
                    <label for="simpleinput" class="form-label">Text Position</label><span tabindex="0"
                        data-bs-toggle="popover" data-bs-trigger="hover" data-bs-placement="top"
                        data-bs-content="Set your content position on slider. default position right">
                        <i class="dripicons-question"></i></span>
                    <select name="text_position" class="form-select" name="text_position">
                        <option value="left" {{ $slider->text_position == 'left' ? 'selected' : '' }}>Left</option>
                        <option value="right" {{ $slider->text_position == 'right' ? 'selected' : '' }}>Right</option>
                    </select>
                </div>
                <div class="mb-3 col-lg-6">
                    <label for="simpleinput" class="form-label">Slider Image (jpg, jpeg, png only ,2mb)<span
                            class="text-danger">*</span></label><span tabindex="0" data-bs-toggle="popover"
                        data-bs-trigger="hover" data-bs-placement="top"
                        data-bs-content="The main image what will display as slider. Its required to generate the slider.">
                        <i class="dripicons-question"></i></span>
                    @if ($slider->slider_image)
                        <span class="mx-2"><img
                                src="{{ url('public/admin/site/sliderManagement/slider/' . $slider->slider_image) }}"
                                alt="{{ $slider->title }}" width="40px"></span>
                    @endif
                    <input type="file" class="form-control" name="slider_image" value="{{ $slider->title }}">
                </div>
            </div>
            <div class="row">
                <div class="mb-3 col-lg-6">
                    <label for="simpleinput" class="form-label">Mobile Image (jpg, jpeg, png only ,2mb)</label><span tabindex="0"
                        data-bs-toggle="popover" data-bs-trigger="hover" data-bs-placement="top"
                        data-bs-content="The slider image for mobile app. The system will hide this slider on mobile if not provided. Keep the ratio 2:1 in size, which means the width of the image should be double of its height.">
                        <i class="dripicons-question"></i></span>
                    @if ($slider->mobile_image)
                        <span class="mx-2"><img
                                src="{{ url('public/admin/site/sliderManagement/mobile/' . $slider->mobile_image) }}"
                                alt="{{ $slider->title }}" width="40px"></span>
                    @endif
                    <input type="file" class="form-control mb-2" name="mobile_image" value="{{ $slider->title }}">
                    @if ($slider->mobile_image)
                        <span><input type="checkbox" name="delete_mobile"> Delete Image</span>
                    @endif
                </div>


                <div class="mb-3 col-lg-6">
                    <label for="simpleinput" class="form-label">Has Category Slider</label><span tabindex="0"
                        data-bs-toggle="popover" data-bs-trigger="hover" data-bs-placement="top"
                        data-bs-content="Check the category slider box if u want to use the slider for category page">
                        <i class="dripicons-question"></i> <input class="ms-2" type="checkbox"
                            {{ $slider->has_category_slider ? 'checked' : '' }} name="has_category_slider"
                            id="cat-check"></span>


                    <select name="category_id" id="category" class="form-select">
                        <option value="" selected disabled>Select Category</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}"
                                {{ $category->id == $slider->category_id ? 'selected' : '' }}>
                                {{ $category->category_name }}</option>
                        @endforeach
                    </select>

                </div>
            </div>
            <div class="row">
                <label for="simpleinput" class="form-label">Status</label>
                <div class="mb-3">
                    <input type="hidden" name="status" value="{{ $slider->status }}">
                    <input type="checkbox" id="switch2" {{ $slider->status == '1' ? 'checked' : '' }}
                        data-switch="primary" value={{ $slider->status }} onclick="updateCheckboxValue(this)">
                    <label for="switch2" data-on-label="On" data-off-label="Off"></label>
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
        $(document).ready(function() {



            $('#currency_form').validate({
                rules: {
                    title: {
                        maxlength: 256,
                    },
                    subtitle: {
                        maxlength: 256,

                    },
                    description: {
                        maxlength: 500,

                    },
                    link: {
                        linkvalid: true,
                    },
                    order: {
                        number: true
                    },
                    slider_image: {
                        required:true
                        filesize: 1024,
                        imageFormat: true
                    },
                    mobile_image: {
                        filesize: 1024,
                        imageFormat: true
                    },

                    category_id: {
                        required: {
                            depends: function() {
                                return $("#cat-check").is(":checked");
                            }

                        },
                    }
                },
                messages: {
                    slider_image: {
                        imageFormat: "Please upload file in these format only (jpg, jpeg, png).",
                        filesize: "Maximum file size is 2MB"
                    },
                    mobile_image: {
                        imageFormat: "Please upload file in these format only (jpg, jpeg, png).",
                        filesize: "Maximum file size is 2MB"
                    },

                },
            });
            $.validator.addMethod("linkvalid", function(value, element) {
                return this.optional(element) ||
                    /^(http|https|ftp):\/\/[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$/i
                    .test(value);
            }, "Please endter valid link.");

            $.validator.addMethod("filesize", function(value, element, param) {
                var maxSize = param * 1024; // Convert param to bytes
                return this.optional(element) || (element.files[0].size <= maxSize);
            }, "File size must be less than {0} KB");

            $.validator.addMethod("imageFormat", function(value, element) {
                var allowedFormats = ["jpg", "jpeg", "png"];
                var extension = value.split('.').pop().toLowerCase();
                return this.optional(element) || $.inArray(extension, allowedFormats) !== -1;
            }, "Invalid image format. Allowed formats: jpg, jpeg, png");
        })
    </script>
    <script>
        $(function() {
                $(document).on('submit', '#currency_form', function(e) {
                e.preventDefault();
                let fd = new FormData(this);
                fd.append('_token', "{{ csrf_token() }}");
                $.ajax({
                    url: "{{ route('slider.update') }}",
                    type: "POST",
                    data: fd,
                    dataType: 'json',
                    processData: false,
                    contentType: false,
                    success: function(result) {
                        if (result.status) {
                            $.NotificationApp.send("Success", result.message, "top-right",
                                "rgba(0,0,0,0.2)", "success")
                            setTimeout(function() {
                                window.location.href = result.location;
                            }, 1000);
                        } else {
                            $.NotificationApp.send("Error", result.message, "top-right",
                                "rgba(0,0,0,0.2)", "error")
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
        var category = <?php echo json_encode($slider); ?>

        if (category.category_id) {
            $('#category').show();
        } else {
            $('#category').hide();

        }

        console.log(category);
        // $('#category').hide();
        $(document).on('click', '#cat-check', function() {
            if ($(this).prop('checked') == true) {
                $('#category').show();

            } else {
                $('#category').hide();
                $('#category').val('');
            }
        })
    </script>
@endsection
