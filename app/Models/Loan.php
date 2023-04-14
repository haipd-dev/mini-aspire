<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    use HasFactory;

    const STATUS_PENDING = 1;

    const STATUS_PAID = 2;

    const STATUS_APPROVE = 3;

    const STATUS_PARTIAL_PAID = 4;

    const STATUS_REJECTED = 5;

    protected $table = 'loans';

    protected $fillable = [
        'user_id',
        'amount',
        'term',
        'submit_date',
        'currency',
        'status',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function repayments()
    {
        return $this->hasMany(LoanRepayment::class, 'loan_id', 'id');
    }
}
