<!DOCTYPE html>
<html>
<head>
    <title>Personnel Report</title>
    <style>
        body { font-family: sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>Personnel List</h1>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Email</th>
            </tr>
        </thead>
        <tbody>
            @foreach($personnel as $p)
            <tr>
                <td>{{ $p->id }}</td>
                <td>{{ $p->first_name }}</td>
                <td>{{ $p->last_name }}</td>
                <td>{{ $p->email }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
