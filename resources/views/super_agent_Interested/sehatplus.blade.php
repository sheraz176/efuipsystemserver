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

                <div class="row g-2 align-items-end">

                    <!-- MSISDN -->
                    <div class="col-md-3">
                        <input type="text" id="msisdnFilter" class="form-control" placeholder="Search MSISDN">
                    </div>

                    <!-- Plan -->
                    <div class="col-md-3">
                        <select id="planFilter" class="form-control">
                            <option value="">All Plans</option>
                            <option value="1">Medical Insurance</option>
                            <option value="2">Family Health Insurance</option>
                            <option value="3">Cashless Individual</option>
                            <option value="4">Cashless Family Plan</option>
                            <option value="5">Cashless Family Plus</option>
                        </select>
                    </div>

                    <!-- Date Picker (UI) -->
                    <div class="col-md-3">
                        <input type="text" id="dateFilter" class="form-control"
                               placeholder="Select date range" readonly>
                    </div>

                    <!-- Buttons -->
                    <div class="col-md-3 d-flex gap-2">

                        <button id="filterBtn" class="btn btn-primary w-50">
                            Search
                        </button>

                        <button id="exportBtn" class="btn btn-success w-50">
                            Export
                        </button>

                    </div>

                    <!-- Hidden fields (IMPORTANT - keep outside UI clutter) -->
                    <input type="hidden" id="startDate">
                    <input type="hidden" id="endDate">

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
                                    <th>Rejected Reason</th>
                                     <th>Remarks</th>

                                    <th>JazzCash TID</th>
                                    <th>JazzCash Reference ID</th>
                                    <th>Update Claim Amount</th>
                                     <th>Update Remarks</th>
                                    <th>Status Action</th>
                                    <th>Images</th>
                                    <th>Send SMS</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                    <ul class="pagination mt-2"></ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Image Modal -->
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

<!-- SMS Modal -->
<div class="modal fade" id="smsModal" tabindex="-1" aria-labelledby="smsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Send SMS</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="smsMsisdn">
        <div class="mb-3">
          <label for="smsMessage" class="form-label">Message</label>
          <textarea id="smsMessage" class="form-control" rows="4" placeholder="Enter your message"></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" onclick="sendSms()">Send</button>
      </div>
    </div>
  </div>
</div>

@include('superadmin.partials.script')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<!-- DateRangePicker CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

<!-- Moment JS (IMPORTANT) -->
<script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>

<!-- DateRangePicker JS -->
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>


<script>


$(document).ready(function () {

    $('#dateFilter').daterangepicker({
        opens: 'left',
        autoUpdateInput: false,
        locale: {
            format: 'YYYY-MM-DD',
            separator: ' to ',
            applyLabel: 'Apply',
            cancelLabel: 'Clear'
        }
    });

    $('#dateFilter').on('apply.daterangepicker', function (ev, picker) {

        let start = picker.startDate.format('YYYY-MM-DD');
        let end = picker.endDate.format('YYYY-MM-DD');

        $(this).val(start + ' to ' + end);

        $('#startDate').val(start);
        $('#endDate').val(end);

        fetchClaims(1);
    });

    $('#dateFilter').on('cancel.daterangepicker', function () {
        $(this).val('');
        $('#startDate').val('');
        $('#endDate').val('');
        fetchClaims(1);
    });

});





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
      let startDate = $('#startDate').val();
      let endDate = $('#endDate').val();
    


    $.ajax({
        url: "https://jazzcash-health.efulife.com/api/getallCustomerClaims",
        type: "GET",
        data: {
            page: page,
            customer_msisdn: msisdn,
            plan_id: plan_id,
            startDate : startDate,
            endDate : endDate
                 },

        success: function(res) {
            table.clear();
            window.claimsData = res.data;
            currentPage = res.current_page;

            res.data.forEach(function(item, index) {
                let imagesBtn = '';
                if(item.images && item.images.length > 0){
                    imagesBtn = `<button class="btn btn-sm btn-info" onclick="openImageModal(${index})">View (${item.images.length})</button>`;
                }

                let statusOptions = ['PENDING','APPROVED','REJECTED'].map(s =>
                    `<option value="${s}" ${item.status === s ? 'selected':''}>${s}</option>`
                ).join('');

                let smsBtn = `<button class="btn btn-sm btn-primary" onclick="openSmsModal(${index})">Send SMS</button>`;

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
                    item.claim_remarks ?? '-',
                    item.jazzcash_tid ?? '-',
                    item.jazzcash_reference_id ?? '-',
                    `<button class="btn btn-sm btn-success" onclick="updateClaimAmount('${item.claim_id}', '${item.claim_amount ?? ''}')">Update</button>`,
                      `<button class="btn btn-sm btn-success" onclick="updateRemarks('${item.claim_id}', '${item.claim_remarks ?? ''}')">Update Remarks</button>
                         ` ,

                    `<div class="d-flex gap-1 align-items-center">
                        <select class="form-control statusSelect" data-claim-id="${item.claim_id}" style="width:auto">
                            ${statusOptions}
                        </select>
                        <select class="form-control reasonSelect" data-claim-id="${item.claim_id}" style="width:auto; display:none;">
                            <option value="">Select Reason</option>
                            <option value="Not Eligible">Not Eligible</option>
                            <option value="Invalid Documents">Invalid Documents</option>
                            <option value="Ex-Gratia">Ex-Gratia</option>
                            <option value="Other">Other</option>
                        </select>
                        <input type="text" class="form-control otherReasonInput" placeholder="Enter other reason" style="display:none; width:150px;" />
                        <button class="btn btn-sm btn-warning updateStatusBtn" data-claim-id="${item.claim_id}">Update</button>
                    </div>`,
                    imagesBtn,
                    smsBtn
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
                alert(xhr.responseJSON?.message || 'Error updating amount');
            }
        });
    }
}


