<?php

namespace App\Http\Controllers\Api\Billing;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Billing\DiscountInvoiceRequest;
use App\Http\Requests\Api\Billing\GenerateInvoiceRequest;
use App\Http\Requests\Api\Billing\PayInvoiceRequest;
use App\Http\Resources\InvoiceResource;
use App\Models\Appointment;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class InvoiceController extends Controller
{
    public function generate(GenerateInvoiceRequest $request): JsonResponse
    {
        $appointment = Appointment::query()
            ->where('clinic_id', $request->user()->clinic_id)
            ->with('clinic')
            ->findOrFail($request->integer('appointment_id'));

        $invoice = Invoice::query()->create([
            'clinic_id' => $appointment->clinic_id,
            'patient_id' => $appointment->patient_id,
            'appointment_id' => $appointment->id,
            'amount' => $appointment->clinic->fees,
            'discount' => 0,
            'status' => 'pending',
        ]);

        return (new InvoiceResource($invoice))
            ->response()
            ->setStatusCode(JsonResponse::HTTP_CREATED);
    }

    public function pay(PayInvoiceRequest $request, int $invoice): JsonResponse
    {
        $user = $request->user();
        $data = $request->validated();

        $paidInvoice = DB::transaction(function () use ($user, $invoice, $data): Invoice {
            $invoiceRecord = $this->invoiceForClinic($user, $invoice, lock: true);

            if ($invoiceRecord->status === 'paid') {
                abort(JsonResponse::HTTP_CONFLICT, 'This invoice has already been paid.');
            }

            $invoiceRecord->payments()->create([
                'clinic_id' => $invoiceRecord->clinic_id,
                'amount' => $invoiceRecord->net_total,
                'method' => $data['method'],
                'paid_at' => now(),
                'received_by_user_id' => $user->id,
            ]);

            $invoiceRecord->update([
                'payment_method' => $data['method'],
                'status' => 'paid',
            ]);

            return $invoiceRecord->load('payments');
        });

        return (new InvoiceResource($paidInvoice))->response();
    }

    public function discount(DiscountInvoiceRequest $request, int $invoice): JsonResponse
    {
        $user = $request->user();
        $discountedInvoice = DB::transaction(function () use ($request, $user, $invoice): Invoice {
            $invoiceRecord = $this->invoiceForClinic($user, $invoice, lock: true);
            $discountInCents = (int) round((float) $request->validated('discount') * 100);

            if ($invoiceRecord->status === 'paid') {
                abort(JsonResponse::HTTP_CONFLICT, 'A paid invoice cannot be discounted.');
            }

            if ($discountInCents > $invoiceRecord->amountInCents()) {
                throw ValidationException::withMessages([
                    'discount' => ['The discount may not exceed the invoice amount.'],
                ]);
            }

            $invoiceRecord->update([
                'discount' => number_format($discountInCents / 100, 2, '.', ''),
            ]);

            return $invoiceRecord;
        });

        return (new InvoiceResource($discountedInvoice))->response();
    }

    private function invoiceForClinic(User $user, int $invoiceId, bool $lock = false): Invoice
    {
        $query = Invoice::query()->where('clinic_id', $user->clinic_id);

        if ($lock) {
            $query->lockForUpdate();
        }

        return $query->findOrFail($invoiceId);
    }
}
