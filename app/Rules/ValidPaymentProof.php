<?php
namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidPaymentProof implements Rule {
    public function passes($attribute, $value) {
        if (!$value) return false;

        $size = $value->getSize();
        if ($size > 5120 * 1024) { // 5MB
            return false;
        }

        $mimeType = $value->getMimeType();
        $allowed = ['image/jpeg', 'image/png'];

        return in_array($mimeType, $allowed);
    }

    public function message() {
        return 'The :attribute must be a JPG or PNG image (max 5MB).';
    }
}