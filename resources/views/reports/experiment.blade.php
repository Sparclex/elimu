<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ public_path('css/app.css') }}" media="all">
    <title>Document</title>
</head>
<body class="font-sans">
<h1>Experiment Report: {{$experiment->id}} {{$experiment->assay->name}}</h1>
<table class="mt-4">
    @foreach($experiment->assay->inputParameters->parameters as $row)
        @if ($loop->first)
            <tr>
                @foreach($row as $key => $parameter)
                    <th class="w-64">{{strtoupper($key)}}</th>
                @endforeach
            </tr>
        @endif
        <tr>
            @foreach($row as $key => $parameter)
                <td class="w-64">{{$parameter}}</td>
            @endforeach
        </tr>
    @endforeach
</table>
</body>
</html>
