<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\PaymentResource;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * Display a paginated list of payments.
     */
    public function index(Request $request)
    {
        $query = Payment::with(['membership.user']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('provider')) {
            $query->where('provider', $request->provider);
        }

        $payments = $query->latest()->paginate(25)->withQueryString();

        return PaymentResource::collection($payments);
    }

    /**
     * Display a single payment.
     */
    public function show(Payment $payment)
    {
        $payment->load(['membership.user', 'membership.category']);
        return new PaymentResource($payment);
    }
}