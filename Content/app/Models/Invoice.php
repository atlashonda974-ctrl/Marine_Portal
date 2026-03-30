<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'invoice_number',
        'invoice_file_path',
        'invoice_file_name',
        'remarks',
        'status',
        'payment_proof_path',
        'payment_proof_name',
        'payment_approved_by',
        'payment_approved_at',
        'email_sent_at',
        'payment_email_sent_at',
        'created_by_user_id',
        'admin_remark',
        'admin_action_by',
        'admin_action_at',
        'certificate_created',
        'certificate_created_at',
        'certificate_rejected',
        'certificate_rejected_at',
        'tmp1', // Used for certificate_transaction_id
        'tmp2', // Used for certificate_number
        'tmp3'  // Used for additional data if needed
    ];

    protected $casts = [
        'payment_approved_at' => 'datetime',
        'email_sent_at' => 'datetime',
        'payment_email_sent_at' => 'datetime',
        'admin_action_at' => 'datetime',
        'certificate_created' => 'boolean',
        'certificate_created_at' => 'datetime',
        'certificate_rejected' => 'boolean',
        'certificate_rejected_at' => 'datetime',
    ];

    /**
     * Get email logs for this invoice
     */
    public function emailLogs()
    {
        return $this->hasMany(EmailLog::class, 'invoice_id');
    }

    public function markEmailSent()
    {
        $this->update(['email_sent_at' => now()]);
    }

    public function markPaymentEmailSent()
    {
        $this->update(['payment_email_sent_at' => now()]);
    }

    public function approvePayment($approvedBy)
    {
        $this->update([
            'status' => 'verified',
            'payment_approved_by' => $approvedBy,
            'payment_approved_at' => now()
        ]);
    }

    public function rejectPayment($rejectedBy)
    {
        $this->update([
            'status' => 'rejected',
            'payment_approved_by' => $rejectedBy,
            'payment_approved_at' => now()
        ]);
    }

    /**
     * Mark as certificate created (auto-approves)
     */
    public function markCertificateCreated($actionBy)
    {
        $this->update([
            'status' => 'approved',  // CRITICAL: Auto-approve when certificate is created
            'certificate_created' => true,
            'certificate_created_at' => now(),
            'admin_action_by' => $actionBy,
            'admin_action_at' => now()
        ]);
    }

    /**
     * Reject with admin remark (can be before or after certificate)
     */
    public function rejectWithRemark($rejectedBy, $remark)
    {
        $updateData = [
            'status' => 'rejected',
            'admin_remark' => $remark,
            'admin_action_by' => $rejectedBy,
            'admin_action_at' => now()
        ];

        // If certificate was already created, mark it as rejected too
        if ($this->certificate_created) {
            $updateData['certificate_rejected'] = true;
            $updateData['certificate_rejected_at'] = now();
        }

        $this->update($updateData);
    }

    /**
     * Mark certificate as created with transaction details
     * Uses existing fields + tmp fields for additional data
     */
    public function markCertificateCreatedWithDetails($actionBy, $transactionId, $certificateNumber)
    {
        $this->update([
            'status' => 'approved',  // CRITICAL: Auto-approve when certificate is created
            'certificate_created' => true,
            'certificate_created_at' => now(),
            'tmp1' => $transactionId, // Store transaction_id in tmp1
            'tmp2' => $certificateNumber, // Store certificate_number in tmp2
            'admin_action_by' => $actionBy,
            'admin_action_at' => now()
        ]);
    }

    /**
     * Accessors for certificate data stored in tmp fields
     */
    public function getCertificateTransactionIdAttribute()
    {
        return $this->tmp1;
    }

    public function getCertificateNumberAttribute()
    {
        return $this->tmp2;
    }

    /**
     * Check if certificate is created
     */
    public function isCertificateCreated()
    {
        return $this->certificate_created;
    }

    /**
     * Check if invoice is approved (has certificate)
     */
    public function isApproved()
    {
        // Certificate created = automatically approved
        return $this->status === 'approved' || $this->certificate_created;
    }

    /**
     * Check if invoice is rejected
     */
    public function isRejected()
    {
        return $this->status === 'rejected';
    }

    /**
     * Check if can create certificate (not rejected)
     */
    public function canCreateCertificate()
    {
        return !in_array($this->status, ['rejected']) && !$this->certificate_created;
    }

    public function hasInvoiceFile()
    {
        return !empty($this->invoice_file_path) && file_exists(public_path($this->invoice_file_path));
    }

    public function hasPaymentProof()
    {
        return !empty($this->payment_proof_path) && file_exists(public_path($this->payment_proof_path));
    }
    
    /**
     * Get the transaction associated with this invoice
     */
    public function transaction()
    {
        return $this->belongsTo(\App\Models\Transaction::class, 'tmp1'); // tmp1 stores transaction_id
    }
    
    /**
     * Accessor for admin_action_by to always return name
     */
    public function getAdminActionByAttribute($value)
    {
        // If it's numeric, try to get user name from users table
        if (is_numeric($value)) {
            try {
                $user = \App\Models\User::find($value);
                return $user ? $user->name : "User #{$value}";
            } catch (\Exception $e) {
                return "User #{$value}";
            }
        }
        
        // Return the stored value (should be name)
        return $value;
    }

    /**
     * Check if invoice is pending (no certificate created yet)
     */
    public function isPendingCertificate()
    {
        return !$this->certificate_created && !$this->isRejected();
    }
}