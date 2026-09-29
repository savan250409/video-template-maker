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
      <li class="breadcrumb-item"><a href="#">Template</a></li>
      <li class="breadcrumb-item active" aria-current="page">Template</li>
    </ol>
  </nav>
  
  <div class="row">
    <div class="col-md-12 grid-margin stretch-card">
      <div class="card">
        <div class="card-body">
          <div class="d-flex justify-content-between mb-3">
            <h6 class="card-title my-auto" style="margin-right:10px;">Template</h6>
            <div class="row justify-content-end">
                
                <div class="col-md-7">
                    <form action="{{ Route('animated-template.search') }}" method="post">
                    @csrf
                        <input name="search" class="form-control" placeholder="Template Search..." value="{{ isset($search) ? $search : '' }}" style="border-radius:20px;" />
                    </form>
                </div>
                <div class="col-md-4 p-0" style="width:auto;">
                    <a href="{{ Route('animated-template.create') }}" class="btn btn-primary" style="border-radius:20px;"><i data-feather="plus-circle" style="margin-right:7px;"></i>Add Template</a>
                </div>
            </div>
          </div>
          <div class="row" >
            @foreach ($templates as $key => $template)
              <div class="frame_div col-md-3 template-{{ $template->id }}" style="width: auto; padding: 2px 3px;">
                <div class="frame" style="width:200px;height:340px;padding:4px 5px;border-radius:5px;">
                  <div class="img_frame">
                      @if(\Illuminate\Support\Facades\File::exists(storage_path('app/public/uploads/template/'.$template->id.'/'.$template->zip.'/res/data.html')))
                      <iframe src="{{ upload_url('template/'.$template->id.'/'.$template->zip.'/res/data.html') }}" style="width:190px;height:330px;border-radius:5px;"></iframe>
                      @else
                      <img src="{{ upload_url('template/thumbnail/'.$template->thumbnail) }}" style="width:190px;height:330px;border-radius:5px;">
                      @endif
                    <div class="frame_header">
                        <span>{{ $template->zip }}</span>
                    </div>
                    <div style="position: absolute;color: #fff;top: 33px;z-index: 2;left: 19px;width: 85%;">
                        <span class="category_name" style="font-size:small;">
                          {{ $template->category->name }}
                        </span>
                    </div>
                    <div class="frame_footer">
                        <div class="d-flex justify-content-around bg-white py-2" style="height:40px;width:180px;border-radius:20px;">
                            @if(Auth::user()->type == 'demo')
                            <button style="border:none;background:none;height:fit-content;" data-toggle="tooltip"
                          data-tooltip="Delete"><i class="fa fa-trash" aria-hidden="true"></i>
                            </button>
                            @else
                            <a onclick="destroy({{ $template->id }});" data-toggle="tooltip"
                          data-tooltip="Delete"><i class="fa fa-trash" aria-hidden="true"></i>
                            </a>
                            @endif
                            <a href="{{url('admin/animated-template/' .$template->id.'/edit')}}" data-toggle="tooltip" data-tooltip="Edit"><i
                            class="fa fa-pen" aria-hidden="true" style="color:black;"></i>
                            </a>
                            <a href="{{ Route('custom-notification.create', [$template->id]) }}" data-toggle="tooltip"
                          data-tooltip="Notify"><i class="fa fa-bell" aria-hidden="true" style="color:black;"></i>
                            </a>
                            
                        </div>
                      
                    </div>
                    <div class="frame_gradient" style="border-radius:5px;width:190px;"></div>
                  </div>
                  
                </div>
              </div>
            @endforeach
            <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="model-title">Modal title</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                  </div>
                  <div class="modal-body">
                    <video controls id="video" style="width: 100%; height: 500px;">
                        <source src="" id="model-source" />
                    </video>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="d-flex justify-content-end">
          {!! $templates->links() !!}
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
  function free(checkbox, id) {
    var checked = checkbox.checked == true ? true : false;
    $.ajax({
          url: "{{ Route('animated-template.free.status') }}",
          type: 'POST',
          data:{
            "id": id,
            "_token": '{{ csrf_token() }}',
            'status': checked 
          },
          success:function(response){
            const Toast = Swal.mixin({
              toast: true,
              position: 'top-end',
              showConfirmButton: false,
              timer: 3000,
              timerProgressBar: true,
            });
            
            Toast.fire({
              icon: 'success',
              title: response == 1 ? 'Template is paid!' : 'Template is free!'
            })
          },
        });
  }

  function status(checkbox, id) {
    var checked = checkbox.checked == true ? true : false;
    $.ajax({
          url: "{{ Route('animated-template.status') }}",
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
              title: 'Template status changed successfully!'
            })
          },
        });
  }
  function destroy(id) {
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
          url: "{{ Route('animated-template.destroy') }}",
          type: 'POST',
          data:{
            "id": id,
            "_token": '{{ csrf_token() }}',
          },
          success:function(){
            $('.template-'+id).remove();
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