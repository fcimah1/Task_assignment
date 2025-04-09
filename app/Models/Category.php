<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name'];
    protected $table = 'categories';

    public function Task()
    {
        return $this->hasMany('App\Models\Task', 'category_id', 'id');
    }
}
