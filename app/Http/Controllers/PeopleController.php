<?php

namespace App\Http\Controllers;

use App\Models\People;
use Illuminate\Http\Request;

class PeopleController extends Controller
{
    public function show(People $person)
    {
        $person->load(['type', 'reportsTo', 'reportees', 'reportees.type', 'tasks', 'tasks.unit']);

        return view('people.show', [
            'person' => $person,
        ]);
    }
}
