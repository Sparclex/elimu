<?php

namespace App;

use App\Models\Study;
use App\Policies\Authorization;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

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

    public static function boot()
    {
        parent::boot();

        static::addGlobalScope(function ($model) {
            $model->withSelectedStudy();
        });
    }

    public function managedStudies()
    {
        return $this->studies()
            ->wherePivot('power', '>=', Authorization::LABMANAGER);
    }

    public function studies()
    {
        return $this->belongsToMany(Study::class)
            ->withPivot(['power', 'selected']);
    }

    public function study()
    {
        return $this->belongsTo(Study::class);
    }

    public function scopeWithSelectedStudy(Builder $query)
    {
        return $query->addSubSelect(
            'study_id',
            DB::table('study_user')
                ->whereColumn('users.id', 'study_user.user_id')
                ->where('selected', true)
                ->select('study_id')
        );
    }

    public function isScientist($study = null)
    {
        return $this->hasPower(Authorization::SCIENTIST, $study);
    }

    public function hasPower($power, $study = null)
    {
        $studyId = $study ? $study->id : $this->study_id;
        return $this->studies()
            ->wherePivot('power', '>=', $power)
            ->wherePivot('study_id', $studyId)
            ->exists();
    }

    public function isManager($study = null)
    {
        return $this->hasPower(Authorization::LABMANAGER, $study);
    }
}
