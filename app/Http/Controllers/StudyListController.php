<?php

namespace App\Http\Controllers;

use App\Models\Study;
use Illuminate\Routing\Controller;

class StudyListController extends Controller
{
    public function handle()
    {
        return Study::all(['id', 'study_id', 'name']);
    }
}
