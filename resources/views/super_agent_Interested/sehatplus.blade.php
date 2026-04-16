@extends('super_agent_Interested.layout.master')
@include('superadmin.partials.style')

@section('content')
<div class="container mt-4">
    <!-- Breadcrumb -->
    <div class="row mb-3">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb pl-0">
                    <li class="breadcrumb-item"><a href="#"><i class="material-icons"></i>Home</a></li>
                    <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Claims Report</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Filters -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="ms-panel">
                <div class="ms-panel-header ms-panel-custome align-items-center">
                    <div class="row g-2">
                        <div class="col-md-4">
                            <input type="text" id="msisdnFilter" class="form-control" placeholder="Search MSISDN">
                        </div>
                        <div class="col-md-4">
                            <select id="planFilter" class="form-control">
                                <option value="">All Plans</option>
                                <option value="1">Medical Insurance</option>
                                <option value="2">Family Health Insurance</option>
                                 <option value="3">Cashless Individual</option>
                                   <option value="4">Cashless Family Plan</option>
                                     <option value="5">Cashless Family Plus</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button id="filterBtn" class="btn btn-primary w-100">Search</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Claims Table Card -->
   <div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <!-- Horizontal Scroll Wrapper -->
                <div class="table-responsive">
                    <table id="claimsTable" class="table table-striped table-bordered dt-responsive nowrap w-100">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>MSISDN</th>
                                <th>Plan Name</th>
                                <th>Product Name</th>
                                <th>Date</th>
                                <th>Customer Name</th>
                                <th>Amount</th>
                                <th>Claim Amount</th>
                                <th>Remarks</th>
                                <th>JazzCash TID</th>
                                <th>JazzCash Reference ID</th>
                                <th>Update Claim Amount</th>
                                <th>Status Action</th>
                                <th>Images</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
                <!-- Pagination -->
                <ul class="pagination mt-2"></ul>
            </div>
        </div>
    </div>
</div>
</div>

<!-- Image Modal with Carousel -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-body">
        <div id="carouselImages" class="carousel slide" data-bs-ride="carousel">
          <div class="carousel-inner" id="carouselInner"></div>
          <button class="carousel-control-prev" type="button" data-bs-target="#carouselImages" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
          </button>
          <button class="carousel-control-next" type="button" data-bs-target="#carouselImages" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
          </button>
        </div>
      </div>
    </div>
  </div>
</div>

@include('superadmin.partials.script')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

<script>
let currentPage = 1;

// Initialize DataTable
let table = $('#claimsTable').DataTable({
    responsive: true,
    paging: false,
    searching: false,
    ordering: false,
    info: false,
    autoWidth: false,
});

// ==============================
// Fetch Claims Data
// ==============================
function fetchClaims(page = 1) {
    let msisdn = $('#msisdnFilter').val();
    let plan_id = $('#planFilter').val();

    $.ajax({
        url: "https://jazzcash-health.efulife.com/api/getallCustomerClaims",
        type: "GET",
        data: { page: page, customer_msisdn: msisdn, plan_id: plan_id },
        success: function(res) {
            table.clear();
            window.claimsData = res.data;
            currentPage = res.current_page;

            res.data.forEach(function(item, index) {
                let imagesBtn = '';
                if(item.images && item.images.length > 0){
                    imagesBtn = `<button class="btn btn-sm btn-info" onclick="openImageModal(${index})">View (${item.images.length})</button>`;
                }

                // Status Select
                let statusOptions = ['PENDING','APPROVED','REJECTED'].map(s =>
                    `<option value="${s}" ${item.status === s ? 'selected':''}>${s}</option>`
                ).join('');

              table.row.add([
    index + 1 + ((res.current_page-1)*res.per_page),
    item.customer_msisdn,
    item.plan_name ?? '-',
    item.product_name ?? '-',
    item.submitted_at,
    item.customer_name ?? '-',
    item.amount ?? '-',
    item.claim_amount ?? '-',
     item.remarks ?? '-',
      item.jazzcash_tid ?? '-',
       item.jazzcash_reference_id ?? '-',

    `<button class="btn btn-sm btn-success" onclick="updateClaimAmount('${item.claim_id}', '${item.claim_amount ?? ''}')">Update</button>`,
    // Status Select + Update button combined properly
    `<div class="d-flex gap-1">
        <select class="form-control statusSelect" data-claim-id="${item.claim_id}" style="width:auto">
            ${statusOptions}
        </select>
        <button class="btn btn-sm btn-warning updateStatusBtn" data-claim-id="${item.claim_id}">Update</button>
    </div>`,
    imagesBtn
]);
            });

            table.draw();

            // Pagination
            let start = Math.max(res.current_page - 2, 1);
            let end = Math.min(start + 4, res.last_page);
            let pagination = '';

            if(res.current_page > 1){
                pagination += `<li class="page-item"><a class="page-link" href="#" onclick="fetchClaims(${res.current_page - 1})">Prev</a></li>`;
            }

            for(let i=start;i<=end;i++){
                pagination += `<li class="page-item ${i == res.current_page ? 'active':''}">
                    <a class="page-link" href="#" onclick="fetchClaims(${i})">${i}</a>
                </li>`;
            }

            if(res.current_page < res.last_page){
                pagination += `<li class="page-item"><a class="page-link" href="#" onclick="fetchClaims(${res.current_page + 1})">Next</a></li>`;
            }

            $('.pagination').html(pagination);
        }
    });
}

// Initial Load
fetchClaims();

// Filter Button
$('#filterBtn').click(function(){ fetchClaims(1); });

// ==============================
// Update Claim Amount
// ==============================
function updateClaimAmount(claim_id, current_amount) {
    let newAmount = prompt("Enter new Claim Amount:", current_amount || '');
    if(newAmount !== null){
        $.ajax({
            url: `https://jazzcash-health.efulife.com/api/update-claim-amount`,
            type: "POST",
            data: { claim_id: claim_id, claim_amount: newAmount },
            success: function(res){
                alert(res.message);
                fetchClaims(currentPage);
            },
            error: function(xhr){
                alert(xhr.responseJSON.message || 'Error updating amount');
            }
        });
    }
}

// ==============================
// Update Status
// ==============================
$(document).on('click', '.updateStatusBtn', function(){
    let claim_id = $(this).data('claim-id');
    let select = $(this).closest('tr').find('.statusSelect');
    let newStatus = select.val();
    let remarks = '';

    if(newStatus === 'REJECTED'){
        remarks = prompt("Enter Remarks (Required for REJECTED):");
        if(!remarks){ alert("Remarks required for REJECTED"); return; }
    }

    $.ajax({
        url: `https://jazzcash-health.efulife.com/api/update-claim-status`,
        type: "POST",
        data: { claim_id: claim_id, status: newStatus, remarks: remarks },
        success: function(res){
            alert(res.message);
            fetchClaims(currentPage);
        },
        error: function(xhr){
            alert(xhr.responseJSON.message || 'Error updating status');
        }
    });
});

// ==============================
// Open Image Modal (Multiple Images)
// ==============================
function openImageModal(index){
    let claim = window.claimsData[index];
    if(claim.images && claim.images.length > 0){
        let innerHtml = claim.images.map((img, i) => `
            <div class="carousel-item ${i === 0 ? 'active' : ''}">
                <img src="${img}" class="d-block w-100" alt="Claim Image">
            </div>
        `).join('');
        $('#carouselInner').html(innerHtml);
        $('#imageModal').modal('show');
    }
}
</script>
@endsection
