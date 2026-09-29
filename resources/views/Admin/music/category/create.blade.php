@extends('layout.master')

@push('plugin-styles')
  <!-- Plugin css import here -->
@endpush

@section('content')
  <!-- Page content here -->
  <nav class="page-breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="#">Music</a></li>
      <li class="breadcrumb-item active" aria-current="page">Category</li>
    </ol>
  </nav>

  <div class="row">
    <div class="col-md-6 grid-margin stretch-card">
      <div class="card">
        <div class="card-body">
          <h6 class="card-title">Add Category</h6>
          <form class="forms-sample" method="POST" action="{{ Route('music-category.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
              <label for="category-name" class="form-label">Name</label>
              <input type="text" name="category_name" class="form-control" id="category-name" placeholder="Enter category name">
              @error('category_name')
                <span class="text-danger" role="alert"><strong>{{ $message }}</strong></span>
              @enderror
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