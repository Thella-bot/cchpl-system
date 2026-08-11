<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Internship;
use App\Models\JobListing;
use App\Models\MeetingMinute;
use App\Models\Scholarship;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ServicesController extends Controller
{
    public function index(Request $request, string $type)
    {
        $model = $this->resolveModel($type);

        $items = $model::query()
            ->when($request->filled('q'), fn ($q) => $q->where('title', 'like', "%{$request->q}%"))
            ->when($request->filled('category'), fn ($q) => $q->where('membership_category_id', $request->category))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $categories = \App\Models\MembershipCategory::orderBy('name')->get();

        return view('admin.services.index', [
            'type' => $type,
            'label' => $this->resolveLabel($type),
            'items' => $items,
            'categories' => $categories,
        ]);
    }

    public function create(string $type)
    {
        $categories = \App\Models\MembershipCategory::orderBy('name')->get();

        return view('admin.services.create', [
            'type' => $type,
            'categories' => $categories,
        ]);
    }

    public function store(Request $request, string $type)
    {
        $model = $this->resolveModel($type);
        $rules = $this->resolveValidationRules($type);

        $data = $request->validate($rules);

        if ($request->hasFile('file')) {
            $data['file_path'] = $request->file('file')->store("services/{$type}", 'public');
        }

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store("services/{$type}", 'public');
        }

        $model::create($data);

        return redirect()->route('admin.services.index', $type)->with('success', "{$this->resolveLabel($type)} created successfully.");
    }

    public function edit(string $type, int $id)
    {
        $model = $this->resolveModel($type);
        $item = $model::findOrFail($id);
        $categories = \App\Models\MembershipCategory::orderBy('name')->get();

        return view('admin.services.edit', [
            'type' => $type,
            'item' => $item,
            'categories' => $categories,
        ]);
    }

    public function update(Request $request, string $type, int $id)
    {
        $model = $this->resolveModel($type);
        $item = $model::findOrFail($id);
        $rules = $this->resolveValidationRules($type, true);

        $data = $request->validate($rules);

        if ($request->hasFile('file')) {
            if ($item->file_path) {
                Storage::disk('public')->delete($item->file_path);
            }
            $data['file_path'] = $request->file('file')->store("services/{$type}", 'public');
        }

        if ($request->hasFile('image')) {
            if ($item->image_path) {
                Storage::disk('public')->delete($item->image_path);
            }
            $data['image_path'] = $request->file('image')->store("services/{$type}", 'public');
        }

        $item->update($data);

        return redirect()->route('admin.services.index', $type)->with('success', "{$this->resolveLabel($type)} updated successfully.");
    }

    public function destroy(string $type, int $id)
    {
        $model = $this->resolveModel($type);
        $item = $model::findOrFail($id);

        if ($item->file_path) {
            Storage::disk('public')->delete($item->file_path);
        }
        if ($item->image_path) {
            Storage::disk('public')->delete($item->image_path);
        }

        $item->delete();

        return back()->with('success', "{$this->resolveLabel($type)} deleted successfully.");
    }

    private function resolveModel(string $type): string
    {
        return match ($type) {
            'minutes' => MeetingMinute::class,
            'events' => Event::class,
            'jobs' => JobListing::class,
            'scholarships' => Scholarship::class,
            'internships' => Internship::class,
            default => abort(404, 'Unknown service type.'),
        };
    }

    private function resolveLabel(string $type): string
    {
        return match ($type) {
            'minutes' => 'Meeting minutes',
            'events' => 'Event',
            'jobs' => 'Job listing',
            'scholarships' => 'Scholarship',
            'internships' => 'Internship',
            default => ucfirst($type),
        };
    }

    private function resolveValidationRules(string $type, bool $update = false): array
    {
        $prefix = $update ? 'sometimes|nullable' : 'required';

        return match ($type) {
            'minutes' => [
                'membership_category_id' => 'nullable|exists:membership_categories,id',
                'title' => 'required|string|max:255',
                'description' => 'nullable|string|max:5000',
                'meeting_date' => 'required|date',
                'location' => 'nullable|string|max:255',
                'file' => $prefix.'|file|mimes:pdf,doc,docx|max:10240',
                'is_published' => 'boolean',
            ],
            'events' => [
                'membership_category_id' => 'nullable|exists:membership_categories,id',
                'title' => 'required|string|max:255',
                'description' => 'nullable|string|max:5000',
                'event_date' => 'required|date',
                'location' => 'nullable|string|max:255',
                'venue' => 'nullable|string|max:255',
                'image' => $prefix.'|file|mimes:jpg,jpeg,png|max:5120',
                'capacity' => 'nullable|integer|min:1',
                'spots_taken' => 'nullable|integer|min:0',
                'registration_deadline' => 'nullable|date',
                'price' => 'nullable|numeric|min:0',
                'currency' => 'nullable|string|max:3',
                'is_published' => 'boolean',
            ],
            'jobs' => [
                'membership_category_id' => 'nullable|exists:membership_categories,id',
                'title' => 'required|string|max:255',
                'description' => 'required|string|max:5000',
                'company_name' => 'required|string|max:255',
                'location' => 'nullable|string|max:255',
                'employment_type' => 'nullable|string|max:255',
                'salary_range' => 'nullable|string|max:255',
                'application_deadline' => 'nullable|date',
                'application_url' => 'nullable|url|max:2048',
                'contact_email' => 'nullable|email|max:255',
                'is_published' => 'boolean',
            ],
            'scholarships' => [
                'membership_category_id' => 'nullable|exists:membership_categories,id',
                'title' => 'required|string|max:255',
                'description' => 'required|string|max:5000',
                'provider' => 'nullable|string|max:255',
                'eligibility' => 'nullable|string|max:5000',
                'benefit' => 'nullable|string|max:5000',
                'application_deadline' => 'nullable|date',
                'application_url' => 'nullable|url|max:2048',
                'contact_email' => 'nullable|email|max:255',
                'is_published' => 'boolean',
            ],
            'internships' => [
                'membership_category_id' => 'nullable|exists:membership_categories,id',
                'title' => 'required|string|max:255',
                'description' => 'required|string|max:5000',
                'company_name' => 'required|string|max:255',
                'location' => 'nullable|string|max:255',
                'duration' => 'nullable|string|max:255',
                'stipend' => 'nullable|string|max:255',
                'application_deadline' => 'nullable|date',
                'application_url' => 'nullable|url|max:2048',
                'contact_email' => 'nullable|email|max:255',
                'is_published' => 'boolean',
            ],
            default => [],
        };
    }
}
