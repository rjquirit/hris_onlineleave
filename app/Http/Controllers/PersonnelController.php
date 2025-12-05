<?php

namespace App\Http\Controllers;

use App\Models\Personnel;
use Illuminate\Http\Request;

class PersonnelController extends Controller
{
    public function index()
    {
        return Personnel::all();
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'position' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'salary' => 'required|numeric',
        ]);

        // Check email uniqueness using blind index
        $encryptionService = app(\App\Services\EncryptionService::class);
        $emailSearchIndex = $encryptionService->generateBlindIndex($validatedData['email']);

        if (Personnel::where('email_search_index', $emailSearchIndex)->exists()) {
            return response()->json([
                'message' => 'The email has already been taken.',
                'errors' => ['email' => ['The email has already been taken.']],
            ], 422);
        }

        $personnel = Personnel::create($validatedData);

        return response()->json($personnel, 201);
    }

    public function show($id)
    {
        return Personnel::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $personnel = Personnel::findOrFail($id);

        $validatedData = $request->validate([
            'first_name' => 'string|max:255',
            'last_name' => 'string|max:255',
            'email' => 'string|email|max:255',
            'position' => 'string|max:255',
            'department' => 'string|max:255',
            'salary' => 'numeric',
        ]);

        // Check email uniqueness using blind index (if email is being updated)
        if (isset($validatedData['email'])) {
            $encryptionService = app(\App\Services\EncryptionService::class);
            $emailSearchIndex = $encryptionService->generateBlindIndex($validatedData['email']);

            $existingPersonnel = Personnel::where('email_search_index', $emailSearchIndex)
                ->where('id', '!=', $id)
                ->first();

            if ($existingPersonnel) {
                return response()->json([
                    'message' => 'The email has already been taken.',
                    'errors' => ['email' => ['The email has already been taken.']],
                ], 422);
            }
        }

        $personnel->update($validatedData);

        return response()->json($personnel);
    }

    public function destroy($id)
    {
        $personnel = Personnel::findOrFail($id);
        $personnel->delete();

        return response()->json(['message' => 'Personnel deleted successfully']);
    }

    public function downloadPdf()
    {
        $personnel = Personnel::all();
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.sample', compact('personnel'));

        return $pdf->download('personnel.pdf');
    }

    public function exportExcel()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\PersonnelExport, 'personnel.xlsx');
    }
}
