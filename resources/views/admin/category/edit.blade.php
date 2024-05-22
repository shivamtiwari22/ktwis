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
            <li class="breadcrumb-item"><a href="{{ route('admin.categories.list') }}">Category</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit</li>
        </ol>
    </nav>
    <div class="card p-4">
        <div class="d-flex justify-content-end mb-2">
            <a href="{{ route('admin.categories.create') }}" class="btn btn-success mx-2">Add New Category</a>
            <a href="{{ route('admin.categories.list') }}" class="btn btn-primary">View All Category</a>
        </div>
        <form id="category_form">
            <input type="hidden" value="{{ $category->id }}" name="cat_id">
            {{-- <div class="mb-3">
            <label for="category_name" class="form-label">Category Name</label>
            <input type="text" id="category_name" name="category_name" class="form-control" value="{{$category->category_name}}">
        </div> --}}
            <div class="row">
                <div class="mb-3 col-lg-6">
                    <label for="category_name" class="form-label pe-auto">Category Name</label><span
                        class="menu-arrow"></span>
                    <input type="text" id="category_name" name="category_name" class="form-control"
                        value="{{ $category->category_name }}">
                </div>
                <div class="mb-3 col-lg-6">
                    <label for="category_img" class="form-label pe-auto">Category Image <span>(JPG, JPEG, PNG, 2MB
                        max)</span></label><span
                        class="menu-arrow"></span>
                    <span>
                        @if ($category->image)
                            <img src="{{ url('public/admin/category/images/' . $category->image) }}"
                                alt="{{ $category->category_name }}" width="40px">
                        @endif
                    </span>
                    <input type="file" id="category_img" name="category_img" class="form-control">
                </div>
            </div>

            <div class="row">
                <div class="mb-3 col-lg-6">
                    <label for="category_name" class="form-label pe-auto">Slug</label><span
                        class="menu-arrow red">*</span>
                 <input type="text" id="slug" name="slug" readonly  value="{{$category->slug}}" class="form-control">
                    
                </div>

                <div class="mb-3 col-lg-6">
                    <label for="" class="form-label pe-auto">Meta Title</label>
                 <input type="text" id="meta-title" name="meta_title"  class="form-control"  value="{{$category->meta_title}}">
                    
                </div>
            </div>


            
            <div class="row">
                <div class="mb-3 col-lg-6">
                    <label for="category_name" class="form-label pe-auto">Meta Description</label>
                 <textarea id="meta_description" name="meta_description"  class="form-control" >{{$category->meta_description}}</textarea>
                    
                </div>

                <div class="mb-3 col-lg-6">
                    <label for="" class="form-label pe-auto">Keywords</label>
              
                 <textarea id="keywords" name="keywords"  class="form-control" >{{$category->keywords}}</textarea>
                    
                </div>
            </div>


            <div class="row">
                <div class="mb-3 col-lg-6">
                    <label for="category_name" class="form-label pe-auto">Og Tag</label>
                 <input id="tags" name="ogtag"  class="form-control"  value="{{$category->ogtag}}" >
                    
                </div>

                <div class="mb-3 col-lg-6">
                    <label for="" class="form-label pe-auto">Schema Markup</label>
              
                 <textarea id="markup" name="schema_markup"  class="form-control" >{{$category->schema_markup}}</textarea>
                    
                </div>
            </div>

            {{-- <div class="mb-3" id="appendParentCategories">
        </div> --}}

            <label for="category_name" class="form-label">Select Parent Category (Optional )</label>
            <div class="input-group mb-3">
                <input type="text" class="form-control" placeholder="Seacrh Categories......" id="searchCategory">
                <button class="btn btn-light dropdown-toggle category_drop_down_toggle" type="button">All
                    Categories</button>
            </div>
            <div class="category_search_drop_down">
                <a class="dropdown-item pb-0">
                    <div class="d-flex">
                        @if (!is_null($parent_category))
                            <input type="checkbox" value="{{ $parent_category->id }}" name="parent_category_id"
                                class="single_checkbox" checked>
                            <div data-id="{{ $parent_category->id }}"
                                class="w-100 category_down category_down_search text-decoration-underline">
                                {{ $parent_category->category_name }}</div>
                        @endif
                    </div>
                </a>
            </div>
            <div class="category_drop_down">
                @foreach ($category_list as $category)
                    <a class="dropdown-item pb-0">
                        <div class="d-flex"><input type="checkbox" class="single_checkbox" value="{{ $category->id }}"
                                name="parent_category_id">
                            <div data-id="{{ $category->id }}" class="w-100 category_down text-decoration-underline">
                                {{ $category->category_name }}</div>
                        </div>
                    </a>
                @endforeach
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
            $('#category_name').on('input', function() {
                var name = $(this).val();
                var slug = name.toLowerCase().replace(/\s+/g, '-');
                $('#slug').val(slug);
            });

            // Hiding categgory dropdown on-load
            $('.category_drop_down').hide();

            // Toggle working of category dropdown
            $('.category_drop_down_toggle').click(function() {
                $('.category_drop_down').toggle();
                // empty the seacrh categories when accessesing category by toggle
                $(".category_search_drop_down").empty();
            });

            // removing child category if openend on click
            $(document).on('click', '.remove_category_down', function() {
                $(this).closest('.d-flex').siblings().remove();
                $(this).removeClass("remove_category_down").addClass("category_down");
            });

            // Only Check one Checkbox at a time 
            $(document).on('click', '.single_checkbox', function() {
                $('.single_checkbox').not(this).prop('checked', false);
            });
        });
    </script>


    {{-- Category By Search --}}
    <script>
        $('body').on('keyup', '#searchCategory', function(event) {
            event.preventDefault();
            $('.category_drop_down').hide();
            var search_by_category = $(this).val();
            console.log(search_by_category);
            $.fn.newmsg(search_by_category);
        });

        $(function() {
            $.fn.newmsg = function(search_by_category) {
                let fd = new FormData();
                fd.append('_token', "{{ csrf_token() }}");
                fd.append('search_by_category', search_by_category);
                $.ajax({
                    url: "{{ route('admin.categories.searchCategory') }}",
                    type: "POST",
                    data: fd,
                    dataType: 'json',
                    processData: false,
                    contentType: false,
                    success: function(result) {
                        if (result.data) {
                            console.log(result.data.length);
                            if (result.data.length == 0) {
                                $(".category_search_drop_down").empty();
                            } else {
                                var html = "";
                                $.each(result.data, function(i, category) {
                                    let category_name = category.category_name;
                                    let category_id = category.id;

                                    html += `
                        <a class="dropdown-item pb-0">
                            <div class="d-flex">
                            <input type="checkbox" value="${category_id}" name="parent_category_id" class="single_checkbox">
                            <div data-id="${category_id}" class="w-100 category_down category_down_search text-decoration-underline">${category_name}</div>
                            </div>
                        </a>
                        `;
                                });

                                $(".category_search_drop_down").html(html);
                            }
                        }
                    }
                });
            }

        });
    </script>


    {{-- Dropdown Category list --}}
    <script>
        $(document).on('click', '.category_down', function() {
            var clickedElement = $(this);

            var selectedValue = clickedElement.data('id');
            let fd = new FormData();
            fd.append('_token', "{{ csrf_token() }}");
            fd.append('selectedValue', selectedValue);
            $.ajax({
                url: "{{ route('admin.categories.get_child_category') }}",
                type: "POST",
                data: fd,
                dataType: 'json',
                processData: false,
                contentType: false,
                success: function(result) {
                    var html = "";
                    $.each(result.data, function(i, category) {
                        let category_name = category.category_name;
                        let category_id = category.id;

                        html += `
                <a class="dropdown-item pb-0">
                    <div class="d-flex">
                    <input type="checkbox" value="${category_id}" name="parent_category_id" class="single_checkbox">
                    <div data-id="${category_id}" class="w-100 category_down text-decoration-underline">${category_name}</div>
                    </div>
                </a>
                `;
                    });

                    // Append the generated HTML to the parent of the selected div
                    clickedElement.parent().after(html);
                    clickedElement.removeClass("category_down").addClass("remove_category_down");
                }
            });
        });
    </script>


    {{-- displaying Parent category list --}}
    {{-- <script>
        $(document).on('click', '.category_down', function() {
        var selectedValue = $(this).data('id');
        let fd = new FormData();
            fd.append('_token', "{{ csrf_token() }}");
            fd.append('selectedValue',selectedValue);
        $.ajax({
                url: "{{ route('admin.categories.get_parent_category') }}",
                type:"POST",
                data: fd,
                dataType: 'json',
                processData: false,
                contentType: false,
                success:function(result){
                    if(result.data.length==0){
                    $("#appendParentCategories").empty();
                    }else if (result.data.length==1){
                        let html = `
                            <div>
                                <h4>Main Category<h4>
                    `
                    $("#appendParentCategories").html(html);
                    }
                    else{
                    var array =result.data; 
                    var separator = " -> ";
                    var separatedString = array.join(separator);
                    let html = `
                            <div>
                                <h4>Parent Categories<h4>
                                <h6>${separatedString}<h6>
                    `
                    $("#appendParentCategories").html(html);
                }
            }
            });
    });
</script> --}}


    {{-- Saving New Category --}}
    <script>
        $(function() {
            $('#category_form').on('submit', function(e) {
                e.preventDefault();
                let fd = new FormData(this);
                fd.append('_token', "{{ csrf_token() }}");
                if (validateForm()) {
                    $.ajax({
                        url: "{{ route('admin.categories.update') }}",
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
                                    window.location.href = result.location;
                                }, 1000);
                            } else {
                                $.NotificationApp.send("Error", result.message, "top-right",
                                    "rgba(0,0,0,0.2)", "error")
                            }
                        },
                    });
                }
            })


            function validateForm() {
                var isValid = true;

                $('.error-message').remove();

                $('#category_name').each(function() {
                     var  input = $(this).attr('id');
                    var value = $.trim($(this).val());
                    if ($.trim($(this).val()) === '') {
                        var errorMessage = 'This field is required';
                        $(this).addClass('is-invalid');
                        $(this).after('<span class="error-message" style="color:red;">' + errorMessage +
                            '</span>');
                        isValid = false;
                    } else if (input === "category_name") {
                        if (value.length < 4 || value.length > 26) {
                            var errorMessage = 'Tax name must be between 4 and 26 characters.';
                            $(this).addClass('is-invalid');
                            $(this).after('<span class="error-message" style="color:red;">' + errorMessage +
                                '</span>');
                            isValid = false;
                        }
                    } else {
                        $(this).removeClass('is-invalid');
                    }
                });


                
                $('#category_img').each(function() {
                    var $input = $(this);
                    var file = $input[0].files[0]; // Get the selected file
                    if (file) {
                        var allowedExtensions = ['png', 'jpg', 'jpeg'];
                        var maxFileSize = 2 * 1024 * 1024; // 2MB in bytes

                        var fileExtension = file.name.split('.').pop().toLowerCase();
                        if (allowedExtensions.indexOf(fileExtension) === -1) {
                            var errorMessage = 'Only PNG, JPEG, and JPG files are allowed';
                            $input.addClass('is-invalid');
                            $input.after(' <span class="error-message" style="color:red;">' + errorMessage +
                                '</span>');
                            isValid = false;
                        } else if (file.size > maxFileSize) {
                            var errorMessage = 'File size cannot exceed 2MB';
                            $input.addClass('is-invalid');
                            $input.after('<span class="error-message" style="color:red;">' + errorMessage +
                                '</span>');
                            isValid = false;
                        }
                    }
                })


                $('#category_name').on('input change', function() {
                    $(this).removeClass('is-invalid');
                    $(this).next('.error-message').remove();
                });

                return isValid;
            }
        });
    </script>
@endsection
