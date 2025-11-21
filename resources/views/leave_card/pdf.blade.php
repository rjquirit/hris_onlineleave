<!DOCTYPE html>
<html>
<head>
    <title>Leave Card</title>
    <style>
        body { font-family: sans-serif; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 4px; text-align: center; }
        th { background-color: #f2f2f2; }
        .header { text-align: center; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Leave Card</h2>
        @if($personnel)
            <p>Name: {{ $personnel->first_name }} {{ $personnel->last_name }}</p>
            <p>Position: {{ $personnel->position }}</p>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th>Period</th>
                <th>Particulars</th>
                <th>VL Earned</th>
                <th>VL Abs/Und Pay</th>
                <th>VL Bal</th>
                <th>VL Abs/Und No Pay</th>
                <th>SL Earned</th>
                <th>SL Abs/Und Pay</th>
                <th>SL Bal</th>
                <th>SL Abs/Und No Pay</th>
                <th>CTO Earned</th>
                <th>CTO Abs/Und Pay</th>
                <th>CTO Bal</th>
                <th>Remarks</th>
            </tr>
        </thead>
        <tbody>
            @foreach($leaveCards as $card)
                <tr>
                    <td>{{ $card->PERIOD }}</td>
                    <td>{{ $card->PARTICULARS }}</td>
                    <td>{{ $card->VL_EARNED }}</td>
                    <td>{{ $card->VL_ABSENCE_UNDERTIMEWITHPAY }}</td>
                    <td>{{ $card->VL_BALANCE }}</td>
                    <td>{{ $card->VL_ABSENCE_UNDERTIMEWITHOUTPAY }}</td>
                    <td>{{ $card->SL_EARNED }}</td>
                    <td>{{ $card->SL_ABSENCE_UNDERTIMEWITHPAY }}</td>
                    <td>{{ $card->SL_BALANCE }}</td>
                    <td>{{ $card->SL_ABSENCE_UNDERTIMEWITHOUTPAY }}</td>
                    <td>{{ $card->CTO_EARNED_HRS }}</td>
                    <td>{{ $card->CTO_ABSENCE_UNDERTIMEWITHPAY_HRS }}</td>
                    <td>{{ $card->CTO_BALANCE_HRS }}</td>
                    <td>{{ $card->CTO_REMARK }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
