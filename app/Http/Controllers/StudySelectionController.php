<?php

namespace App\Http\Controllers;

use App\Models\Study;
use Illuminate\Contracts\Auth\Guard;
use Laravel\Nova\Actions\Action;

class StudySelectionController extends Controller
{
    public function handle(Study $study, Guard $guard)
    {
        $user = $guard->user();
        if (!$user->can('select-study', $study)) {
            abort(403);
        }

        $user->studies()->updateExistingPivot($user->study, [
            'selected' => false
        ]);

        $user->studies()->updateExistingPivot($study, [
            'selected' => true
        ]);

        return Action::message('Changed study successfully');
    }
}
