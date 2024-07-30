@extends('superadmin.layout.master')

@section('content')
<div class="container">
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="py-3 mb-4"><span class="text-muted fw-light">Bulk Manager/</span> Create New Bulk File</h4>

        <!-- Basic Layout -->
        <div class="row">
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
        </div>
    </div>
</div>


@endsection
