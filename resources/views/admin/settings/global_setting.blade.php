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
            <li class="breadcrumb-item"><a aria-current="page">Global Settings</a></li>
        </ol>
    </nav>
    <div class="card p-4 mt-4">

        <form id="system_form" enctype="multipart/form-data">
            <div class="row">
                <div class="mb-3 col-lg-4">
                    <label for="simpleinput" class="form-label">Google Analytic Id<span class="text-danger">*</span></label>
                    <input type="text" class="form-control"    oninput="this.value = 
                    !!this.value && Math.abs(this.value) >= 0 ? Math.abs(this.value) : null"  name="google_analytic" value="{{$settings ? $settings->google_analytic : ''}}">

                </div>
                <div class="mb-3 col-lg-4">
                    <label for="simpleinput" class="form-label">Meta Titile<span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="meta_title" value="{{$settings ? $settings->meta_title : ''}}">
                </div>

                <div class="mb-3 col-lg-4">
                    <label for="simpleinput" class="form-label">Meta Description <span class="text-danger">*</span></label>
    
                    <textarea name="meta_description" id=""  class="form-control" >{{$settings ? $settings->meta_description : ''}}</textarea>
                </div>

            </div>


            <div class="row">
                <div class="mb-3 col-lg-4">
                    <label for="simpleinput" class="form-label">keywords</label>
                    <textarea name="keywords" id=""  class="form-control" >{{$settings ? $settings->keywords : ''}}</textarea>
                </div>

                <div class="mb-3 col-lg-4">
                    <label for="simpleinput" class="form-label">Og Tag</label>
                     <input type="text" name="ogtag" class="form-control" value="{{$settings ? $settings->ogtag : ''}}">
                </div>

                <div class="mb-3 col-lg-4">
                    <label for="simpleinput" class="form-label">Schema Markup</label>
                     <textarea name="schema_markup" id=""  class="form-control" >{{$settings ? $settings->schema_markup : ''}}</textarea>
                </div>
            </div>

            <div class="row">
                <div class="mb-3 col-lg-4">
                    <label for="simpleinput" class="form-label">Google Tag Manager</label>
                    <textarea name="google_tag_manager" id=""  class="form-control" >{{$settings ? $settings->google_tag_manager : ''}}</textarea>
                </div>

                <div class="mb-3 col-lg-4">
                    <label for="simpleinput" class="form-label">Search Console</label>
                    <input type="text" name="search_console" class="form-control" value="{{$settings ? $settings->search_console : ''}}">

                </div>

                <div class="mb-3 col-lg-4 form-group   ">
                    <label for="simpleinput" class="form-label">Facebook Pixel </label>
                    <input type="text" name="facebook_pixel" class="form-control" value="{{$settings ? $settings->facebook_pixel : ''}}">
                    

                </div>
             
            </div>

            <h4>Social Media</h4>

            <div class="row mt-4">
                <div class="mb-3 col-lg-6">
                    <label for="simpleinput" class="form-label"> Media Name </label>
                    <select name="media_name[]" id="" class="form-select">
                        <option value="" selected disabled>Select</option>
                        <option value="whatsapp" {{isset($social[0]) && $social[0]->name == 'whatsapp' ? 'selected' : '' }}>
                            Whatsapp
                        </option>
                        <option value="facebook" {{isset($social[0]) && $social[0]->name == 'facebook' ? 'selected' : '' }}>
                           Facebook
                        </option>
                        <option value="youtube" {{isset($social[0]) && $social[0]->name == 'youtube' ? 'selected' : '' }}>
                            Youtube
                         </option>
                         <option value="instagram" {{isset($social[0]) && $social[0]->name == 'instagram' ? 'selected' : '' }}>
                             Instagram
                         </option>
                         <option value="linkedin" {{isset($social[0]) && $social[0]->name == 'linkedin' ? 'selected' : '' }}>
                            Linkedin
                        </option>
                        <option value="twitter" {{isset($social[0]) && $social[0]->name == 'twitter' ? 'selected' : '' }}>
                            Twitter
                        </option>
                    </select>
                </div>
                <div class="mb-3 col-lg-6 form-group">
                    <label for="simpleinput" class="form-label"> Url </label>
                    <input type="text" class="form-control url-input" name="url[]"  value="{{isset($social[0]) ? $social[0]->url : '' }}">
                    <span class="invalid-feedback" role="alert" style="display: none;"></span>
                </div>
            </div>

            <div class="row">
                <div class="mb-3 col-lg-6">
                    <label for="simpleinput" class="form-label"> Media Name </label>
                    <select name="media_name[]" id="" class="form-select">
                        <option value="" selected disabled>Select</option>
                        <option value="whatsapp" {{isset($social[1]) && $social[1]->name == 'whatsapp' ? 'selected' : '' }}>
                            Whatsapp
                        </option>
                        <option value="facebook" {{isset($social[1]) && $social[1]->name == 'facebook' ? 'selected' : '' }}>
                           Facebook
                        </option>
                        <option value="youtube" {{isset($social[1]) && $social[1]->name == 'youtube' ? 'selected' : '' }}>
                            Youtube
                         </option>
                         <option value="instagram" {{isset($social[1]) && $social[1]->name == 'instagram' ? 'selected' : '' }}>
                             Instagram
                         </option>
                         <option value="linkedin" {{isset($social[1]) && $social[1]->name == 'linkedin' ? 'selected' : '' }}>
                            Linkedin
                        </option>
                        <option value="twitter" {{isset($social[1]) && $social[1]->name == 'twitter' ? 'selected' : '' }}>
                            Twitter
                        </option>
                    </select>
                </div>
                <div class="mb-3 col-lg-6 form-group">
                    <label for="simpleinput" class="form-label"> Url </label>
                    <input type="text" class="form-control url-input" name="url[]"  value="{{isset($social[1]) ? $social[1]->url : '' }}">
                    <span class="invalid-feedback" role="alert" style="display: none;"></span>
                </div>
            </div>

            <div class="row">
                <div class="mb-3 col-lg-6">
                    <label for="simpleinput" class="form-label"> Media Name </label>
                    <select name="media_name[]" id="" class="form-select">
                        <option value="" selected disabled>Select</option>
                        <option value="whatsapp" {{isset($social[2]) && $social[2]->name == 'whatsapp' ? 'selected' : '' }}>
                            Whatsapp
                        </option>
                        <option value="facebook" {{isset($social[2]) && $social[2]->name == 'facebook' ? 'selected' : '' }}>
                           Facebook
                        </option>
                        <option value="youtube" {{isset($social[2]) && $social[2]->name == 'youtube' ? 'selected' : '' }}>
                            Youtube
                         </option>
                         <option value="instagram" {{isset($social[2]) && $social[2]->name == 'instagram' ? 'selected' : '' }}>
                             Instagram
                         </option>
                         <option value="linkedin" {{isset($social[2]) && $social[2]->name == 'linkedin' ? 'selected' : '' }}>
                            Linkedin
                        </option>
                        <option value="twitter" {{isset($social[2]) && $social[2]->name == 'twitter' ? 'selected' : '' }}>
                            Twitter
                        </option>
                    </select>
                </div>
                <div class="mb-3 col-lg-6 form-group">
                    <label for="simpleinput" class="form-label"> Url </label>
                    <input type="text" class="form-control url-input" name="url[]"  value="{{isset($social[2]) ? $social[2]->url : '' }}">
                    <span class="invalid-feedback" role="alert" style="display: none;"></span>
                </div>
            </div>

            <div class="row">
                <div class="mb-3 col-lg-6">
                    <label for="simpleinput" class="form-label"> Media Name </label>
                    <select name="media_name[]" id="" class="form-select">
                        <option value="" selected disabled>Select</option>

                        <option value="whatsapp" {{isset($social[3]) && $social[3]->name == 'whatsapp' ? 'selected' : '' }}>
                            Whatsapp
                        </option>
                        <option value="facebook" {{isset($social[3]) && $social[3]->name == 'facebook' ? 'selected' : '' }}>
                           Facebook
                        </option>
                        <option value="youtube" {{isset($social[3]) && $social[3]->name == 'youtube' ? 'selected' : '' }}>
                            Youtube
                         </option>
                         <option value="instagram" {{isset($social[3]) && $social[3]->name == 'instagram' ? 'selected' : '' }}>
                             Instagram
                         </option>
                         <option value="linkedin" {{isset($social[3]) && $social[3]->name == 'linkedin' ? 'selected' : '' }}>
                            Linkedin
                        </option>
                        <option value="twitter" {{isset($social[3]) && $social[3]->name == 'twitter' ? 'selected' : '' }}>
                            Twitter
                        </option>
                    </select>
                </div>
                <div class="mb-3 col-lg-6 form-group">
                    <label for="simpleinput" class="form-label"> Url </label>
                    <input type="text" class="form-control url-input" name="url[]"  value="{{isset($social[3]) ? $social[3]->url : '' }}">
                    <span class="invalid-feedback" role="alert" style="display: none;"></span>
                </div>
            </div>

            <div class="row">
                <div class="mb-3 col-lg-6">
                    <label for="simpleinput" class="form-label"> Media Name </label>
                    <select name="media_name[]" id="" class="form-select">
                        <option value="" selected disabled>Select</option>

                        <option value="whatsapp" {{isset($social[4]) && $social[4]->name == 'whatsapp' ? 'selected' : '' }}>
                            Whatsapp
                        </option>
                        <option value="facebook" {{isset($social[4]) && $social[4]->name == 'facebook' ? 'selected' : '' }}>
                           Facebook
                        </option>
                        <option value="youtube" {{isset($social[4]) && $social[4]->name == 'youtube' ? 'selected' : '' }}>
                            Youtube
                         </option>
                         <option value="instagram" {{isset($social[4]) && $social[4]->name == 'instagram' ? 'selected' : '' }}>
                             Instagram
                         </option>
                         <option value="linkedin" {{isset($social[4]) && $social[4]->name == 'linkedin' ? 'selected' : '' }}>
                            Linkedin
                        </option>
                        <option value="twitter" {{isset($social[4]) && $social[4]->name == 'twitter' ? 'selected' : '' }}>
                            Twitter
                        </option>
                    </select>
                </div>
                <div class="mb-3 col-lg-6 form-group">
                    <label for="simpleinput" class="form-label"> Url </label>
                    <input type="text" class="form-control url-input" name="url[]"  value="{{isset($social[4]) ? $social[4]->url : '' }}">
                    <span class="invalid-feedback" role="alert" style="display: none;"></span>

                </div>
            </div>

            <div class="row">
                <div class="mb-3 col-lg-6">
                    <label for="simpleinput" class="form-label"> Media Name </label>
                    <select name="media_name[]" id="" class="form-select">
                        <option value="" selected disabled>Select</option>

                        <option value="whatsapp" {{isset($social[5]) && $social[5]->name == 'whatsapp' ? 'selected' : '' }}>
                            Whatsapp
                        </option>
                        <option value="facebook" {{isset($social[5]) && $social[5]->name == 'facebook' ? 'selected' : '' }}>
                           Facebook
                        </option>
                        <option value="youtube" {{isset($social[5]) && $social[5]->name == 'youtube' ? 'selected' : '' }}>
                            Youtube
                         </option>
                         <option value="instagram" {{isset($social[5]) && $social[5]->name == 'instagram' ? 'selected' : '' }}>
                             Instagram
                         </option>
                         <option value="linkedin" {{isset($social[5]) && $social[5]->name == 'linkedin' ? 'selected' : '' }}>
                            Linkedin
                        </option>
                        <option value="twitter" {{isset($social[5]) && $social[5]->name == 'twitter' ? 'selected' : '' }}>
                            Twitter
                        </option>
                    </select>
                </div>
                <div class="mb-3 col-lg-6 form-group">
                    <label for="simpleinput" class="form-label"> Url </label>
                    <input type="text" class="form-control url-input" name="url[]"  value="{{ isset($social[5])  ? $social[5]->url : '' }}">
                    <span class="invalid-feedback" role="alert" style="display: none;"></span>

                </div>
            </div>
            

            <div class="row">
                <div class="mb-3 col-lg-4">
                    <label for="simpleinput" class="form-label"> Logo:<span class="text-danger">*</span> </label> 
                    <img src="{{  $settings ? asset('public/admin/global/'. $settings->logo) : ''}}" height="30px" width="30px" alt="Logo">
                    <input type="file" name="logo" id="" class="form-control">
                   
                </div>

                <div class="mb-3 col-lg-4">
                    <label for="simpleinput" class="form-label"> Email:<span class="text-danger">*</span> </label>
                    <input type="email" name="email"  class="form-control" value="{{{$settings ? $settings->email : ''}}}" >
                </div>

                <div class="mb-3 col-lg-4">
                    <label for="simpleinput" class="form-label"> Phone:<span class="text-danger">*</span> </label>
                    <input type="text" name="phone"  class="form-control" value="{{$settings ? $settings->phone : ''}}">
                </div>
            </div>

            <div class="row">
                <div class="mb-3 col-lg-4">
                    <label for="simpleinput" class="form-label"> Address: </label>
                    <textarea name="address" id=""  class="form-control" >{{$settings ? $settings->address : ''}}</textarea>                   
                </div>

                <div class="mb-3 col-lg-4">
                    <label for="simpleinput" class="form-label"> Android Link: </label>
                    <input type="text" name="android_link"  class="form-control" value="{{$settings ? $settings->android_link : ''}}">
                </div>

                <div class="mb-3 col-lg-4">
                    <label for="simpleinput" class="form-label"> Android Url: </label>
                    <input type="text" name="android_url"  class="form-control" value="{{$settings ? $settings->android_url : ''}}">
                </div>

            </div>

            <div class="row">
                <div class="mb-3 col-lg-4">
                    <label for="simpleinput" class="form-label">Iphone Link: </label>
                    <input type="text" name="iphone_link"  class="form-control" value="{{$settings ? $settings->iphone_link : ''}}">
                </div>

                <div class="mb-3 col-lg-4">
                    <label for="simpleinput" class="form-label">Iphone Url: </label>
                    <input type="text" name="iphone_url"  class="form-control" value="{{$settings ? $settings->iphone_url : ''}}">
                </div>

                <div class="mb-3 col-lg-4">
                    <label for="simpleinput" class="form-label"> Copy Write Text: </label>
                    <textarea name="copywrite_text" id=""  class="form-control" >{{$settings ? $settings->copywrite_text : ''}}</textarea>   
                </div>
                
            </div>

            <div class="row">
                <div class="mb-3 col-lg-4">
                    <label for="simpleinput" class="form-label">QR code:<span class="text-danger">*</span> </label> 
                    <img src="{{  $settings ? asset('public/admin/global/'. $settings->qr_code) : ''}}" height="30px" width="30px" alt="Qr Code">
                    <input type="file" name="qr_code" id="" class="form-control">
                   
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
                google_analytic: {
                    required: true,
                    maxlength:11
                },
                meta_title: {
                    required: true,
                    maxlength:36
                },
                meta_description : {
                    required: true,
                    maxlength:56
                },
                email: {
                    required: true,
                    email: true,
                    emailWithDomain: true,
                    maxlength:40

                },
                phone: {
                    required: true,
                    number: true,
                    maxlength:12

                },
                logo : {
                    imageFormat: true,
                        filesize: 1024
                },
                android_link : {
                    linkvalid:true
                },
                android_link : {
                    linkvalid:true
                },
                android_url : {
                    urlvalid :true
                },
                iphone_link : {
                    linkvalid:true

                },
                iphone_url : {
                    urlvalid:true
                },
                'url[]': {
                     urlvalid: true
            }
           

            },
            messages: {
                logo: {
                        imageFormat: "Please upload file in these format only (jpg, jpeg, png).",
                        filesize: "Maximum file size is 2MB"
                    },

                    'url[]': {
                urlvalid: "Please enter a valid URL"
            },
            }
            ,
        errorElement: 'span',
        errorPlacement: function(error, element) {
            error.addClass('invalid-feedback');
            element.closest('.form-group').append(error);
        },
        highlight: function(element, errorClass, validClass) {
            $(element).addClass('is-invalid');
        },
        unhighlight: function(element, errorClass, validClass) {
            $(element).removeClass('is-invalid');
        }
            
        });


    //     $('.url-input').each(function() {
    //     $(this).rules('add', {
    //         required: true,
    //         url: true,
    //         messages: {
    //             required: "URL is required",
    //             url: "Please enter a valid URL"
    //         },
    //         errorElement: 'span',
    //     errorPlacement: function(error, element) {
    //         error.addClass('invalid-feedback');
    //         element.closest('.form-group').append(error);
    //     },
    //     highlight: function(element, errorClass, validClass) {
    //         $(element).addClass('is-invalid');
    //     },
    //     unhighlight: function(element, errorClass, validClass) {
    //         $(element).removeClass('is-invalid');
    //     }
    //     });
    // });


        $.validator.addMethod("linkvalid", function(value, element) {
                return this.optional(element) ||
                    /^(http|https|ftp):\/\/[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$/i
                    .test(value);
            }, "Please enter valid link.");

            $.validator.addMethod("urlvalid", function(value, element) {
                return this.optional(element) ||
                    /^(http|https|ftp):\/\/[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$/i
                    .test(value);
            }, "Please enter valid Url.");
        
        $.validator.addMethod("emailWithDomain", function(value, element) {
                return this.optional(element) || /^[\w-]+(\.[\w-]+)*@[\w-]+(\.[\w-]+)+$/.test(value);
            }, "Please enter a valid email address.");

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
            $(document).on('submit','#system_form', function(e){
                e.preventDefault();
                let fd = new FormData(this);
                fd.append('_token', "{{ csrf_token() }}");
                $.ajax({
                    url: "{{ route('admin.update_global_setting') }}",
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
    </script>
    <script>
        function updateCheckboxValue(checkbox) {
            var hiddenInput = document.querySelector('input[name="status"][type="hidden"]');
            hiddenInput.value = checkbox.checked ? 1 : 0;
        }
    </script>
@endsection
