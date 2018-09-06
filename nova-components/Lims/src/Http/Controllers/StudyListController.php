<?php

namespace Sparclex\Lims\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Sparclex\Lims\Models\Study;

class StudyListController extends Controller
{
    public function handle()
    {
        return Study::all(['id', 'study_id', 'name']);
    }
}
