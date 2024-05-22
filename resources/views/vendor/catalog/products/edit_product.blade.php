@extends('vendor.layout.app')

@section('meta_tags')
@endsection


@section('title')
@endsection


@section('css')
    <style>
        .custom-checkbox {
            width: 20px;
            height: 20px;
        }

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
            width: 140px;
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

        .loader {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 9999;
        }

        .loader-wheel {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .loader-wheel .spinner {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border-top: 4px solid #f3f3f3;
            border-right: 4px solid #f3f3f3;
            border-bottom: 4px solid #f3f3f3;
            border-left: 4px solid #337ab7;
            animation: spin 1s infinite linear;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>
@endsection


@section('main_content')
    <div class="loader" id="loader">
        <div class="loader-wheel">
            <div class="spinner"></div>
        </div>
    </div>

    <!-- Warning Alert Modal -->

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('vendor.products.index') }}">Products</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit Products</li>
        </ol>
    </nav>
    <div class="card p-4 mt-4 form-control">
        <h2>Edit <span class="badge badge-success-lighten">Product</span></h2>
        <hr>
        <form id="productForm" enctype="multipart/form-data">
            @csrf

            <div class="row">
                <div class="col-sm-4">
                    <label for="name">Name:<span class="red">*</span></label>
                    <input type="hidden" name="id" id="id" value="{{ $product->id }}" class="form-control">
                    <input type="text" name="name" id="name" value="{{ $product->name }}" class="form-control">
                </div>
                <div class="col-sm-4">
                    <label for="featured_image">Featured Image (JPG, JPEG, PNG, 2MB max):</label>
                    <div class="row">
                        <div class="col-sm-9">
                            <input type="file" accept="image/*" name="featured_image" id="featured_image"
                                class="form-control">
                        </div>
                        <br>
                        <div class="col-sm-3">
                            <a href="{{ asset('public/vendor/featured_image/' . $product->featured_image) }}"
                                target="_blank"> <img
                                    src="{{ asset('public/vendor/featured_image/' . $product->featured_image) }}"
                                    style="width: 40px; height: 40px; border: 1px solid black;" alt="Image"></a>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <label for="status">Status<span class="red">*</span></label>
                    <select name="status" class="form-control" id="status_product">
                        <option value="">select status</option>
                        <option value="inactive" {{ $product->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="active" {{ $product->status === 'active' ? 'selected' : '' }}>Active</option>
                    </select>
                </div>
            </div><br>

            <div class="row">
                <div class="col-sm-4">
                    <label for="gallery_images">Gallery Images (Upload upto 10 images only):</label>
                    <input type="file" name="gallery_images[]" multiple accept="image/*" id="gallery_image"
                        class="form-control">
                </div>
                <div class="col-sm-4">
                    <label for="categories">Categories:<span class="red">*</span></label>
                    <select name="categories[]" id="categories" class="select2 form-control select2-multiple"
                        data-toggle="select2" multiple="multiple" data-placeholder="Choose ...">
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @if ($product->categories->contains($category->id)) selected @endif>
                                {{ ucwords($category->category_name) }}</option>
                        @endforeach
                    </select>
                    <label for="categories" id="categories-error" class="error"></label>
                </div>
                <div class="col-sm-2">
                    <label for="requires_shipping">Requires Shipping : </label><br>
                    <input type="checkbox" name="requires_shipping" class="form-check-input product_color custom-checkbox"
                        id="requires_shipping" <?php if ($product->requires_shipping == 1) {
                            echo 'checked';
                        } ?>>
                </div>

                <div class="col-sm-2">
                    <label for="requires_shipping">has Varaint : </label><br>
                    <input type="checkbox" name="" class="form-check-input product_color custom-checkbox"
                        id=""  disabled <?php if ($product->has_variant == 1) {
                            echo 'checked';
                        } ?>>
                </div>
            </div><br>

            <div class="row">
                <div class="col-sm-4">
                    <label for="slug">Slug:</label>
                    <input type="text" name="slug" value="{{ $product->slug }}" id="slug" class="form-control"
                        readonly>
                </div>
                <div class="col-sm-4">
                    <label for="brand">Brand:<span class="red">*</span></label>
                    <input type="text" name="brand" value="{{ $product->brand }}" id="brand" class="form-control">
                </div>
                <div class="col-sm-4">
                    <label for="model_number">Model Number:</label>
                    <input type="text" name="model_number" id="model_number" value="{{ $product->model_number }}"
                        class="form-control">
                </div>
            </div><br>

            <div>
                <label for="meta_description">Tags:</label><br>
                <input type="text" id="tag-input1" name="tag-input" class="form-control existing-tags-input"
                    value="{{ $product->tags }}">
            </div><br>

            <div>
                <?php
                $dimensions = explode(',', $product->dimensions);
                $length = $dimensions[0];
                $width = $dimensions[1];
                $height = $dimensions[2];
                ?>
                <label for="length">Dimensions (cm):</label>
                <div class="row">
                    <div class="col-sm-4"><input type="number" min="1" name="length" id="length"
                            value="{{ $length }}" class="form-control" placeholder="Lenght in cm"></div>
                    <div class="col-sm-4"><input type="number" min="1" name="width" id="width"
                            value="{{ $width }}" class="form-control" placeholder="Width in cm"></div>
                    <div class="col-sm-4"><input type="number" min="1" name="height" id="height"
                            value="{{ $height }}" class="form-control" placeholder="Height in cm"></div>
                </div>
            </div><br>


            <div class="row">
                <div class="col-sm-4">
                    <label for="min_order_qty">Minimum Order Quantity:<span class="red">*</span></label>
                    <input type="number" name="min_order_qty" id="min_order_qty" min="1"
                        value="{{ $product->min_order_qty }}" class="form-control">
                </div>
                <div class="col-sm-4">
                    <label for="weight">Weight (g):<span class="red">*</span></label>
                    <input type="number" name="weight" id="weight" min="1" value="{{ $product->weight }}"
                        class="form-control">
                </div>
                <div class="col-sm-4">
                    <label for="meta_title">Meta Title:</label>
                    <input type="text" name="meta_title" id="meta_title" value="{{ $product->meta_title }}"
                        class="form-control">
                </div>
            </div><br>


            <div class="row">
                <div class="col-sm-6">
                    <label for="meta_description">Meta Description:</label>
                    <input type="text" name="meta_description" id="meta_description"
                        value="{{ $product->meta_description }}" class="form-control">
                </div>
                <div class="col-sm-6">
                    <label for="linked_items">Linked Items:</label>
                    <input name="linked_items" id="linked_items" value="{{ $product->linked_items }}"
                        class="form-control">
                </div>
            </div><br>

            <div class="row">
                <div class="col-sm-6">
                    <label for="key_features">Key Features::<span class="red">*</span></label>
                    <textarea name="key_features" id="key_features" value="" class="form-control">{{ $product->key_features }}</textarea>
                </div>
                <div class="col-sm-6">
                    <label for="description">Description:<span class="red">*</span></label>
                    <textarea name="description" id="description" class="form-control">{{ $product->description }}</textarea>
                </div>
            </div><br>

            <div class="row">
                <div class="col-sm-6">
                    <label for="key_features">Og Tag :</label>
                    <textarea name="ogtag" id="" class="form-control">{{ $product->ogtag }}</textarea>
                </div>
                <div class="col-sm-6">
                    <label for="description">Schema Markup :</label>
                    <textarea name="schema_markup" id="" class="form-control">{{ $product->schema_markup }}</textarea>
                </div>
            </div><br>
            <div class="d-flex">
                <button type="submit" class="btn btn-primary mx-auto">Update</button>
            </div>
        </form>
    </div>
    {{-- Toster js --}}
    <script src="{{ asset('public/assets/js/pages/demo.toastr.js') }}"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

@section('script')




    <script>
        $(document).ready(function() {
            function generateSlug(value) {
                return value.toLowerCase().replace(/ /g, '-').replace(/[^\w-]+/g, '');
            }

            $('#name').on('input', function() {
                var name = $(this).val();
                var slug = generateSlug(name);
                $('#slug').val(slug);
            });
        });


        $(document).ready(function() {

            $('#productForm').validate({
                rules: {
                    name: {
                        required: true,
                        maxlength: 46,
                    },

                    status: {
                        required: true,

                    },
                    'categories[]': 'required',
                    brand: {
                        required: true,
                        maxlength: 26,
                    },
                    model_number: {
                        maxlength: 26,
                    },
                    min_order_qty: {
                        required: true,
                        maxlength: 46,
                    },

                    weight: {
                        required: true,
                        maxlength: 46,
                    },
                    description: {
                        required: true,
                        maxlength: 256,

                    },
                    key_features: {
                        required: true,
                        maxlength: 256,

                    },

                    featured_image: {
                        imageFormat: true,
                        filesize: 2024,
                    },
                    'gallery_images[]': {
                        imageFormat: true,
                        totalFileSize: 20024,
                    },
                },

                messages: {
                    featured_image: {
                        imageFormat: "Please upload file in these format only (jpg, jpeg, png).",
                        filesize: "Maximum file size is 2MB"
                    },
                    'gallery_images[]': {
                        imageFormat: "Please upload file in these format only (jpg, jpeg, png).",
                        totalFileSize: "Maximum file size is 20MB"
                    }

                },
            });

            $.validator.messages.extension = "This field is required.";

            $.validator.addMethod("filesize", function(value, element, param) {
                var maxSize = param * 1024; // Convert param to bytes
                return this.optional(element) || (element.files[0].size <= maxSize);
            }, "File size must be less than {0} KB");

            $.validator.addMethod("imageFormat", function(value, element) {
                var allowedFormats = ["jpg", "jpeg", "png"];
                var extension = value.split('.').pop().toLowerCase();
                return this.optional(element) || $.inArray(extension, allowedFormats) !== -1;
            }, "Invalid image format. Allowed formats: jpg, jpeg, png");


            $.validator.addMethod("totalFileSize", function(value, element, param) {
                var maxSize = param * 1024 * 1024; // Convert param to bytes (20 MB in this case)
                var totalSize = 0;

                // Loop through all selected files in the file input
                for (var i = 0; i < element.files.length; i++) {
                    totalSize += element.files[i].size;
                }

                return this.optional(element) || (totalSize <= maxSize);
            }, "Total file size must be less than {0} MB");
        })
    </script>


<script>
    $(document).ready(function() {
        $('#gallery_image').on('change', function() {
            var files = $(this).prop('files');
            var maxImages = 10;

            if (files.length > maxImages) {
                alert('You can only upload a maximum of ' + maxImages + ' images.');
                $(this).val('');
                return false;
            }

            $('#image-preview').empty();

            for (var i = 0; i < files.length; i++) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    var img = $('<img>').attr('src', e.target.result);
                    $('#image-preview').append(img);
                };
                reader.readAsDataURL(files[i]);
            }
        });
    });