function updateRemarks(claim_id, claim_remarks) {
    let newAmounts = prompt("Enter new Claim Remarks:", claim_remarks || '');
    if(newAmounts !== null){
        $.ajax({
            url: `https://jazzcash-health.efulife.com/api/update-claim-remarks`,
            type: "POST",
            data: { claim_id: claim_id, claim_remarks: newAmounts},
            success: function(res){
                alert(res.message);
                fetchClaims(currentPage);
            },
            error: function(xhr){
                alert(xhr.responseJSON?.message || 'Error updating Remarks');
            }
        });
    }
}

// ==============================
// Update Status + Reason
// ==============================
$(document).on('change', '.statusSelect', function() {
    let tr = $(this).closest('tr');
    let newStatus = $(this).val();

    if(newStatus === 'REJECTED'){
        tr.find('.reasonSelect').show();
    } else {
        tr.find('.reasonSelect').hide().val('');
        tr.find('.otherReasonInput').hide().val('');
    }
});

$(document).on('change', '.reasonSelect', function() {
    let tr = $(this).closest('tr');
    if ($(this).val() === 'Other') {
        tr.find('.otherReasonInput').show();
    } else {
        tr.find('.otherReasonInput').hide().val('');
    }
});

$(document).on('click', '.updateStatusBtn', function(){
    let claim_id = $(this).data('claim-id');
    let tr = $(this).closest('tr');
    let newStatus = tr.find('.statusSelect').val();
    let remarks = '';

    if(newStatus === 'REJECTED'){
        let reason = tr.find('.reasonSelect').val();
        if(!reason){
            alert("Please select a reason for REJECTED");
            return;
        }

        if(reason === 'Other'){
            let otherReason = tr.find('.otherReasonInput').val().trim();
            if(!otherReason){
                alert("Please enter the other reason");
                return;
            }
            remarks = otherReason;
        } else {
            remarks = reason;
        }
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
            alert(xhr.responseJSON?.message || 'Error updating status');
        }
    });
});

// ==============================
// Image Modal
// ==============================
function openImageModal(index){
    let claim = window.claimsData[index];
    if(claim.images && claim.images.length > 0){
        let innerHtml = claim.images.map((img, i) => `
            <div class="carousel-item ${i === 0 ? 'active' : ''}">
                <div class="d-flex flex-column align-items-center">
                    <img src="${img}" class="d-block w-100" alt="Claim Image">
                    <a href="${img}" target="_blank" download="claim_image_${i+1}.jpg" class="btn btn-sm btn-primary mt-2">Download</a>
                </div>
            </div>
        `).join('');
        $('#carouselInner').html(innerHtml);
        $('#imageModal').modal('show');
    }
}

// ==============================
// SMS Modal
// ==============================
function openSmsModal(index){
    let claim = window.claimsData[index];
    $('#smsMsisdn').val(claim.customer_msisdn);
    $('#smsMessage').val(`Dear ${claim.customer_name}, `);
    $('#smsModal').modal('show');
}

function sendSms(){
    let msisdn = $('#smsMsisdn').val();
    let message = $('#smsMessage').val().trim();

    if(!message){
        alert('Please enter a message');
        return;
    }

    $.ajax({
        url: '{{ route("send.sms.sehat") }}',
        type: 'POST',
        data: {
            msisdn: msisdn,
            message: message,
            _token: '{{ csrf_token() }}'
        },
        success: function(res){
            alert(res.message);
            $('#smsModal').modal('hide');
        },
        error: function(xhr){
            alert(xhr.responseJSON?.message || 'Error sending SMS');
        }
    });
}


$('#exportBtn').on('click', function () {

    let startDate = $('#startDate').val();
    let endDate = $('#endDate').val();

    if (!startDate || !endDate) {
        alert('Please select start and end date');
        return;
    }

    let url = "https://jazzcash-health.efulife.com/api/customer-claims-export"
        + "?startDate=" + startDate
        + "&endDate=" + endDate;

    // ?? Trick: force browser download via hidden iframe
    let iframe = document.createElement('iframe');
    iframe.style.display = 'none';
    iframe.src = url;
    document.body.appendChild(iframe);

});


</script>
@endsection