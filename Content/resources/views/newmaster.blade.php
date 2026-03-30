<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="keywords" content="" />
    <meta name="author" content="" />
    <meta name="robots" content="" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Atlas Insurance - Admin Dashboard" />
    <meta property="og:title" content="Atlas Insurance - Admin Dashboard" />
    <meta property="og:description" content="Atlas Insurance - Admin Dashboard" />
    <meta property="og:image" content="social-image.png" />
    <meta name="format-detection" content="telephone=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Atlas Insurance - Marine Certificate')</title>

    <!-- CSS Files -->
    <link href="{{ URL::asset('vendor/jqvmap/css/jqvmap.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ URL::asset('vendor/chartist/css/chartist.min.css') }}">
    <link href="{{ URL::asset('vendor/bootstrap-select/dist/css/bootstrap-select.min.css') }}" rel="stylesheet">
    
    <!-- DataTables CSS -->
    <link href="{{ URL::asset('vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('vendor/datatables/css/buttons.dataTables.min.css') }}" rel="stylesheet">
    
    <!-- Date Picker CSS -->
    <link href="{{ URL::asset('vendor/bootstrap-daterangepicker/daterangepicker.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('vendor/bootstrap-datepicker/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
    
    <!-- Icons -->
    <link href="{{ URL::asset('css/lineicon.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Main Styles -->
    <link href="{{ URL::asset('css/style.css') }}" rel="stylesheet">
    
    <!-- Page Specific CSS -->
    @stack('styles')
    
    <style>
        /* Toast/Message Styles */
        .toast-container {
            position: fixed;
            top: 80px;
            right: 20px;
            z-index: 9999;
        }
        .alert-dismissible {
            padding-right: 3rem;
        }
        .alert {
            margin-bottom: 1rem;
            border-radius: 0.375rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        }
        
        /* DataTable Custom Styles */
        .dataTables_wrapper .dataTables_filter input {
            margin-left: 0.5em;
            border-radius: 4px;
            padding: 5px 10px;
        }
        .dataTables_wrapper .dataTables_length select {
            border-radius: 4px;
            padding: 5px;
        }
        
        /* Loading Overlay */
        .loading-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.8);
            z-index: 9998;
            justify-content: center;
            align-items: center;
        }
        .loading-overlay.active {
            display: flex;
        }
    </style>
</head>
<body>

<?php
use Illuminate\Support\Facades\Session;

function hasPassed30Daysmaster() {
    $userDate = Session::get('user')['updated_at'] ?? null;
    
    if (is_null($userDate)) {
        return true;
    }
    
    $givenDate = new DateTime($userDate);
    $currentDate = new DateTime();
    $difference = $currentDate->diff($givenDate);
    
    return $difference->days >= 25 && $difference->invert == 1;
}
?>

<!-- Loading Overlay -->
<div class="loading-overlay" id="loadingOverlay">
    <div class="spinner-border text-primary" role="status">
        <span class="sr-only">Loading...</span>
    </div>
</div>

<!-- Toast Container for Messages -->
<div class="toast-container" id="toastContainer"></div>

<!-- Header -->
{{ View::make('header') }}

<!-- Navbar -->
@if(!hasPassed30Daysmaster())
    {{ View::make('navbar') }}
@endif

