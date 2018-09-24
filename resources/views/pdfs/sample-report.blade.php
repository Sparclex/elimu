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
            border-bottom: 1px solid #333;
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
        }
        p {
            padding: 0;
            margin: 0;
        }
        footer p {

        }
        footer > p:first-child {
            float:left;
        }
        footer > p:last-child {
            float: right;
            text-align: right;
        }
        .label {

        }
        td {
            border: 1px solid #000;
            padding-right: 5px;
        }
        td:last-child {
            padding-right: 0;
        }
        .text-right {
            text-align: right;
        }
        .td-fit {
            width: 1%;
        }
        table {
            width: 100%;
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
    <table>
        <tr>
            <td class="label">Assay</td>
            <td>Something</td>
            <td class="label text-right">Version</td>
            <td>v1.0</td>
            <td class="label text-right">SOP</td>
            <td class="td-fit">BRTC_LAB_194_V01</td>
        </tr>
        <tr>
            <td class="label">Master Mix Lot</td>
            <td>XYZ</td>
            <td class="label text-right">Expiration Date</td>
            <td>January 2020</td>
            <td class="label text-right">Status</td>
            <td class="td-fit">Passed</td>
        </tr>

    </table>
</div>
<footer>
    <p>Approved by</p>
    <p>Page x of y</p>
</footer>
</body>
</html>
