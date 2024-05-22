@extends('admin.layout.app')

@section('meta_tags')
@endsection


@section('title')
@endsection


@section('css')
    <style>
        .tags-input-wrapper {
            background: white;
            padding: 10px;
            border-radius: 4px;
            max-width: 100%;
            border: 1px solid #dce3de;
        }

        .tags-input-wrapper input {
            border: none;
            background: transparent;
            outline: none;
            width: 200px;
            margin-left: 8px;
        }

        .tags-input-wrapper .tag {
            display: inline-block;
            background-color: #fa0e7e;
            color: white;
            border-radius: 40px;
            padding: 0px 3px 0px 7px;
            margin-right: 5px;
            margin-bottom: 5px;
            box-shadow: 0 5px 15px -2px rgba(250, 14, 126, .7)
        }

        .tags-input-wrapper .tag a {
            margin: 0 7px 3px;
            display: inline-block;
            cursor: pointer;
        }

        .red {
            color: red !important;
        }


        .card {
            margin-bottom: unset;
        }
    </style>
@endsection


@section('main_content')
    <!-- Warning Alert Modal -->
    <div id="warning-alert-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-body p-4">
                    <div class="text-center">
                        <i class="dripicons-warning h1 text-warning"></i>
                        <h4 class="mt-2">Confirm</h4>
                        <p class="mt-3">Are You Sure to Delete this Vendor Application</p>
                        <button type="button" class="btn btn-warning my-2" data-bs-dismiss="modal">Confirm</button>
                    </div>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.appereance.blogs') }}">Blogs</a></li>
            <li class="breadcrumb-item active" aria-current="page">Add Blogs</li>
        </ol>
    </nav>
    <h2>Add <span class="badge badge-success-lighten">Blogs</span></h2>
    <hr>
    <form id="add_blogs_form">
        @csrf
        <div class="row">
            <div class="col-sm-4">
                <label for="title">Title:<span class="red">*</span></label>
                <input type="text" id="title" name="title" class="form-control" placeholder="Title">
            </div>
            <div class="col-sm-4">
                <label for="banner_image">Banner Image:<span class="red">*</span> (JPG, JPEG, PNG, 2MB
                    max)</label>
                <input type="file" id="banner_image" name="banner_image" class="form-control" accept="image/*">
            </div>
            <div class="col-sm-4">
                <label for="status">Status:<span class="red">*</span></label>
                <select id="blogs_status" name="blogs_status" class="form-control">
                    <option value="">--Select Status--</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
        </div><br>

        <div class="row">
            <div class="col-sm-4">
                <label for="slug">Slug:<span class="red">*</span></label>
                <input type="text" id="slug" name="slug" readonly class="form-control">
            </div>
            <div class="col-sm-4">
                <label for="meta_title">Meta Title:</label>
                <input type="text" id="meta_title" name="meta_title" class="form-control" placeholder="Meta Title">
            </div>
            <div class="col-sm-4">
                <label for="meta_description">Meta Description:</label>
                <input type="text" id="meta_description" name="meta_description" class="form-control"
                    placeholder="Meta Description">
            </div>
        </div><br>

        <div>
            <label for="meta_description">Tags:</label><br>
            <input type="text" id="tag-input1" name="tag-input" class="form-control">
            <span id="tag-input1-error" class="error"></span>
        </div><br>
        <div>
            <label for="excerpt">Excerpt:<span class="red">*</span></label>
            <textarea id="excerpt" name="excerpt" class="form-control" placeholder="Enter your text here..."></textarea>
        </div><br>

        <div>
            <label for="content">Content:<span class="red">*</span></label>
            <textarea id="content" name="content" class="form-control summernote"></textarea>
            <span id="note-error" style="color: red"></span>

        </div>

        <div class="d-flex">
            <button type="submit" class="btn btn-success mx-auto"> Submit</button>
        </div>
    </form>
@endsection

