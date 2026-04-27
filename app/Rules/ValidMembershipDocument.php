<?php
namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidMembershipDocument implements Rule {
    protected $documentType;

    public function __construct($documentType = null) {
        $this->documentType = $documentType;
    }

    public function passes($attribute, $value) {
        if (!$value) return false;

        $size = $value->getSize();
        if ($size > 5120 * 1024) { // 5MB
            return false;
        }

        $mimeType = $value->getMimeType();
        $allowed = ['application/pdf', 'image/jpeg', 'image/png', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];

        return in_array($mimeType, $allowed);
    }

    public function message() {
        return 'The :attribute must be a PDF, Word document, or image file (max 5MB).';
    }
}