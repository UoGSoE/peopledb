<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/** NB: this is just a convenience model to make searching/whatevering easier. */

class PeopleTypeTask extends Model
{
    use HasFactory;

    protected $table = 'people_type_task';

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}
