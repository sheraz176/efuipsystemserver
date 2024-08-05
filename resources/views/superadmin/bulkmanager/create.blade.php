@extends('superadmin.layout.master')
@include('superadmin.partials.style')
<link href="{{asset('newdes/assets/css/toastr.min.css')}}" rel="stylesheet">

@section('content')
<div class="container">
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="py-3 mb-4"><span class="text-muted fw-light">Bulk Manager/</span> Create New Bulk File</h4>

        <!-- Basic Layout -->
        {{-- <div class="row">
            <div class="col-xl">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Basic Layout</h5>
                        <small class="text-muted float-end">Bulk Manager</small>


                    </div>
                    <div class="card-body">
                        <form method="post" action="{{ route('superadmin.builkmanager.store') }}" enctype="multipart/form-data">
                            @csrf
                            <!-- Other fields... -->

                            <!-- File Upload -->
                            <div class="form-group">
                                <label for="bulk_file">Upload Excel File:</label>
                                <input type="file" name="bulk_file" class="form-control" required>
                            </div>
                            <br>
                            <button type="submit" class="btn btn-primary">Upload Excel</button>
                        </form>
                    </div>
                </div>
            </div>
        </div> --}}
        <div class="row">
            <div class="col-xl">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Basic Layout</h5>
                        <small class="text-muted float-end">Bulk Manager</small>


                    </div>
                    <div class="card-body">
                        <form action="{{ route('superadmin.file.upload') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <!-- Other fields... -->

                            <!-- File Upload -->
                            <div class="form-group">
                                <label for="file">Upload CSV File:</label>
                                <input type="file" name="file" class="form-control" required>
                            </div>
                            <br>
                            <button type="submit" class="btn btn-primary">Upload CSV</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>


    </div>
</div>



<script>
    function toastSuccess() {
        // alert('hi');
        toastr.remove();
        toastr.options.positionClass = "toast-top-right";
        toastr.success('Upload Csv File Successfull.', 'Successfull !');
    }

    function toastdanger() {
        toastr.remove();
        toastr.options.positionClass = "toast-top-right";
        toastr.error('Invalid response from API', 'Some thing Wrong !');
    }
    function toasterror() {
        toastr.remove();
        toastr.options.positionClass = "toast-top-right";
        toastr.error('Subscription Not Found', 'Some thing Wrong !');
    }

</script>
<script>
    $(document).ready(function() {

        var created = "{{ Session::get('success') }}";
        if (created) {
            toastSuccess();
        }

        var error = "{{ Session::get('error') }}";
        if (error) {
            toastdanger();
        }

        var erroring = "{{ Session::get('erroring') }}";
        if (erroring) {
            toasterror();
        }



    });

</script>

<script src="{{asset('newdes/assets/js/toastr.min.js')}}"> </script>
<script src="{{asset('newdes/assets/js/toast.js')}}"> </script>

@endsection
