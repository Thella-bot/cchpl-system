<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Internship;
use App\Models\JobListing;
use App\Models\Membership;
use App\Models\MeetingMinute;
use App\Models\Scholarship;

class MemberDashboardController extends Controller
{
    public function index()
    {
        $memberships = Membership::where('user_id', auth()->id())
            ->with(['category', 'payments' => fn ($q) => $q->orderBy('created_at', 'desc'), 'documents'])
            ->latest()
            ->get();

        $membership = $memberships->first();

        $serviceStats = [
            'meeting_minutes' => 0,
            'events' => 0,
            'jobs' => 0,
            'scholarships' => 0,
            'internships' => 0,
        ];

        if ($membership && $membership->category_id) {
            $serviceStats = [
                'meeting_minutes' => MeetingMinute::where('is_published', true)
                    ->where(function ($q) use ($membership) {
                        $q->whereNull('membership_category_id')
                          ->orWhere('membership_category_id', $membership->category_id);
                    })->count(),
                'events' => Event::where('is_published', true)
                    ->where(function ($q) use ($membership) {
                        $q->whereNull('membership_category_id')
                          ->orWhere('membership_category_id', $membership->category_id);
                    })->count(),
                'jobs' => JobListing::where('is_published', true)
                    ->where(function ($q) use ($membership) {
                        $q->whereNull('membership_category_id')
                          ->orWhere('membership_category_id', $membership->category_id);
                    })->count(),
                'scholarships' => Scholarship::where('is_published', true)
                    ->where(function ($q) use ($membership) {
                        $q->whereNull('membership_category_id')
                          ->orWhere('membership_category_id', $membership->category_id);
                    })->count(),
                'internships' => Internship::where('is_published', true)
                    ->where(function ($q) use ($membership) {
                        $q->whereNull('membership_category_id')
                          ->orWhere('membership_category_id', $membership->category_id);
                    })->count(),
            ];
        }

        return view('member.dashboard', compact('memberships', 'membership', 'serviceStats'));
    }
}
