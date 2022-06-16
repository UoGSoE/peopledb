<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UnitController extends Controller
{
    public function index()
    {
        return view('units.index', [
            'units' => Unit::with(['owner', 'emails', 'tasks'])->orderBy('name')->get(),
            'users' => User::orderBy('surname')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:units',
        ]);

        Unit::create([
            'name' => $request->name,
            'owner_id' => $request->user()->id,
        ]);

        return redirect()->route('units.index')->with('success', 'Unit created.');
    }

    public function update(Unit $unit, Request $request)
    {
        Validator::make([
            'email' => $request->input('emails'),
        ], [
            'email' => 'required|string',
        ])->validate();

        $emails = collect(explode(',', $request->input('emails')))->map(function ($email) {
            return strtolower(trim($email));
        })->unique()->filter();

        foreach ($emails as $email) {
            Validator::make(
                [
                'email' => $email,
            ],
                [
                'email' => 'required|string|email',
            ]
            )->validate();
        }

        Validator::make([
            'name' => $request->input('name'),
            'owner_id' => $request->input('owner_id'),
        ], [
            'name' => 'required|string|max:255,unique:units,name,' . $unit->id,
            'owner_id' => 'required|exists:users,id',
        ]);

        foreach ($unit->tasks as $task) {
            Validator::make([
                'description' => $request->input('description.' . $task->id),
                'is_optional' => $request->input('is_optional.' . $task->id),
                'is_onboarding' => $request->input('is_onboarding.' . $task->id),
                'is_active' => $request->input('is_active.' . $task->id),
            ], [
                'description' => 'required|string',
                'is_optional' => 'required|boolean',
                'is_onboarding' => 'required|boolean',
                'is_active' => 'required|boolean',
            ])->validate();
        }

        $unit->update([
            'name' => $request->name,
            'owner_id' => $request->owner_id,
        ]);

        $unit->emails()->delete();
        foreach ($emails as $email) {
            $email = strtolower(trim($email));
            $unit->emails()->create([
                'email' => $email,
            ]);
        }

        foreach ($unit->tasks as $task) {
            $task->update([
                'description' => $request->input('description.' . $task->id),
                'is_optional' => $request->input('is_optional.' . $task->id),
                'is_onboarding' => $request->input('is_onboarding.' . $task->id),
                'is_active' => $request->input('is_active.' . $task->id),
            ]);
        }

        if ($request->input('description.new') && $request->input('description.new') !== '') {
            Validator::make([
                'description' => $request->input('description.new'),
                'is_optional' => $request->input('is_optional.new'),
                'is_onboarding' => $request->input('is_onboarding.new'),
                'is_active' => $request->input('is_active.new'),
            ], [
                'description' => 'required|string',
                'is_optional' => 'required|boolean',
                'is_onboarding' => 'required|boolean',
                'is_active' => 'required|boolean',
            ])->validate();

            $unit->tasks()->save(Task::make([
                'description' => $request->input('description.new'),
                'is_optional' => $request->input('is_optional.new'),
                'is_onboarding' => $request->input('is_onboarding.new'),
                'is_active' => $request->input('is_active.new'),
            ]));
        }

        return redirect(route('units.index'))->with('success', 'Unit/tasks updated.');
    }
}
