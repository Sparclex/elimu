<?php

namespace App\Nova;

use App\Fields\StudyUserFields;
use App\Policies\Authorization;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\Gravatar;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Password;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Timezone;

class User extends Resource
{
    public static $displayInNavigation = true;

    public static $model = 'App\\User';

    public static $title = 'name';

    public static $search = [
        'id',
        'name',
        'email',
    ];

    public function fields(Request $request)
    {
        return [
            ID::make()->hideFromIndex(),

            Gravatar::make(),

            Text::make('Name')
                ->sortable()
                ->rules('required', 'max:255'),

            Text::make('Email')
                ->sortable()
                ->rules('required', 'email', 'max:255')
                ->creationRules('unique:users,email')
                ->updateRules('unique:users,email,{{resourceId}}'),

            Password::make('Password')
                ->onlyOnForms()
                ->creationRules('required', 'string', 'min:6')
                ->updateRules('nullable', 'string', 'min:6'),

            Boolean::make('Is Admin', 'is_admin')
                ->rules('required')
                ->canSee(function () {
                    return auth()->user()->is_admin;
                }),

            Timezone::make('Timezone')->rules('required')->sortable(),

            DateTime::make('Created at')->onlyOnDetail(),

            BelongsToMany::make('Studies')
                ->fields(new StudyUserFields)
        ];
    }

    public function getRoles()
    {
        $roles = [
            Authorization::MONITOR => Authorization::MONITOR,
            Authorization::SCIENTIST => Authorization::SCIENTIST,
            Authorization::LABMANAGER => Authorization::LABMANAGER,
        ];
        if (Authorization::isAdministrator()) {
            $roles[Authorization::ADMINISTRATOR] = Authorization::ADMINISTRATOR;
        }
        return $roles;
    }
}