<!-- Main Content -->
<div class="content-body">
    <div class="container-fluid">
        
        <!-- Display Laravel Session Messages -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>Success!</strong> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Error!</strong> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('warning'))
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <strong>Warning!</strong> {{ session('warning') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('info'))
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <strong>Info!</strong> {{ session('info') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Validation Errors!</strong>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        
        <!-- Page Content -->
        @yield('content')
    </div>
</div>

<!-- Core JavaScript Libraries (Load in correct order) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="{{ asset('vendor/global/global.min.js') }}"></script>
<script src="{{ asset('vendor/bootstrap-select/dist/js/bootstrap-select.min.js') }}"></script>

<!-- DataTables Core -->
<script src="{{ asset('vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/js/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/js/buttons.html5.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/js/buttons.print.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/js/jszip.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/js/pdfmake.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/js/vfs_fonts.js') }}"></script>

<!-- Date Picker Scripts -->
<script src="{{ asset('vendor/moment/moment.min.js') }}"></script>
<script src="{{ asset('vendor/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
<script src="{{ asset('vendor/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>

<!-- AutoNumeric for number formatting -->
<script src="https://cdn.jsdelivr.net/npm/autonumeric@4.6.0"></script>

<!-- Chart Libraries -->
<script src="{{ asset('vendor/chart.js/Chart.bundle.min.js') }}"></script>
<script src="{{ asset('vendor/apexchart/apexchart.js') }}"></script>
<script src="{{ asset('vendor/peity/jquery.peity.min.js') }}"></script>
<script src="{{ asset('vendor/chartist/js/chartist.min.js') }}"></script>

<!-- Other Plugins -->
<script src="{{ asset('vendor/svganimation/vivus.min.js') }}"></script>
<script src="{{ asset('vendor/svganimation/svg.animation.js') }}"></script>

<!-- Custom Scripts -->
<script src="{{ asset('js/custom.min.js') }}"></script>
<script src="{{ asset('js/deznav-init.js') }}"></script>

<!-- Global Utilities Script -->
<script>
(function($) {
    'use strict';
    
    // Global namespace for app utilities
    window.AppUtils = {
        
        // DataTable Default Configuration
        dataTableConfig: {
            pageLength: 25,
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rtip',
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
            responsive: true,
            order: [[0, 'desc']]
        },
        
        // DataTable with Export Buttons Configuration
        dataTableWithExport: function(customConfig = {}) {
            let config = $.extend(true, {}, this.dataTableConfig, {
                dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                     '<"row"<"col-sm-12 col-md-6"B>>' +
                     'rtip',
                buttons: [
                    {
                        extend: 'copy',
                        className: 'btn btn-primary btn-sm',
                        exportOptions: {
                            columns: ':visible'
                        }
                    },
                    {
                        extend: 'csv',
                        className: 'btn btn-primary btn-sm',
                        exportOptions: {
                            columns: ':visible'
                        }
                    },
                    {
                        extend: 'excel',
                        className: 'btn btn-primary btn-sm',
                        exportOptions: {
                            columns: ':visible'
                        }
                    },
                    {
                        extend: 'pdf',
                        className: 'btn btn-primary btn-sm',
                        exportOptions: {
                            columns: ':visible'
                        }
                    },
                    {
                        extend: 'print',
                        className: 'btn btn-primary btn-sm',
                        exportOptions: {
                            columns: ':visible'
                        }
                    }
                ]
            }, customConfig);
            
            return config;
        },
        
        // Initialize DataTable
        initDataTable: function(selector, customConfig = {}, withExport = true) {
            let config = withExport ? 
                this.dataTableWithExport(customConfig) : 
                $.extend(true, {}, this.dataTableConfig, customConfig);
            
            // Destroy existing DataTable if it exists
            if ($.fn.DataTable.isDataTable(selector)) {
                $(selector).DataTable().destroy();
            }
            
            return $(selector).DataTable(config);
        },
        
        // Show Toast Message
        showToast: function(message, type = 'success', duration = 5000) {
            const types = {
                success: { icon: 'fa-check-circle', class: 'alert-success' },
                error: { icon: 'fa-times-circle', class: 'alert-danger' },
                warning: { icon: 'fa-exclamation-triangle', class: 'alert-warning' },
                info: { icon: 'fa-info-circle', class: 'alert-info' }
            };
            
            const config = types[type] || types.success;
            const toastId = 'toast-' + Date.now();
            
            const toast = $(`
                <div id="${toastId}" class="alert ${config.class} alert-dismissible fade show" role="alert">
                    <i class="fas ${config.icon} me-2"></i>
                    <strong>${message}</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `);
            
            $('#toastContainer').append(toast);
            
            setTimeout(function() {
                $(`#${toastId}`).fadeOut(300, function() {
                    $(this).remove();
                });
            }, duration);
        },
        
        // Show Loading Overlay
        showLoading: function() {
            $('#loadingOverlay').addClass('active');
        },
        
        // Hide Loading Overlay
        hideLoading: function() {
            $('#loadingOverlay').removeClass('active');
        },
        
        // Initialize Date Range Picker
        initDateRangePicker: function(selector, options = {}) {
            const defaultOptions = {
                autoUpdateInput: false,
                locale: {
                    format: 'YYYY-MM-DD',
                    cancelLabel: 'Clear'
                }
            };
            
            const config = $.extend({}, defaultOptions, options);
            
            $(selector).daterangepicker(config);
            
            $(selector).on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
            });
            
            $(selector).on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');
            });
        },
        
        // Initialize Single Date Picker
        initDatePicker: function(selector, options = {}) {
            const defaultOptions = {
                format: 'yyyy-mm-dd',
                autoclose: true,
                todayHighlight: true
            };
            
            const config = $.extend({}, defaultOptions, options);
            
            $(selector).datepicker(config);
        },
        
        // Confirm Delete Action
        confirmDelete: function(message = 'Are you sure you want to delete this item?') {
            return confirm(message);
        },
        
        // AJAX Form Submit with Loading
        ajaxFormSubmit: function(formSelector, successCallback, errorCallback) {
            $(formSelector).on('submit', function(e) {
                e.preventDefault();
                
                AppUtils.showLoading();
                
                $.ajax({
                    url: $(this).attr('action'),
                    method: $(this).attr('method'),
                    data: new FormData(this),
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        AppUtils.hideLoading();
                        if (successCallback) {
                            successCallback(response);
                        }
                    },
                    error: function(xhr) {
                        AppUtils.hideLoading();
                        if (errorCallback) {
                            errorCallback(xhr);
                        } else {
                            AppUtils.showToast('An error occurred. Please try again.', 'error');
                        }
                    }
                });
            });
        }
    };
    
    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut('slow', function() {
            $(this).remove();
        });
    }, 5000);
    
    // Global Settings
    function getUrlParams(dParam) {
        var dPageURL = window.location.search.substring(1),
            dURLVariables = dPageURL.split('&'),
            dParameterName,
            i;

        for (i = 0; i < dURLVariables.length; i++) {
            dParameterName = dURLVariables[i].split('=');

            if (dParameterName[0] === dParam) {
                return dParameterName[1] === undefined ? true : decodeURIComponent(dParameterName[1]);
            }
        }
    }
    
    var direction = getUrlParams('dir');
    if(direction != 'rtl') {
        direction = 'ltr';
    }
    
    var dezSettingsOptions = {
        typography: "roboto",
        version: "light",
        layout: "vertical",
        headerBg: "color_1",
        navheaderBg: "color_3",
        sidebarBg: "color_1",
        sidebarStyle: "mini",
        sidebarPosition: "fixed",
        headerPosition: "fixed",
        containerLayout: "wide",
        direction: direction
    };
    
    new dezSettings(dezSettingsOptions);
    
    $(window).on('resize', function() {
        var sidebar = 'mini';
        var screenWidth = $(window).width();
        if(screenWidth < 600) {
            sidebar = 'overlay';
        }
        dezSettingsOptions.sidebarStyle = sidebar;
        new dezSettings(dezSettingsOptions);
    });

})(jQuery);
</script>

<!-- Page Specific Scripts -->
@stack('scripts')

</body>
</html>