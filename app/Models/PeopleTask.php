<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

/** NB: this is just a convenience model to make searching/whatevering easier. */

class PeopleTask extends Pivot
{
    public $incrementing = true;

    protected $table = 'people_task';

    protected $casts = [
        'completed_at' => 'datetime',
    ];

    public function completer()
    {
        return $this->belongsTo(User::class, 'completed_by');
    }

    public function person()
    {
        return $this->belongsTo(People::class, 'people_id');
    }

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function scopeIncomplete($query)
    {
        return $query->whereNull('completed_at');
    }

    public function isComplete(): bool
    {
        return $this->completed_at !== null;
    }

    public function isntComplete(): bool
    {
        return ! $this->isComplete();
    }
}
