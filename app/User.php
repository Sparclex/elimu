<?php

namespace App;

use App\Models\Study;
use App\Policies\Authorization;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'is_admin' => 'boolean',
    ];

    public function study()
    {
        return $this->belongsTo(Study::class);
    }

    public function managedStudies()
    {
        return $this->belongsToMany(Study::class)
            ->withPivot('power')
            ->wherePivot('power', '>=', Authorization::LABMANAGER);
    }

    public function isScientist()
    {
        return $this->hasPowerForStudy(Authorization::SCIENTIST);
    }

    public function hasPowerForStudy($power)
    {
        $studyId = $this->study_id;
        return $this->studies()
            ->wherePivot('power', '>=', $power)
            ->wherePivot('study_id', $studyId)
            ->exists();
    }

    public function studies()
    {
        return $this->belongsToMany(Study::class)
            ->withPivot('power');
    }
}
