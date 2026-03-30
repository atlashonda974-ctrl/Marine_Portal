@extends('newmaster')

@section('title', 'Invoice Management - Atlas Insurance')

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
    
    /* DataTable wrapper spacing */
    .dataTables_wrapper .row {
        margin-bottom: 10px;
    }
    
    /* DataTable filter and length positioning */
    div.dataTables_wrapper div.dataTables_filter {
        text-align: right;
    }
    
    div.dataTables_wrapper div.dataTables_length {
        text-align: left;
    }
    
    .status-badge {
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: bold;
        display: inline-block;
        text-align: center;
        min-width: 120px;
    }
    .status-pending_payment_Proof {
        background-color: #ff9900;
        color: white;
    }
    .status-pending_payment {
        background-color: #ffc107;
        color: white;
    }
    .status-payment_uploaded {
        background-color: #17a2b8;
        color: white;
    }
    .status-verified {
        background-color: #28a745;
        color: white;
    }
    .status-approved {
        background-color: #28a745;
        color: white;
    }
    .status-rejected {
        background-color: #dc3545;
        color: white;
    }
    .status-certificate_created {
        background-color: #6f42c1;
        color: white;
    }
    
    .action-buttons {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        flex-wrap: wrap;
    }
    
    .action-buttons a,
    .action-buttons span {
        font-size: 18px;
        cursor: pointer;
        transition: opacity 0.3s;
    }
    
    .action-buttons a:hover,
    .action-buttons span:hover {
        opacity: 0.7;
    }
    
    .custom-close-btn {
        background: none;
        border: none;
        font-size: 1.5rem;
        opacity: 0.7;
        transition: opacity 0.3s;
        padding: 0.5rem;
        line-height: 1;
        color: #000;
    }
    .custom-close-btn:hover {
        opacity: 1;
        color: #000;
        background: none;
    }
    
    .resend-email-btn {
        cursor: pointer;
        color: #007bff;
        transition: color 0.3s;
        position: relative;
        display: inline-block;
    }
    
    .resend-email-btn:hover {
        color: #0056b3;
    }
    
    .email-log-btn {
        cursor: pointer;
        color: #17a2b8;
        font-size: 18px;
        transition: color 0.3s;
        position: relative;
        display: inline-block;
    }
    
    .email-log-btn:hover {
        color: #138496;
    }
    
    .action-buttons-container {
        display: flex;
        align-items: center;
        justify-content: center;
        flex-wrap: wrap;
        gap: 8px;
    }
    
    td small {
        font-size: 11px;
        color: #666;
        display: block;
    }
    
    #uploadSectionHeader {
        cursor: pointer;
        transition: background-color 0.3s;
    }
    
    #uploadSectionHeader:hover {
        background-color: #f8f9fa;
    }
    
    #uploadSectionButton {
        transition: transform 0.3s ease;
    }
    
    .collapse-icon {
        transition: transform 0.3s ease;
    }
    
    .collapsed .collapse-icon {
        transform: rotate(0deg);
    }
    
    .expanded .collapse-icon {
        transform: rotate(45deg);
    }
    
    /* Center align table cells */
    #invoicesTable tbody td {
        vertical-align: middle;
        text-align: center;
        padding: 12px 8px;
    }
    
    /* Left align remarks column */
    #invoicesTable tbody td:nth-child(9) {
        text-align: left;
    }
    
    /* Center align table headers */
    #invoicesTable thead th {
        text-align: center;
        vertical-align: middle;
        padding: 12px 8px;
    }
    
    /* Payment button styling */
    .payment-btn-wrapper {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
    }
    
    .payment-btn-wrapper a,
    .payment-btn-wrapper span {
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }
    
    /* Invoice number specific styling */
    .invoice-number-cell {
        font-weight: bold;
        font-size: 14px;
    }
</style>
@endpush

