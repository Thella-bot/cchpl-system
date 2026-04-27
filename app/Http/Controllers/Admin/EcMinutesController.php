<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Documents\EcMinutesDocument;
use Illuminate\Http\Request;

class EcMinutesController extends Controller
{
    protected $document;

    public function __construct(EcMinutesDocument $document)
    {
        $this->document = $document;
    }

    public function create()
    {
        return view('admin.document-review.compose-minutes');
    }

    public function store(Request $request)
    {
        $review = $this->document->store($request);

        return redirect()
            ->route('admin.documents.show', $review)
            ->with('success', 'EC Minutes saved. Preview and approve when ready.');
    }
}
