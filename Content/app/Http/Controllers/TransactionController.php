<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cache; 
use App\Helpers\BankHelper;
use App\Helpers\incoterm_helper;
use Illuminate\Http\Request;
use App\Models\Brand;
use App\Models\Product;
use App\Models\Vehicle;
use App\Models\Transaction;
use App\Models\Peril;
use App\Models\User;
use Carbon\Carbon;
use File;
use DB;
use App\Models\Invoice;
use Log;

class TransactionController extends Controller
{
    
    public function addInsured(Request $request){
        
        $userRole   = Session::get('user')['role'];
        $userid     = Session::get('user')['name'];
        $client_code = Session::get('user')['client_code'];
        
        // Get invoice_id from request (works for both GET query param and POST hidden input)
        $invoiceId   = $request->input('invoice_id');
        $invoiceData = null;
        
        // If invoice_id is provided, fetch and validate the invoice
        if ($invoiceId) {
            $invoiceData = Invoice::find($invoiceId);
            
            if (!$invoiceData) {
                return back()->with('error', 'Invoice not found');
            }
            
            if ($invoiceData->status === 'rejected') {
                return back()->with('error', 'Cannot create certificate for rejected invoice');
            }
            
            if ($invoiceData->certificate_created) {
                return back()->with('info', 
                    'Certificate already created for this invoice.<br>' .
                    'Certificate #: ' . ($invoiceData->tmp2 ?? 'N/A') . '<br>' .
                    'Status: ' . strtoupper($invoiceData->status)
                );
            }
            
            Log::info('Starting certificate creation for invoice', [
                'invoice_id'          => $invoiceId,
                'invoice_number'      => $invoiceData->invoice_number,
                'current_status'      => $invoiceData->status,
                'certificate_created' => $invoiceData->certificate_created,
                'userid'              => $userid
            ]);
        }
        
        // Get the next portal number for this user
        $last_cvrnum_row = Transaction::where('created_by', $userid)->orderBy('id', 'desc')->first();
        $next_cvrnum     = $last_cvrnum_row ? $last_cvrnum_row->portal_no + 1 : 1;

        // =========================================================
        // POST — Certificate creation
        // =========================================================
        if ($request->isMethod('post')) {
            $data = $request->all();
            
            DB::beginTransaction();
            
            try {
                $trans = new Transaction;
                
                // --- Insured Information ---
                $trans->mop       = $data['mop'];
                $trans->portal_no = $data['portal_no'];
                $trans->app_date  = $data['app_date'];
                $trans->bus_line  = $data['bus_line'];
                $trans->bus_class = $data['bus_class'];
                $trans->mar_catg  = $data['mar_catg'];
                $trans->insured   = $data['insured'];
                $trans->per_carry = $data['per_carry'] ?? null;
                $trans->address   = $data['address']   ?? null;

                // --- Consignment Information ---
                $trans->po_no     = $data['po_no']     ?? null;
                $trans->voyage_no = $data['voyage_no'] ?? null;
                $trans->arr_date  = $data['arr_date']  ?? null;
                $trans->inv_no    = $data['inv_no'];
                $trans->inv_date  = $data['inv_date']  ?? null;

                // conv is a multiple select — comes as array, store as CSV
                $convValue     = $data['conv'] ?? null;
                $trans->conv   = is_array($convValue) ? implode(',', $convValue) : $convValue;

                $trans->bl_no      = $data['bl_no'];
                $trans->bl_date    = $data['bl_date'];

                // Packing
                if (($data['packingSelect'] ?? '') === 'Other') {
                    $trans->packing = $data['otherPackingText'] ?? null;
                } else {
                    $trans->packing = $data['packingSelect'] ?? null;
                }

                $trans->voyage_from = $data['voyage_from'];
                $trans->voyage_to   = $data['voyage_to'];
                $trans->via         = $data['via']         ?? null;

                // vessel field holds the CODE (set by JS hidden input)
                $trans->vessel   = $data['vessel'];
                $trans->lc_no    = $data['lc_no']    ?? null;
                $trans->lc_date  = $data['lc_date']  ?? null;

                // bank is a multiple select — store as CSV
                $bankArray   = $data['bank'] ?? [];
                $trans->bank = is_array($bankArray) ? implode(',', $bankArray) : $bankArray;

                $trans->incoterms   = $data['incoterms'];
                $trans->salling     = $data['salling'];
                $trans->builty_no   = $data['builty_no']   ?? null;
                $trans->builty_date = $data['builty_date'] ?? null;
                $trans->add_terms   = $data['add_terms']   ?? null;
                $trans->sub_mat     = $data['sub_mat'];

                // --- Basis of Valuation ---
                $trans->ex_rate  = $data['ex_rate'];
                $trans->cur_type = $data['cur_type'];
                $trans->si_fc    = $data['si_fc'];
                $trans->inc_per  = $data['inc_per'];
                $trans->inc_chrg = $data['inc_chrg'];
                $trans->tol_per  = $data['tol_per'];
                $trans->tolrence = $data['tolrence'];
                $trans->si_tfc   = $data['si_tfc'];
                $trans->si_rs    = $data['si_rs'];

                // --- Premium ---
                $trans->gross_pre  = $data['gross_pre'];
                $trans->admin_sur  = $data['admin_sur'];
                $trans->sub_total  = $data['sub_total'];
                $trans->reg        = $data['region'];
                $trans->gst_per    = $data['gst_per'];
                $trans->gst        = $data['gst'];
                $trans->fif        = $data['fif'];
                $trans->stamp_duty = $data['stamp_duty'];
                $trans->net_pre    = $data['net_pre'];
                $trans->doc_desc   = $data['doc_desc'] ?? null;

                $trans->created_by = $userid;

                // Link with invoice if coming from invoice management
                if ($invoiceId) {
                    $trans->invoice_id = $invoiceId;
                }

                // Save transaction
                $trans->save();
                $lastInsertedId = $trans->id;

                // --- Save Perils ---
                $selectedPerils    = $data['selected_perils']  ?? [];
                $perilCodes        = $data['peril_code']       ?? [];
                $perilDescriptions = $data['peril_dsc']        ?? [];
                $perilPercentages  = $data['peril_per']        ?? [];
                $perilCharges      = $data['peril_chrg']       ?? [];

                if (!empty($selectedPerils)) {
                    foreach ($perilCodes as $index => $code) {
                        if (in_array((string) $index, $selectedPerils)) {
                            $peril             = new Peril;
                            $peril->trans_id   = $data['portal_no'];
                            $peril->code       = $code;
                            $peril->dsc        = $perilDescriptions[$index] ?? '';
                            $peril->per        = $perilPercentages[$index]  ?? 0;
                            $peril->cal        = $perilCharges[$index]      ?? 0;
                            $peril->created_by = $userid;
                            $peril->save();
                        }
                    }
                }

                // =====================================================
                // CRITICAL: Update invoice status when certificate saved
                // Status should be: payment_uploaded (waiting for payment upload)
                // =====================================================
                if ($invoiceId) {
                    $now = Carbon::now();

                    $updated = DB::table('invoices')
                        ->where('id', $invoiceId)
                        ->update([
                            'status'                => 'payment_uploaded',  // Status after certificate creation
                            'certificate_created'   => 1,
                            'certificate_created_at' => $now,
                            'tmp1'                  => $lastInsertedId,   // transaction_id
                            'tmp2'                  => $data['portal_no'], // certificate_number
                            'admin_action_by'       => $userid,
                            'admin_action_at'       => $now,
                            'updated_at'            => $now
                        ]);

                    Log::info('Invoice updated after certificate creation', [
                        'invoice_id'         => $invoiceId,
                        'rows_affected'      => $updated,
                        'certificate_number' => $data['portal_no'],
                        'transaction_id'     => $lastInsertedId,
                        'new_status'         => 'payment_uploaded',
                        'updated_by'         => $userid,
                    ]);

                    if ($updated === 0) {
                        Log::error('No rows affected when updating invoice', [
                            'invoice_id' => $invoiceId,
                            'exists'     => DB::table('invoices')->where('id', $invoiceId)->exists()
                        ]);
                        DB::rollBack();
                        return back()->with('error', 'Failed to update invoice status. Please contact administrator.');
                    }
                }

                DB::commit();

                // Success message
                $message = 'Certificate #' . $data['portal_no'] . ' created successfully!';
                if ($invoiceId) {
                    $finalInvoice = Invoice::find($invoiceId);
                    $message .= '<br>Invoice #' . ($finalInvoice->invoice_number ?? $invoiceId) . ' - Certificate created successfully.';
                    $message .= '<br>Certificate Number: ' . $data['portal_no'];
                    $message .= '<br><strong>Next Step:</strong> Please upload payment proof';
                }

                Session::flash('success', $message);
                return redirect('/sendemail/' . $lastInsertedId);

            } catch (\Exception $e) {
                DB::rollBack();

                Log::error('Certificate creation failed', [
                    'error'      => $e->getMessage(),
                    'trace'      => $e->getTraceAsString(),
                    'invoice_id' => $invoiceId ?? null,
                    'userid'     => $userid
                ]);

                return back()->with('error', 'Certificate creation failed: ' . $e->getMessage());
            }
        }

        // =========================================================
        // GET — Show certificate creation form
        // =========================================================

        $url = "http://172.16.22.204/Marine/api/get_open_policy.php?client_code=$client_code";
        $ch  = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        $response    = curl_exec($ch);
        curl_close($ch);
        $openPolData = json_decode($response);

        if (is_null($openPolData) || empty($openPolData)) {
            $route = route('home');
            echo "<script>
                alert('No Open Policy Exist, Contact to Administration');
                window.location.href = '{$route}';
            </script>";
            exit();
        }

        $loc  = $openPolData[0]->PLC_LOC_CODE;
        $type = $openPolData[0]->PDT_DOCTYPE;
        $doc  = $openPolData[0]->GDH_DOCUMENTNO;
        $year = $openPolData[0]->GDH_YEAR;
        $busc = $openPolData[0]->PBC_BUSICLASS_CODE;
        $dept = $openPolData[0]->PDP_DEPT_CODE;

        $vessels = Cache::remember('api_data_vessels_116', now()->addHours(24), function () {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_URL, "http://172.16.22.204/Marine/api/vessels.php");
            $r = curl_exec($ch);
            curl_close($ch);
            return json_decode($r);
        });

