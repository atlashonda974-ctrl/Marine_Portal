@extends('newmaster')

@section('title', 'Admin Invoice Management - Atlas Insurance')

@push('styles')
<!-- DataTables Buttons CSS from CDN -->
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">

<style>
    /* DataTables Buttons Container */
    .dt-buttons {
        margin-bottom: 15px !important;
        margin-top: 10px !important;
        display: inline-block !important;
        float: right !important;
    }
    
    /* Clear float after buttons */
    .dataTables_wrapper .row:has(.dt-buttons)::after {
        content: "";
        display: table;
        clear: both;
    }
    
    /* Individual Button Styling */
    .dt-buttons .btn {
        margin-right: 5px !important;
        margin-bottom: 5px !important;
        font-size: 13px !important;
        padding: 6px 12px !important;
        border-radius: 4px !important;
        display: inline-block !important;
    }
    
    .dt-buttons .btn i {
        margin-right: 5px;
    }
    
    /* Button colors */
    .dt-buttons .btn-primary {
        background-color: #003478 !important;
        border-color: #003478 !important;
        color: white !important;
    }
    
    .dt-buttons .btn-success {
        background-color: #28a745 !important;
        border-color: #28a745 !important;
        color: white !important;
    }
    
    .dt-buttons .btn-danger {
        background-color: #dc3545 !important;
        border-color: #dc3545 !important;
        color: white !important;
    }
    
    .dt-buttons .btn-info {
        background-color: #17a2b8 !important;
        border-color: #17a2b8 !important;
        color: white !important;
    }
    
    /* Hover effects */
    .dt-buttons .btn:hover {
        opacity: 0.85 !important;
        transform: translateY(-1px);
        transition: all 0.3s;
    }

    .badge {
        font-size: 12px;
        font-weight: bold;
        padding: 8px 12px;
        border-radius: 20px;
    }
    .bg-warning {
        background-color: #ff9900 !important;
    }
    .bg-info {
        background-color: #17a2b8 !important;
    }
    .bg-success {
        background-color: #28a745 !important;
    }
    .bg-danger {
        background-color: #dc3545 !important;
    }
    .btn-group-sm .btn {
        font-size: 12px;
        padding: 5px 10px;
    }
    
    .user-remarks {
        color: #495057;
        font-style: italic;
    }
    
    .admin-remarks {
        color: #dc3545;
        font-weight: 500;
    }
    
    .remarks-content {
        max-height: 300px;
        overflow-y: auto;
    }
    
    .action-buttons-group {
        display: flex;
        gap: 5px;
        flex-wrap: wrap;
        justify-content: center;
    }
    
    /* Center align specific columns */
    #adminInvoicesTable tbody td:nth-child(1),  /* SR# */
    #adminInvoicesTable tbody td:nth-child(2),  /* Action */
    #adminInvoicesTable tbody td:nth-child(3),  /* Status */
    #adminInvoicesTable tbody td:nth-child(4),  /* Invoice */
    #adminInvoicesTable tbody td:nth-child(5),  /* Action By */
    #adminInvoicesTable tbody td:nth-child(6) { /* Created */
        text-align: center;
        vertical-align: middle;
    }
    
    /* Left align remarks columns */
    #adminInvoicesTable tbody td:nth-child(7),  /* User Remarks */
    #adminInvoicesTable tbody td:nth-child(8) { /* Admin Remarks */
        text-align: left;
        vertical-align: middle;
    }
    
    /* Center align all headers */
    #adminInvoicesTable thead th {
        text-align: center;
        vertical-align: middle;
    }
    
    /* Status badge wrapper */
    .badge-wrapper {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header border-0 pb-0">
                <h4 class="card-title">Admin Invoice Management</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered display" id="adminInvoicesTable">
                        <thead style="background: #003478 !important;">
                            <tr>
                                <th style="color:#FFFFFF !important;"><strong>SR#</strong></th>
                                <th style="color:#FFFFFF !important;"><strong>Action</strong></th>
                                <th style="color:#FFFFFF !important;"><strong>Status</strong></th>
                                <th style="color:#FFFFFF !important;"><strong>Invoice</strong></th>
                                <th style="color:#FFFFFF !important;"><strong>Action By</strong></th>
                                <th style="color:#FFFFFF !important;"><strong>INV_Created</strong></th>
                                <th style="color:#FFFFFF !important;"><strong>User Remarks</strong></th>
                                <th style="color:#FFFFFF !important;"><strong>Admin Remarks</strong></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($invoices as $index => $invoice)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    @if($invoice->status === 'rejected')
                                        <div class="action-buttons-group">
                                            <button class="btn btn-xs btn-warning" onclick="editRejectRemark({{ $invoice->id }})">
                                                <i class="fas fa-edit"></i> Edit Remark
                                            </button>
                                            <button class="btn btn-xs btn-success" onclick="unrejectInvoice({{ $invoice->id }})">
                                                <i class="fas fa-undo"></i> Unreject
                                            </button>
                                        </div>
                                    @elseif($invoice->certificate_created)
                                        <div class="btn-group btn-group-sm" role="group">
                                            <button type="button" class="btn btn-success" disabled title="Certificate Already Created">
                                                <i class="fas fa-check-circle"></i> Certificate Created
                                            </button>
                                            <button type="button" class="btn btn-danger" 
                                                    onclick="rejectInvoice({{ $invoice->id }})" 
                                                    title="Reject Invoice">
                                                <i class="fas fa-times"></i> Reject
                                            </button>
                                        </div>
                                    @else
                                        <div class="btn-group btn-group-sm" role="group">
                                            <button type="button" class="btn btn-primary" 
                                                    onclick="createCertificate({{ $invoice->id }})" 
                                                    title="Create Certificate">
                                                <i class="fas fa-file-certificate"></i> Create Certificate
                                            </button>
                                            <button type="button" class="btn btn-danger" 
                                                    onclick="rejectInvoice({{ $invoice->id }})" 
                                                    title="Reject Invoice">
                                                <i class="fas fa-times"></i> Reject
                                            </button>
                                        </div>
                                    @endif
                                </td>
                                <td>
    @php
        $statusClass = '';
        $statusText = '';
        
        // Check certificate_created first - this is the main indicator
        if ($invoice->certificate_created == 1) {
            $statusClass = 'bg-success';
            $statusText = 'APPROVED';
        } else {
            // Other statuses only if certificate not created
            switch(strtolower($invoice->status)) {
                case 'pending_payment':
                case 'pending_payment_proof':
                    $statusClass = 'bg-warning';
                    $statusText = 'PENDING PAYMENT';
                    break;
                case 'payment_uploaded':
                    $statusClass = 'bg-info';
                    $statusText = 'PAYMENT UPLOADED';
                    break;
                case 'approved':
                    $statusClass = 'bg-success';
                    $statusText = 'APPROVED';
                    break;
                case 'rejected':
                    $statusClass = 'bg-danger';
                    $statusText = 'REJECTED';
                    break;
                default:
                    $statusClass = 'bg-secondary';
                    $statusText = strtoupper($invoice->status);
            }
        }
    @endphp
    <span class="badge {{ $statusClass }} p-2">{{ $statusText }}</span>
    
    @if($invoice->certificate_created)
        <br><small class="text-success">
            <i class="fas fa-file-certificate"></i> 
            Cert #{{ $invoice->tmp2 ?? 'N/A' }}
        </small>
    @endif
</td>
                                <td>
                                    <strong>{{ $invoice->invoice_number }}</strong>
                                </td>
                                <td>
                                    {{ $invoice->admin_action_by ?? 'N/A' }}<br>
                                    @if($invoice->admin_action_at)
                                        <small>{{ $invoice->admin_action_at->format('Y-m-d h:i A') }}</small>
                                    @endif
                                </td>
                                <td>
                                    {{ $invoice->created_at->format('Y-m-d') }}<br>
                                    <small>{{ $invoice->created_at->format('h:i A') }}</small>
                                </td>
                                <td id="userRemark_{{ $invoice->id }}">
                                    @if($invoice->remarks)
                                        <span class="user-remarks" title="{{ $invoice->remarks }}">
                                            {{ Str::limit($invoice->remarks, 50) }}
                                            @if(strlen($invoice->remarks) > 50)
                                                <a href="javascript:void(0);" onclick="showFullRemarks({{ $invoice->id }}, '{{ addslashes($invoice->remarks) }}', 'User Remarks')" class="text-primary">
                                                    <small>[View full]</small>
                                                </a>
                                            @endif
                                        </span>
                                    @else
                                        <span class="text-muted">No remarks</span>
                                    @endif
                                </td>
                                <td id="adminRemark_{{ $invoice->id }}">
                                    @if($invoice->admin_remark)
                                        <span class="admin-remarks" title="{{ $invoice->admin_remark }}">
                                            {{ Str::limit($invoice->admin_remark, 50) }}
                                            @if(strlen($invoice->admin_remark) > 50)
                                                <a href="javascript:void(0);" onclick="showFullRemarks({{ $invoice->id }}, '{{ addslashes($invoice->admin_remark) }}', 'Admin Remarks')" class="text-primary">
                                                    <small>[View full]</small>
                                                </a>
                                            @endif
                                        </span>
                                    @else
                                        <span class="text-muted">No remarks yet</span>
                                    @endif
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

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reject Invoice</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="rejectForm">
                    @csrf
                    <input type="hidden" id="reject_invoice_id" name="invoice_id">
                    <div class="mb-3">
                        <label for="admin_remark" class="form-label">Reason for Rejection <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="admin_remark" name="admin_remark" rows="4" placeholder="Enter the reason for rejecting this invoice..." required></textarea>
                        <small class="text-muted">This remark will be shown to the user.</small>
                        <div class="invalid-feedback" id="admin_remark_error"></div>
                    </div>
                    <div class="mb-3">
                        <button type="submit" class="btn btn-danger" id="rejectBtn">
                            <i class="fas fa-times"></i> <span id="rejectBtnText">Reject Invoice</span>
                            <span id="rejectBtnSpinner" class="spinner-border spinner-border-sm d-none"></span>
                        </button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Full Remarks Modal -->
<div class="modal fade" id="fullRemarksModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="remarksModalTitle"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label id="remarksModalLabel" class="form-label"></label>
                    <div class="remarks-content p-3 border rounded bg-light" id="fullRemarksContent" style="min-height: 150px; white-space: pre-wrap; word-wrap: break-word;">
                        <!-- Remarks will be displayed here -->
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- Load DataTables Buttons from CDN (since local files are not working) -->
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    // Initialize DataTable with export buttons
    var adminTable = $('#adminInvoicesTable').DataTable({
        pageLength: 10,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        order: [[0, 'asc']],
        columnDefs: [
            { orderable: false, targets: [1] } // Action column not sortable
        ],
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
             '<"row"<"col-sm-12 text-end"B>>' +
             '<"row"<"col-sm-12"tr>>' +
             '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
        buttons: [
            {
                extend: 'copy',
                text: '<i class="fas fa-copy"></i> Copy',
                className: 'btn btn-primary btn-sm me-1',
                exportOptions: {
                    columns: [0, 2, 3, 4, 5, 6, 7] // Exclude Action column (index 1)
                }
            },
            {
                extend: 'csv',
                text: '<i class="fas fa-file-csv"></i> CSV',
                className: 'btn btn-primary btn-sm me-1',
                exportOptions: {
                    columns: [0, 2, 3, 4, 5, 6, 7]
                }
            },
            {
                extend: 'excel',
                text: '<i class="fas fa-file-excel"></i> Excel',
                className: 'btn btn-success btn-sm me-1',
                exportOptions: {
                    columns: [0, 2, 3, 4, 5, 6, 7]
                }
            },
            {
                extend: 'pdf',
                text: '<i class="fas fa-file-pdf"></i> PDF',
                className: 'btn btn-danger btn-sm me-1',
                orientation: 'landscape',
                pageSize: 'A4',
                exportOptions: {
                    columns: [0, 2, 3, 4, 5, 6, 7]
                }
            },
            {
                extend: 'print',
                text: '<i class="fas fa-print"></i> Print',
                className: 'btn btn-info btn-sm',
                exportOptions: {
                    columns: [0, 2, 3, 4, 5, 6, 7]
                }
            }
        ],
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search records...",
            lengthMenu: "Show _MENU_ entries",
            info: "Showing _START_ to _END_ of _TOTAL_ entries",
            infoEmpty: "Showing 0 to 0 of 0 entries",
            infoFiltered: "(filtered from _MAX_ total entries)",
            paginate: {
                first: "First",
                last: "Last",
                next: "Next",
                previous: "Previous"
            }
        },
        responsive: true
    });
});

