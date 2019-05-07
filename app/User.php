<?php

namespace App;

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
        'name', 'email', 'password', 'username', 'phone_no'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function leaveDays()
    {
        return $this->hasMany('App\LeaveDay');
    }

    public function getIsAdminAttribute()
    {
        return !! in_array($this->role, ['mngr', 'administrator']);
    }
    public function pages()
    {
        return $this->belongsToMany(Page::class, 'markasread')->withTimestamps();
    }
    public function role()
    {
        return $this->belongsTo(Role::class);
    }
    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function timesheets()
    {
        return $this->hasMany(Timesheet::class);
    }
}