        $perilsCacheKey = "api_data_perils_116_{$loc}_{$type}_{$doc}_{$year}";
        $perils = Cache::remember($perilsCacheKey, now()->addHours(24), function () use ($loc, $type, $doc, $year) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_URL, "http://172.16.22.204/Marine/api/get_peril.php?loc=$loc&type=$type&doc=$doc&year=$year");
            $r = curl_exec($ch);
            curl_close($ch);
            return json_decode($r);
        });

        $bankCacheKey = "api_data_banks_116_{$loc}_{$type}_{$doc}_{$year}_{$busc}_{$dept}";
        $mop_banks = Cache::remember($bankCacheKey, now()->addHours(24), function () use ($loc, $type, $doc, $year, $busc, $dept) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_URL, "http://172.16.22.204/Marine/api/get_banks.php?loc=$loc&type=$type&doc=$doc&year=$year&busc=$busc&dept=$dept");
            $r = curl_exec($ch);
            curl_close($ch);
            return json_decode($r);
        });

        return view('Transaction.insured', compact(
            'openPolData',
            'next_cvrnum',
            'vessels',
            'perils',
            'mop_banks',
            'invoiceData',
            'invoiceId'
        ));
    }

    
    public function viewInsured($id=null){

        $pol = Transaction::where('id', $id)->get();
        $peril = Peril::where('trans_id', $pol[0]->portal_no)
        ->where('created_by', $pol[0]->created_by)->get();

        $mop = $pol[0]->mop;
        $url = "http://172.16.22.204/Marine/api/get_mop_dtl.php?mop=$mop";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL,$url);
        $response=curl_exec($ch);
        curl_close($ch);
        $openPolData = json_decode($response);


        $urlVessel = "http://172.16.22.204/Marine/api/vessels.php";
        $chVess = curl_init();
        curl_setopt($chVess, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($chVess, CURLOPT_URL,$urlVessel);
        $responseVess =curl_exec($chVess);
        curl_close($chVess);
        $vessels = json_decode($responseVess);
        

        return view('Transaction.insured_view', compact('pol', 'peril', 'openPolData', 'vessels'));
    }


    public function print_performa($id=null){

    
        // $client_code = Session::get('user')['client_code'];
        

        $pol = Transaction::where('id', $id)->get();
        $peril = Peril::where('trans_id', $pol[0]->portal_no)
        ->where('created_by', $pol[0]->created_by)->get();

        $mop = $pol[0]->mop;
        $banks = $pol[0]->bank;
        
        $vc = $pol[0]->vessel;
        $incoterm = $pol[0]->incoterms;
        $inc_desc = incoterm_helper::getIncotermDescription($incoterm);
        
        $bank_code_array = explode(',', $banks);
        $bank_descriptions = [];

        $urlBankDesc = "http://172.16.22.204/Marine/api/get_bank_desc.php?bank_code=";

        foreach ($bank_code_array as $bankCode) {
            $url = $urlBankDesc . trim($bankCode); // Append the individual bank code
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_URL,$url);
            $responseBankDesc=curl_exec($ch);
            curl_close($ch);
            $banks_desc_JSON = json_decode($responseBankDesc);
            $bank_descriptions[] = $banks_desc_JSON[0]->PBN_BNK_DESC;
            
        }

        $banks_desc_csv = implode(', ', array_filter($bank_descriptions));

        $url = "http://172.16.22.204/Marine/api/get_mop_dtl.php?mop=$mop";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL,$url);
        $response=curl_exec($ch);
        curl_close($ch);
        $openPolData = json_decode($response);
        
        
        $urlVC = "http://172.16.22.204/Marine/api/get_vessel_dsc.php?vc=$vc";
        
        $chVC = curl_init();
        curl_setopt($chVC, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($chVC, CURLOPT_URL,$urlVC);
        $responseVC=curl_exec($chVC);
        curl_close($chVC);
        $vc_dsc_data = json_decode($responseVC);
        $vs_dsc = $vc_dsc_data[0]->PVC_DESC;


        return view('Transaction.print_performa', compact('pol', 'peril', 'openPolData', 'banks_desc_csv', 'inc_desc', 'vs_dsc'));
    }




    public function sendViaNativeMail($id = null)
    {
        $pol = Transaction::where('id', $id)->get();
        $peril = Peril::where('trans_id', $pol[0]->portal_no)->where('created_by', $pol[0]->created_by)->get();



        // Configure native PHP SMTP settings
        ini_set("SMTP", "QS4528.pair.com");
        ini_set("sendmail_from", "owais.zahid@ail.atlas.pk");

        // Construct HTML message
        $message = '<html><body>';
        $message .= "Dear Sir,";
        $message .= "<br><br> New Marine Certificate generated from portal. Details given below:";
        $message .= " <br><br>
        <table border=\"1\" style=\"padding:5px;\">
            <tr>
                <th colspan=\"1\">MOP</th>
                <td>{$pol[0]->mop}</td>
                <th colspan=\"1\">Portal Num</th>
                <td>{$pol[0]->portal_no}</td>
                <th colspan=\"1\">Insured Name</th>
                <td>{$pol[0]->insured}</td>
            </tr>
                <th colspan=\"1\">PO No.</th>
                <td>{$pol[0]->po_no}</td>
                <th colspan=\"1\">Voyage No</th>
                <td>{$pol[0]->voyage_no}</td>
                <th colspan=\"1\">Arrival Date.</th>
                <td>{$pol[0]->arr_date}</td>
            </tr>    

            </tr>
                <th colspan=\"1\">Invoice  No.</th>
                <td>{$pol[0]->po_no}</td>
                <th colspan=\"1\">Invoice Date</th>
                <td>{$pol[0]->inv_no}</td>
                <th colspan=\"1\">Conveyance</th>
                <td>{$pol[0]->conv}</td>
            </tr>

            </tr>
                <th colspan=\"1\">BL/AWB No.</th>
                <td>{$pol[0]->bl_no}</td>
                <th colspan=\"1\">BL/AWB Date</th>
                <td>{$pol[0]->bl_date}</td>
                <th colspan=\"1\">Packing</th>
                <td>{$pol[0]->packing}</td>
            </tr>

            </tr>
                <th colspan=\"1\">Voyage From</th>
                <td>{$pol[0]->voyage_from}</td>
                <th colspan=\"1\">Voyage To</th>
                <td>{$pol[0]->voyage_to}</td>
                <th colspan=\"1\">VIA</th>
                <td>{$pol[0]->via}</td>
            </tr>


            </tr>
                <th colspan=\"1\">Vessel / Carrier</th>
                <td>{$pol[0]->vessel}</td>
                <th colspan=\"1\">LC Number</th>
                <td>{$pol[0]->lc_no}</td>
                <th colspan=\"1\">LC Date</th>
                <td>{$pol[0]->lc_date}</td>
            </tr>


             </tr>
                <th colspan=\"1\">Incoterms</th>
                <td>{$pol[0]->incoterms}</td>
                <th colspan=\"1\">Salling on/or about</th>
                <td>{$pol[0]->salling}</td>
                <th colspan=\"1\">Form E/Builty Number</th>
                <td>{$pol[0]->builty_no}</td>
            </tr>

            </tr>
                <th colspan=\"1\">Builty Date</th>
                <td>{$pol[0]->builty_date}</td>
                <th colspan=\"2\">Additional Terms (if any)</th>
                <td  colspan=\"2\">{$pol[0]->add_terms}</td>
            </tr>

            </tr>
                <th colspan=\"2\">Subject Matter Insured</th>
                <td  colspan=\"4\">{$pol[0]->sub_mat}</td>
            </tr>


            </tr>
                <th colspan=\"1\">FC</th>
                <td>{$pol[0]->si_fc}</td>
                <th colspan=\"1\">Total FC</th>
                <td>{$pol[0]->si_tfc}</td>
                <th colspan=\"1\">Sum Insured (Rs)</th>
                <td>{$pol[0]->si_rs}</td>
            </tr>

            
            </tr>
                <th colspan=\"1\">Gross Premium</th>
                <td>{$pol[0]->gross_pre}</td>
                <th colspan=\"1\">Net Premium</th>
                <td>{$pol[0]->net_pre}</td>
            </tr>
        </table>";
        $message .= "<br><br>Regards,<br><br><br>";
        $message .= "</body></html>";

        // Email headers
        $headers = "From: AIL - Marine <owais.zahid@ail.atlas.pk\r\n";
        $headers .= "Cc: AIL - HO <owais.zahid@ail.atlas.pk>\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

        // Send email
        $mail_result = mail("owais.zahid@ail.atlas.pk", "Marine Certificate Generated.", $message, $headers);
        return redirect('/');
    }


   public function adminReject(Request $request, $id)
    {
        $request->validate([
            'admin_remark' => 'required|string|min:3'
        ]);
        
        try {
            $invoice = Invoice::findOrFail($id);
            
            // Get user name - use session if not authenticated
            if (auth()->check()) {
                $userName = auth()->user()->name;
            } else {
                $userName = Session::get('user')['name'] ?? 'Admin';
            }
            
            // Check if certificate was already created
            if ($invoice->certificate_created) {
                // Add note about certificate rejection
                $remark = $request->admin_remark . " [Certificate #{$invoice->tmp2} was created but is now rejected]";
                
                $invoice->rejectWithRemark($userName, $remark);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Invoice rejected successfully (certificate was already created)'
                ]);
            }
            
            // Normal rejection
            $invoice->rejectWithRemark($userName, $request->admin_remark);
            
            return response()->json([
                'success' => true,
                'message' => 'Invoice rejected successfully'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Invoice rejection failed', [
                'invoice_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to reject invoice: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check if certificate can be created for invoice
     */
    public function checkCertificateCreation($id)
    {
        try {
            $invoice = Invoice::findOrFail($id);
            
            if ($invoice->certificate_created) {
                return response()->json([
                    'can_create' => false,
                    'message' => 'Certificate already created for this invoice'
                ]);
            }
            
            if ($invoice->status === 'rejected') {
                return response()->json([
                    'can_create' => false,
                    'message' => 'Cannot create certificate for rejected invoice'
                ]);
            }
            
            return response()->json([
                'can_create' => true,
                'message' => 'Certificate can be created'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Check certificate creation failed', [
                'invoice_id' => $id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'can_create' => false,
                'message' => 'Error checking invoice status'
            ], 500);
        }
    }

    /**
     * Get certificate details for invoice
     */
    public function getCertificateDetails($id)
    {
        try {
            $invoice = Invoice::findOrFail($id);
            
            if (!$invoice->certificate_created) {
                return response()->json([
                    'success' => false,
                    'message' => 'No certificate created for this invoice'
                ]);
            }
            
            return response()->json([
                'success' => true,
                'certificate_number' => $invoice->tmp2, // certificate_number
                'transaction_id' => $invoice->tmp1,     // transaction_id
                'status' => $invoice->status
            ]);
            
        } catch (\Exception $e) {
            Log::error('Get certificate details failed', [
                'invoice_id' => $id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to get certificate details'
            ], 500);
        }
    }

    /**
     * Update certificate details
     */
    public function updateCertificateDetails(Request $request, $id)
    {
        $request->validate([
            'certificate_number' => 'nullable|string|max:100',
            'transaction_id' => 'nullable|integer'
        ]);
        
        try {
            $invoice = Invoice::findOrFail($id);
            
            if (!$invoice->certificate_created) {
                return response()->json([
                    'success' => false,
                    'message' => 'No certificate created for this invoice'
                ]);
            }
            
            $updateData = [];
            if ($request->filled('certificate_number')) {
                $updateData['tmp2'] = $request->certificate_number;
            }
            if ($request->filled('transaction_id')) {
                $updateData['tmp1'] = $request->transaction_id;
            }
            
            if (!empty($updateData)) {
                // Use direct DB update for reliability
                DB::table('invoices')
                    ->where('id', $id)
                    ->update($updateData);
                
                Log::info('Certificate details updated via direct DB', [
                    'invoice_id' => $id,
                    'updates' => $updateData
                ]);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Certificate details updated successfully'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Update certificate details failed', [
                'invoice_id' => $id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update certificate details'
            ], 500);
        }
    }

    /**
     * Edit reject remark for already rejected invoices
     */
    public function editRejectRemark(Request $request, $id)
    {
        $request->validate([
            'admin_remark' => 'required|string|min:3'
        ]);
        
        try {
            $invoice = Invoice::findOrFail($id);
            
            // Only allow editing if invoice is rejected
            if ($invoice->status !== 'rejected') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only rejected invoices can have their remarks edited'
                ], 400);
            }
            
            // Get user name
            if (auth()->check()) {
                $userName = auth()->user()->name;
            } else {
                $userName = Session::get('user')['name'] ?? 'Admin';
            }
            
            // Update the remark
            $invoice->update([
                'admin_remark' => $request->admin_remark,
                'admin_action_by' => $userName,
                'admin_action_at' => now(),
                'rejection_remark' => $request->admin_remark
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Rejection remark updated successfully'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Edit reject remark failed', [
                'invoice_id' => $id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update remark: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Debug function to manually update invoice status
     */
    public function debugUpdateInvoice($id)
    {
        try {
            $invoice = Invoice::findOrFail($id);
            
            return [
                'before' => [
                    'status' => $invoice->status,
                    'certificate_created' => $invoice->certificate_created,
                    'tmp1' => $invoice->tmp1,
                    'tmp2' => $invoice->tmp2
                ],
                'update_result' => DB::table('invoices')
                    ->where('id', $id)
                    ->update([
                        'status' => 'approved',
                        'certificate_created' => 1,
                        'certificate_created_at' => now(),
                        'updated_at' => now()
                    ]),
                'after' => Invoice::find($id)->toArray()
            ];
            
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
}