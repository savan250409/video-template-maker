@extends('layout.master')

@push('plugin-styles')
  <!-- Plugin css import here -->
  <link href="{{ asset('assets/plugins/select2/select2.min.css') }}" rel="stylesheet" />
  <link href="{{ asset('assets/plugins/jquery-tags-input/jquery.tagsinput.min.css') }}" rel="stylesheet" />
  <link href="{{ asset('assets/plugins/dropify/css/dropify.min.css') }}" rel="stylesheet" />
  <link href="{{ asset('assets/plugins/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" />
  <link href="{{ asset('assets/plugins/simplemde/simplemde.min.css') }}" rel="stylesheet" />


@endpush

@section('content')
  <!-- Page content here -->
  <nav class="page-breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="#">Setting</a></li>
      <li class="breadcrumb-item active" aria-current="page">Setting</li>
    </ol>
  </nav>

  <div class="row">
    <div class="col-md-12 grid-margin stretch-card">
      <div class="card">
        <div class="card-body">
          <h6 class="card-title">Settings</h6>
          <ul class="nav nav-tabs nav-tabs-line" id="lineTab" role="tablist">
              <li class="nav-item">
                <a class="nav-link active" id="app-setting-line-tab" data-bs-toggle="tab" data-bs-target="#app-setting" role="tab" aria-controls="app-setting" aria-selected="false">App Setting</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" id="home-line-tab" data-bs-toggle="tab" data-bs-target="#home" role="tab" aria-controls="home" aria-selected="true">Notification</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" id="ads-line-tab" data-bs-toggle="tab" data-bs-target="#ads" role="tab" aria-controls="ads" aria-selected="false">Ads</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" id="app-update-line-tab" data-bs-toggle="tab" data-bs-target="#app-update" role="tab" aria-controls="app-update" aria-selected="false">App Update</a>
              </li>

          </ul>
            <div class="tab-content mt-3" id="lineTabContent">
              <div class="tab-pane fade show active" id="app-setting" role="tabpanel" aria-labelledby="app-setting-line-tab">
                  <form method="POST" action="{{ Route('setting.store') }}" enctype="multipart/form-data">
                      @csrf
                      <div class="row">
                          <div class="col-md-6">
                                <div class="mb-3">
                                    <div>
                                        <label for="app-logo" class="form-label">App Logo</label>
                                        <div class="d-flex gap-5">
                                            <input type="file" name="settings[app_logo]" accept=".jpg,.png,.jpeg" class="form-control my-auto"
                                                id="app-logo">
                                            <img src="{{ upload_url('setting/'. App\Models\Setting::getSettingValue('app_logo')) }}"
                                                class="rounded-3 my-auto" style="width:25%;height:auto;" alt="">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="d-flex gap-5">
                                        <div>
                                            <label for="app-favicon" class="form-label">Favicon</label>
                                            <input type="file" name="settings[app_favicon]" accept=".jpg,.png,.jpeg" class="form-control my-auto"
                                                id="app-favicon">
                                        </div>
                                        <img src="{{ upload_url('setting/'. App\Models\Setting::getSettingValue('app_favicon')) }}"
                                            class="rounded-3 my-auto" style="width:25%;height:auto;" alt="">
                                    </div>
                                </div>
                            </div>
                          <div class="col-md-12">
                              <div class="mb-3">
                                 <label for="api-status" class="form-label">API Autorization</label> 
                                  <input type="text" name="settings[api_authorization]" class="form-control" id="app-open-id" placeholder="Enter app open ID" value="{{ App\Models\Setting::getSettingValue('api_authorization') }}">
                              </div>
                          </div>
                          <div class="col-md-12">
                            <div class="mb-3">
                              <label for="app-version" class="form-label">Privacy Policy</label>
                              <textarea id="privacy" name="settings[privacy]" class="form-control">{{ App\Models\Setting::getSettingValue('privacy') }}</textarea>
                            </div>
                            <div class="mb-3">
                              <label for="inter-item" class="form-label">Terms & Conditions</label>
                              <textarea id="terms" name="settings[terms]" class="form-control">{{ App\Models\Setting::getSettingValue('terms') }}</textarea>
                            </div>
                            <div class="mb-3">
                              <label for="inter-item" class="form-label">Refund Policy</label>
                              <textarea id="refund" name="settings[refund]" class="form-control">{{ App\Models\Setting::getSettingValue('refund') }}</textarea>
                            </div>
                            
                            
                          </div>
                      </div>
                      <button  type="submit" class="btn btn-primary me-2" {{ Auth::user()->type == 'demo' ? 'disabled="disabled"' : '' }}>Submit</button>
                  </form>
              </div>
              <div class="tab-pane fade" id="home" role="tabpanel" aria-labelledby="home-line-tab">
                  <form method="POST" action="{{ Route('setting.store') }}">
                      @csrf
                      <div class="row">
                          <div class="col-md-6">
                            <div class="mb-3">
                              <label for="one-signal-id" class="form-label">OneSignal App ID</label>
                              <input type="text" name="settings[one_signal_app_id]" class="form-control" id="one-signal-id" placeholder="Enter oneSignal app ID" value="{{ App\Models\Setting::getSettingValue('one_signal_app_id') }}">
                            </div>
                            <div class="mb-3">
                              <label for="one-signal-rest-key" class="form-label">OneSignal Rest Key</label>
                              <input type="text" name="settings[one_signal_rest_key]" class="form-control" id="one-signal-rest-key" placeholder="Enter oneSignal rest key" value="{{ App\Models\Setting::getSettingValue('one_signal_rest_key') }}">
                            </div>
                            <button  type="submit" class="btn btn-primary me-2" {{ Auth::user()->type == 'demo' ? 'disabled="disabled"' : '' }}>Submit</button>
                          </div>
                      </div>
                  </form>
              </div>
              <div class="tab-pane fade" id="ads" role="tabpanel" aria-labelledby="ads-line-tab">
                  <form method="POST" action="{{ Route('setting.store') }}">
                      @csrf
                      <div class="row">
                          <div class="col-md-6">
                            <div class="mb-3">
                                <div class="mb-5">
                                    <div class="d-flex gap-2">
                                        <label for="banner-status" class="form-label">Status</label>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" {{ App\Models\Setting::getSettingValue('ad_status') == 'on' ? 'checked' : '' }} onchange="adsUpdate(this, 'ad_status');">
                                        </div>
                                    </div>
                                </div>
                                <div class="card">
                                    <div class="card-header d-flex justify-content-between">
                                        <div>Banner Ad</div>
                                        <div class="d-flex gap-2">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" {{ App\Models\Setting::getSettingValue('banner_ad_id_status') == 'on' ? 'checked' : '' }} onchange="adsUpdate(this, 'banner_ad_id_status');">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <label for="banner-ad-id" class="form-label">Banner Ad ID</label>
                                          <input type="text" name="settings[banner_ad_id]" class="form-control" id="banner-ad-id" placeholder="Enter banner ad ID" value="{{ App\Models\Setting::getSettingValue('banner_ad_id') }}">
                                          @error('settings[banner_ad_id]')
                                              <span class="text-danger" role="alert"><strong>{{ $message }}</strong></span>
                                          @enderror
                                    </div>
                                </div>
                             </div>  
                          </div>
                          <div class="col-md-6">
                              <div class="mb-3">
                                <div class="card">
                                    <div class="card-header d-flex justify-content-between">
                                        <div>Ads Count</div>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                          <label for="native-count" class="form-label">Native Count</label>
                                          <input type="number" name="settings[ad_native_count]" class="form-control" id="native-count" placeholder="Enter native count" value="{{ App\Models\Setting::getSettingValue('ad_native_count') }}">
                                          @error('settings[ad_native_count]')
                                              <span class="text-danger" role="alert"><strong>{{ $message }}</strong></span>
                                          @enderror
                                        </div>
                                        <div class="mb-3">
                                          <label for="inter-item" class="form-label">Inter Items</label>
                                          <input type="number" name="settings[ad_inter_item]" class="form-control" id="inter-item" placeholder="Enter inter items" value="{{ App\Models\Setting::getSettingValue('ad_inter_item') }}">
                                          @error('settings[ad_inter_item]')
                                              <span class="text-danger" role="alert"><strong>{{ $message }}</strong></span>
                                          @enderror
                                        </div>
                                    </div>
                                </div>
                             </div>
                          </div>
                          <div class="col-md-6">
                            <div class="mb-3">
                                <div class="card">
                                    <div class="card-header d-flex justify-content-between">
                                        <div>Interstital Ad</div>
                                        <div class="d-flex gap-2">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" {{ App\Models\Setting::getSettingValue('interstital_ad_id_status') == 'on' ? 'checked' : '' }} onchange="adsUpdate(this, 'interstital_ad_id_status');">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <label for="interstital-ad-id" class="form-label">Interstital Ad ID</label>
                                          <input type="text" name="settings[interstital_ad_id]" class="form-control" id="interstital-ad-id" placeholder="Enter interstital ad ID" value="{{ App\Models\Setting::getSettingValue('interstital_ad_id') }}">
                                          @error('settings[interstital_ad_id]')
                                              <span class="text-danger" role="alert"><strong>{{ $message }}</strong></span>
                                          @enderror
                                    </div>
                                </div>
                             </div>  
                          </div>
                          <div class="col-md-6">
                            <div class="mb-3">
                                <div class="card">
                                    <div class="card-header d-flex justify-content-between">
                                        <div>Native Ad</div>
                                        <div class="d-flex gap-2">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" {{ App\Models\Setting::getSettingValue('native_ad_id_status') == 'on' ? 'checked' : '' }} onchange="adsUpdate(this, 'native_ad_id_status');">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <label for="native-ad-id" class="form-label">Native Ad ID</label>
                                      <input type="text" name="settings[native_ad_id]" class="form-control" id="native-ad-id" placeholder="Enter native ad ID" value="{{ App\Models\Setting::getSettingValue('native_ad_id') }}">
                                      @error('settings[native_ad_id]')
                                          <span class="text-danger" role="alert"><strong>{{ $message }}</strong></span>
                                      @enderror
                                    </div>
                                </div>
                             </div>  
                          </div>
                          <div class="col-md-6">
                            <div class="mb-3">
                                <div class="card">
                                    <div class="card-header d-flex justify-content-between">
                                        <div>Reward Ad</div>
                                        <div class="d-flex gap-2">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" {{ App\Models\Setting::getSettingValue('reward_ad_id_status') == 'on' ? 'checked' : '' }} onchange="adsUpdate(this, 'reward_ad_id_status');">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <label for="reward-ad-id" class="form-label">Reward Ad ID</label>
                                          <input type="text" name="settings[reward_ad_id]" class="form-control" id="reward-ad-id" placeholder="Enter reward ad ID" value="{{ App\Models\Setting::getSettingValue('reward_ad_id') }}">
                                          @error('settings[reward_ad_id]')
                                              <span class="text-danger" role="alert"><strong>{{ $message }}</strong></span>
                                          @enderror
                                    </div>
                                </div>
                             </div>  
                          </div>
                          <div class="col-md-6">
                            <div class="mb-3">
                                <div class="card">
                                    <div class="card-header d-flex justify-content-between">
                                        <div>App Open</div>
                                        <div class="d-flex gap-2">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" {{ App\Models\Setting::getSettingValue('app_open_id_status') == 'on' ? 'checked' : '' }} onchange="adsUpdate(this, 'app_open_id_status');">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <label for="app-open-id" class="form-label">App Open ID</label>
                                          <input type="text" name="settings[app_open_id]" class="form-control" id="app-open-id" placeholder="Enter app open ID" value="{{ App\Models\Setting::getSettingValue('app_open_id') }}">
                                          @error('settings[app_open_id]')
                                              <span class="text-danger" role="alert"><strong>{{ $message }}</strong></span>
                                          @enderror
                                    </div>
                                </div>
                             </div>  
                          </div>
                          
                      </div>
                      <button  type="submit" class="btn btn-primary me-2" {{ Auth::user()->type == 'demo' ? 'disabled="disabled"' : '' }}>Submit</button>
                  </form>
              </div>
              <div class="tab-pane fade" id="app-update" role="tabpanel" aria-labelledby="app-update-line-tab">
                  <form method="POST" action="{{ Route('setting.store') }}">
                      @csrf
                      <div class="row">
                          <div class="col-md-6">
                            <div class="mb-3 d-flex gap-3">
                                <label for="app-popup-status" class="form-label">Status</label>
                                <div class="form-check form-switch">
                                    <input name="settings[app_update_popup]" class="form-check-input" type="checkbox" {{ App\Models\Setting::getSettingValue('app_update_popup') == 'on' ? 'checked' : '' }} onchange="adsUpdate(this, 'app_update_popup');">
                                </div>
                            </div>
                            <div class="mb-3">
                              <label for="app-version" class="form-label">New App Version</label>
                              <input type="text" name="settings[app_update_version]" class="form-control" id="app-version" placeholder="Enter app version" value="{{ App\Models\Setting::getSettingValue('app_update_version') }}">
                            </div>
                            <div class="mb-3">
                              <label for="inter-item" class="form-label">Description</label>
                              <textarea id="tinymceExample" name="settings[app_update_description]" class="form-control">{{ App\Models\Setting::getSettingValue('app_update_description') }}</textarea>
                            </div>
                            
                          </div>
                          <div class="col-md-6">
                              <div class="mb-3">
                              <label for="app-link" class="form-label">App Link</label>
                              <input type="url" name="settings[app_update_app_link]" class="form-control" id="app-link" placeholder="Enter app link" value="{{ App\Models\Setting::getSettingValue('app_update_app_link') }}">
                            </div>
                            <div class="mb-3 d-flex gap-3">
                                <label for="cancel-option" class="form-label">Cancel Option</label>
                                <div class="form-check form-switch">
                                    <input name="settings[app_update_cancel_option]" class="form-check-input" type="checkbox" {{ App\Models\Setting::getSettingValue('app_update_cancel_option') == 'on' ? 'checked' : '' }} >
                                </div>
                            </div>
                          </div>
                      </div>
                      <button  type="submit" class="btn btn-primary me-2" {{ Auth::user()->type == 'demo' ? 'disabled="disabled"' : '' }}>Submit</button>
                  </form>
              </div>

            </div>
        </div>
      </div>
    </div>
    
  </div>
