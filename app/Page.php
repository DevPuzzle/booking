<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $fillable = ['title', 'summary', 'content'];

    protected $dates = ['published_at'];

    protected $with = ['categories','roles','regions'];

    protected $appends = ['status', 'last_updated'];

    public static function boot()
    {
        parent::boot();

        static::saving(function ($page) {
            $page->last_edited_by = auth()->id();
        });
    }

    public function publish()
    {
        $this->published_at = Carbon::now();
        $this->save();
    }

    public function unpublish()
    {
        $this->published_at = null;
        $this->save();
    }


    public function getAccessibleByAttribute()
    {
        $accessible_by = '';
        if ($this->roles->count()) {
            $accessible_by .= '<b>Roles: </b>' . implode(', ', $this->roles->pluck('name')->toArray()).'<br>';
        }
        if ($this->regions->count()) {
            $accessible_by .= '<b>Regions: </b>' . implode(', ', $this->regions->pluck('name')->toArray()).'<br>';
        }
        return $accessible_by ?: 'Everyone';
    }

    public function getCategoriesListAttribute()
    {
        if (!$this->categories->count()) {
            return '-';
        }

        $list = $this->categories->map(function ($category) {
            return $category->name;
        })->toArray();

        return implode(', ', $list);
    }
    
    public static function getStatusesArray()
    {
        return ['published','scheduled','draft'];
    }


    public function getStatusAttribute()
    {
        if ($this->published_at) {
            if ($this->published_at->isPast()) {
                return 'published';
            }
            return 'scheduled';
        }
        return 'draft';
    }
    public function getLastUpdatedAttribute()
    {
        return $this->updated_at->diffForHumans();
    }

    public function viewableBy()
    {
        return $this->hasMany(ViewBy::class);
    }

    public function roles()
    {
        return $this->morphedByMany(Role::class, 'viewable');
    }

    public function regions()
    {
        return $this->morphedByMany(Region::class, 'viewable');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'markasread')->withTimestamps();
    }

    public function categories()
    {
        
        return $this->belongsToMany(Category::class,'page_category', 'page_id', 'category_id');
    }
}

