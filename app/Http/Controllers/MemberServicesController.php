<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Internship;
use App\Models\JobListing;
use App\Models\MeetingMinute;
use App\Models\MembershipCategory;
use App\Models\Scholarship;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MemberServicesController extends Controller
{
    public function minutes(Request $request)
    {
        $user = auth()->user();

        $minutes = MeetingMinute::query()
            ->published()
            ->notExpired()
            ->when($user->membership?->category_id, fn ($q, $id) => $q->where(function ($q) use ($id) {
                $q->whereNull('membership_category_id')->orWhere('membership_category_id', $id);
            }))
            ->when($request->filled('q'), fn ($q) => $q->where('title', 'like', "%{$request->q}%"))
            ->orderByDesc('meeting_date')
            ->paginate(10)
            ->withQueryString();

        return view('member.services.minutes', compact('minutes'));
    }

    public function events(Request $request)
    {
        $user = auth()->user();

        $events = Event::query()
            ->published()
            ->notExpired()
            ->when($user->membership?->category_id, fn ($q, $id) => $q->where(function ($q) use ($id) {
                $q->whereNull('membership_category_id')->orWhere('membership_category_id', $id);
            }))
            ->when($request->filled('q'), fn ($q) => $q->where('title', 'like', "%{$request->q}%"))
            ->orderByDesc('event_date')
            ->paginate(10)
            ->withQueryString();

        return view('member.services.events', compact('events'));
    }

    public function jobs(Request $request)
    {
        $user = auth()->user();

        $jobs = JobListing::query()
            ->published()
            ->notExpired()
            ->when($user->membership?->category_id, fn ($q, $id) => $q->where(function ($q) use ($id) {
                $q->whereNull('membership_category_id')->orWhere('membership_category_id', $id);
            }))
            ->when($request->filled('q'), fn ($q) => $q->where('title', 'like', "%{$request->q}%"))
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        return view('member.services.jobs', compact('jobs'));
    }

    public function scholarships(Request $request)
    {
        $user = auth()->user();

        $scholarships = Scholarship::query()
            ->published()
            ->notExpired()
            ->when($user->membership?->category_id, fn ($q, $id) => $q->where(function ($q) use ($id) {
                $q->whereNull('membership_category_id')->orWhere('membership_category_id', $id);
            }))
            ->when($request->filled('q'), fn ($q) => $q->where('title', 'like', "%{$request->q}%"))
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        return view('member.services.scholarships', compact('scholarships'));
    }

    public function internships(Request $request)
    {
        $user = auth()->user();

        $internships = Internship::query()
            ->published()
            ->notExpired()
            ->when($user->membership?->category_id, fn ($q, $id) => $q->where(function ($q) use ($id) {
                $q->whereNull('membership_category_id')->orWhere('membership_category_id', $id);
            }))
            ->when($request->filled('q'), fn ($q) => $q->where('title', 'like', "%{$request->q}%"))
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        return view('member.services.internships', compact('internships'));
    }

    public function redirect(string $type, int $id)
    {
        $model = match ($type) {
            'minutes' => MeetingMinute::findOrFail($id),
            'events' => Event::findOrFail($id),
            'jobs' => JobListing::findOrFail($id),
            'scholarships' => Scholarship::findOrFail($id),
            'internships' => Internship::findOrFail($id),
            default => abort(404),
        };

        $model->increment('views');

        $url = match ($type) {
            'minutes' => Storage::url($model->file_path),
            'jobs' => $model->application_url,
            'scholarships' => $model->application_url,
            'internships' => $model->application_url,
            default => null,
        };

        if ($url) {
            return redirect($url);
        }

        return back();
    }
}
