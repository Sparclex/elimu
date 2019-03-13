<?php

namespace App\Http\Controllers;

use App\Collections\ResultDataCollection;
use App\Exports\ResultExport;
use App\Models\Assay;
use Laravel\Nova\Http\Requests\FakeNovaRequest;
use Maatwebsite\Excel\Facades\Excel;

class ResultOverviewController extends Controller
{
    public function handle(Assay $assay)
    {
        return Excel::download(new ResultExport($assay), "results.xlsx");
    }
}
