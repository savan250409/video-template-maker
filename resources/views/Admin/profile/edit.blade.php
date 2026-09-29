@extends('layout.master')

@push('plugin-styles')
  <!-- Plugin css import here -->
@endpush

@section('content')
  <!-- Page content here -->
  <nav class="page-breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="#">Profile</a></li>
      <li class="breadcrumb-item active" aria-current="page">Edit</li>
    </ol>
  </nav>

  <div class="row">
    <div class="col-md-6 grid-margin stretch-card">
      <div class="card">
        <div class="card-body">
          <h6 class="card-title">Edit Profile</h6>
          <form class="forms-sample" method="POST" action="{{ Route('profile.update', [Auth::user()->id]) }}" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            <div class="mb-3">
              <label for="name" class="form-label">Name</label>
              <input type="text" name="user_name" value="{{ $user->name }}" id="name" class="form-control" placeholder="Enter user name">
              @error('user_name')
                <span class="text-danger" role="alert"><strong>{{ $message }}</strong></span>
              @enderror
            </div> 
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" name="user_email" value="{{ $user->email }}" id="email" class="form-control" placeholder="Enter user email">
                @error('user_email')
                    <span class="text-danger" role="alert"><strong>{{ $message }}</strong></span>
                @enderror
            </div>
            <div class="mb-3">
                <label for="mobile" class="form-label">Mobile</label>
                <input type="mobile" name="mobile" value="{{ $user->mobile }}" id="mobile" class="form-control" placeholder="Enter user mobile no.">
                @error('mobile')
                    <span class="text-danger" role="alert"><strong>{{ $message }}</strong></span>
                @enderror
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" name="user_password" value="" id="password" class="form-control" placeholder="Enter user password">
                @error('user_password')
                    <span class="text-danger" role="alert"><strong>{{ $message }}</strong></span>
                @enderror
            </div>
            <div class="mb-3">
                <label for="confirm-password" class="form-label">Confirm Password</label>
                <input type="password" name="confirm_password" value="" id="confirm-password" class="form-control" placeholder="Enter user confirm password">
                @error('confirm_password')
                    <span class="text-danger" role="alert"><strong>{{ $message }}</strong></span>
                @enderror
            </div>
            <div class="mb-3">
                <label for="avatar" class="form-label">Avatar</label>
                <div class="d-flex justify-content-between gap-2">
                    <div class="my-auto">
                        <input type="file" name="user_avatar" accept="image/*" id="avatar" class="form-control" onchange="document.getElementById('avatar-output').src = window.URL.createObjectURL(this.files[0])">
                    </div>
                    <img src="{{ !is_null($user->logo) ? upload_url('profile/'.$user->logo) : 'https://placehold.co/70x70' }}" alt="" id="avatar-output" style="width:200px;height:200px;">
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