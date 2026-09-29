@extends('layout.master')

@push('plugin-styles')
  <!-- Plugin css import here -->
  <link href="{{ asset('assets/plugins/datatables-net/dataTables.bootstrap4.css') }}" rel="stylesheet" />
  <link href="{{ asset('assets/plugins/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('css/frame-card.css') }}">

@endpush

@section('content')

  <!-- Page content here -->
  <nav class="page-breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="#">Banner</a></li>
      <li class="breadcrumb-item active" aria-current="page">Banner</li>
    </ol>
  </nav>
  
  <div class="row">
    <div class="col-md-12 grid-margin stretch-card">
      <div class="card">
        <div class="card-body">
          <div class="d-flex justify-content-between mb-3">
            <h6 class="card-title my-auto">Banner</h6>
            <a href="{{ Route('banners.create') }}" class="btn btn-primary">Add</a>
          </div>
          <div class="row">
              @foreach($banners as $banner)
              <div class="frame_div col-md-4 banner-{{ $banner->id }}" style="width: auto; padding: 10px 6px;">
                    <div class="frame" style="width:325px;height:185px;">
                      <div class="img_frame">
                      <img src="{{ upload_url('banner/'.$banner->banner) }}" alt="" style="width:313px;height:140px;" />
                        <div class="frame_header">
                          <span class="category_name">{{ $banner->name }}</span>
                          <span>{{ $banner->type }}</span>
                        </div>
                        <div class="frame_footer">
                        </div>
                        <div class="frame_gradient" style="border-radius:13px;width:313px;height:60px;"></div>
                      </div>
                      <div class="action_frame ">
                        <ul class="d-inline-flex gap-2">
                          <li>
                            @if(Auth::user()->type == 'demo')
                            <a disabled data-toggle="tooltip"
                              data-tooltip="Delete"><i class="fa fa-trash" aria-hidden="true"></i></a>
                            @else
                            <a onclick="destroy({{ $banner->id }});" data-toggle="tooltip"
                              data-tooltip="Delete"><i class="fa fa-trash" aria-hidden="true"></i></a>
                            @endif
                          </li>
                          <li>
                            <a href="{{url('admin/banners/' .$banner->id.'/edit')}}" data-toggle="tooltip" data-tooltip="Edit"><i
                                class="fa fa-pen" aria-hidden="true"></i></a>
                          </li>
                          <li>
                            <label class="toggle_switch">
                              <input type="checkbox" class="frame-switch form-check-input" onclick="status(this, {{ $banner->id }});" value="1"
                                @if($banner->is_active == 1) checked @endif />
                              <span class="slider round"></span>
                            </label>
                          </li>
                        </ul>
                      </div>
                    </div>
                  </div>
              @endforeach
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

@push('plugin-scripts')
  <!-- Plugin js import here -->
  <script src="{{ asset('assets/plugins/datatables-net/jquery.dataTables.js') }}"></script>
  <script src="{{ asset('assets/plugins/datatables-net-bs4/dataTables.bootstrap4.js') }}"></script>
  <script src="{{ asset('assets/plugins/sweetalert2/sweetalert2.min.js') }}"></script>

@endpush

@push('custom-scripts')
<!-- Custom js here -->
    <script src="https://kit.fontawesome.com/8a66bca49b.js" crossorigin="anonymous"></script>
  <script src="{{ asset('assets/js/data-table.js') }}"></script>
  <script src="{{ asset('assets/js/sweet-alert.js') }}"></script>
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
<script>
  
  function status(checkbox, id) {
    var checked = checkbox.checked == true ? true : false;
    $.ajax({
          url: "{{ Route('banners.status') }}",
          type: 'POST',
          data:{
            "id": id,
            "_token": '{{ csrf_token() }}',
            'status': checked 
          },
          success:function(){
            const Toast = Swal.mixin({
              toast: true,
              position: 'top-end',
              showConfirmButton: false,
              timer: 3000,
              timerProgressBar: true,
            });
            
            Toast.fire({
              icon: 'success',
              title: 'Banner status changed successfully!'
            })
          },
        });
  }
  function destroy(id, type) {
    
    const swalWithBootstrapButtons = Swal.mixin({
          customClass: {
            confirmButton: 'btn btn-success',
            cancelButton: 'btn btn-danger me-2'
          },
          buttonsStyling: false,
        })
        
        swalWithBootstrapButtons.fire({
          title: 'Are you sure?',
          text: "You won't be able to revert this!",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonClass: 'me-2',
          confirmButtonText: 'Yes, delete it!',
          cancelButtonText: 'No, cancel!',
          reverseButtons: true
        }).then((result) => {
          if (result.value) {
            $.ajax({
          url: "{{ Route('banners.destroy') }}",
          type: 'POST',
          data:{
            "id": id,
            "_token": '{{ csrf_token() }}',
            'type': type
          },
          success:function(){
            $('.banner-'+id).remove();
          },
        });
            swalWithBootstrapButtons.fire(
              'Deleted!',
              'Your file has been deleted.',
              'success'
            )
          } else if (
            result.dismiss === Swal.DismissReason.cancel
          ) {
            swalWithBootstrapButtons.fire(
              'Cancelled',
              'Your imaginary file is safe :)',
              'error'
            )
          }
        })

  }
</script>
@endpush