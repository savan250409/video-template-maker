@extends('layout.master')

@push('plugin-styles')
  <!-- Plugin css import here -->
  <link href="{{ asset('assets/plugins/select2/select2.min.css') }}" rel="stylesheet" />
@endpush

@section('content')
@php
    $previousUrl = explode('/', url()->previous());
    $previousUrl = $previousUrl[4];
@endphp
  <!-- Page content here -->
  <nav class="page-breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="#">Schedule Notificaton</a></li>
       <li class="breadcrumb-item active" aria-current="page">Edit</li> </ol>
  </nav>
  <div class="row">
    <div class="col-md-6 grid-margin stretch-card">
      <div class="card">
        <div class="card-body">
          <h6 class="card-title">Edit Notification</h6>
            @if(session()->has('success'))
            <span class="text-success" role="alert"><strong>{{ session()->get('success') }}</strong></span>
            @endif
          <form class="forms-sample" method="POST" action="{{ Route('schedule-notifications.update', [$notification->id]) }}" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            <div class="mb-3">
                <label for="title">Title</label>
                <input type="text" name="notification_title" value="{{ $notification->title }}" class="form-control" placeholder="Enter notification title">
                @error('notification_title')
                    <span class="text-danger" role="alert"><strong>{{ $message }}</strong></span>
                @enderror
            </div>
            <div class="mb-3">
                <label for="templates">Template</label>
                <select name="template" class="js-example-basic-single form-select">
                    <option value="" default>Select template</option>
                    @foreach($templates as $template)
                    <option value="{{ $template->id }}" {{ $notification->template_id == $template->id ? 'selected' : '' }}>{{ $template->zip }}</option>
                    @endforeach
                </select>
                @error('template')
                    <span class="text-danger" role="alert"><strong>{{ $message }}</strong></span>
                @enderror
            </div>
            <div class="mb-3">
                <label for="message" class="form-label">Message</label>
                <textarea name="notification_message" id="message" cols="30" rows="10" class="form-control" placeholder="Enter notification message">{{ $notification->description }}</textarea>
                @error('notification_message')
                    <span class="text-danger" role="alert"><strong>{{ $message }}</strong></span>
                @enderror
            </div>
            <div class="mb-3 d-flex gap-5">
                <div>
                    <label for="Date">Date</label>
                    <input type="date" name="notification_date" value="{{ $notification->date }}" class="form-control">
                </div>
                <div>
                    <label for="Date">Time</label>
                    <input type="time" name="notification_time" value="{{ $notification->time }}" class="form-control">
                </div>
                @error('notification_date')
                    <span class="text-danger" role="alert"><strong>{{ $message }}</strong></span>
                @enderror
                @error('notification_time')
                    <span class="text-danger" role="alert"><strong>{{ $message }}</strong></span>
                @enderror
            </div>
            <div class="mb-3">
                <label for="Image" class="form-label">Image</label>
                <div class="d-flex justify-content-between">
                  <div class="my-auto">
                      <input type="file" class="form-control" name="image" id="image" accept="image/*" onchange="document.getElementById('image-output').src = window.URL.createObjectURL(this.files[0])">
                  </div>
                  <img src="{{ upload_url('notification/'.$notification->image) }}" id="image-output" class="img-lg" alt="">
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
@endpush

@push('custom-scripts')
  <!-- Custom js here -->
  <script src="{{ asset('assets/js/select2.js') }}"></script>
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