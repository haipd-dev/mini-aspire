<?php

namespace App\Repositories;

use App\Interfaces\Repositories\LoanRepaymentRepositoryInterface;
use App\Interfaces\Repositories\LoanRepositoryInterface;
use App\Models\Loan;

class LoanRepository extends BaseRepository implements LoanRepositoryInterface
{
    protected $loanRepaymentRepository;

    public function __construct(
        LoanRepaymentRepositoryInterface $loanRepaymentRepository
    ) {
        parent::__construct();
        $this->loanRepaymentRepository = $loanRepaymentRepository;
    }

    public function getModel()
    {
        return Loan::class;
    }

    public function getByUserId($userId, $skip = 0, $limit = 10)
    {
        $query = $this->_model->newQuery()->where('user_id', $userId)->skip($skip)->limit($limit)
            ->orderBy('created_at', 'desc');

        return $query->get();
    }

    public function search($search, $skip = 0, $limit = 10)
    {
        $query = $this->_model->newQuery();
        foreach ($search as $key => $value) {
            $query = $query->where($key, '=', $value);
        }
        $query->skip($skip)->limit($limit)
            ->orderBy('created_at', 'desc');

        return $query->get();
    }
}
