<?php

namespace App\Http\Controllers;

use App\Http\Requests\PriorityUploadRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PriorityController extends Controller
{
    public function index(): View
    {
        return view('priority.index');
    }

    public function upload(Request $request): RedirectResponse
    {
        $request->validate([
            'file5366' => 'required|mimes:xlsx|max:50000'
        ]);

        $file = $request->file('file5366');

        if (! str_contains($file->getClientOriginalName(), '5366')) {
            return redirect()->back()->with('message', "File need to contain '5366' in the name");
        }

        $userIdHash = hash('sha256', Auth::user()->id);
        $fileName = $userIdHash . '_' . now()->format('Y-m-d') . '_' . '5366' . '.' . $file->getClientOriginalExtension();
        $filePath = $file->storeAs("uploads/$userIdHash", $fileName);

        return redirect()->back()->with('message', 'File uploaded successfully.');
    }
}
