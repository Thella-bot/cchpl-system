<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DocumentReview;
use App\Models\MembershipCategory;

class ContentAdminController extends Controller
{
    public function dashboard()
    {
        $categoryCount = MembershipCategory::count();
        $pendingDocuments = DocumentReview::where('status', DocumentReview::STATUS_PENDING_REVIEW)->count();

        return view('admin.content-admin.dashboard', compact('categoryCount', 'pendingDocuments'));
    }
}
