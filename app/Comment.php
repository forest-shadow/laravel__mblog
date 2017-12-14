<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    public function post()
    {
        return $this->hasOne('App/Post');
    }

    public function author()
    {
        return $this->hasOne('App/User');
    }

    public function allow()
    {
        $this->status = 1;
        $this->save();
    }

    public function disAllow()
    {
        $this->status = 0;
        $this->save();
    }

    public function toggleStatus()
    {
        if($this->status = 0)
        {
            return $this->allow();
        }

        return $this->disAllow();
    }

    public function remove()
    {
        $this->delete();
    }
}