<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
        'title',
        'description',
        'order',
        'column_id',
    ];

    public function status()
    {
        return $this->belongsTo(Column::class);
    }
}
