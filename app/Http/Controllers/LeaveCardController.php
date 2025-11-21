<?php

namespace App\Http\Controllers;

use App\Models\LeaveCard;
use App\Models\Personnel;
use Illuminate\Http\Request;
use App\Exports\LeaveCardExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class LeaveCardController extends Controller
{
    public function show($personnel_id)
    {
        $leaveCards = LeaveCard::where('personnel_id', $personnel_id)->get();
        $personnel = Personnel::find($personnel_id);

        return response()->json([
            'personnel' => $personnel,
            'leave_cards' => $leaveCards
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'personnel_id' => 'required',
            'PERIOD' => 'nullable|string',
            'PARTICULARS' => 'nullable|string',
            // Add other validations as needed
        ]);

        $leaveCard = LeaveCard::create($request->all());
        return response()->json($leaveCard);
    }

    public function update(Request $request, $id)
    {
        $leaveCard = LeaveCard::findOrFail($id);
        $leaveCard->update($request->all());
        return response()->json($leaveCard);
    }

    public function destroy($id)
    {
        LeaveCard::destroy($id);
        return response()->json(['status' => 'success']);
    }

    public function exportExcel($personnel_id)
    {
        return Excel::download(new LeaveCardExport($personnel_id), 'leave_card_' . $personnel_id . '.xlsx');
    }

    public function exportPdf($personnel_id)
    {
        $leaveCards = LeaveCard::where('personnel_id', $personnel_id)->get();
        $personnel = Personnel::find($personnel_id);

        $pdf = Pdf::loadView('leave_card.pdf', compact('leaveCards', 'personnel'));
        return $pdf->download('leave_card_' . $personnel_id . '.pdf');
    }
}