// Show full remarks in modal
window.showFullRemarks = function(invoiceId, remarks, type) {
    $('#remarksModalTitle').text(type + ' - Invoice #' + invoiceId);
    $('#remarksModalLabel').text(type + ':');
    $('#fullRemarksContent').text(remarks);
    $('#fullRemarksModal').modal('show');
};

// Reject invoice function
window.rejectInvoice = function(invoiceId) {
    $('#reject_invoice_id').val(invoiceId);
    $('#admin_remark').val('');
    $('#admin_remark').removeClass('is-invalid');
    $('#admin_remark_error').text('');
    $('#rejectModal').modal('show');
};

// Edit reject remark function
window.editRejectRemark = function(invoiceId) {
    var currentRemark = $('#adminRemark_' + invoiceId).text().trim();
    
    // Remove "No remarks yet" text if it exists
    if (currentRemark === 'No remarks yet' || currentRemark === '') {
        currentRemark = '';
    } else {
        // Get the full remark from title attribute
        var $remarkSpan = $('#adminRemark_' + invoiceId).find('.admin-remarks');
        if ($remarkSpan.length) {
            currentRemark = $remarkSpan.attr('title') || currentRemark;
        }
    }
    
    $('#reject_invoice_id').val(invoiceId);
    $('#admin_remark').val(currentRemark);
    $('#admin_remark').removeClass('is-invalid');
    $('#admin_remark_error').text('');
    $('#rejectModal').modal('show');
};

