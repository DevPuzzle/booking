<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Timesheet extends Model
{
    protected $fillable = ['hours','notes' ,'date', 'user_id'];
    protected $dates = ['date'];

    public function user()
    {
      return $this->belongsTo(User::class);
    }
}
