<?php

namespace App\Http\Controllers;

use App\Models\Study;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class StudyListController extends Controller
{
    public function handle()
    {
        return Auth::user()->studies()->get(['studies.id', 'studies.study_id', 'studies.name']);
    }
}
