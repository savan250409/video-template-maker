@extends('layout.master')

@push('plugin-styles')
  <!-- Plugin css import here -->
  <link href="{{ asset('assets/plugins/select2/select2.min.css') }}" rel="stylesheet" />
  <link href="{{ asset('assets/plugins/jquery-tags-input/jquery.tagsinput.min.css') }}" rel="stylesheet" />
  <link href="{{ asset('assets/plugins/dropify/css/dropify.min.css') }}" rel="stylesheet" />


@endpush

@section('content')
  <!-- Page content here -->
  <nav class="page-breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="#">Video Template</a></li>
      <li class="breadcrumb-item active" aria-current="page">Template</li>
    </ol>
  </nav>

  <div class="row">
    <div class="col-md-12 grid-margin stretch-card">
      <div class="card">
        <div class="card-body">
          <h6 class="card-title">Add Template</h6>
          <form class="forms-sample" method="POST" action="{{ Route('animated-template.update', [$template->id]) }}" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            <div class="row">
                <div class="col-md-6">
                  <div class="mb-3">
                    <label for="template-name" class="form-label">Title</label>
                    <input type="text" name="template_title" value="{{ $template->title }}" class="form-control" id="template-name" placeholder="Enter title">
                    @error('template_title')
                      <span class="text-danger" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                  </div>
                  <div class="mb-3">
                      <label for="category-name" class="form-label">Category</label>
                      <select name="template_category" class="form-select" required>
                        <option value="" default>Select Category</option>
                        @foreach ($categories as $key => $category)
                            <option value="{{ $category->id }}" {{ $template->category_id == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                      @error('template_category')
                        <span class="text-danger" role="alert"><strong>{{ $message }}</strong></span>
                      @enderror
                    </div>
                    <div class="mb-3 d-flex gap-5">
                      <div>
                        <label for="category-name" class="form-label">Premium</label>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="template_premium" {{ $template->is_paid == 1 ? 'checked' : '' }}>
                          </div>
                      </div>
                      <div>
                        <label for="category-name" class="form-label">Active</label>
                        <div class="form-check form-switch">
                            <input class="form-check-input" onchange="templateActiveStatus(this);" id="template-active" type="checkbox" name="template_active" {{ $template->is_active == 1 ? 'checked' : '' }}>
                          </div>
                      </div>
                    </div>
                    <div class="mb-3">
                        <label for="type" class="form-label">Type</label>
                        <select name="type" class="form-select" id="type" disabled>
                            <option value="video" {{ $template->type == 'video' ? 'selected' : null }}>Video</option>
                            <option value="post" {{ $template->type == 'post' ? 'selected' : null }}>Post</option>
                        </select>
                    </div>
                  
                  
                </div>
                <div class="col-md-6">
                  <div class="mb-3">
                      <label for="category-logo" class="form-label">Thumbnail</label>
                      <div class="d-flex justify-content-between">
                        <div class="my-auto">
                            <input type="file" accept="image/*" class="form-control" name="template_thumbnail" id="category-logo" onchange="document.getElementById('logo-output').src = window.URL.createObjectURL(this.files[0])">
                        </div>
                        <img src="{{ !is_null($template->thumbnail) ? upload_url('template/thumbnail/'.$template->thumbnail) : 'https://placehold.co/400' }}" id="logo-output" style="width:100px;height:150px;" alt="">
                        </div>
                    </div>
                  <div class="mb-3">
                    <label for="category-name" class="form-label">Template.zip</label>
                    <input type="file" accept=".zip" name="template_zip" id="myDropify">
                  </div>

                </div>
            </div>
            <button  type="submit" class="btn btn-primary me-2" {{ Auth::user()->type == 'demo' ? 'disabled="disabled"' : '' }}>Submit</button>
          </form>
        </div>
      </div>
    </div>
    
  </div>
@endsection

@push('plugin-scripts')
  <!-- Plugin js import here -->
  <script src="{{ asset('assets/plugins/select2/select2.min.js') }}"></script>
  <script src="{{ asset('assets/plugins/jquery-tags-input/jquery.tagsinput.min.js') }}"></script>
  <script src="{{ asset('assets/plugins/dropify/js/dropify.min.js') }}"></script>

@endpush

@push('custom-scripts')
  <!-- Custom js here -->
  <script src="{{ asset('assets/js/select2.js') }}"></script>
  <script src="{{ asset('assets/js/tags-input.js') }}"></script>
  <script src="{{ asset('assets/js/dropify.js') }}"></script>
  
@endpush