// Unreject invoice function
window.unrejectInvoice = function(invoiceId) {
    Swal.fire({
        title: 'Unreject Invoice?',
        html: 'This will restore the invoice to <strong>Payment Uploaded</strong> status.<br>' +
              '<small class="text-info">' +
              '<i class="fas fa-info-circle"></i> The admin remark will be cleared and the <strong>Create Certificate</strong> button will be available again.' +
              '</small>',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="fas fa-undo"></i> Yes, Unreject',
        cancelButtonText: 'Cancel',
        showLoaderOnConfirm: true,
        preConfirm: () => {
            return $.ajax({
                url: '{{ route("admin.invoices.unreject", ["id" => ":id"]) }}'.replace(':id', invoiceId),
                type: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                }
            }).then(response => {
                if (!response.success) {
                    throw new Error(response.message || 'Failed to unreject invoice');
                }
                return response;
            }).catch(error => {
                Swal.showValidationMessage(
                    error.message || 'Request failed'
                );
            });
        },
        allowOutsideClick: () => !Swal.isLoading()
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                icon: 'success',
                title: 'Unrejected!',
                text: 'Invoice has been restored successfully',
                timer: 1500,
                showConfirmButton: false
            }).then(() => {
                location.reload();
            });
        }
    });
};

// Create certificate function
window.createCertificate = function(invoiceId) {
    Swal.fire({
        title: 'Create Certificate?',
        html: 'You will be redirected to certificate creation page.<br>' +
              '<small class="text-success">' +
              '<i class="fas fa-info-circle"></i> Invoice will be <strong>auto-approved</strong> when certificate is successfully created.' +
              '</small>',
        icon: 'info',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#d33',
        confirmButtonText: '<i class="fas fa-file-certificate"></i> Go to Certificate Creation',
        cancelButtonText: 'Cancel',
        showLoaderOnConfirm: true,
        preConfirm: () => {
            return new Promise((resolve) => {
                // Check if invoice can have certificate created
                $.ajax({
                    url: '{{ route("admin.invoices.checkCertificate", ["id" => ":id"]) }}'.replace(':id', invoiceId),
                    type: 'GET',
                    success: function(response) {
                        if (response.can_create) {
                            resolve();
                        } else {
                            Swal.showValidationMessage(response.message || 'Cannot create certificate for this invoice');
                        }
                    },
                    error: function() {
                        Swal.showValidationMessage('Error checking invoice status');
                    }
                });
            });
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Redirect to addInsured page with invoice ID as parameter
            window.location.href = '{{ url("addInsured") }}?invoice_id=' + invoiceId;
        }
    });
};

