@extends('layout.master')

@push('plugin-styles')
  <!-- Plugin css import here -->
@endpush

@section('content')
  <!-- Page content here -->
  <nav class="page-breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="#">Video Template</a></li>
      <li class="breadcrumb-item active" aria-current="page">Category</li>
    </ol>
  </nav>

    @php
        $currentUrl = explode('/', Url()->current());
    @endphp
  <div class="row">
    <div class="col-md-6 grid-margin stretch-card">
      <div class="card">
        <div class="card-body">
          <h6 class="card-title">Edit Category</h6>
          <form class="forms-sample" method="POST" action="{{ Route('template-category.update', [$category->id]) }}" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            <input type="hidden" name="category_type" value="{{ $currentUrl[4] }}">
            <div class="mb-3">
              <label for="category-name" class="form-label">Name</label>
              <input type="text" name="category_name" value="{{ $category->name }}" class="form-control" id="category-name" placeholder="Enter category name" required>
              @error('category_name')
                  <span class="text-danger" role="alert"><strong>{{ $message }}</strong></span>
              @enderror
            </div>
            <div class="mb-3">
              <label for="category-type" class="form-label">Type</label>
              <select class="form-select" name="category_type" id="category-type">
                  <option value="" default>Select type</option>
                  <option value="random" {{ $category->template_type == 'random' ? 'selected' : '' }}>Random</option>
                  <option value="latest" {{ $category->template_type == 'latest' ? 'selected' : '' }}>Latest</option>
                  <option value="trending" {{ $category->template_type == 'trending' ? 'selected' : '' }}>Trending</option>
              </select>
              @error('category_type')
                  <span class="text-danger" role="alert"><strong>{{ $message }}</strong></span>
              @enderror
            </div>
            <div class="mb-3">
              <label for="category-logo" class="form-label">Logo</label>
              <div class="d-flex justify-content-between">
                <div class="my-auto">
                    <input type="file" class="form-control" accept="image/*" name="category_logo" id="category-logo" onchange="document.getElementById('logo-output').src = window.URL.createObjectURL(this.files[0])">
                </div>
                <img src="{{ upload_url('category/template/thumbnail/'.$category->logo) }}" id="logo-output" class="img-lg" alt="">
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
@endpush

@push('custom-scripts')
  <!-- Custom js here -->
@endpush