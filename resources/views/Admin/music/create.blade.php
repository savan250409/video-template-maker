@extends('layout.master')

@push('plugin-styles')
  <!-- Plugin css import here -->
@endpush

@section('content')
  <!-- Page content here -->
  <nav class="page-breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="#">Music</a></li>
      <li class="breadcrumb-item active" aria-current="page">Add</li>
    </ol>
  </nav>

  <div class="row">
    <div class="col-md-6 grid-margin stretch-card">
      <div class="card">
        <div class="card-body">
          <h6 class="card-title">Add Music</h6>
          <form class="forms-sample" method="POST" action="{{ Route('musics.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
              <label for="category" class="form-label">Music Category</label>
              <select name="music_category" class="form-select" id="category">
                <option value="" default>Select category</option>
                @foreach ($categories as $category)
                <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
              </select>
              @error('music_category')
                <span class="text-danger" role="alert"><strong>{{ $message }}</strong></span>
              @enderror
            </div>
            <div class="mb-3">
              <label for="music-file" class="form-label">Select Music</label>
              <div id="file-input-container" class="d-flex flex-column">
                  <div class="d-flex gap-2">
                      <input type="file" accept="audio/*" name="music_file[]" onchange="fileHandler(this);" class="form-control" multiple required>
                  </div>
                  <div id="file-name-inputs" class="d-flex flex-column"></div>
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
<script>
  function fileHandler(input) {
        var fileNameInputsContainer = document.getElementById('file-name-inputs');

        while (fileNameInputsContainer.firstChild) {
            fileNameInputsContainer.removeChild(fileNameInputsContainer.firstChild);
        }

        var files = input.files;

        for (var i = 0; i < files.length; i++) {
            var file = files[i];
            var fileName = file.name;
            var name = file.name.split('.')[0];

            var fileNameInput = document.createElement('input');
            fileNameInput.type = 'text';
            fileNameInput.name = 'music_file_name[]';
            fileNameInput.className = 'form-control mt-2';
            fileNameInput.value = name;
            fileNameInput.placeholder = 'Music name';
            fileNameInput.required = true;

            fileNameInputsContainer.appendChild(fileNameInput);
        }
    }
</script>
@endpush