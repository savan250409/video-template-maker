@extends('layout.master2')

@section('content')
<div class="page-content d-flex align-items-center justify-content-center">

  <div class="row w-100 mx-0 auth-page">
    <div class="col-md-8 col-xl-6 mx-auto">
      <div class="card">
        <div class="row">
          <div class="col-md-5 pe-md-0">
            <div class="auth-side-wrapper" style="background-image: url({{ asset('login-banner-1.jpg') }});background-position:bottom;">

            </div>
          </div>
          <div class="col-md-7 ps-md-0">
            <div class="auth-form-wrapper px-4 py-5">
              <img src="{{ upload_url('setting/'. App\Models\Setting::getSettingValue('app_logo')) }}" style="height:auto;width:150px;" />
              <h5 class="text-muted fw-normal mb-4 mt-2">Welcome back! Log in to your account.</h5>
              <form method="POST" action="{{ Route('authenticate') }}" class="forms-sample">
                @csrf
                <div class="mb-3">
                  <label for="userEmail" class="form-label">Email</label>
                  <input type="email" name="email" class="form-control" id="userEmail" placeholder="Email">
                </div>
                <div class="mb-3">
                  <label for="userPassword" class="form-label">Password</label>
                  <input type="password" name="password" class="form-control" id="userPassword" autocomplete="current-password" placeholder="Password">
                </div>
                <div class="form-check mb-3">
                  <input type="checkbox" name="remember_me" class="form-check-input" id="authCheck">
                  <label class="form-check-label" for="authCheck">
                    Remember me
                  </label>
                </div>
                <div>
                  <button type="submit" class="btn btn-primary me-2 mb-2 mb-md-0">Login</button>
                </div>
                <a href="{{ Route('register') }}" class="d-block mt-3 text-muted">Not a user? Sign up</a>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

</div>
@endsection