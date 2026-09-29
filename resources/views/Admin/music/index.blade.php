@extends('layout.master')

@push('plugin-styles')
  <!-- Plugin css import here -->
  <link href="{{ asset('assets/plugins/datatables-net/dataTables.bootstrap4.css') }}" rel="stylesheet" />
  <link href="{{ asset('assets/plugins/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" />

@endpush

@section('content')

  <!-- Page content here -->
  <nav class="page-breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="#">Music</a></li>
      <li class="breadcrumb-item active" aria-current="page">Music</li>
    </ol>
  </nav>
  
  <div class="row">
    <div class="col-md-12 grid-margin stretch-card">
      <div class="card">
        <div class="card-body">
          <div class="d-flex justify-content-between mb-3">
            <h6 class="card-title my-auto">Music</h6>
            <a href="{{ Route('musics.create') }}" class="btn btn-primary">Add</a>
          </div>
          <div class="table-responsive">
            <table id="dataTableExample" class="table">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Music</th>
                  <th>Category</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                @php
                  $j = 0;
                @endphp
                @foreach ($musics as $key => $music)
                  <tr class="music-{{ $music->id }}">
                    <td>{{ ++$j }}</td>
                    <td>
                      <div class="d-flex">
                        <audio controls>
                          <source src="{{ upload_url('music/'.$music->music) }}">
                        </audio>
                        <span class="my-auto mx-2">{{ $music->music }}</span>
                      </div>
                    </td>
                    <td><b>{{ $music->getCategory->name }}</b></td>
                    <td>
                      <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" onchange="status(this, {{ $music->id }});" {{ $music->is_active == 1 ? 'checked' : '' }}>
                      </div>
                    </td>
                    <td>
                      <div class="d-flex gap-2">
                        <a href="{{ Route('musics.edit', [$music->id]) }}" class="btn btn-secondary btn-icon" style="background: #F7F6FB;border: solid 1px #EFEBFF;">
                          <img src="{{ asset('icons/edit.svg') }}" style="max-width:18px;" />
                        </a>
                        @if(Auth::user()->type == 'demo')
                        <button disabled="disabled" class="btn btn-danger btn-icon" style="background: #F7F6FB;border: solid 1px #EFEBFF;">
                          <img src="{{ asset('icons/delete.svg') }}" style="max-width:18px;" />
                        </button>
                        @else
                        <a href="#" onclick="destroy({{ $music->id }})" class="btn btn-danger btn-icon" style="background: #F7F6FB;border: solid 1px #EFEBFF;">
                          <img src="{{ asset('icons/delete.svg') }}" style="max-width:18px;" />
                        </a>
                        @endif
                      </div>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
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
          url: "{{ Route('musics.status') }}",
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
              title: 'Music status changed successfully!'
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
          url: "{{ Route('musics.destroy') }}",
          type: 'POST',
          data:{
            "id": id,
            "_token": '{{ csrf_token() }}',
          },
          success:function(){
            $('.music-'+id).remove();
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