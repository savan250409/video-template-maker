@extends('layout.master')

@push('plugin-styles')
  <!-- Plugin css import here -->
@endpush

@section('content')
  <!-- Page content here -->
  <nav class="page-breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="#">Banner</a></li>
      <li class="breadcrumb-item active" aria-current="page">Add</li>
    </ol>
  </nav>

  <div class="row">
    <div class="col-md-6 grid-margin stretch-card">
      <div class="card">
        <div class="card-body">
          <h6 class="card-title">Add Banner</h6>
          <form class="forms-sample" method="POST" action="{{ Route('banners.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="mb-3" id="banner-type-model">
              <label for="type" class="form-label">Banner Type</label>
              <select name="banner_type" id="type" class="form-select" required>
                <option value="" default>Select type</option>
                <option value="category">Category</option>
                <option value="app-url">App URL</option>
              </select>
              @error('banner_type')
                <span class="text-danger" role="alert"><strong>{{ $message }}</strong></span>
              @enderror
            </div>
            <div class="d-none" id="url-model">
              <div class="mb-3">
                <label for="url" class="form-label">URL</label>
                <input type="url" name="url" id="url" class="form-control" placeholder="Enter URL">
              </div>
            </div>
            <div class="d-none" id="category-model">
              <div class="mb-3">
                <label for="category" class="form-label">Category</label>
                <select name="banner_category" id="category" class="form-select">
                  <option value="" default>Select category</option>
                  @foreach ($categories as $category)
                      <option value="{{ $category->id }}">{{ $category->name }}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="mb-3">
              <label for="banner-name" class="form-label">Name</label>
              <input type="text" name="banner_name" class="form-control" id="banner-name" placeholder="Enter banner name">
              @error('banner_name')
                <span class="text-danger" role="alert"><strong>{{ $message }}</strong></span>
              @enderror
            </div>
            <div class="mb-3">
              <label for="banner" class="form-label">Banner</label>
              <input type="file" class="form-control" name="banner" accept="image/*" id="banner" onchange="document.getElementById('banner-output').src = window.URL.createObjectURL(this.files[0])">
              @error('banner')
                <span class="text-danger" role="alert"><strong>{{ $message }}</strong></span>
              @enderror
            </div>
            <div class="mb-3">
              <img src="https://placehold.co/900x450" id="banner-output" class="" style="height: 230px;width:100%;" alt="">
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
@endpush

@push('custom-scripts')
  <!-- Custom js here -->
  <script>
    $('#banner-type').change(function() {
        let bannerType = $(this).val();
        if(bannerType == 'setting') {
            $('#url-model').removeClass('d-none');
            
            $('#url').prop('required', true);
            $('#banner-type-model').addClass('d-none');
            $('#type').prop('required', false);
        } else {
            $('#url-model').addClass('d-none');
            $('#url').prop('required', false);
            $('#banner-type-model').removeClass('d-none');
            $('#type').prop('required', true);
        }
    });
    $('#type').change(function() {
      let type = $(this).val();
      let url = $('#url');
      let urlModel = $('#url-model');
      let category = $('#category');
      let categoryModel = $('#category-model');
      if (type == 'category') {
        categoryModel.removeClass('d-none');
        urlModel.addClass('d-none');
        category.prop('required', true);
        url.prop('required', false);
      }
      if (type == 'normal-url') {
        categoryModel.addClass('d-none');
        urlModel.removeClass('d-none');
        category.prop('required', false);
        url.prop('required', true);
      }
      if (type == 'special-url') {
        categoryModel.addClass('d-none');
        urlModel.removeClass('d-none');
        category.prop('required', false);
        url.prop('required', true);
      }
      if (type == 'app-url') {
        categoryModel.addClass('d-none');
        urlModel.removeClass('d-none');
        category.prop('required', false);
        url.prop('required', true);
      }
    });
  </script>
@endpush