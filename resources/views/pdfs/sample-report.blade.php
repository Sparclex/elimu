<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        body {
            font-family: sans-serif;
        }

        @page {
            margin: 95px 25px;
        }

        header {
            position: fixed;
            top: -70px;
            left: 0px;
            height: 50px;
            border-bottom: 1px solid #22292f;
        }

        .logo {
            float: left;
            height: 50px;
        }

        .title {
            position: fixed;
            width: 100%;
            top: -70px;
            left: 0px;
            height: 50px;
            font-size: 20px;
            text-align: center;
            font-weight: normal;
        }

        .date {
            float: right;
            text-align: right;
            margin: 0;
            padding: 0;
        }

        footer {
            position: fixed;
            bottom: -60px;
            left: 0px;
            right: 0px;
            font-size: 10px;
        }

        p {
            padding: 0;
            margin: 0;
        }

        table {
            border-collapse: collapse;
        }

        .parameters {
            width: 100%;
            font-size: 12px;
        }

        .parameters td, th {
            padding: 5px;
        }

        .parameters thead {
            border-bottom: 2px solid #dae1e7;
        }

        .parameters th {
            font-weight: normal;
            text-transform: uppercase;
        }

        .parameters td {
            color: #3d4852;
        }

        .parameters tr:nth-child(2n) {
            background: #dae1e7;
        }

        .informations {
            list-style: none;
        }
        .row > * {
            display: inline-block;
            width: 32%;
            padding: 5px;
        }
        .row > *:nth-child(2) {
            text-align: center;
        }
        .row > *:last-child {
            text-align: right;
        }

        .label {
            color: #606f7b;
            font-size: 0.9rem;
            text-transform: uppercase;
        }

        h3 {
            color: #22292f;
            padding-bottom: 0.25rem;
            font-weight: normal;
            border-bottom: 2px solid #3E8F6B;
        }
    </style>
    <title>Document</title>
</head>
<body class="font-sans">
<header>
    <img src="{{public_path('img/logo.jpg')}}" height="50" class="logo">
    <h1 class="title">BSPZV3a - Malaria qPCR Report</h1>
    <p class="date">
        {{\Carbon\Carbon::now()->format('d M Y')}}<br>
        {{\Carbon\Carbon::now()->format('H:i:s')}}
    </p>
</header>
<div>
    <div class="informations">
        <div class="row">
            <div>
                <p class="label">Assay</p>
                <p>{{$experiment->reagent->assay->name}}</p>
            </div>
            <div>
                <p class="label">Version</p>
                <p>v1.0.0</p>
            </div>
            <div>
                <p class="label">SOP</p>
                <p>{{$experiment->reagent->assay->sop}}</p>
            </div>
        </div>
        <div class="row">
            <div>
                <p class="label">Master Mix Lot</p>
                <p>{{$experiment->reagent->lot}}</p>
            </div>
            <div>
                <p class="label">Expiration Date</p>
                <p>{{$experiment->reagent->expires_at->format('F Y')}}</p>
            </div>
            <div>
                <p class="label">Status</p>
                <p>Passed</p>
            </div>
        </div>
        <div class="row">
            <div>
                <p class="label">Lims Version</p>
                <p>{{config('lims.name')}} v{{config('lims.version')}}</p>
            </div>
            <div>
                <p class="label">Experiment ID</p>
                <p>{{$experiment->id}}</p>
            </div>
            <div>
                <p class="label"></p>
                <p></p>
            </div>
        </div>
    </div>

    <h3>Input Parameters</h3>
    <table class="parameters">
        <thead>
        <tr>
            @foreach($experiment->inputParameters[0] as $key => $value)
                <th>{{$key}}</th>
            @endforeach
        </tr>
        </thead>
        <tbody>
        @foreach($experiment->inputParameters as $row)
            <tr>
                @foreach($row as $key => $value)
                    <td>{{$value}}</td>
                @endforeach
            </tr>
        @endforeach

        </tbody>
    </table>
</div>
<script type='text/php'>
      if ( isset($fontMetrics) ) {
        $pdf = $fontMetrics->getCanvas();
        $font = $fontMetrics->get_font('helvetica', 'normal');
        $size = 8;
        $y = $pdf->get_height() - 28;
        $x = $pdf->get_width() - 18 - $fontMetrics->get_text_width('Page 1 of 9', $font, $size);
        $pdf->page_text($x, $y, 'Page {PAGE_NUM} of {PAGE_COUNT}', $font, $size);
        $pdf->page_text(
            18,
            $y,
            'Approved by',
            $font,
            $size);
      }




</script>
</body>
</html>
