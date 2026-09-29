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
      <li class="breadcrumb-item"><a href="#">Report</a></li>
      <li class="breadcrumb-item active" aria-current="page">Report</li>
    </ol>
  </nav>
  
  <div class="row">
    <div class="col-md-12 grid-margin stretch-card">
      <div class="card">
        <div class="card-body">
          <div class="d-flex justify-content-between mb-3">
            <h6 class="card-title my-auto">Reports</h6>
          </div>
          <div class="table-responsive">
            <table id="dataTableExample" class="table">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Name</th>
                  <th>Email</th>
                  <th>Video</th>
                  <th>Report</th>
                  <th>Date</th>
                  <th>Message</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                @php
                  $j = 0;
                @endphp
                @foreach ($reports as $key => $report)
                <tr class="report-{{ $report->id }} template-{{ $report->getTemplate->id }}">
                <td>{{ ++$j }}</td>
                <td><b>{{ $report->getTemplate->zip }}</b></td>
                <td>{{ $report->email }}</td>
                <td>
                    <img src="{{ upload_url('template/thumbnail/'.$report->getTemplate->thumbnail) }}" style="height: 85px; width: 65%; border-radius: unset;" />
                </td>
                <td>{{ $report->report }}</td>
                <td>{{ Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $report->created_at)->format('d M, Y') }}</td>
                <td>{{ $report->message }}</td>
                <td>
                  <div class="d-flex gap-2">
                    @if(Auth::user()->type == 'demo')
                    <button class="btn btn-inverse-info btn-icon" style="background: #F7F6FB;border: solid 1px #EFEBFF;" disabled="disabled">
                      <img src="{{ asset('icons/delete.svg') }}" style="max-width:18px;" />
                    </button>
                    @else
                    <a href="#" onclick="destroy({{ $report->id }}, 'report')" class="btn btn-inverse-info btn-icon" style="background: #F7F6FB;border: solid 1px #EFEBFF;">
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
          url: "{{ Route('report.destroy') }}",
          type: 'POST',
          data:{
            "id": id,
            "_token": '{{ csrf_token() }}',
            'type': type
          },
          success:function(){
            $('.report-'+id).remove();
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