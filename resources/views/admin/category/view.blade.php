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
            <li class="breadcrumb-item active" aria-current="page">View</li>
        </ol>
    </nav>
    <div class="card p-4">
        <div class="d-flex justify-content-end mb-2">
            <a href="{{ route('admin.categories.create') }}" class="btn btn-success mx-2">Add New Category</a>
            <a href="{{ route('admin.categories.list') }}" class="btn btn-primary">View All Category</a>
        </div>
        <form id="category_form">
            <div class="row">
                <div class="mb-3 col-lg-6">
                    <label for="category_name" class="form-label pe-auto">Category Name</label><span
                        class="menu-arrow"></span>
                    <input type="text" id="category_name" name="category_name" class="form-control"
                        value="{{ $category->category_name }}" disabled>
                </div>
                <div class="mb-3 col-lg-6">
                    <label for="category_img" class="form-label pe-auto">Category Image</label><span
                        class="menu-arrow"></span>
                    <div>
                        @if ($category->image)
                            <img src="{{ url('public/admin/category/images/' . $category->image) }}"
                                alt="{{ $category->category_name }}" height="40px">
                        @endif
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="mb-3 col-lg-6">
                    <label for="category_name" class="form-label pe-auto">Slug:</label>
                  <p>{{$category->slug}}</p>
                    
                </div>

                <div class="mb-3 col-lg-6">
                    <label for="" class="form-label pe-auto">Meta Title:</label>
                    <p>{{$category->meta_title}}</p>

                </div>
            </div>


            <div class="row">
                <div class="mb-3 col-lg-6">
                    <label for="category_name" class="form-label pe-auto">Meta Description:</label>
                  <p>{{$category->meta_description}}</p>
                    
                </div>

                <div class="mb-3 col-lg-6">
                    <label for="" class="form-label pe-auto">Keywords :</label>
              
                  <p>{{$category->keywords}}</p>
                    
                </div>
            </div>


            <div class="row">
                <div class="mb-3 col-lg-6">
                    <label for="category_name" class="form-label pe-auto">Og Tag:</label>
                <p>{{$category->ogtag}}</p>    
                </div>

                <div class="mb-3 col-lg-6">
                    <label for="" class="form-label pe-auto">Schema Markup:</label>
              
                 <p>{{$category->schema_markup}}</p>
                    
                </div>
            </div>
    </div>




    <div class="mb-3" id="appendParentCategories">
    </div>
    </form>

    </div>
@endsection

@section('script')
    <script>
        var categoryNames = {!! json_encode($categoryNames) !!};
        var separator = " -> ";
        var separatedString = categoryNames.join(separator);
        var html = `
        <div>
            <h4>Parent Categories</h4>
            <h6>${separatedString}</h6>
        </div>
    `;
        $("#appendParentCategories").html(html);
    </script>
@endsection
