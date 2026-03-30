<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailLog extends Model
{
    protected $table = 'email_log';
    
    protected $fillable = [
        'invoice_id',
        'email_type',
        'recipient_email',
        'sent_at', 
        'status',
        'created_at', 
        'updated_at'  
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
    
    
    public $timestamps = true;

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }

    public function scopeSent($query)
    {
        return $this->where('status', 'sent');
    }

    public function scopeFailed($query)
    {
        return $this->where('status', 'failed');
    }

    public function scopeInvoiceCreated($query)
    {
        return $this->where('email_type', 'invoice_created');
    }

    public function scopePaymentUploaded($query)
    {
        return $this->where('email_type', 'payment_uploaded');
    }
}