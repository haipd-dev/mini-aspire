<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    $loanHelper = new \App\Helpers\LoanHelper();
    $loanHelper->calculateRepayment(500, 2, 'random string');
    return 'Mini Aspire Here';
});