// Edit certificate details
window.editCertificateDetails = function(invoiceId) {
    // Fetch current certificate details
    $.ajax({
        url: '{{ route("admin.invoices.getCertificateDetails", ["id" => ":id"]) }}'.replace(':id', invoiceId),
        type: 'GET',
        success: function(response) {
            if (response.success) {
                $('#certificate_invoice_id').val(invoiceId);
                $('#certificate_number').val(response.certificate_number || '');
                $('#transaction_id').val(response.transaction_id || '');
                $('#certificateDetailsModal').modal('show');
            } else {
                AppUtils.showToast(response.message, 'error');
            }
        },
        error: function() {
            AppUtils.showToast('Failed to load certificate details', 'error');
        }
    });
};

// Reject form submission
$('#rejectForm').submit(function(e) {
    e.preventDefault();
    
    var invoiceId = $('#reject_invoice_id').val();
    var adminRemark = $('#admin_remark').val().trim();
    
    // Validation
    if (adminRemark.length < 3) {
        $('#admin_remark').addClass('is-invalid');
        $('#admin_remark_error').text('Remark must be at least 3 characters');
        AppUtils.showToast('Please enter a valid remark (min 3 characters)', 'warning');
        return;
    }
    
    // Clear errors
    $('#admin_remark').removeClass('is-invalid');
    $('#admin_remark_error').text('');
    
    // Disable submit button
    $('#rejectBtn').prop('disabled', true);
    $('#rejectBtnText').text('Rejecting...');
    $('#rejectBtnSpinner').removeClass('d-none');
    
    AppUtils.showLoading();
    
    $.ajax({
        url: '{{ route("admin.invoices.reject", ["id" => ":id"]) }}'.replace(':id', invoiceId),
        type: 'POST',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            admin_remark: adminRemark
        },
        success: function(response) {
            AppUtils.hideLoading();
            
            // Re-enable button
            $('#rejectBtn').prop('disabled', false);
            $('#rejectBtnText').text('Reject Invoice');
            $('#rejectBtnSpinner').addClass('d-none');
            
            if (response.success) {
                // Close modal
                $('#rejectModal').modal('hide');
                
                // Show success message
                Swal.fire({
                    icon: 'success',
                    title: 'Rejected!',
                    text: 'Invoice rejected successfully',
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    // Reload page
                    location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.message || 'Failed to reject invoice'
                });
            }
        },
        error: function(xhr) {
            AppUtils.hideLoading();
            
            // Re-enable button
            $('#rejectBtn').prop('disabled', false);
            $('#rejectBtnText').text('Reject Invoice');
            $('#rejectBtnSpinner').addClass('d-none');
            
            if (xhr.status === 422) {
                // Validation error
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    var errors = xhr.responseJSON.errors;
                    if (errors.admin_remark) {
                        $('#admin_remark').addClass('is-invalid');
                        $('#admin_remark_error').text(errors.admin_remark[0]);
                    }
                }
                AppUtils.showToast('Validation Error: Please enter a valid remark', 'error');
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to reject invoice. Please try again.'
                });
            }
        }
    });
});