@section('content')
<div class="row">
    <!-- Upload Section (Collapsible) -->
    <div class="col-12">
        <div class="card">
            <div class="card-header border-0 pb-2" id="uploadSectionHeader">
                <h4 class="card-title d-flex justify-content-between align-items-center">
                    Upload New Invoice
                    <span class="d-flex align-items-center justify-content-center bg-primary text-white rounded-circle collapse-icon" 
                          style="width: 24px; height: 24px; font-size: 12px; cursor: pointer; margin-left: 12px; margin-bottom: 2px;" 
                          id="uploadSectionButton">
                        +
                    </span>
                </h4>
            </div>
            <div class="card-body" id="uploadSectionBody" style="display: none;">
                <form id="uploadInvoiceForm" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="invoice_number" class="form-label">Invoice Number <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="invoice_number" name="invoice_number" required>
                            <div class="invalid-feedback" id="invoice_number_error"></div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="invoice_file" class="form-label">Upload Invoice <span class="text-danger">*</span></label>
                            <input type="file" class="form-control" id="invoice_file" name="invoice_file" accept=".pdf,.jpg,.jpeg,.png" required>
                            <small class="text-muted">Allowed formats: PDF, JPG, JPEG, PNG (Max: 5MB)</small>
                            <div class="invalid-feedback" id="invoice_file_error"></div>
                        </div>
                        
                        <div class="col-md-12 mb-3">
                            <label for="remarks" class="form-label">Remarks</label>
                            <textarea class="form-control" id="remarks" name="remarks" rows="3" placeholder="Enter any remarks about this invoice"></textarea>
                        </div>
                        
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary" id="saveBtn">
                                <i class="fas fa-save"></i> <span id="saveBtnText">Save Invoice</span>
                                <span id="saveBtnSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Invoices Table -->
    <div class="col-12">
        <div class="card">
            <div class="card-header border-0 pb-0 d-flex justify-content-between align-items-center">
                <h4 class="card-title">Existing Invoices</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered display" id="invoicesTable">
                        <thead style="background: #003478 !important;">
                            <tr>
                                <th style="color:#FFFFFF !important;"><strong>SR#</strong></th>
                                <th style="color:#FFFFFF !important;"><strong>Actions</strong></th>
                                <th style="color:#FFFFFF !important;"><strong>Status</strong></th>
                                <th style="color:#FFFFFF !important;"><strong>Invoice</strong></th>
                                <th style="color:#FFFFFF !important;"><strong>Printout</strong></th>
                                <th style="color:#FFFFFF !important;"><strong>Payment</strong></th>
                                <th style="color:#FFFFFF !important;"><strong>INV_Created</strong></th>
                                <th style="color:#FFFFFF !important;"><strong>Certificate Created By</strong></th>
                                <th style="color:#FFFFFF !important;"><strong>Remarks</strong></th>
                            </tr>
                        </thead>
                        <tbody id="invoicesTableBody">
                            <tr>
                                <td colspan="9" class="text-center">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Payment Proof Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload Payment Proof</h5>
                <button type="button" class="btn custom-close-btn" data-bs-dismiss="modal" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="paymentProofForm">
                    @csrf
                    <input type="hidden" id="invoice_id" name="invoice_id">
                    <div class="mb-3">
                        <label for="payment_proof" class="form-label">Payment Proof <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" id="payment_proof" name="payment_proof" accept=".pdf,.jpg,.jpeg,.png" required>
                        <small class="text-muted">Allowed formats: PDF, JPG, JPEG, PNG (Max: 5MB)</small>
                        <div class="invalid-feedback" id="payment_proof_error"></div>
                    </div>
                    <div class="mb-3">
                        <button type="submit" class="btn btn-success" id="uploadPaymentBtn">
                            <i class="fas fa-upload"></i> <span id="uploadPaymentBtnText">Upload Payment</span>
                            <span id="uploadPaymentBtnSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                        </button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </form>
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
    
    // Base URL configuration
    var baseUrl = window.location.origin + window.location.pathname.split('/').slice(0, -1).join('/');
    if (baseUrl.endsWith('/')) {
        baseUrl = baseUrl.slice(0, -1);
    }
    
    var invoicesTable = null;
    
    // Toggle upload section
    $('#uploadSectionHeader').click(function() {
        $('#uploadSectionBody').slideToggle(300);
        $('#uploadSectionButton').toggleClass('expanded');
    });
    
    // Close payment modal function
    function closePaymentModal() {
        $('#paymentModal').modal('hide');
        $('#paymentProofForm')[0].reset();
        $('#payment_proof').removeClass('is-invalid');
        $('#payment_proof_error').text('');
    }
    
    // Resend Email Function
    window.resendEmail = function(invoiceId, isPaymentEmail = false) {
        Swal.fire({
            title: 'Are you sure?',
            text: 'Do you want to resend this email?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, resend it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                AppUtils.showLoading();
                
                var url = isPaymentEmail ? 
                    baseUrl + '/invoices/' + invoiceId + '/resend-payment-email' :
                    baseUrl + '/invoices/' + invoiceId + '/resend-email';
                
                $.ajax({
                    url: url,
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        AppUtils.hideLoading();
                        if (response.success) {
                            AppUtils.showToast(response.message || 'Email resent successfully!', 'success');
                            setTimeout(function() {
                                loadInvoices();
                            }, 1500);
                        } else {
                            AppUtils.showToast(response.message || 'Failed to resend email.', 'error');
                        }
                    },
                    error: function(xhr) {
                        AppUtils.hideLoading();
                        AppUtils.showToast('Failed to resend email. Please try again.', 'error');
                    }
                });
            }
        });
    };
    
    // View Email Log Function
    window.viewEmailLog = function(invoiceId) {
        window.open(baseUrl + '/email-log/' + invoiceId, '_blank');
    };
    
    // Upload Invoice Form Submit
    $('#uploadInvoiceForm').submit(function(e) {
        e.preventDefault();
        
        // Clear previous errors
        $('.invalid-feedback').text('');
        $('.form-control').removeClass('is-invalid');
        
        // Disable submit button
        $('#saveBtn').prop('disabled', true);
        $('#saveBtnText').text('Saving...');
        $('#saveBtnSpinner').removeClass('d-none');
        
        AppUtils.showLoading();
        
        var formData = new FormData(this);
        
        $.ajax({
            url: baseUrl + '/invoices-store',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                AppUtils.hideLoading();
                
                if (response.success) {
                    // Reset form
                    $('#uploadInvoiceForm')[0].reset();
                    
                    // Show success message
                    var emailStatus = response.email_sent ? 'Email sent successfully!' : 'Invoice saved but email failed.';
                    var icon = response.email_sent ? 'success' : 'warning';
                    
                    Swal.fire({
                        icon: icon,
                        title: 'Success!',
                        html: 'Invoice uploaded successfully!<br><small>' + emailStatus + '</small>',
                        timer: 3000,
                        showConfirmButton: false
                    });
                    
                    // Reload table
                    loadInvoices();
                    
                    // Collapse upload section
                    $('#uploadSectionBody').slideUp(300);
                    $('#uploadSectionButton').removeClass('expanded');
                } else {
                    Swal.fire('Error', response.message || 'Something went wrong', 'error');
                }
                
                // Re-enable button
                $('#saveBtn').prop('disabled', false);
                $('#saveBtnText').text('Save Invoice');
                $('#saveBtnSpinner').addClass('d-none');
            },
            error: function(xhr) {
                AppUtils.hideLoading();
                
                // Re-enable button
                $('#saveBtn').prop('disabled', false);
                $('#saveBtnText').text('Save Invoice');
                $('#saveBtnSpinner').addClass('d-none');
                
                if (xhr.status === 422) {
                    // Validation errors
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        var errors = xhr.responseJSON.errors;
                        $.each(errors, function(key, value) {
                            $('#' + key + '_error').text(value[0]);
                            $('#' + key).addClass('is-invalid');
                        });
                        Swal.fire('Validation Error', 'Please fix the errors in the form', 'error');
                    }
                } else if (xhr.status === 404) {
                    Swal.fire('404 Error', 'Route not found: ' + baseUrl + '/invoices-store', 'error');
                } else {
                    Swal.fire('Error', 'Server error: ' + (xhr.responseJSON?.message || 'Unknown error'), 'error');
                }
            }
        });
    });
    
    // Load Invoices Function
    function loadInvoices() {
        $.ajax({
            url: baseUrl + '/invoices-list',
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    var html = '';
                    
                    if (response.data.length === 0) {
                        html = '<tr><td colspan="9" class="text-center">No invoices found</td></tr>';
                    } else {
                        response.data.forEach(function(invoice, index) {
                            // Status configuration
                            var statusClass = 'status-' + invoice.status;
                            var statusText = invoice.status.replace(/_/g, ' ').toUpperCase();
                            
                            // Format dates
                            var createdDate = new Date(invoice.created_at).toLocaleDateString();
                            var createdTime = new Date(invoice.created_at).toLocaleTimeString();
                            
                            // Certificate created check
                            var certificateCreated = (invoice.certificate_created == 1 || invoice.certificate_created === true);
                            var certificateNumber = invoice.tmp2 || 'N/A'; // ADDED THIS LINE
                            var transactionId = invoice.tmp1 || null;
                            
                            // Printout buttons - Show ONLY if certificate is created
                            var printoutBtn = '';
                            if (certificateCreated && transactionId) {
                                printoutBtn = '<div class="action-buttons">' +
                                    '<a href="' + baseUrl + '/print/' + transactionId + '" target="_blank" title="Print Certificate">' +
                                    '<i class="fas fa-print text-info"></i></a>' +
                                    '<a href="' + baseUrl + '/invoices/' + invoice.id + '/download" target="_blank" title="Download Invoice">' +
                                    '<i class="fas fa-download text-primary"></i></a>' +
                                    '</div>';
                            } else {
                                printoutBtn = '<span class="text-muted">Not Created</span>';
                            }
                            
                            // Invoice number column - with proper formatting
                            var invoiceNumber = '<div class="invoice-number-cell" style="text-align: center; vertical-align: middle;">' +
                                '<strong>' + invoice.invoice_number + '</strong>';
                                
                            if (certificateCreated) {
                                invoiceNumber += '<br><small class="text-success" style="font-size: 11px;">Certificate #' + certificateNumber + '</small>';
                            }
                            
                            invoiceNumber += '</div>';
                            
                            // Payment button logic
                            var paymentBtn = '';
                            
                            if (invoice.payment_proof_path && invoice.payment_proof_path !== null && invoice.payment_proof_path !== '') {
                                paymentBtn = '<div class="payment-btn-wrapper">' +
                                    '<a href="' + baseUrl + '/invoices/' + invoice.id + '/download-payment" target="_blank" title="Download Payment Proof">' +
                                    '<i class="fas fa-file-alt text-success"></i> <span>View</span></a>' +
                                    '</div>';
                            } else if (certificateCreated && invoice.status === 'payment_uploaded') {
                                paymentBtn = '<div class="payment-btn-wrapper">' +
                                    '<a href="#" class="upload-payment-btn" data-invoice-id="' + invoice.id + '" title="Upload Payment Proof">' +
                                    '<i class="fas fa-upload text-warning"></i> <span>Upload</span></a>' +
                                    '</div>';
                            } else if (!certificateCreated) {
                                paymentBtn = '<span class="text-muted">Pending Certificate</span>';
                            } else if (invoice.status === 'approved' || invoice.status === 'verified') {
                                paymentBtn = '<span class="text-success">Completed</span>';
                            } else {
                                paymentBtn = '<span class="text-muted">N/A</span>';
                            }
                            
                            // Certificate Created By
                            var certificateCreatedBy = 'N/A';
                            if (invoice.admin_action_by) {
                                certificateCreatedBy = invoice.admin_action_by;
                                if (invoice.admin_action_at) {
                                    var actionDate = new Date(invoice.admin_action_at).toLocaleDateString();
                                    var actionTime = new Date(invoice.admin_action_at).toLocaleTimeString();
                                    certificateCreatedBy += '<br><small>' + actionDate + ' ' + actionTime + '</small>';
                                }
                            } else if (certificateCreated) {
                                certificateCreatedBy = 'Admin';
                                if (invoice.certificate_created_at) {
                                    var certDate = new Date(invoice.certificate_created_at).toLocaleDateString();
                                    var certTime = new Date(invoice.certificate_created_at).toLocaleTimeString();
                                    certificateCreatedBy += '<br><small>' + certDate + ' ' + certTime + '</small>';
                                }
                            }
                            
                            // Actions column
                            var actionsHtml = '<div class="action-buttons-container">';
                            
                            // Invoice email resend
                            if (invoice.email_sent_at) {
                                actionsHtml += '<span class="resend-email-btn" onclick="resendEmail(' + invoice.id + ')" title="Resend Invoice Email">' +
                                    '<i class="fas fa-envelope text-primary"></i></span>';
                            } else {
                                actionsHtml += '<span class="resend-email-btn" onclick="resendEmail(' + invoice.id + ')" title="Resend Invoice Email (Failed)">' +
                                    '<i class="fas fa-envelope text-danger"></i></span>';
                            }
                            
                            // Payment email resend
                            if (invoice.payment_proof_path) {
                                if (invoice.payment_email_sent_at) {
                                    actionsHtml += '<span class="resend-email-btn" onclick="resendEmail(' + invoice.id + ', true)" title="Resend Payment Email">' +
                                        '<i class="fas fa-file-invoice text-success"></i></span>';
                                } else {
                                    actionsHtml += '<span class="resend-email-btn" onclick="resendEmail(' + invoice.id + ', true)" title="Resend Payment Email (Failed)">' +
                                        '<i class="fas fa-file-invoice text-danger"></i></span>';
                                }
                            }
                            
                            // Email log button
                            actionsHtml += '<span class="email-log-btn" onclick="viewEmailLog(' + invoice.id + ')" title="View Email Log">' +
                                '<i class="fas fa-history"></i></span>';
                            
                            actionsHtml += '</div>';
                            
                            // Build row with new column order
                            html += '<tr>' +
                                '<td>' + (index + 1) + '</td>' +
                                '<td>' + actionsHtml + '</td>' +
                                '<td><span class="status-badge ' + statusClass + '">' + statusText + '</span></td>' +
                                '<td>' + invoiceNumber + '</td>' +
                                '<td>' + printoutBtn + '</td>' +
                                '<td>' + paymentBtn + '</td>' +
                                '<td>' + createdDate + '<br><small>' + createdTime + '</small></td>' +
                                '<td>' + certificateCreatedBy + '</td>' +
                                '<td>' + (invoice.remarks || 'N/A') + '</td>' +
                                '</tr>';
                        });
                    }
                    
                    $('#invoicesTableBody').html(html);
                    
                    // Initialize or refresh DataTable
                    if (invoicesTable) {
                        invoicesTable.destroy();
                    }
                    
                    // Direct DataTable initialization with buttons
                    invoicesTable = $('#invoicesTable').DataTable({
                        pageLength: 10,
                        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                        order: [[0, 'asc']],
                        columnDefs: [
                            { orderable: false, targets: [1] } // Actions column not sortable
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
                                    columns: [0, 1, 2, 3, 4, 5, 6, 7, 8] // Updated to all 9 columns
                                }
                            },
                            {
                                extend: 'csv',
                                text: '<i class="fas fa-file-csv"></i> CSV',
                                className: 'btn btn-primary btn-sm me-1',
                                exportOptions: {
                                    columns: [0, 1, 2, 3, 4, 5, 6, 7, 8] // Updated to all 9 columns
                                }
                            },
                            {
                                extend: 'excel',
                                text: '<i class="fas fa-file-excel"></i> Excel',
                                className: 'btn btn-success btn-sm me-1',
                                exportOptions: {
                                    columns: [0, 1, 2, 3, 4, 5, 6, 7, 8] // Updated to all 9 columns
                                }
                            },
                            {
                                extend: 'pdf',
                                text: '<i class="fas fa-file-pdf"></i> PDF',
                                className: 'btn btn-danger btn-sm me-1',
                                orientation: 'landscape',
                                pageSize: 'A4',
                                exportOptions: {
                                    columns: [0, 1, 2, 3, 4, 5, 6, 7, 8] // Updated to all 9 columns
                                }
                            },
                            {
                                extend: 'print',
                                text: '<i class="fas fa-print"></i> Print',
                                className: 'btn btn-info btn-sm',
                                exportOptions: {
                                    columns: [0, 1, 2, 3, 4, 5, 6, 7, 8] // Updated to all 9 columns
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
                    
                } else {
                    $('#invoicesTableBody').html(
                        '<tr><td colspan="9" class="text-center text-danger">Error loading invoices: ' + 
                        (response.message || 'Unknown error') + '</td></tr>'
                    );
                }
            },
            error: function(xhr) {
                $('#invoicesTableBody').html(
                    '<tr><td colspan="9" class="text-center text-danger">Failed to load invoices. Status: ' + 
                    xhr.status + '. Check route: ' + baseUrl + '/invoices-list</td></tr>'
                );
            }
        });
    }
    
    // Upload payment button click
    $(document).on('click', '.upload-payment-btn', function(e) {
        e.preventDefault();
        var invoiceId = $(this).data('invoice-id');
        $('#invoice_id').val(invoiceId);
        $('#paymentModal').modal('show');
    });
    
    // Close modal handlers
    $(document).on('click', '.btn-secondary[data-bs-dismiss="modal"], .btn-close, .custom-close-btn', function() {
        closePaymentModal();
    });
    
    // Payment Proof Form Submit
    $('#paymentProofForm').submit(function(e) {
        e.preventDefault();
        
        // Clear errors
        $('#payment_proof_error').text('');
        $('#payment_proof').removeClass('is-invalid');
        
        // Disable button
        $('#uploadPaymentBtn').prop('disabled', true);
        $('#uploadPaymentBtnText').text('Uploading...');
        $('#uploadPaymentBtnSpinner').removeClass('d-none');
        
        AppUtils.showLoading();
        
        var formData = new FormData(this);
        var invoiceId = $('#invoice_id').val();
        
        $.ajax({
            url: baseUrl + '/invoices/' + invoiceId + '/payment-proof',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                AppUtils.hideLoading();
                
                if (response.success) {
                    closePaymentModal();
                    
                    var emailStatus = response.email_sent ? 'Email sent!' : 'Email failed';
                    var icon = response.email_sent ? 'success' : 'warning';
                    
                    Swal.fire({
                        icon: icon,
                        title: 'Success!',
                        html: 'Payment proof uploaded!<br><small>' + emailStatus + '</small>',
                        timer: 3000,
                        showConfirmButton: false
                    });
                    
                    loadInvoices();
                } else {
                    Swal.fire('Error', response.message, 'error');
                }
                
                // Re-enable button
                $('#uploadPaymentBtn').prop('disabled', false);
                $('#uploadPaymentBtnText').text('Upload Payment');
                $('#uploadPaymentBtnSpinner').addClass('d-none');
            },
            error: function(xhr) {
                AppUtils.hideLoading();
                
                // Re-enable button
                $('#uploadPaymentBtn').prop('disabled', false);
                $('#uploadPaymentBtnText').text('Upload Payment');
                $('#uploadPaymentBtnSpinner').addClass('d-none');
                
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    $.each(errors, function(key, value) {
                        $('#' + key + '_error').text(value[0]);
                        $('#' + key).addClass('is-invalid');
                    });
                    Swal.fire('Validation Error', 'Please check the form', 'error');
                } else {
                    Swal.fire('Error', 'Something went wrong: ' + xhr.status, 'error');
                }
            }
        });
    });
    
    // Initial load
    loadInvoices();
    
    // Auto-refresh every 30 seconds
    setInterval(function() {
        loadInvoices();
    }, 30000);
});
</script>
@endpush