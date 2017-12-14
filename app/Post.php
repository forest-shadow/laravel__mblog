<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Support\Facades\Storage;

class Post extends Model
{
    use Sluggable;

    const IS_DRAFT = 0;
    const IS_PUBLIC = 1;

    // Add Post properties for mass assignment
    protected $fillable = ['title', 'content'];

    /**
     * Add one-to-one Category relation
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function category()
    {
        return $this->hasOne('App/Category');
    }

    /**
     * Add one-to-one User relation
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function author()
    {
        return $this->hasOne('App/User');
    }


    /**
     * Add many-to-many relation to Tag
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
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

    /**
     * Add new post
     * @param $fields - array of posts properties: title, content
     * @return static - return new created post
     */
    public static function add($fields)
    {
        $post = new static;
        $post->fill($fields);
        $post->user_id = 1;
        $post->save();

        return $post;
    }


    /**
     * Edit post
     * @param $fields
     */
    public function edit($fields)
    {
        $this->fill($fields);
        $this->save();
    }


    /**
     * Delete post with tied image
     */
    public function remove()
    {
        //also delete image tied to post
        Storage::delete('uploads/' . $this->image);
        $this->delete();
    }

    /**
     * Upload image for post
     * @param $image
     */
    public function uploadImage($image)
    {
        if($image == null) { return; }
        // if use edit mode, delete previous uploaded image to replace it with new one
        Storage::delete('uploads/' . $this->image);
        // generate image's filename
        $filename = str_random(10) . '.' . $image->extension();
        $image->saveAs('uploads', $filename);
        $this->image = $filename;
        $this->save();
    }

    public function getImage()
    {
        if($this->image == null)
        {
            return '/img/no-image.png';
        }

        return '/uploads/' . $this->image;
    }


    /**
     * Set posts category. Here we use direct category_id coupling -
     * assigning it to appropriate property that deals with category_id column
     * @param $id - category id
     */
    public function setCategory($id)
    {
        if($id == null) {return;}

        $this->category_id = $id;
        $this->save();
    }

    /**
     * Set posts tag. Here we use Laravel method of coupling tag ids -
     * through functional approach and using sync()
     * @param $ids - array og post tags
     */
    public function setTags($ids)
    {
        if($ids == null) {return;}

        $this->tags()->sync($ids);
    }


    /**
     * Change post status to Draft
     */
    public function setDraft()
    {
        $this->status = Post::IS_DRAFT;
        $this->save();
    }

    /**
     * Change post status to Public
     */
    public function setPublic()
    {
        $this->status = Post::IS_PUBLIC;
        $this->save();
    }


    /**
     * Post status toggler
     * @param $value
     */
    public function toggleStatus($value)
    {
        if($value == null)
        {
            return $this->setDraft();
        }

        return $this->setPublic();
    }

    /**
     * Add post to Featured
     */
    public function setFeatured()
    {
        $this->is_featured = 1;
        $this->save();
    }

    /**
     * Remove post from Featured
     */
    public function setStandard()
    {
        $this->is_featured = 0;
        $this->save();
    }

    /**
     * Post's display in featured toggler
     * @param $value
     */
    public function toggleFeatured($value)
    {
        if($value == null)
        {
            return $this->setStandard();
        }

        return $this->setFeatured();
    }
}
