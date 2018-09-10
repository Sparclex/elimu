<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\Study;

class StudyListController extends Controller
{
    public function handle()
    {
        return Study::all(['id', 'study_id', 'name']);
    }
}
