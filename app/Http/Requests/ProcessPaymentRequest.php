<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProcessPaymentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'membership_id' => ['required', 'exists:memberships,id'],
            'amount' => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'payment_method' => ['required', 'in:card,bank_transfer,mobile_money'],
            'transaction_reference' => ['required', 'string', 'max:100'],
            'payment_date' => ['required', 'date', 'before_or_equal:today'],
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'amount' => number_format($this->amount, 2, '.', ''),
            'transaction_reference' => strtoupper(trim($this->transaction_reference)),
        ]);
    }
}
