<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $table = 'invoices';

    protected $fillable = [
        'gate_id',
        'billed_to',
        'invoice_no',
        'invoice_date',
        'gst_type',
        'cash_discount_percent',
        'cash_discount_amount',
        'labor_charges',
        'other_charges',
        'taxable_amount',
        'sgst_percent',
        'sgst_amount',
        'cgst_percent',
        'cgst_amount',
        'igst_percent',
        'igst_amount',
        'gst_amount',
        'tds_amount',
        'insurance_amount',
        'tcs_amount',
        'due_days',
        'advance_payment',
        'total_amount',
        'created_by',
    ];

    protected $casts = [
        'gate_id' => 'integer',
        'invoice_date' => 'date',
        'cash_discount_percent' => 'decimal:3',
        'cash_discount_amount' => 'decimal:3',
        'labor_charges' => 'decimal:3',
        'other_charges' => 'decimal:3',
        'taxable_amount' => 'decimal:3',
        'sgst_percent' => 'decimal:3',
        'sgst_amount' => 'decimal:3',
        'cgst_percent' => 'decimal:3',
        'cgst_amount' => 'decimal:3',
        'igst_percent' => 'decimal:3',
        'igst_amount' => 'decimal:3',
        'gst_amount' => 'decimal:3',
        'tds_amount' => 'decimal:3',
        'insurance_amount' => 'decimal:3',
        'tcs_amount' => 'decimal:3',
        'due_days' => 'integer',
        'advance_payment' => 'decimal:3',
        'total_amount' => 'decimal:3',
        'created_by' => 'integer',
    ];

    public function gate()
    {
        return $this->belongsTo(GateAndGaadi::class, 'gate_id');
    }
}