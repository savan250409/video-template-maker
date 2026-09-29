@extends('layout.master')

@push('plugin-styles')
  <!-- Plugin css import here -->
@endpush

@section('content')
  <!-- Page content here -->
  <nav class="page-breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="#">Music</a></li>
      <li class="breadcrumb-item active" aria-current="page">Edit</li>
    </ol>
  </nav>

  <div class="row">
    <div class="col-md-6 grid-margin stretch-card">
      <div class="card">
        <div class="card-body">
          <h6 class="card-title">Edit Music</h6>
          <form class="forms-sample" method="POST" action="{{ Route('musics.update', [$music->id]) }}" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            <div class="mb-3">
              <label for="category" class="form-label">Music Category</label>
              <select name="music_category" class="form-select" id="category">
                <option value="" default>Select category</option>
                @foreach ($categories as $category)
                <option value="{{ $category->id }}" {{ $music->category_id == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="mb-3">
              <label for="music-file" class="form-label">Select Music</label>
              <input type="file" accept="audio/*" name="music_file" id="music-file" class="form-control" onchange="fileHandler(this);">
              <input type="text" class="form-control mt-1" name="file_name" id="file-name" disabled placeholder="Enter music name" />
            </div>
            <div class="mb-3 d-grid">
              <label for="audio" class="">Audio</label>
              <audio controls>
                <source src="{{ upload_url('music/'.$music->music) }}">
              </audio>
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
      function fileHandler(file) {
          var file = file.files[0];
          var fileName = file.name.split('.')[0];
          var inputFileName = document.getElementById('file-name');
          inputFileName.value = fileName;
          inputFileName.required = true;
          inputFileName.disabled = false;
      }
  </script>
@endpush