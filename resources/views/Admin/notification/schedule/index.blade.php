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
      <li class="breadcrumb-item"><a href="#">Schedule Notification</a></li>
      <li class="breadcrumb-item active" aria-current="page">Schedule Notification</li>
    </ol>
  </nav>
  
  <div class="row">
    <div class="col-md-12 grid-margin stretch-card">
      <div class="card">
        <div class="card-body">
          <div class="d-flex justify-content-between mb-3">
            <h6 class="card-title my-auto">Scheduled Notifications</h6>
            <a href="{{ Route('schedule-notifications.create') }}" class="btn btn-primary">Add</a>
          </div>
          <div class="table-responsive">
            <table id="dataTableExample" class="table">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Title</th>
                  <th>Video</th>
                  <th>Schedule</th>
                  <th>Created</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                @php
                  $j = 0;
                @endphp
                @foreach ($scheduledNotifications as $key => $scheduled)
                <tr class="notification-{{ $scheduled->id }}">
                <td>{{ ++$j }}</td>
                <td><b>{{ $scheduled->getTemplate->zip }}</b></td>
                <td style="position: relative;">
                  <div style="position: relative;">
                    <!-- Play button -->
                    <img src="https://cdn-icons-png.flaticon.com/512/27/27223.png" style="position: absolute; top: 50%; left: 25%; transform: translate(-50%, -50%); z-index: 2; height:35px; width:30px; cursor:pointer;" onclick="preview(this);" data-bs-toggle="modal" data-bs-target="#exampleModal" data-url="{{ upload_url('template/video/'.$scheduled->getTemplate->video) }}" data-zip="{{ $scheduled->getTemplate->zip }}" />
                    <!-- Thumbnail -->
                    <img src="{{ upload_url('template/thumbnail/'.$scheduled->getTemplate->thumbnail) }}" style="height: 90px; width: 45%; border-radius: unset;" />
                  </div>
                </td>
                <td>{{ Carbon\Carbon::createFromFormat('Y-m-d', $scheduled->date)->format('d M, Y').' | '.Carbon\Carbon::createFromFormat('H:i:s', $scheduled->time)->format('H:i') }}</td>
                <td>{{ Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $scheduled->created_at)->format('d M, Y') }}</td>
                <td>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" onchange="status(this, {{ $scheduled->id }});" {{ $scheduled->is_active == 1 ? 'checked' : '' }}>
                      </div>
                </td>
                <td>
                  <div class="d-flex gap-2">
                    <a href="{{ Route('schedule-notifications.edit', [$scheduled->id]) }}" class="btn btn-secondary btn-icon" style="background: #F7F6FB;border: solid 1px #EFEBFF;">
                      <img src="{{ asset('uploads/edit.svg') }}" style="max-width:18px;" />
                    </a>
                    @if(Auth::user()->type == 'demo')
                    <button class="btn btn-danger btn-icon" style="background: #F7F6FB;border: solid 1px #EFEBFF;">
                      <img src="{{ asset('uploads/delete.svg') }}" style="max-width:18px;" />
                    </button>
                    @else
                    <a href="#" onclick="destroy({{ $scheduled->id }})" class="btn btn-danger btn-icon" style="background: #F7F6FB;border: solid 1px #EFEBFF;">
                      <img src="{{ asset('uploads/delete.svg') }}" style="max-width:18px;" />
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
          url: "{{ Route('schedule-notification.status') }}",
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
              title: 'Scheduled notification status changed successfully!'
            })
          },
        });
  }
  
      function preview(element) {
    let videoUrl = element.getAttribute('data-url');
    let zipName = element.getAttribute('data-zip');
    var modelTitle = document.getElementById('model-title');
    var modelSource = document.getElementById('model-source');
    modelTitle.textContent = zipName;
    modelSource.src = videoUrl;
    document.getElementById('video').load();
    
    document.getElementById('video').play();
  }
  </script>
<script>
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
          url: "{{ Route('schedule-notifications.destroy') }}",
          type: 'POST',
          data:{
            "id": id,
            "_token": '{{ csrf_token() }}',
          },
          success:function(){
            $('.notification-'+id).remove();
          },
        });
            swalWithBootstrapButtons.fire(
              'Deleted!',
              'Your scheduled notification has been deleted.',
              'success'
            )
          } else if (
            result.dismiss === Swal.DismissReason.cancel
          ) {
            swalWithBootstrapButtons.fire(
              'Cancelled',
              'Your scheduled notification is safe :)',
              'error'
            )
          }
        })

  }
</script>
@endpush