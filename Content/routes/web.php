<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\MainController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\EmailLogController;
use App\Http\Controllers\InvoiceWebController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['middleware'=>"web"], function(){
    
    // ========== PUBLIC ROUTES (No Authentication Required) ==========
    Route::match(['get', 'post'], '/login', [UserController::class, 'login'])->name('login');
    Route::match(['get', 'post'], '/makeHash', [UserController::class, 'makeHash']);
    Route::match(['get', 'post'], '/testQuery', [UserController::class, 'testQuery']);
    
    // ========== LOGOUT ==========
    Route::get('/logout', function () {
        Session::forget('user');
        if(session('status')){
            return redirect('/login')->with('status', 'Password Change. Login with new credentials');
        }else{
            return redirect('/login');
        }
    })->name('logout');

    // ========== CHANGE PASSWORD ==========
    Route::match(['get', 'post'], '/changePassword', [UserController::class, 'changePassword'])->name('changePassword');

    // ========== HOME ROUTE (Root:Redirects based on user route) ==========
    Route::match(['get', 'post'],'/', function() { 
        $user = Session::get('user'); 
        
        // If not logged in, redirect to login
        if(!$user) {
            return redirect()->route('login');
        }
        
        // Redirect based on user's route field
        if(isset($user['route']) && $user['route'] == 'invoice'){
            return redirect()->route('invoices.index');
        }
        
        if(isset($user['route']) && $user['route'] == 'form'){
            return app(MainController::class)->main(request());
        }
        
        // Default: show main controller
        return app(MainController::class)->main(request()); 
    })->name('home');

    // ========== MARINE FORM ROUTES (for users with route='form') ==========
    Route::match(['get', 'post'],'addInsured', [TransactionController::class, 'addInsured'])->name('addInsured');
    Route::match(['get', 'post'],'viewInsured/{id}', [TransactionController::class, 'viewInsured'])->name('viewInsured');
    Route::match(['get', 'post'],'print/{id}', [TransactionController::class, 'print_performa'])->name('printPerforma');
    Route::match(['get', 'post'],'sendemail/{id}', [TransactionController::class, 'sendViaNativeMail'])->name('sendEmail');
    Route::match(['get', 'post'],'datewiseRep', [ReportController::class, 'dateWiseReport'])->name('dateWiseReport');
    Route::match(['get', 'post'],'colReport', [ReportController::class, 'colReport'])->name('colReport');

    // ========== INVOICE ROUTES (for users with route='invoice') ==========
    
    // Main invoice page (Web interface)
    Route::get('/invoices', [InvoiceWebController::class, 'index'])->name('invoices.index');

    // Invoice AJAX/API routes
    Route::get('/invoices-list', [InvoiceController::class, 'index'])->name('invoices.list');
    Route::post('/invoices-store', [InvoiceController::class, 'store'])->name('invoices.store');
    Route::post('/invoices/{id}/payment-proof', [InvoiceController::class, 'uploadPaymentProof'])->name('invoices.payment-proof');
    Route::get('/invoices/{id}/download', [InvoiceController::class, 'downloadInvoice'])->name('invoices.download');
    Route::get('/invoices/{id}/download-payment', [InvoiceController::class, 'downloadPaymentProof'])->name('invoices.download-payment');
    Route::post('/invoices/{id}/resend-email', [InvoiceController::class, 'resendEmail'])->name('invoices.resend-email');
    Route::post('/invoices/{id}/resend-payment-email', [InvoiceController::class, 'resendPaymentEmail'])->name('invoices.resend-payment-email');

    // Email log routes
    Route::get('/email-log/{invoice}', [InvoiceController::class, 'emailLog'])->name('email.log');
    Route::get('/email-log/{invoice}/get', [InvoiceController::class, 'getEmailLog'])->name('email.log.get');

    // ========== ADMIN INVOICE MANAGEMENT ROUTES ==========
    Route::get('/admin/invoices', [InvoiceController::class, 'adminIndex'])->name('admin.invoices');
    Route::post('/admin/invoices/{id}/reject', [TransactionController::class, 'adminReject'])->name('admin.invoices.reject');
    Route::post('/admin/invoices/{id}/edit-reject-remark', [TransactionController::class, 'editRejectRemark'])->name('admin.invoices.editRejectRemark');
    Route::post('/admin/invoices/{id}/unreject', [InvoiceController::class, 'unreject'])->name('admin.invoices.unreject');
    Route::get('/admin/invoices/{id}/check-certificate', [TransactionController::class, 'checkCertificateCreation'])->name('admin.invoices.checkCertificate');
    Route::get('/admin/invoices/{id}/certificate-details', [TransactionController::class, 'getCertificateDetails'])->name('admin.invoices.getCertificateDetails');
    Route::post('/admin/invoices/{id}/update-certificate-details', [TransactionController::class, 'updateCertificateDetails'])->name('admin.invoices.updateCertificateDetails');

    // ========== DEBUG/TEST ROUTES ==========
    Route::get('/debug/invoice/{id}', [TransactionController::class, 'debugUpdateInvoice'])->name('debug.invoice');
    
    Route::get('/test-route', function() {
        return response()->json(['message' => 'Routes are working!']);
    });
    
    Route::get('/check-controller', function() {
        if (class_exists(InvoiceController::class)) {
            return response()->json(['message' => 'InvoiceController exists']);
        } else {
            return response()->json(['message' => 'InvoiceController NOT found'], 404);
        }
    });
    
    // ========== DATABASE INSPECTION ROUTES ==========
    Route::get('/cinvoices', function () {
        try {
            // Check if table exists using a simple query
            $tableExists = false;
            try {
                DB::select('SELECT 1 FROM invoices LIMIT 1');
                $tableExists = true;
            } catch (\Exception $e) {
                $tableExists = false;
            }
            
            if ($tableExists) {
                // Get basic info without Schema facade
                $columnsInfo = DB::select("SHOW COLUMNS FROM invoices");
                $columns = [];
                $columnDetails = [];
                
                foreach ($columnsInfo as $column) {
                    $columns[] = $column->Field;
                    $columnDetails[$column->Field] = [
                        'type' => $column->Type,
                        'nullable' => $column->Null === 'YES',
                        'key' => $column->Key,
                        'default' => $column->Default,
                        'extra' => $column->Extra,
                    ];
                }
                
                $records = DB::table('invoices')->get();
                
                return response()->json([
                    'table_exists' => true,
                    'columns' => $columns,
                    'column_details' => $columnDetails,
                    'records' => $records,
                    'record_count' => DB::table('invoices')->count(),
                ]);
            } else {
                return response()->json([
                    'table_exists' => false,
                    'message' => 'invoices table not found or not accessible'
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    });
    
    Route::get('/email_log', function () {
        if (Schema::hasTable('users')) {
            $columns = Schema::getColumnListing('users');
            $records = DB::table('users')->get();
            return response()->json([
                'table_exists' => true,
                'columns' => $columns,
                'records' => $records,
            ]);
        } else {
            return response()->json([
                'table_exists' => false,
                'message' => 'email_logs table not found'
            ]);
        }
    });

    // ========== API ENDPOINT: Get Current User ==========
    Route::get('/api/current-user', function() {
        $user = Session::get('user');
        
        if($user) {
            return response()->json([
                'logged_in' => true,
                'user' => $user,
                'id' => $user->id ?? $user['id'] ?? null,
                'name' => $user->name ?? $user['name'] ?? null,
                'email' => $user->email ?? $user['email'] ?? null,
                'role' => $user->role ?? $user['role'] ?? null,
                'route' => $user->route ?? $user['route'] ?? null,
                'location' => $user->location ?? $user['location'] ?? null,
                'client_code' => $user->client_code ?? $user['client_code'] ?? null,
            ]);
        } else {
            return response()->json([
                'logged_in' => false,
                'message' => 'No user logged in'
            ], 401);
        }
    })->name('api.current-user');

    // ========== API ENDPOINT: Check Authentication ==========
    Route::get('/api/check-auth', function() {
        return response()->json([
            'authenticated' => Session::has('user'),
            'user' => Session::get('user')
        ]);
    })->name('api.check-auth');

    // ========== API ENDPOINT: Get User Attribute ==========
    Route::get('/api/user/{attribute}', function($attribute) {
        $user = Session::get('user');
        
        if(!$user) {
            return response()->json(['error' => 'Not logged in'], 401);
        }
        
        // Check if attribute exists (handle both object and array)
        if(is_object($user) && isset($user->$attribute)) {
            return response()->json([
                $attribute => $user->$attribute
            ]);
        } elseif(is_array($user) && isset($user[$attribute])) {
            return response()->json([
                $attribute => $user[$attribute]
            ]);
        } else {
            return response()->json([
                'error' => 'Attribute not found'
            ], 404);
        }
    })->name('api.user-attribute');
});