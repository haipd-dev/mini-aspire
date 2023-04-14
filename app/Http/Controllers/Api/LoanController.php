<?php

namespace App\Http\Controllers\Api;
use App\Interfaces\Services\LoanServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller as BaseController;
class LoanController extends BaseController
{

    public function index(Request $request){

    }
    public function store(Request $request, LoanServiceInterface $loanService){
        $user = $request->user();
        $request->validate(
            [
                'request_id' => 'required|string',
                'amount' => 'required|integer|min:10',
                'term' => 'required|integer|min:1',
                'date' => 'nullable|date:Y-m-d'
            ]
        );
        $amount = $request->get('amount');
        $term = $request->get('term');
        $date = $request->get('date');
        $loan = $loanService->createLoan($user->id, $amount, $term ,$date);
        return response()->json($loan)->setStatusCode(Response::HTTP_CREATED);
    }

    public function payLoanRepayment(Request $request, LoanServiceInterface $loanService){
        $repaymentId = $request->get('id');
        $amount = $request->get('amount');
        $loanService->payRepayment($repaymentId, $amount);
    }
}
