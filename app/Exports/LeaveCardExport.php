<?php

namespace App\Exports;

use App\Models\LeaveCard;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class LeaveCardExport implements FromCollection, WithHeadings
{
    protected $personnel_id;

    public function __construct($personnel_id)
    {
        $this->personnel_id = $personnel_id;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return LeaveCard::where('personnel_id', $this->personnel_id)->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Personnel ID',
            'Period',
            'Particulars',
            'VL Earned',
            'VL Absence/Undertime With Pay',
            'VL Balance',
            'VL Absence/Undertime Without Pay',
            'SL Earned',
            'SL Absence/Undertime With Pay',
            'SL Balance',
            'SL Absence/Undertime Without Pay',
            'CTO Earned (Hrs)',
            'CTO Absence/Undertime With Pay (Hrs)',
            'CTO Balance (Hrs)',
            'CTO Remark',
            'Updated At',
            'Created At',
        ];
    }
}
