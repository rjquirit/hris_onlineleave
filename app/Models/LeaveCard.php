<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveCard extends Model
{
    protected $table = 'leave_card';

    protected $primaryKey = 'leavecardid';

    public $timestamps = true;

    protected $fillable = [
        'personnel_id',
        'PERIOD',
        'PARTICULARS',
        'VL_EARNED',
        'VL_ABSENCE_UNDERTIMEWITHPAY',
        'VL_BALANCE',
        'VL_ABSENCE_UNDERTIMEWITHOUTPAY',
        'SL_EARNED',
        'SL_ABSENCE_UNDERTIMEWITHPAY',
        'SL_BALANCE',
        'SL_ABSENCE_UNDERTIMEWITHOUTPAY',
        'CTO_EARNED_HRS',
        'CTO_ABSENCE_UNDERTIMEWITHPAY_HRS',
        'CTO_BALANCE_HRS',
        'CTO_REMARK',
    ];

    public function personnel()
    {
        return $this->belongsTo(Personnel::class, 'personnel_id', 'personnel_id'); // Assuming personnel_id maps to personnel_id in office_personnel
    }
}
