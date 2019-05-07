<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    public static function boot()
    {
        parent::boot();

        static::saving(function($region){
            $region->slug = str_slug($region->name);
        });
    }

    public function pages()
    {
        return $this->morphToMany(Page::class, 'viewable');
    }

}