// Certificate details form submission
$('#certificateDetailsForm').submit(function(e) {
    e.preventDefault();
    
    var invoiceId = $('#certificate_invoice_id').val();
    var certificateNumber = $('#certificate_number').val();
    var transactionId = $('#transaction_id').val();
    
    AppUtils.showLoading();
    
    $.ajax({
        url: '{{ route("admin.invoices.updateCertificateDetails", ["id" => ":id"]) }}'.replace(':id', invoiceId),
        type: 'POST',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            certificate_number: certificateNumber,
            transaction_id: transactionId
        },
        success: function(response) {
            AppUtils.hideLoading();
            
            if (response.success) {
                $('#certificateDetailsModal').modal('hide');
                AppUtils.showToast('Certificate details updated successfully', 'success');
                location.reload();
            } else {
                AppUtils.showToast(response.message, 'error');
            }
        },
        error: function() {
            AppUtils.hideLoading();
            AppUtils.showToast('Failed to update certificate details', 'error');
        }
    });
});

// Close modal and clear form
$('#rejectModal').on('hidden.bs.modal', function() {
    $('#rejectForm')[0].reset();
    $('#admin_remark').removeClass('is-invalid');
    $('#admin_remark_error').text('');
    $('#rejectBtn').prop('disabled', false);
    $('#rejectBtnText').text('Reject Invoice');
    $('#rejectBtnSpinner').addClass('d-none');
});
</script>
@endpush