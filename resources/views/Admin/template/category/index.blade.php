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
      <li class="breadcrumb-item"><a href="#">Template</a></li>
      <li class="breadcrumb-item active" aria-current="page">Category</li>
    </ol>
  </nav>
  
  <div class="row">
    <div class="col-md-12 grid-margin stretch-card">
      <div class="card">
        <div class="card-body">
          <div class="d-flex justify-content-between mb-3">
            <h6 class="card-title my-auto">Category</h6>
            <a href="{{ Route('template-category.create') }}" class="btn btn-primary">Add</a>
          </div>
          <div class="table-responsive">
            <table id="dataTableExample" class="table table-hover">
              <thead>
                <tr>
                  <th>#</th>
                  <th class="col-10">Name</th>
                  <th>Status</th>
                  <th>Action</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
                @php
                  $j = 0;
                @endphp
                @foreach ($categories as $key => $category)
                  <tr class="category-{{ $category->id }}" data-id="{{ $category->id }}" style="cursor: move;">
                    <td>{{ ++$j }}</td>
                    <td>
                      <div class="d-flex gap-3">
                        <img src="{{ upload_url('category/template/thumbnail/'.$category->logo) }}" alt="">
                        <b class="my-auto">{{ $category->name }}</b>
                      </div>
                    </td>
                    <td>
                      <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" onchange="status(this, {{ $category->id }});" {{ $category->is_active == 1 ? 'checked' : '' }}>
                      </div>
                    </td>
                    <td>
                      <div class="d-flex gap-2">
                        <a href="{{ Route('template-category.edit', [$category->id]) }}" class="btn btn-light btn-icon" style="background: #F7F6FB;border: solid 1px #EFEBFF;">
                          <img src="{{ asset('uploads/edit.svg') }}" style="max-width:18px;" />
                        </a>
                        @if(Auth::user()->type == 'demo')
                        <button disabled="disabled" class="btn btn-light btn-icon" style="background: #F7F6FB;border: solid 1px #EFEBFF;">
                          <img src="{{ asset('uploads/delete.svg') }}" style="max-width:18px;" />
                        </button>
                        @else
                        <a href="#" onclick="destroy({{ $category->id }}, 'template')" class="btn btn-light btn-icon" style="background: #F7F6FB;border: solid 1px #EFEBFF;">
                          <img src="{{ asset('uploads/delete.svg') }}" style="max-width:18px;" />
                        </a>
                        @endif
                      </div>
                    </td>
                    <td><img src="{{ asset('uploads/drag_handle.svg') }}" style="max-width:25px;" /></td>
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
  <script src="{{ asset('assets/plugins/sweetalert2/sweetalert2.min.js') }}"></script>

@endpush

@push('custom-scripts')
<!-- Custom js here -->
<script src="https://code.jquery.com/jquery-3.6.1.min.js" integrity="sha256-o88AwQnZB+VDvE9tvIXrMQaPlFFSUTR+nldQm1LuPXQ=" crossorigin="anonymous"></script>
  <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js" integrity="sha256-lSjKY0/srUM9BE3dPm+c4fBo1dky2v27Gdjm2uoZaL0=" crossorigin="anonymous"></script>
  
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
          url: "{{ Route('template-category.status') }}",
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
              title: 'Template category status changed successfully!'
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
          text: "The assigned templates(files) and banners will also be deleted, And you will not be able to revert it!",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonClass: 'me-2',
          confirmButtonText: 'Yes, delete it!',
          cancelButtonText: 'No, cancel!',
          reverseButtons: true
        }).then((result) => {
          if (result.value) {
            $.ajax({
          url: "{{ Route('template-category.destroy') }}",
          type: 'POST',
          data:{
            "id": id,
            "_token": '{{ csrf_token() }}',
            'type': type
          },
          success:function(){
            $('.category-'+id).remove();
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
              'Your category, template files and banners are safe :)',
              'error'
            )
          }
        })

  }
  
  $(document).ready(function() {
      $("#dataTableExample tbody").sortable({
        stop: function(event, ui) {
          var sortIds = [];
          $('#dataTableExample tbody tr').each(function() {
            sortIds.push($(this).data('id'));
          });
          $.ajax({
            type: "POST",
            url: "{{ route('template-category.order') }}", 
            data: {
              sortedIds: sortIds,
              '_token': '{{ csrf_token() }}'
            },
            success: function(data) {
              const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
              });

              Toast.fire({
                icon: 'success',
                title: 'Template Category Order Changed Successfully'
              });
            }
          });
        }
      });
    });
</script>
@endpush