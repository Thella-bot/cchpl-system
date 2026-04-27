<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Documents\AgmNoticeDocument;
use Illuminate\Http\Request;

class AgmNoticeController extends Controller
{
    protected $document;

    public function __construct(AgmNoticeDocument $document)
    {
        $this->document = $document;
    }

    public function create()
    {
        return view('admin.document-review.compose-agm');
    }

    public function store(Request $request)
    {
        $review = $this->document->store($request);

        return redirect()
            ->route('admin.documents.show', $review)
            ->with('success', 'AGM Notice saved. Preview and approve when ready.');
    }
}