@section('script')
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
    <script>
        (function() {
            "use strict"

            var TagsInput = function(opts) {
                this.options = Object.assign(TagsInput.defaults, opts);
                this.init();
            }

            TagsInput.prototype.init = function(opts) {
                this.options = opts ? Object.assign(this.options, opts) : this.options;

                if (this.initialized)
                    this.destroy();

                if (!(this.orignal_input = document.getElementById(this.options.selector))) {
                    console.error("tags-input couldn't find an element with the specified ID");
                    return this;
                }

                this.arr = [];
                this.wrapper = document.createElement('div');
                this.input = document.createElement('input');
                init(this);
                initEvents(this);

                this.initialized = true;
                return this;
            }

            TagsInput.prototype.addTag = function(string) {

                if (this.anyErrors(string))
                    return;

                this.arr.push(string);
                var tagInput = this;

                var tag = document.createElement('span');
                tag.className = this.options.tagClass;
                tag.innerText = string;

                var closeIcon = document.createElement('a');
                closeIcon.innerHTML = '&times;';

                closeIcon.addEventListener('click', function(e) {
                    e.preventDefault();
                    var tag = this.parentNode;

                    for (var i = 0; i < tagInput.wrapper.childNodes.length; i++) {
                        if (tagInput.wrapper.childNodes[i] == tag)
                            tagInput.deleteTag(tag, i);
                    }
                })


                tag.appendChild(closeIcon);
                this.wrapper.insertBefore(tag, this.input);
                this.orignal_input.value = this.arr.join(',');

                return this;
            }

            TagsInput.prototype.deleteTag = function(tag, i) {
                tag.remove();
                this.arr.splice(i, 1);
                this.orignal_input.value = this.arr.join(',');
                return this;
            }

            TagsInput.prototype.anyErrors = function(string) {
                if (this.options.max != null && this.arr.length >= this.options.max) {
                    console.log('max tags limit reached');
                    return true;
                }

                if (!this.options.duplicate && this.arr.indexOf(string) != -1) {
                    console.log('duplicate found " ' + string + ' " ')
                    return true;
                }

                return false;
            }

            TagsInput.prototype.addData = function(array) {
                var plugin = this;

                array.forEach(function(string) {
                    plugin.addTag(string);
                })
                return this;
            }

            TagsInput.prototype.getInputString = function() {
                return this.arr.join(',');
            }


            TagsInput.prototype.destroy = function() {
                this.orignal_input.removeAttribute('hidden');

                delete this.orignal_input;
                var self = this;

                Object.keys(this).forEach(function(key) {
                    if (self[key] instanceof HTMLElement)
                        self[key].remove();

                    if (key != 'options')
                        delete self[key];
                });

                this.initialized = false;
            }

            function init(tags) {
                tags.wrapper.append(tags.input);
                tags.wrapper.classList.add(tags.options.wrapperClass);
                tags.orignal_input.setAttribute('hidden', 'true');
                tags.orignal_input.parentNode.insertBefore(tags.wrapper, tags.orignal_input);
            }

            function initEvents(tags) {
                tags.wrapper.addEventListener('click', function() {
                    tags.input.focus();
                });


                tags.input.addEventListener('keydown', function(e) {
                    var str = tags.input.value.trim();

                    if (!!(~[9, 13, 188].indexOf(e.keyCode))) {
                        e.preventDefault();
                        tags.input.value = "";
                        if (str != "")
                            tags.addTag(str);
                    }

                });
            }


            TagsInput.defaults = {
                selector: '',
                wrapperClass: 'tags-input-wrapper',
                tagClass: 'tag',
                max: null,
                duplicate: false
            }

            window.TagsInput = TagsInput;

        })();

        var tagInput1 = new TagsInput({
            selector: 'tag-input1',
            duplicate: false,
            max: 20
        });
    </script>
    <script>
        $(document).ready(function() {
            $('#title').on('input', function() {
                var name = $(this).val();
                var slug = name.toLowerCase().replace(/\s+/g, '-');
                $('#slug').val(slug);
            });
        });
    </script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('.summernote').summernote({
                height: 200
            });
        });
    </script>
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script> --}}
    <script>
        $(document).ready(function() {
            $('#add_blogs_form').validate({
                rules: {
                    title: {
                        required: true,
                        maxlength:35,
                        minlength:2
                    },
                    banner_image: {
                        required: true,
                        imageFormat: true,
                        filesize: 2024
                    },
                    blogs_status: {
                        required: true,
                      
                    },
                    excerpt: {
                        required: true
                    },
                    meta_title :{
                        maxlength:26
                    },
                    meta_description : {
                        maxlength:36
                    }

                },
                messages: {
                    title: {
                        required: "Please enter the title.",
                        maxlength: "Title must not exceed 35 characters."
                    },
                    banner_image: {
                        required: "Please select a banner image.",
                        imageFormat: "Please upload file in these format only (jpg, jpeg, png).",
                        filesize: "Maximum file size is 2MB"
                    },
                    blogs_status: {
                        required: "Please select the status."
                    },
                    excerpt: {
                        required: "Please enter the excerpt."
                    },
                },
            });

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
            $(document).on('submit', '#add_blogs_form', function(e) {
                e.preventDefault();
                var form = $(this);
                var formData = new FormData(form[0]);
                if (validateForm_edit()) {
                    $.ajax({
                        url: "{{ route('admin.appereance.store_blogs') }}",
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
                            console.log(xhr.responseText);
                            $.NotificationApp.send("Error", xhr.responseText, "top-right",
                                "rgba(0,0,0,0.2)", "error");

                        }
                    });
                }
            });
        });


        function validateForm_edit() {

            var isValid = true;

$('.error-message').remove();
            var contentValue = $('.summernote').summernote('code');

            if ($.trim(contentValue) === '' || contentValue === '<p><br></p>') {
                var errorMessage = 'Please enter the content';
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

                return isValid;
            

        }
    </script>
@endsection
