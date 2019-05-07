<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    public static function boot()
    {
        parent::boot();

        static::saving(function($role){
            $role->slug = str_slug($role->name);
        });
    }
    public function pages()
    {
        return $this->morphToMany(Page::class, 'viewable');
    }
    public function user()
    {
        return $this->hasOne(User::class);
    }
}
