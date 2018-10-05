<?php

namespace App\Http\Controllers;

use App\Models\Study;
use Illuminate\Support\Facades\Auth;
use Laravel\Nova\Actions\Action;

class StudySelectionController extends Controller
{
    public function handle(Study $study)
    {
        if (!Auth::user()->can('select-study', $study)) {
            abort(403);
        }
        Auth::user()->study_id = $study->id;
        Auth::user()->save();
        return Action::message('Changed study successfully');
    }
}
