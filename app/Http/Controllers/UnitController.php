<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Unit;
use App\Models\User;
use App\Models\PeopleType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UnitController extends Controller
{
    public function index()
    {
        return view('units.index', [
            'units' => Unit::with(['owner', 'emails', 'tasks', 'tasks.peopleTypes'])->orderBy('name')->get(),
            'users' => User::orderBy('surname')->get(),
            'peopleTypes' => PeopleType::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'new_unit_name' => 'required|string|max:255|unique:units,name',
        ]);

        Unit::create([
            'name' => $request->new_unit_name,
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
            $data = [
                'description' => $request->input('description.' . $task->id),
                'is_optional' => $request->input('is_optional.' . $task->id),
                'is_onboarding' => $request->input('is_onboarding.' . $task->id),
                'is_active' => $request->input('is_active.' . $task->id),
            ];
            if ($request->filled('applies_to.' . $task->id)) {
                $data['applies_to'] = $request->input('applies_to.' . $task->id);
            }
            Validator::make($data, [
                'description' => 'required|string',
                'is_optional' => 'required|boolean',
                'is_onboarding' => 'required|boolean',
                'is_active' => 'required|boolean',
                'applies_to' => 'sometimes|required|array',
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
            $appliesTo = is_array($request->input('applies_to.' . $task->id)) ? $request->input('applies_to.' . $task->id) : [];
            $task->peopleTypes()->sync(array_values($appliesTo));
        }

        if ($request->filled('description.0')) {
            Validator::make([
                'description' => $request->input('description.0'),
                'is_optional' => $request->input('is_optional.0'),
                'is_onboarding' => $request->input('is_onboarding.0'),
                'is_active' => $request->input('is_active.0'),
            ], [
                'description' => 'required|string',
                'is_optional' => 'required|boolean',
                'is_onboarding' => 'required|boolean',
                'is_active' => 'required|boolean',
            ])->validate();

            $newTask = $unit->tasks()->save(Task::make([
                'description' => $request->input('description.0'),
                'is_optional' => $request->input('is_optional.0'),
                'is_onboarding' => $request->input('is_onboarding.0'),
                'is_active' => $request->input('is_active.0'),
            ]));

            $appliesTo = $request->input('applies_to.0') ? $request->input('applies_to.0') : [];
            $newTask->peopleTypes()->sync(array_values($appliesTo));
        }

        return redirect(route('units.index'))->with('success', 'Unit/tasks updated.');
    }
}
