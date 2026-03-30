<?php

namespace App\Http\Controllers;

use App\Models\EmailLog;
use Illuminate\Http\Request;

class EmailLogController extends Controller
{
    /**
     * Display all email logs
     */
    public function index()
    {
        $logs = EmailLog::with('invoice')
            ->orderBy('sent_at', 'desc')
            ->paginate(50);
        
        return response()->json([
            'success' => true,
            'data' => $logs
        ]);
    }
    
    /**
     * Get email logs by type
     */
    public function getByType($type)
    {
        $validTypes = ['invoice_created', 'payment_uploaded'];
        
        if (!in_array($type, $validTypes)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid email type'
            ], 400);
        }
        
        $logs = EmailLog::where('email_type', $type)
            ->with('invoice')
            ->orderBy('sent_at', 'desc')
            ->paginate(50);
        
        return response()->json([
            'success' => true,
            'data' => $logs
        ]);
    }
    
    /**
     * Get email logs by status
     */
    public function getByStatus($status)
    {
        $validStatuses = ['sent', 'failed'];
        
        if (!in_array($status, $validStatuses)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid status'
            ], 400);
        }
        
        $logs = EmailLog::where('status', $status)
            ->with('invoice')
            ->orderBy('sent_at', 'desc')
            ->paginate(50);
        
        return response()->json([
            'success' => true,
            'data' => $logs
        ]);
    }
    
    /**
     * Get email statistics
     */
    public function getStats()
    {
        $total = EmailLog::count();
        $sent = EmailLog::sent()->count();
        $failed = EmailLog::failed()->count();
        $invoiceCreated = EmailLog::invoiceCreated()->count();
        $paymentUploaded = EmailLog::paymentUploaded()->count();
        
        return response()->json([
            'success' => true,
            'data' => [
                'total' => $total,
                'sent' => $sent,
                'failed' => $failed,
                'invoice_created' => $invoiceCreated,
                'payment_uploaded' => $paymentUploaded,
                'success_rate' => $total > 0 ? round(($sent / $total) * 100, 2) : 0
            ]
        ]);
    }
    
    /**
     * Get recent email logs
     */
    public function getRecentLogs()
    {
        $logs = EmailLog::with('invoice')
            ->orderBy('sent_at', 'desc')
            ->limit(50)
            ->get();
        
        return response()->json([
            'success' => true,
            'data' => $logs
        ]);
    }
    
    /**
     * Generate email report
     */
    public function generateEmailReport(Request $request)
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'email_type' => 'nullable|string'
        ]);
        
        $query = EmailLog::query();
        
        if ($request->start_date && $request->end_date) {
            $query->whereBetween('sent_at', [$request->start_date, $request->end_date]);
        }
        
        if ($request->email_type) {
            $query->where('email_type', $request->email_type);
        }
        
        $logs = $query->with('invoice')
            ->orderBy('sent_at', 'desc')
            ->get();
        
        return response()->json([
            'success' => true,
            'data' => $logs,
            'summary' => [
                'total' => $logs->count(),
                'sent' => $logs->where('status', 'sent')->count(),
                'failed' => $logs->where('status', 'failed')->count(),
                'by_type' => $logs->groupBy('email_type')->map->count()
            ]
        ]);
    }
}