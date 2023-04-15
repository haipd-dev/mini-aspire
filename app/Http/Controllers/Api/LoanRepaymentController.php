<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Interfaces\Repositories\LoanRepaymentRepositoryInterface;
use App\Interfaces\Services\LoanServiceInterface;
use Illuminate\Http\Request;

class LoanRepaymentController extends Controller
{
    public function pay(
        Request $request,
        LoanServiceInterface $loanService,
        LoanRepaymentRepositoryInterface $loanRepaymentRepository
    ) {
        $request->validate([
            'amount' => 'required|numeric',
        ]);
        $repaymentId = $request->route('id');
        $amount = $request->get('amount');
        $repayment = $loanRepaymentRepository->find($repaymentId);
        $this->authorize('pay', $repayment);

        return response()->json($loanService->payRepayment($repaymentId, $amount));
    }
}
