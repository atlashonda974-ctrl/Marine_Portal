<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\EmailLog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;


class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = Invoice::orderBy('created_at', 'desc')->get();
        return response()->json([
            'success' => true,
            'data' => $invoices
        ]);
    }
    
    public function store(Request $request)
    {
        // Simple validation
        if (!$request->invoice_number) {
            return response()->json([
                'success' => false,
                'message' => 'Invoice number is required',
                'errors' => ['invoice_number' => ['Invoice number is required']]
            ], 422);
        }
        
        if (!$request->hasFile('invoice_file')) {
            return response()->json([
                'success' => false,
                'message' => 'Invoice file is required',
                'errors' => ['invoice_file' => ['Invoice file is required']]
            ], 422);
        }
        
        try {
            // Create directories
            $invoiceDir = public_path('uploads/invoices');
            if (!file_exists($invoiceDir)) {
                mkdir($invoiceDir, 0777, true);
            }
            
            $file = $request->file('invoice_file');
            $fileName = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            $file->move($invoiceDir, $fileName);
            
            // Create invoice
            $invoice = Invoice::create([
                'invoice_number' => $request->invoice_number,
                'invoice_file_path' => 'uploads/invoices/' . $fileName,
                'invoice_file_name' => $file->getClientOriginalName(),
                'remarks' => $request->remarks,
                'status' => 'pending_payment_Proof'
            ]);
            
            // Try to send email with proper configuration
            $emailSent = $this->sendInvoiceCreatedEmail($invoice);
            
            EmailLog::create([
                'invoice_id' => $invoice->id,
                'email_type' => 'invoice_created',
                'recipient_email' => 'owais.zahid@ail.atlas.pk',
                'status' => $emailSent ? 'sent' : 'failed'
            ]);
            
            if ($emailSent) {
                $invoice->update(['email_sent_at' => now()]);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Invoice saved successfully',
                'data' => $invoice,
                'email_sent' => $emailSent
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
    
    private function sendInvoiceCreatedEmail(Invoice $invoice)
    {
        try {
            ini_set("SMTP", "vqs3572.pair.com");
            
            $emailFrom = 'owais.zahid@ail.atlas.pk';
            $emailCC = 'owais.zahid@ail.atlas.pk';
            ini_set("sendmail_from", $emailFrom);

            $headers = "From: AIL - Invoice Portal <$emailFrom>\r\n";
            $headers .= "Cc: $emailCC\r\n";
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

            $userName = session('user.name', 'System Administrator');
            $portalLink = url('/invoices');

            $message = '<html><body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">';
            $message .= '<div style="max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 10px;">';
            $message .= '<h2 style="color: #0062cc; border-bottom: 2px solid #0062cc; padding-bottom: 10px;">New Invoice Uploaded</h2>';
            $message .= '<p>Dear Admin,</p>';
            $message .= '<p>A new invoice has been uploaded to the AIL Invoice Portal. Please review the details below:</p>';
            
            $message .= '<div style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin: 20px 0;">';
            $message .= '<table style="width: 100%; border-collapse: collapse;">';
            $message .= '<tr><td style="padding: 8px 0; font-weight: bold; width: 150px;">Invoice Number:</td><td style="padding: 8px 0;">' . htmlspecialchars($invoice->invoice_number) . '</td></tr>';
            $message .= '<tr><td style="padding: 8px 0; font-weight: bold;">Upload Date:</td><td style="padding: 8px 0;">' . $invoice->created_at->format('Y-m-d H:i:s') . '</td></tr>';
            $message .= '<tr><td style="padding: 8px 0; font-weight: bold;">Remarks:</td><td style="padding: 8px 0;">' . htmlspecialchars($invoice->remarks ?? 'N/A') . '</td></tr>';
            $message .= '<tr><td style="padding: 8px 0; font-weight: bold;">Status:</td><td style="padding: 8px 0;"><span style="color: #ff9900; font-weight: bold;">Pending Payment</span></td></tr>';
            $message .= '<tr><td style="padding: 8px 0; font-weight: bold;">Portal Link:</td><td style="padding: 8px 0;"><a href="' . $portalLink . '" style="color: #0062cc; text-decoration: none;">' . $portalLink . '</a></td></tr>';
            $message .= '</table>';
            $message .= '</div>';
            
            $message .= '<p>Please log in to the portal to review the invoice and wait for payment proof to be uploaded.</p>';
          $message .= '<p style="margin-top: 30px;">Best regards,<br><strong>' . htmlspecialchars($userName) . '</strong></p>';
            $message .= '</div>';
            $message .= '</body></html>';

            $subject = "New Invoice Uploaded - " . $invoice->invoice_number;

            $mailResult = mail('owais.zahid@ail.atlas.pk', $subject, $message, $headers);

            if (!$mailResult) {
                // Log::warning('Failed to send invoice email for: ' . $invoice->invoice_number);
            }

            return $mailResult;

        } catch (\Exception $e) {
            // Log::error('Error sending invoice email: ' . $e->getMessage());
            return false;
        }
    }
    
    public function uploadPaymentProof(Request $request, $id)
    {
        try {
            $invoice = Invoice::findOrFail($id);
            
            if (!$request->hasFile('payment_proof')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment proof is required'
                ], 422);
            }
            
            $paymentDir = public_path('uploads/payments');
            if (!file_exists($paymentDir)) {
                mkdir($paymentDir, 0777, true);
            }
            
            $file = $request->file('payment_proof');
            $fileName = time() . '_payment_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            $file->move($paymentDir, $fileName);
            
            $invoice->update([
                'payment_proof_path' => 'uploads/payments/' . $fileName,
                'payment_proof_name' => $file->getClientOriginalName(),
                'status' => 'payment_uploaded'
            ]);
            
            // Try to send email with proper configuration
            $emailSent = $this->sendPaymentUploadedEmail($invoice);
            
            EmailLog::create([
                'invoice_id' => $invoice->id,
                'email_type' => 'payment_uploaded',
                'recipient_email' => 'owais.zahid@ail.atlas.pk',
                'status' => $emailSent ? 'sent' : 'failed'
            ]);
            
            if ($emailSent) {
                $invoice->update(['payment_email_sent_at' => now()]);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Payment proof uploaded',
                'data' => $invoice,
                'email_sent' => $emailSent
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
    
    private function sendPaymentUploadedEmail(Invoice $invoice)
    {
        try {
            ini_set("SMTP", "vqs3572.pair.com");
            
            $emailFrom = 'owais.zahid@ail.atlas.pk';
            $emailCC = 'owais.zahid@ail.atlas.pk';
            ini_set("sendmail_from", $emailFrom);

            $headers = "From: AIL - Invoice Portal <$emailFrom>\r\n";
            $headers .= "Cc: $emailCC\r\n";
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

            $userName = session('user.name', 'System Administrator');
            $portalLink = url('/invoices');

            $message = '<html><body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">';
            $message .= '<div style="max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 10px;">';
            $message .= '<h2 style="color: #28a745; border-bottom: 2px solid #28a745; padding-bottom: 10px;">Payment Proof Uploaded</h2>';
            $message .= '<p>Dear Admin,</p>';
            $message .= '<p>Payment proof has been uploaded for an invoice on the AIL Invoice Portal. Please review the details below:</p>';
            
            $message .= '<div style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin: 20px 0;">';
            $message .= '<table style="width: 100%; border-collapse: collapse;">';
            $message .= '<tr><td style="padding: 8px 0; font-weight: bold; width: 150px;">Invoice Number:</td><td style="padding: 8px 0;">' . htmlspecialchars($invoice->invoice_number) . '</td></tr>';
            $message .= '<tr><td style="padding: 8px 0; font-weight: bold;">Invoice Date:</td><td style="padding: 8px 0;">' . $invoice->created_at->format('Y-m-d H:i:s') . '</td></tr>';
            $message .= '<tr><td style="padding: 8px 0; font-weight: bold;">Payment Uploaded:</td><td style="padding: 8px 0;">' . now()->format('Y-m-d H:i:s') . '</td></tr>';
            $message .= '<tr><td style="padding: 8px 0; font-weight: bold;">Remarks:</td><td style="padding: 8px 0;">' . htmlspecialchars($invoice->remarks ?? 'N/A') . '</td></tr>';
            $message .= '<tr><td style="padding: 8px 0; font-weight: bold;">Status:</td><td style="padding: 8px 0;"><span style="color: #17a2b8; font-weight: bold;">Payment Uploaded - Awaiting Verification</span></td></tr>';
            $message .= '<tr><td style="padding: 8px 0; font-weight: bold;">Portal Link:</td><td style="padding: 8px 0;"><a href="' . $portalLink . '" style="color: #0062cc; text-decoration: none;">' . $portalLink . '</a></td></tr>';
            $message .= '</table>';
            $message .= '</div>';
            
            $message .= '<p>Please log in to the portal to verify the payment proof and approve/reject the payment.</p>';
            $message .= '<p style="margin-top: 30px;">Best regards,<br><strong>' . htmlspecialchars($userName) . '</strong></p>';
            $message .= '</div>';
            $message .= '</body></html>';

            $subject = "Payment Proof Uploaded - " . $invoice->invoice_number;

            $mailResult = mail('owais.zahid@ail.atlas.pk', $subject, $message, $headers);

            if (!$mailResult) {
                // Log::warning('Failed to send payment email for: ' . $invoice->invoice_number);
            }

            return $mailResult;

        } catch (\Exception $e) {
            // Log::error('Error sending payment email: ' . $e->getMessage());
            return false;
        }
    }
    
    public function downloadInvoice($id)
    {
        $invoice = Invoice::findOrFail($id);
        $filePath = public_path($invoice->invoice_file_path);
        
        if (!file_exists($filePath)) {
            abort(404, 'File not found');
        }
        
        return response()->download($filePath, $invoice->invoice_file_name);
    }
    
    public function downloadPaymentProof($id)
    {
        $invoice = Invoice::findOrFail($id);
        $filePath = public_path($invoice->payment_proof_path);
        
        if (!file_exists($filePath)) {
            abort(404, 'File not found');
        }
        
        return response()->download($filePath, $invoice->payment_proof_name);
    }
    
    /**
     * Resend invoice created email
     */
    public function resendEmail($id)
    {
        try {
            $invoice = Invoice::findOrFail($id);
            
            // Try to send email with proper configuration
            $emailSent = $this->sendInvoiceCreatedEmail($invoice);
            
            EmailLog::create([
                'invoice_id' => $invoice->id,
                'email_type' => 'invoice_created_resend',
                'recipient_email' => 'owais.zahid@ail.atlas.pk',
                'status' => $emailSent ? 'sent' : 'failed'
            ]);
            
            if ($emailSent) {
                $invoice->update(['email_sent_at' => now()]);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Invoice email resent successfully',
                'email_sent' => $emailSent
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Resend payment uploaded email
     */
    public function resendPaymentEmail($id)
    {
        try {
            $invoice = Invoice::findOrFail($id);
            
            // Try to send email with proper configuration
            $emailSent = $this->sendPaymentUploadedEmail($invoice);
            
            EmailLog::create([
                'invoice_id' => $invoice->id,
                'email_type' => 'payment_uploaded_resend',
                'recipient_email' => 'owais.zahid@ail.atlas.pk',
                'status' => $emailSent ? 'sent' : 'failed'
            ]);
            
            if ($emailSent) {
                $invoice->update(['payment_email_sent_at' => now()]);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Payment email resent successfully',
                'email_sent' => $emailSent
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
    
//  public function AddInsured(Request $request)
// {
//     // Get user session data
//     $userRole = Session::get('user')['role'] ?? null;
//     $userid = Session::get('user')['name'] ?? 'Admin';
//     $client_code = Session::get('user')['client_code'] ?? null;
    
//     // Get invoice_id from query parameter (when coming from admin)
//     $invoiceId = $request->query('invoice_id') ?? $request->invoice_id ?? null;
    
//     // Store invoice_id in session for POST request
//     if ($invoiceId && $request->isMethod('get')) {
//         Session::put('admin_invoice_id', $invoiceId);
//     }
    
//     // Get invoice data if available
//     $invoiceData = null;
//     if ($invoiceId) {
//         $invoiceData = Invoice::find($invoiceId);
//         if ($invoiceData) {
//             // Check if certificate already created
//             if ($invoiceData->certificate_created) {
//                 Session::flash('error', 'Certificate already created for this invoice!');
//                 return redirect()->route('admin.invoices');
//             }
            
//             // Check if invoice is rejected
//             if ($invoiceData->status === 'rejected') {
//                 Session::flash('error', 'Cannot create certificate for rejected invoice!');
//                 return redirect()->route('admin.invoices');
//             }
//         }
//     }
    
//     // Generate next certificate number
//     $next_cvrnum = 1;
//     $last_cvrnum_row = \App\Models\Transaction::where('created_by', $userid)
//         ->orderBy('id', 'desc')
//         ->first();
//     if ($last_cvrnum_row) {
//         $next_cvrnum = $last_cvrnum_row->portal_no + 1;
//     }
    
//     // Handle POST request (certificate creation)
//     if($request->isMethod('post')){
//         // Validate required fields
//         $request->validate([
//             'openpolnumber' => 'required|string',
//             'client_name' => 'required|string',
//             'vessel_code' => 'required|string',
//             'vessel_name' => 'required|string',
//             'voyage_from' => 'required|string',
//             'voyage_to' => 'required|string',
//             'commencement_date' => 'required|date',
//             'cargo_description' => 'required|string',
//             'amount_insured' => 'required|numeric',
//             'premium' => 'required|numeric',
//             'premium_currency' => 'required|string',
//             'premium_currency_rate' => 'required|numeric',
//         ]);
        
//         // Get admin invoice ID from session
//         $adminInvoiceId = Session::get('admin_invoice_id');
        
//         // Get user name for approval
//         $adminUserName = auth()->user()->name ?? Session::get('user')['name'] ?? 'Admin';
        
//         // Create transaction with ALL form data
//         $trans = \App\Models\Transaction::create(array_merge(
//             $request->all(),
//             [
//                 'created_by' => $userid,
//                 'invoice_id' => $adminInvoiceId,
//                 'portal_no' => $next_cvrnum,
//                 'app_date' => now()->format('Y-m-d'),
//                 'transaction_date' => now()->format('Y-m-d H:i:s'),
//                 'status' => 'completed'
//             ]
//         ));
    
//         if($trans->id){
//             $lastInsertedId = $trans->id;
            
//             // Save perils if any
//             if ($request->has('peril_id')) {
//                 foreach ($request->peril_id as $peril) {
//                     if (!empty($peril)) {
//                         \App\Models\TransactionPeril::create([
//                             'transaction_id' => $lastInsertedId,
//                             'peril_id' => $peril,
//                             'created_at' => now(),
//                             'updated_at' => now()
//                         ]);
//                     }
//                 }
//             }
            
//             // APPROVE THE INVOICE HERE when certificate is saved
//             if ($adminInvoiceId) {
//                 $invoice = Invoice::find($adminInvoiceId);
//                 if ($invoice) {
//                     // Update invoice - approve it now with user name
//                     $invoice->update([
//                         'certificate_transaction_id' => $lastInsertedId,
//                         'certificate_number' => $trans->portal_no,
//                         'certificate_generated_at' => now(),
//                         'certificate_created' => true,
//                         'status' => 'approved',
//                         'admin_action_by' => $adminUserName, // Store user name
//                         'admin_action_at' => now()
//                     ]);
                    
//                     // Send certificate created email
//                     $this->sendCertificateCreatedEmail($invoice, $trans);
//                 }
                
//                 // Clear the session after use
//                 Session::forget('admin_invoice_id');
//             }
            
//             // Redirect based on where it came from
//             if ($adminInvoiceId || $userRole === 'admin') {
//                 Session::flash('success', 'Certificate #' . $trans->portal_no . ' created successfully! Invoice is now approved.');
//                 return redirect()->route('admin.invoices');
//             } else {
//                 return redirect('/sendemail/' . $lastInsertedId);
//             }
//         }
//         else{
//             Session::flash('error', 'Certificate creation failed!');
//             return back()->withInput();
//         }
//     }

//     // GET request - Load form data
//     $vessels = \Illuminate\Support\Facades\Cache::remember('api_data_vessels', now()->addHours(96), function () {
//         $urlVessel = "http://172.16.22.204/Marine/api/vessels.php";
//         $responseVessel = @file_get_contents($urlVessel);
//         return $responseVessel ? json_decode($responseVessel) : [];
//     });

//     $perils = \Illuminate\Support\Facades\Cache::remember('api_data_perils', now()->addHours(96), function () {
//         $urlPeril = "http://172.16.22.204/Marine/api/getAllperil.php";
//         $responsePeril = @file_get_contents($urlPeril);
//         return $responsePeril ? json_decode($responsePeril) : [];
//     });

//     $mop_banks = \Illuminate\Support\Facades\Cache::remember('api_data_banks', now()->addHours(96), function () {
//         $urlBank = "http://172.16.22.204/Marine/api/getAllBanks.php";
//         $responseBank = @file_get_contents($urlBank);
//         return $responseBank ? json_decode($responseBank) : [];
//     });
    
//     // For regular users, check open policy
//     $openPolData = [];
//     if ($client_code && $userid !== 'Admin') {
//         $url = "http://172.16.22.204/Marine/api/get_open_policy.php?client_code=$client_code";
//         $response = @file_get_contents($url);
//         $openPolData = $response ? json_decode($response) : [];

//         if (is_null($openPolData) || empty($openPolData)) {
//             $route = route('home'); 
//             echo "<script>
//             alert('No Open Policy Exist, Contact to Administration');
//             window.location.href = '{$route}';
//             </script>";
//             exit();
//         }
//     }
    
//     $pol = [];
    
//     return view('invoices.marinecertificate', compact(
//         'next_cvrnum', 
//         'vessels', 
//         'perils', 
//         'mop_banks', 
//         'openPolData', 
//         'pol',
//         'invoiceData'
//     ));
// }
// private function sendCertificateCreatedEmail(Invoice $invoice, $transaction)
// {
//     try {
//         ini_set("SMTP", "vqs3572.pair.com");
        
//         $emailFrom = 'owais.zahid@ail.atlas.pk';
//         $emailCC = 'owais.zahid@ail.atlas.pk';
//         ini_set("sendmail_from", $emailFrom);

//         $headers = "From: AIL - Invoice Portal <$emailFrom>\r\n";
//         $headers .= "Cc: $emailCC\r\n";
//         $headers .= "MIME-Version: 1.0\r\n";
//         $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

//         $portalLink = url('/invoices');
//          $userName = session('user.name', 'System Administrator');

//         $message = '<html><body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">';
//         $message .= '<div style="max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 10px;">';
//         $message .= '<h2 style="color: #28a745; border-bottom: 2px solid #28a745; padding-bottom: 10px;">Certificate Created - Invoice Approved</h2>';
//         $message .= '<p>Dear Admin,</p>';
//         $message .= '<p>A certificate has been created and the invoice has been approved on the AIL Invoice Portal. Please review the details below:</p>';
        
//         $message .= '<div style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin: 20px 0;">';
//         $message .= '<table style="width: 100%; border-collapse: collapse;">';
//         $message .= '<tr><td style="padding: 8px 0; font-weight: bold; width: 150px;">Invoice Number:</td><td style="padding: 8px 0;">' . htmlspecialchars($invoice->invoice_number) . '</td></tr>';
//         $message .= '<tr><td style="padding: 8px 0; font-weight: bold;">Certificate Number:</td><td style="padding: 8px 0;">' . htmlspecialchars($transaction->portal_no) . '</td></tr>';
//         $message .= '<tr><td style="padding: 8px 0; font-weight: bold;">Client Name:</td><td style="padding: 8px 0;">' . htmlspecialchars($transaction->client_name) . '</td></tr>';
//         $message .= '<tr><td style="padding: 8px 0; font-weight: bold;">Vessel:</td><td style="padding: 8px 0;">' . htmlspecialchars($transaction->vessel_name) . '</td></tr>';
//         $message .= '<tr><td style="padding: 8px 0; font-weight: bold;">Amount Insured:</td><td style="padding: 8px 0;">' . number_format($transaction->amount_insured, 2) . ' ' . htmlspecialchars($transaction->premium_currency) . '</td></tr>';
//         $message .= '<tr><td style="padding: 8px 0; font-weight: bold;">Premium:</td><td style="padding: 8px 0;">' . number_format($transaction->premium, 2) . ' ' . htmlspecialchars($transaction->premium_currency) . '</td></tr>';
//         $message .= '<tr><td style="padding: 8px 0; font-weight: bold;">Approved By:</td><td style="padding: 8px 0;">' . htmlspecialchars($invoice->admin_action_by) . '</td></tr>';
//         $message .= '<tr><td style="padding: 8px 0; font-weight: bold;">Approved At:</td><td style="padding: 8px 0;">' . $invoice->admin_action_at->format('Y-m-d H:i:s') . '</td></tr>';
//         $message .= '<tr><td style="padding: 8px 0; font-weight: bold;">Portal Link:</td><td style="padding: 8px 0;"><a href="' . $portalLink . '" style="color: #0062cc; text-decoration: none;">' . $portalLink . '</a></td></tr>';
//         $message .= '</table>';
//         $message .= '</div>';
        
//         $message .= '<p>Certificate has been successfully created and the invoice is now marked as approved.</p>';
//           $message .= '<p style="margin-top: 30px;">Best regards,<br><strong>' . htmlspecialchars($userName) . '</strong></p>';
//         $message .= '</div>';
//         $message .= '</body></html>';

//         $subject = "Certificate Created - Invoice " . $invoice->invoice_number . " Approved";

//         $mailResult = mail('owais.zahid@ail.atlas.pk', $subject, $message, $headers);
        
//         // Log the email
//         EmailLog::create([
//             'invoice_id' => $invoice->id,
//             'email_type' => 'certificate_created',
//             'recipient_email' => 'owais.zahid@ail.atlas.pk',
//             'status' => $mailResult ? 'sent' : 'failed'
//         ]);

//         return $mailResult;

//     } catch (\Exception $e) {
//         // Log::error('Error sending certificate email: ' . $e->getMessage());
//         return false;
//     }
// }



public function adminIndex()
{
    // NO CACHE - Get fresh data every time
    $invoices = Invoice::orderBy('created_at', 'desc')->get();
    return view('invoices.admininvoices', compact('invoices'));
}
/**
 * Admin reject invoice with remark (before or after certificate)
 */

// public function adminReject(Request $request, $id)
// {
//     $request->validate([
//         'admin_remark' => 'required|string|min:3'
//     ]);
    
//     try {
//         $invoice = Invoice::findOrFail($id);
        
//         // Get user name
//         $userName = auth()->user()->name ?? Session::get('user')['name'] ?? 'Admin';
        
//         // Pass user name to the model method
//         $invoice->rejectWithRemark($userName, $request->admin_remark);
        
//         return response()->json([
//             'success' => true,
//             'message' => 'Invoice rejected successfully'
//         ]);
        
//     } catch (\Exception $e) {
//         return response()->json([
//             'success' => false,
//             'message' => 'Error: ' . $e->getMessage()
//         ], 500);
//     }
// }

// /**
//  * Mark certificate as created (auto-approves)
//  */

// public function markCertificate($id)
// {
//     try {
//         $invoice = Invoice::findOrFail($id);
        
//         // Check if invoice is rejected
//         if ($invoice->status === 'rejected') {
//             return response()->json([
//                 'success' => false,
//                 'message' => 'Cannot create certificate for rejected invoice'
//             ], 400);
//         }
        
//         // Check if certificate already created
//         if ($invoice->certificate_created) {
//             return response()->json([
//                 'success' => false,
//                 'message' => 'Certificate has already been created for this invoice'
//             ], 400);
//         }
        
//         // Build redirect URL with invoice_id parameter
//         $redirectUrl = route('invoice.marinecertificate') . '?invoice_id=' . $id;
        
//         return response()->json([
//             'success' => true,
//             'message' => 'Redirecting to certificate creation...',
//             'redirect_url' => $redirectUrl
//         ]);
        
//     } catch (\Exception $e) {
//         return response()->json([
//             'success' => false,
//             'message' => 'Error: ' . $e->getMessage()
//         ], 500);
//     }
// }





public function emailLog($invoiceId)
{
    return view('invoices.email-log', ['invoiceId' => $invoiceId]);
}
public function getEmailLog($invoiceId)
{
    try {
        $invoice = Invoice::findOrFail($invoiceId);
        
        // Get email logs with all columns
        $emailLogs = EmailLog::where('invoice_id', $invoiceId)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($log) {
                // Return all columns as they are
                return [
                    'id' => $log->id,
                    'invoice_id' => $log->invoice_id,
                    'email_type' => $log->email_type,
                    'recipient_email' => $log->recipient_email,
                    'status' => $log->status,
                    'sent_at' => $log->sent_at ? $log->sent_at->toDateTimeString() : null,
                    'created_at' => $log->created_at->toDateTimeString(),
                    'updated_at' => $log->updated_at->toDateTimeString(),
                    // Include any additional fields
                    'subject' => $log->subject ?? null,
                    'body' => $log->body ?? null,
                    'email_content' => $log->email_content ?? null,
                    'error_message' => $log->error_message ?? null,
                    'attempt_count' => $log->attempt_count ?? $log->attempts ?? 1, // Include attempt count
                ];
            });
        
        return response()->json([
            'success' => true,
            'invoice_number' => $invoice->invoice_number,
            'email_logs' => $emailLogs
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 404);
    }
}


public function unreject($id)
{
    try {
        // Find the invoice
        $invoice = Invoice::findOrFail($id);
        
        // Check if invoice is actually rejected
        if ($invoice->status !== 'rejected') {
            return response()->json([
                'success' => false,
                'message' => 'Only rejected invoices can be unrejected'
            ], 400);
        }
        
        // Check if certificate was already created
        if ($invoice->certificate_created == 1) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot unreject an invoice that has a certificate created'
            ], 400);
        }
        
        // Update invoice status back to payment_uploaded
        $invoice->status = 'payment_uploaded';
        $invoice->admin_remark = null; // Clear admin remark
        $invoice->admin_action_by = auth()->user()->name ?? 'Admin';
        $invoice->admin_action_at = now();
        $invoice->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Invoice unrejected successfully',
            'invoice' => $invoice
        ]);
        
    } catch (\Exception $e) {
        \Log::error('Error unrejecting invoice: ' . $e->getMessage());
        
        return response()->json([
            'success' => false,
            'message' => 'Failed to unreject invoice: ' . $e->getMessage()
        ], 500);
    }
}


}