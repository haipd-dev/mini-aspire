<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller as BaseController;
use App\Interfaces\Repositories\LoanRepaymentRepositoryInterface;
use App\Interfaces\Repositories\LoanRepositoryInterface;
use App\Interfaces\Services\LoanServiceInterface;
use App\Models\Loan;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class LoanController extends BaseController
{
    public function index(Request $request, LoanRepositoryInterface $loanRepository)
    {
        $id = $request->route('id');
        /** @var $loan Loan */
        $loan = $loanRepository->find($id);
        $loan->repayments;
        $this->authorize('view', $loan);

        return response()->json($loan);
    }

    public function list(Request $request, LoanRepositoryInterface $loanRepository)
    {
        $userId = $request->user()->id;
        $skip = $request->get('skip', 0);
        $limit = $request->get('limit', 20);

        return response()->json($loanRepository->getByUserId($userId, $skip, $limit));
    }

    public function listRepayments(Request $request, LoanRepaymentRepositoryInterface $loanRepaymentRepository)
    {
        $id = $request->route('id');
        $listRepayments = $loanRepaymentRepository->getByLoanId($id);
        return response()->json($listRepayments);
    }

    public function store(Request $request, LoanServiceInterface $loanService)
    {
        $user = $request->user();
        $request->validate(
            [
                'request_id' => 'required|string',
                'amount' => 'required|integer|min:10',
                'term' => 'required|integer|min:1',
                'date' => 'nullable|date:Y-m-d',
            ]
        );
        $amount = $request->get('amount');
        $term = $request->get('term');
        $date = $request->get('date');
        $loan = $loanService->createLoan($user->id, $amount, $term, $date);

        return response()->json($loan)->setStatusCode(Response::HTTP_CREATED);
    }

    public function approveLoan(Request $request, LoanServiceInterface $loanService)
    {
        $id = $request->route('id');
        $this->authorize('approve', Loan::class);

        return response()->json($loanService->approveLoan($id));
    }

    public function getAllLoans(Request $request, LoanRepositoryInterface $loanRepository)
    {
        $skip = $request->get('skip', 0);
        $limit = $request->get('limit', 10);
    }
}
