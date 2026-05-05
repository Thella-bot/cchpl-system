<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\MembershipResource;
use App\Models\Membership;
use Illuminate\Http\Request;

class MembershipController extends Controller
{

public function index(Request $request)
    {
        $query = Membership::with(['user', 'category']);

if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

$memberships = $query->latest()->paginate(25)->withQueryString();

return MembershipResource::collection($memberships);
    }

public function show(Membership $membership)
    {
        $membership->load(['user', 'category', 'documents', 'payments']);
        return new MembershipResource($membership);
    }
}