<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name'];

    public static function boot()
    {
        parent::boot();

        static::saving(function($page){
            $page->slug = str_slug($page->name);
        });
    }

    public function pages()
    {
        return $this->belongsToMany(Page::class ,'page_category','category_id','page_id');
    }
}
