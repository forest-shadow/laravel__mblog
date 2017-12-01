<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class Post extends Model
{
    use Sluggable;

    public function category()
    {
        return $this->hasOne('App/Category');
    }

    public function author()
    {
        return $this->hasOne('App/User');
    }

    public function tags()
    {
        return $this->belongsToMany(
            'App/Tag',
            'post_tags',
            'post_id',
            'tag_id');
    }

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable()
    {
        return [
            'slug' => [
                'source' => 'title'
            ]
        ];
    }
}