@endsection

@push('plugin-scripts')
  <!-- Plugin js import here -->
  <script src="{{ asset('assets/plugins/select2/select2.min.js') }}"></script>
  <script src="{{ asset('assets/plugins/jquery-tags-input/jquery.tagsinput.min.js') }}"></script>
  <script src="{{ asset('assets/plugins/dropify/js/dropify.min.js') }}"></script>
  <script src="{{ asset('assets/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
  <script src="{{ asset('assets/plugins/tinymce/tinymce.min.js') }}"></script>

@endpush

@push('custom-scripts')
  <!-- Custom js here -->
  <script src="{{ asset('assets/js/tinymce.js') }}"></script>
  <script src="{{ asset('assets/js/select2.js') }}"></script>
  <script src="{{ asset('assets/js/tags-input.js') }}"></script>
  <script src="{{ asset('assets/js/dropify.js') }}"></script>
  <script src="{{ asset('assets/js/sweet-alert.js') }}"></script>
  <script>
  function adsUpdate(element, dataId) {
     $.ajax({
          url: "{{ Route('ads.update')}}",
          type: 'POST',
          data:{
            "ads_key": dataId,
            "ads_value": element.checked,
            "_token": '{{ csrf_token() }}',
          },
          success:function(){
            Swal.fire({
                position: 'bottom-end',
                icon: 'success',
                width: 300,
                text: dataId+" "+"status changed successfully!",
                showConfirmButton: false,
                timer: 1500
              })
          },
        });
  }
  </script>
  <script>
  @if (Session::has('success'))
  Swal.fire({
        position: 'top-end',
        icon: 'success',
        width: 300,
        text: "{{ Session::get('success') }}",
        showConfirmButton: false,
        timer: 1500
      })
  @endif
</script>

@endpush