<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Models\Claim;
use Carbon\Carbon;
use Response;
use File;
use DB;

class ReportController extends Controller
{
    public function dateWiseReport(Request $request){

        $userid = Session::get('user')['name'];
        $role = Session::get('user')['role'];


        $date1 = Carbon::now()->startOfYear();
        $date2 = Carbon::now()->endOfMonth();
        $fromDate = Carbon::parse($date1)->format('Y-m-d');
        $toDate = Carbon::parse($date2)->format('Y-m-d');


         if($request->isMethod('post')){
            $data = $request->all();
            $toDate = $data['to_date'];
            $fromDate = $data['from_date'];
        }

        if($role == 'admin'){
            $polData =
            DB::table('transactions')
            ->select('transactions.*')
            ->whereDate('created_at', '>=', $fromDate)
            ->whereDate('created_at', '<=', $toDate)
            ->orderby('created_at', 'desc')
            ->get();
        }else{
            $polData =
            DB::table('transactions')
            ->select('transactions.*')
            ->where('created_by', $userid)
            ->whereDate('created_at', '>=', $fromDate)
            ->whereDate('created_at', '<=', $toDate)
            ->orderby('created_at', 'desc')
            ->get();
        }
        

            
        return view('Reports.datewise', compact('polData', 'fromDate', 'toDate'));
    }
	
	
	
	public function colReport(Request $request)
    {
        $startDate = $request->input('start_date') ?? Carbon::now()->startOfYear()->format('Y-m-d');
        $endDate = $request->input('end_date') ?? Carbon::now()->format('Y-m-d');
        $statusFilter = $request->input('status_filter', 'all');

        // Get data from helper
        $result = Helper::getCol($startDate, $endDate);
        $data = collect($result['data'] ?? []);

        // Apply outstanding filter if selected
        if ($statusFilter === 'outstanding') {
            $data = $data->filter(function ($record) {
                $outstanding = ($record->GDH_GROSSPREMIUM ?? 0) - ($record->TOT_COL ?? 0);
                return $outstanding > 0;
            });
        }

        // Return view
        return view('Reports.col_report', [
            'data' => $data,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status_filter' => $statusFilter,
            'api_date_range' => [
                'from' => Carbon::parse($startDate)->format('d-M-Y'),
                'to' => Carbon::parse($endDate)->format('d-M-Y'),
            ]
        ]);
    }


}
