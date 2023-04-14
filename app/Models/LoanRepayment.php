<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanRepayment extends Model
{
    use HasFactory;

    const STATUS_PENDING = 1;

    const STATUS_PAID = 2;

    protected $table = 'loan_repayments';

    protected $fillable = [
        'loan_id',
        'amount',
        'pay_date',
        'status',
        'paid_amount',
        'currency',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function loan()
    {
        return $this->belongsTo(Loan::class, 'loan_id', 'id');
    }
}
