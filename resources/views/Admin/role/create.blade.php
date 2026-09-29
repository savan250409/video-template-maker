@extends('layout.master')

@push('plugin-styles')
  <!-- Plugin css import here -->
@endpush

@section('content')
  <!-- Page content here -->
  <nav class="page-breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="#">Role</a></li>
      <li class="breadcrumb-item active" aria-current="page">Create</li>
    </ol>
  </nav>

  <div class="row">
    <div class="col-md-6 grid-margin stretch-card">
      <div class="card">
        <div class="card-body">
          <h6 class="card-title">Add Role</h6>
          <form class="forms-sample" method="POST" action="{{ Route('role.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
              <label for="role-name" class="form-label">Name</label>
              <input type="text" name="role_name" class="form-control" id="role-name" placeholder="Enter role name">
              @error('role_name')
                <span class="text-danger" role="alert"><strong>{{ $message }}</strong></span>
              @enderror
            </div>
            <div class="mb-3">
              {{-- <div class="form-group"> --}}
                <strong>Permissions</strong>
                <br/>
                @foreach ($permissions as $permission)
                <label for="{{ $permission->name }}">
                  <input type="checkbox" class="" name="permission[]" value="{{ $permission->name }}">
                  {{ $permission->name }}
                </label><br/>
                @endforeach
              {{-- </div> --}}
            </div>            
            
            <button type="submit" class="btn btn-primary me-2">Submit</button>
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