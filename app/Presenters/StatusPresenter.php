<?php
namespace App\Presenters;

use App\Models\Membership;
use App\Models\Payment;
use App\Models\Resignation;

class StatusPresenter
{
    public static function membershipStatusBadge(string $status): string
    {
        return match ($status) {
            Membership::STATUS_APPROVED  => 'bg-success',
            Membership::STATUS_PENDING   => 'bg-warning text-dark',
            Membership::STATUS_SUSPENDED => 'bg-danger',
            Membership::STATUS_EXPIRED   => 'bg-warning text-dark',
            Membership::STATUS_REJECTED  => 'bg-secondary',
            Membership::STATUS_RESIGNED  => 'bg-secondary',
            default                => 'bg-secondary',
        };
    }

    public static function paymentStatusBadge(string $status): string
    {
        return match ($status) {
            Payment::STATUS_VERIFIED => 'bg-success',
            Payment::STATUS_REJECTED => 'bg-danger',
            Payment::STATUS_VOIDED   => 'bg-secondary',
            default               => 'bg-warning text-dark',
        };
    }

    public static function resignationStatusBadge(string $status): string
    {
        return match ($status) {
            Resignation::STATUS_ACKNOWLEDGED => 'bg-green-100 text-green-800',
            Resignation::STATUS_CANCELLED    => 'bg-gray-100 text-gray-500',
            default                   => 'bg-yellow-100 text-yellow-800',
        };
    }
}
