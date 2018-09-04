<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
        table {
            width: 80%;
            border-collapse: collapse;
        }
        td, th {
            padding: 4px 8px;
            text-align: left;
        }
        thead tr {
            background: #f0f0f0;
        }
    </style>
</head>
<body>
    <div style="display: flex; align-items: center; justify-content: center; height: 100vh; width: 100%">
    <table>
        <thead>
            <tr>
                @foreach(array_keys($data[0]) as $header)
                    <th>{{$header}}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
        @foreach($data as $row)
            <tr>
                @foreach($row as $col)
                    <td>{{$col}}</td>
                @endforeach
            </tr>
        @endforeach
        </tbody>
    </table>
    </div>
</body>
</html>
