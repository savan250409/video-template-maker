@extends('layout.master')

@push('plugin-styles')
  <!-- Plugin css import here -->
@endpush

@section('content')
@php
    $previousUrl = explode('/', url()->previous());
    $previousUrl = $previousUrl[4];
@endphp
  <!-- Page content here -->
  <nav class="page-breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="#">Notificaton</a></li>
    </ol>
  </nav>
  <div class="row">
    <div class="col-md-6 grid-margin stretch-card">
      <div class="card">
        <div class="card-body">
          <h6 class="card-title">Send Notification</h6>
            @if(session()->has('success'))
            <span class="text-success" role="alert"><strong>{{ session()->get('success') }}</strong></span>
            @endif
          <form class="forms-sample" method="POST" action="{{ Route('custom-notification.store') }}" enctype="multipart/form-data">
            @csrf
            @if($previousUrl == 'animated-template')
            <input type="hidden" name="template_id" value="{{ $templateId }}" />
            @endif
            <div class="mb-3">
                <label for="title">Title</label>
                <input type="text" name="notification_title" class="form-control" placeholder="Enter notification title">
                @error('notification_title')
                    <span class="text-danger" role="alert"><strong>{{ $message }}</strong></span>
                @enderror
            </div>
            @if($previousUrl != 'animated-template')
            <div class="mb-3">
              <label for="type" class="form-label">Select Type</label>
              <select name="type" id="type" class="form-select">
                <option value="Home" default>Home</option>
                <option value="category">Category</option>
                <option value="url">URL</option>
              </select>
            </div>
            <div id="category-model" class="d-none">
                <div class="mb-3">
                    <label for="category">Category</label>
                    <select name="notification_category" class="form-select" id="category">
                    <option value="" default>Select category</option>
                    @foreach ($categories as $key => $category)
                      <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                  </select>
                  @error('notification_category')
                        <span class="text-danger" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>
            </div>
            <div id="url-model" class="d-none">
                <div class="mb-3">
                    <label for="url" class="form-label">URL</label>
                    <input type="url" name="url" class="form-control" id="url" placeholder="Enter url">
                    @error('url')
                        <span class="text-danger" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>
            </div>
            @endif
            <div class="mb-3">
                <label for="message" class="form-label">Message</label>
                <textarea name="notification_message" id="message" cols="30" rows="10" class="form-control" placeholder="Enter notification message"></textarea>
                @error('notification_message')
                    <span class="text-danger" role="alert"><strong>{{ $message }}</strong></span>
                @enderror
            </div>
            <div class="mb-3">
                <label for="Image" class="form-label">Image</label>
                <div class="d-flex justify-content-between">
                  <div class="my-auto">
                      <input type="file" class="form-control" name="image" id="image" accept="image/*" onchange="document.getElementById('image-output').src = window.URL.createObjectURL(this.files[0])">
                  </div>
                  <img src="https://placehold.co/400" id="image-output" class="img-lg" alt="">
                  </div>
              </div>
            @if(Auth::user()->type == 'demo')
            <button type="button" class="btn btn-primary me-2" disabled>Submit</button>
            @else
            <button  type="submit" class="btn btn-primary me-2">Submit</button>
            @endif
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
    $('#type').change(function() {
        let type = $(this).val();
        let category = $('#category');
        let url = $('#url');
        let categoryModel = $('#category-model');
        let urlModel = $('#url-model');
        if (type == 'category') {
            categoryModel.removeClass('d-none');
            urlModel.addClass('d-none');
            category.prop('required', true);
            url.prop('required', false);
        } else if (type == 'url') {
            categoryModel.addClass('d-none');
            urlModel.removeClass('d-none'); 
            url.prop('required', true);
            category.prop('required', false);
        } else {
            categoryModel.addClass('d-none');
            urlModel.addClass('d-none');
            category.prop('required', false);
            url.prop('required', false);
        }
    });
  </script>
@endpush