<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Column extends Model
{

    protected $fillable = [
        'title',
        'order'
    ];

    public function tasks()
    {
        return $this->hasMany(Task::class)->orderBy('order');
    }
}
