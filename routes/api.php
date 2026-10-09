<?php

use App\Http\Controllers\Api\Billing\InvoiceController;
use App\Http\Controllers\Api\Clinical\ConsultationController;
use App\Http\Controllers\Api\Clinical\PrescriptionController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth'])->group(function (): void {
    Route::post('/consultations', [ConsultationController::class, 'store'])
        ->middleware('doctor');

    Route::post('/consultations/{consultation}/prescriptions', [PrescriptionController::class, 'store'])
        ->whereNumber('consultation')
        ->middleware('doctor');

    Route::get('/prescriptions/{prescription}/print', [PrescriptionController::class, 'print'])
        ->whereNumber('prescription');

    Route::post('/invoices/generate', [InvoiceController::class, 'generate']);
    Route::patch('/invoices/{invoice}/pay', [InvoiceController::class, 'pay'])->whereNumber('invoice');
    Route::patch('/invoices/{invoice}/discount', [InvoiceController::class, 'discount'])->whereNumber('invoice');
});
