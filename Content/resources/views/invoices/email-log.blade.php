@extends('master')
@section('content')

<!-- Add Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<div class="content-body">
    <div class="container-fluid">
        <div class="row">
            <div class="col-xl-12 col-xxl-12 col-lg-12 col-md-12">
                <div class="card">
                    <div class="card-header border-0 pb-0 d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="card-title">Email Log for Invoice #<span id="invoiceNumber">PKR0078</span></h4>
                            <p class="mb-0 text-muted" id="invoiceDetails"></p>
                        </div>
                        <div>
                            <button type="button" class="btn btn-primary me-2" id="refreshLog">
                                <i class="fas fa-sync-alt"></i> Refresh
                            </button>
                            <a href="{{ route('invoices.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="emailLogContent">
                            <!-- Email log will be loaded here -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* ULTRA FORCE WHITE TEXT ON ALL BADGE ELEMENTS - MAXIMUM SPECIFICITY */
    .email-log-item .email-type,
    .email-log-item .email-type *,
    .email-log-item .email-type i,
    .email-log-item .email-type span,
    .email-log-item .email-status-badge,
    .email-log-item .email-status-badge *,
    .email-log-item .email-status-badge i,
    .email-log-item .email-status-badge span,
    .email-log-item .recent-badge,
    .email-log-item .recent-badge *,
    .email-log-item .recent-badge i,
    .email-log-item .recent-badge span,
    .email-log-item .attempt-badge,
    .email-log-item .attempt-badge *,
    .email-log-item .attempt-badge i,
    .email-log-item .attempt-badge span {
        color: #ffffff !important;
        text-shadow: 0 1px 2px rgba(0,0,0,0.4) !important;
    }
    
    /* Additional layer of protection */
    span.email-type,
    span.email-type *,
    span.email-status-badge,
    span.email-status-badge *,
    span.recent-badge,
    span.recent-badge *,
    span.attempt-badge,
    span.attempt-badge * {
        color: #ffffff !important;
    }
    
    .email-log-item {
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 15px;
        background-color: #f8f9fa;
        transition: all 0.3s ease;
    }
    
    .email-log-item:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        transform: translateY(-2px);
    }
    
    .email-log-item.recent {
        border: 2px solid #007bff;
        background-color: #e7f3ff;
        box-shadow: 0 4px 12px rgba(0,123,255,0.15);
    }
    
    .email-log-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
        padding-bottom: 10px;
        border-bottom: 1px solid #dee2e6;
    }
    
    /* EMAIL TYPE BADGE - WHITE TEXT FORCED */
    .email-type {
        padding: 6px 14px !important;
        border-radius: 20px !important;
        font-size: 12px !important;
        font-weight: bold !important;
        display: inline-flex !important;
        align-items: center !important;
        color: #ffffff !important;
        text-shadow: 0 1px 2px rgba(0,0,0,0.4) !important;
    }
    
    .email-type i {
        margin-right: 6px !important;
        font-size: 11px !important;
        color: #ffffff !important;
        text-shadow: 0 1px 2px rgba(0,0,0,0.4) !important;
    }
    
    /* EMAIL TYPE BADGE COLORS */
    .type-invoice {
        background: linear-gradient(135deg, #4361ee, #3a56d4) !important;
    }
    
    .type-payment {
        background: linear-gradient(135deg, #4cc9f0, #3ab5d9) !important;
    }
    
    .type-payment_confirmation {
        background: linear-gradient(135deg, #3a0ca3, #2f0b82) !important;
    }
    
    .type-payment_uploaded {
        background: linear-gradient(135deg, #7209b7, #5d0892) !important;
    }
    
    .type-reminder {
        background: linear-gradient(135deg, #f72585, #d41e6f) !important;
    }
    
    .type-other {
        background: linear-gradient(135deg, #6c757d, #5a6268) !important;
    }
    
    /* STATUS BADGE - WHITE TEXT FORCED */
    .email-status-badge {
        padding: 6px 14px !important;
        border-radius: 20px !important;
        font-size: 12px !important;
        font-weight: bold !important;
        display: inline-flex !important;
        align-items: center !important;
        color: #ffffff !important;
        text-shadow: 0 1px 2px rgba(0,0,0,0.4) !important;
    }
    
    .email-status-badge i {
        margin-right: 6px !important;
        font-size: 11px !important;
        color: #ffffff !important;
        text-shadow: 0 1px 2px rgba(0,0,0,0.4) !important;
    }
    
    /* STATUS BADGE COLORS */
    .status-sent {
        background: linear-gradient(135deg, #2ecc71, #27ae60) !important;
    }
    
    .status-success {
        background: linear-gradient(135deg, #2ecc71, #27ae60) !important;
    }
    
    .status-failed {
        background: linear-gradient(135deg, #e74c3c, #c0392b) !important;
    }
    
    .status-error {
        background: linear-gradient(135deg, #e74c3c, #c0392b) !important;
    }
    
    .status-pending {
        background: linear-gradient(135deg, #f39c12, #d35400) !important;
    }
    
    .status-queued {
        background: linear-gradient(135deg, #3498db, #2980b9) !important;
    }
    
    .status-processing {
        background: linear-gradient(135deg, #9b59b6, #8e44ad) !important;
    }
    
    .email-details {
        font-size: 14px;
    }
    
    .email-details p {
        margin-bottom: 5px;
    }
    
    .detail-row {
        display: flex;
        margin-bottom: 5px;
    }
    
    .detail-label {
        font-weight: bold;
        width: 140px;
        min-width: 140px;
        color: #212529 !important;
    }
    
    .detail-value {
        flex: 1;
        color: #212529 !important;
    }
    
    .no-logs {
        text-align: center;
        padding: 40px;
        color: #6c757d !important;
    }
    
    .spinner-border {
        display: block;
        margin: 40px auto;
    }
    
    .email-id {
        font-size: 11px;
        color: #6c757d !important;
        font-family: monospace;
        background-color: #f8f9fa;
        padding: 2px 6px;
        border-radius: 4px;
    }
    
    /* RECENT BADGE - WHITE TEXT FORCED */
    .recent-badge {
        background: linear-gradient(135deg, #007bff, #0056b3) !important;
        padding: 4px 12px !important;
        border-radius: 10px !important;
        font-size: 10px !important;
        font-weight: bold !important;
        margin-left: 8px !important;
        display: inline-flex !important;
        align-items: center !important;
        color: #ffffff !important;
        text-shadow: 0 1px 2px rgba(0,0,0,0.4) !important;
    }
    
    .recent-badge i {
        margin-right: 4px !important;
        font-size: 9px !important;
        color: #ffffff !important;
        text-shadow: 0 1px 2px rgba(0,0,0,0.4) !important;
    }
    
    .timestamp {
        font-size: 12px;
        color: #6c757d !important;
    }
    
    /* ATTEMPT BADGE - WHITE TEXT FORCED */
    .attempt-badge {
        padding: 4px 12px !important;
        border-radius: 10px !important;
        font-size: 11px !important;
        font-weight: bold !important;
        margin-left: 8px !important;
        background: linear-gradient(135deg, #34495e, #2c3e50) !important;
        color: #ffffff !important;
        text-shadow: 0 1px 2px rgba(0,0,0,0.4) !important;
    }
    
    /* Fix for card text */
    .card-title,
    .card-header h4,
    .card-header .card-title,
    .card-body,
    .card-body * {
        color: #212529 !important;
    }
    
    /* Override any inherited white text */
    h4, h5, h6, p, span:not(.email-type):not(.email-status-badge):not(.recent-badge):not(.attempt-badge), div, strong, small {
        color: #212529 !important;
    }
    
    /* Specific override for alert text */
    .alert-danger,
    .alert-danger * {
        color: #721c24 !important;
    }
    
    .alert-danger i {
        color: #721c24 !important;
    }
</style>

<script>
$(document).ready(function() {
    // Get invoice ID from URL
    var pathArray = window.location.pathname.split('/');
    var invoiceId = pathArray[pathArray.length - 1];
    
    // SIMPLE URL CONSTRUCTION - Use the original working method
    var baseUrl = window.location.origin + window.location.pathname.split('/').slice(0, -2).join('/');
    if (baseUrl.endsWith('/')) {
        baseUrl = baseUrl.slice(0, -1);
    }
    
    // Load email log
    function loadEmailLog() {
        $('#emailLogContent').html(`
            <div class="text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
                <p class="mt-2">Loading email log...</p>
            </div>
        `);
        
        $.ajax({
            url: baseUrl + '/email-log/' + invoiceId + '/get',
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    // Set invoice number
                    $('#invoiceNumber').text(response.invoice_number || 'PKR0078');
                    
                    if (response.email_logs.length === 0) {
                        $('#emailLogContent').html(`
                            <div class="no-logs">
                                <i class="fas fa-envelope fa-3x mb-3"></i>
                                <h4>No Email Logs Found</h4>
                                <p>No email activity has been recorded for this invoice.</p>
                            </div>
                        `);
                    } else {
                        // Build email logs
                        var html = '';
                        
                        response.email_logs.forEach(function(log, index) {
                            var typeClass = 'type-other';
                            var typeText = log.email_type;
                            var typeIcon = 'fas fa-envelope';
                            
                            // Handle different email types
                            if (log.email_type === 'invoice') {
                                typeClass = 'type-invoice';
                                typeText = 'Invoice Email';
                                typeIcon = 'fas fa-file-invoice';
                            } else if (log.email_type === 'payment') {
                                typeClass = 'type-payment';
                                typeText = 'Payment Email';
                                typeIcon = 'fas fa-money-bill-wave';
                            } else if (log.email_type === 'payment_confirmation') {
                                typeClass = 'type-payment_confirmation';
                                typeText = 'Payment Confirmation';
                                typeIcon = 'fas fa-check-circle';
                            } else if (log.email_type === 'payment_uploaded') {
                                typeClass = 'type-payment_uploaded';
                                typeText = 'Payment Uploaded Email';
                                typeIcon = 'fas fa-upload';
                            } else if (log.email_type === 'reminder') {
                                typeClass = 'type-reminder';
                                typeText = 'Reminder Email';
                                typeIcon = 'fas fa-bell';
                            } else if (log.email_type === 'unopened') {
                                typeClass = 'type-reminder';
                                typeText = 'Payment Unopened Email';
                                typeIcon = 'fas fa-eye-slash';
                            }
                            
                            // Status badge
                            var statusClass = 'status-pending';
                            var statusText = log.status;
                            var statusIcon = 'fas fa-clock';
                            
                            if (log.status === 'sent' || log.status === 'success') {
                                statusClass = 'status-sent';
                                statusText = 'Sent Successfully';
                                statusIcon = 'fas fa-check-circle';
                            } else if (log.status === 'failed' || log.status === 'error') {
                                statusClass = 'status-failed';
                                statusText = 'Failed to Send';
                                statusIcon = 'fas fa-times-circle';
                            } else if (log.status === 'queued') {
                                statusClass = 'status-queued';
                                statusText = 'Queued';
                                statusIcon = 'fas fa-hourglass-half';
                            } else if (log.status === 'processing') {
                                statusClass = 'status-processing';
                                statusText = 'Processing';
                                statusIcon = 'fas fa-cog fa-spin';
                            }
                            
                            // Format dates
                            var sentAt = log.sent_at ? new Date(log.sent_at).toLocaleString() : 'Not sent';
                            var createdAt = new Date(log.created_at).toLocaleString();
                            
                            // Add "Recent" badge for most recent email
                            var recentBadge = index === 0 ? '<span class="recent-badge"><i class="fas fa-star"></i> MOST RECENT</span>' : '';
                            
                            // Add attempt badge if attempt_count exists
                            var attemptBadge = '';
                            if (log.attempt_count) {
                                attemptBadge = `<span class="attempt-badge">Attempt: ${log.attempt_count}</span>`;
                            } else if (log.attempts) {
                                attemptBadge = `<span class="attempt-badge">Attempt: ${log.attempts}</span>`;
                            }
                            
                            html += `
                                <div class="email-log-item ${index === 0 ? 'recent' : ''}">
                                    <div class="email-log-header">
                                        <div>
                                            <span class="email-type ${typeClass}"><i class="${typeIcon}"></i> ${typeText}</span>
                                            ${recentBadge}
                                        </div>
                                        
                                    </div>
                                    <div class="email-details">
                                        <div class="detail-row">
                                            <span class="detail-label">Status:</span>
                                            <span class="detail-value">
                                                <span class="email-status-badge ${statusClass}"><i class="${statusIcon}"></i> ${statusText}</span>
                                                ${attemptBadge}
                                            </span>
                                        </div>
                                        <div class="detail-row">
                                            <span class="detail-label">Recipient:</span>
                                            <span class="detail-value"><i class="fas fa-user-circle me-1"></i> ${log.recipient_email || 'N/A'}</span>
                                        </div>
                                        <div class="detail-row">
                                            <span class="detail-label">Sent At:</span>
                                            <span class="detail-value"><i class="fas fa-calendar-alt me-1"></i> ${sentAt}</span>
                                        </div>
                                        <div class="detail-row">
                                            <span class="detail-label">Invoice ID:</span>
                                            <span class="detail-value"><i class="fas fa-file-invoice me-1"></i> ${log.invoice_id}</span>
                                        </div>
                                        <div class="detail-row">
                                            <span class="detail-label">Attempt Count:</span>
                                            <span class="detail-value"><i class="fas fa-redo me-1"></i> ${log.attempt_count || log.attempts || '1'}</span>
                                        </div>
                                        ${log.subject ? `
                                        <div class="detail-row">
                                            <span class="detail-label">Subject:</span>
                                            <span class="detail-value"><i class="fas fa-tag me-1"></i> ${log.subject}</span>
                                        </div>
                                        ` : ''}
                                        ${log.error_message ? `
                                        <div class="detail-row">
                                            <span class="detail-label">Error:</span>
                                            <span class="detail-value"><i class="fas fa-exclamation-triangle me-1"></i> ${log.error_message}</span>
                                        </div>
                                        ` : ''}
                                    </div>
                                </div>
                            `;
                        });
                        
                        $('#emailLogContent').html(html);
                    }
                } else {
                    $('#emailLogContent').html(`
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle"></i>
                            Failed to load email log: ${response.message || 'Unknown error'}
                        </div>
                    `);
                }
            },
            error: function(xhr) {
                $('#emailLogContent').html(`
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i>
                        Error loading email log. Please try again later.
                    </div>
                `);
            }
        });
    }
    
    // Refresh button
    $('#refreshLog').click(function() {
        loadEmailLog();
        $(this).find('i').addClass('fa-spin');
        setTimeout(() => {
            $(this).find('i').removeClass('fa-spin');
        }, 1000);
    });
    
    // Initial load
    loadEmailLog();
});
</script>
@endsection