</script>
@endsection
<script>
    $(document).on('submit', '#productForm', function(e) {
        event.preventDefault();
        $('#saveButton').attr('disabled', 'disabled');

        $('#loader').show();
        var formData = new FormData(this);

        $.ajax({
            url: "{{ route('vendor.products.update_products') }}",
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                console.log(response.message);
                $('#loader').hide();
                // location.reload();
                // $.NotificationApp.send("Success",response.message,"top-right","rgba(0,0,0,0.2)","success");
                toastr.clear();
                toastr.success(response.message, 'Success', {
                    class: 'toast-success',
                    timeOut: 2000,
                    closeButton: true
                });
                setTimeout(function() {
                    window.location.href =
                        "{{ route('vendor.products.index') }}";
                }, 2000);
            },
            error: function(xhr, status, error) {
                $('#loader').hide();
                console.log(xhr.responseText);
                var errors = JSON.parse(xhr.responseText).message;
                // $.each(errors.errors, function(key, value) {
                //     $('#' + key).after('<div class="error-message">' +
                //         value + '</div>');
                // });
                // toastr.clear();
                // toastr.error(error, 'Error', {
                //     timeOut: 2000,
                //     closeButton: true
                // });
                $.NotificationApp.send("Error", errors, "top-right", "rgba(0,0,0,0.2)", "error");

            },
            complete: function() {
                $('#saveButton').removeAttr('disabled');
            }
        });

    });


    function validateForm() {
        var isValid = true;

        $('.error-message').remove();

        $('#name,#status_product ,#description , #categories, #brand, #model_number ,#weight , #min_order_qty , #length ,#width, #height')
            .each(function() {
                if ($.trim($(this).val()) === '') {
                    var errorMessage = 'This field is required';
                    $(this).addClass('is-invalid');
                    $(this).after('<span class="error-message" style="color:red;">' + errorMessage +
                        '</span>');
                    isValid = false;
                } else {
                    $(this).removeClass('is-invalid');
                }
            });

        $('#name ,#status_product, #description, #categories, #brand, #model_number , #weight , #min_order_qty , #length ,#width, #height ')
            .on('input change', function() {
                $(this).removeClass('is-invalid');
                $(this).next('.error-message').remove();
            });

        return isValid;
    }
</script>
@endsection


@section('script')
<script>
    $(document).ready(function() {
        var existingTags = "{{ $product->tags }}".split(',');
        var tagInput1 = new TagsInput({
            selector: 'tag-input1',
            duplicate: false,
            max: 20
        });

        tagInput1.init();
        tagInput1.addData(existingTags);
    });


   
</script>

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
</script>
<link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/css/bootstrap-select.css" />
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/js/bootstrap-select.min.js"></script>
@endsection
