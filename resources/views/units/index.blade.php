@extends('layouts.app')

@section('content')

<div class="level">
    <div class="level-left">
        <div class="level-item">
            <h3 class="title is-3">Units and Tasks</h3>
        </div>
    </div>
    <div class="level-right">
        <div class="level-item">
            <form action="{{ route('unit.store') }}" method="POST">
                @csrf
                <div class="field is-horizontal">
                    <div class="field-label is-normal">
                        <label class="label" for="new-unit-name">New&nbsp;Unit</label>
                    </div>
                    <div class="field-body">
                        <div class="field">
                            <div class="control is-expanded">
                                <input class="input" type="text" name="new_unit_name" id="new-unit-name" value="{{ old('new_unit_name') }}" required>
                            </div>
                        </div>
                        <div class="field">
                            <div class="control">
                                <button class="button">Create</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@foreach($units as $unit)
    <hr>
    <div class="box">
        <form action="{{ route('unit.update', $unit->id) }}" method="POST">
            @csrf
            <h4 class="title is-4">
                <div class="field has-addons">
                    <div class="control is-expanded">
                        <input class="input is-large" type="text" name="name" value="{{ $unit->name }}" required>
                    </div>
                    <div class="control">
                        <div class="select is-large">
                            <select name="owner_id" required>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}" @if ($unit->owner_id == $user->id) selected @endif>{{ $user->full_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </h4>

            <label class="label" for="emails">Notification Emails (comma seperated)</label>
            <div class="field">
                <div class="control">
                <input class="input" type="text" name="emails" value="{{ $unit->emails->pluck('email')->implode(', ') }}" required>
                </div>
            </div>

            <table class="table is-fullwidth is-striped">
                <thead>
                    <tr>
                        <th>Description</th>
                        <th>Applies to?</th>
                        <th>Optional?</th>
                        <th>Active?</th>
                        <th>Onboarding/Departing</th>
                </thead>
                <tbody>
                    @foreach($unit->tasks as $task)
                        @include('units.partials.task_row')
                    @endforeach
                    @include('units.partials.task_row', ['task' => \App\Models\Task::makeDefault(['unit_id' => $unit->id])])
                </tbody>
            </table>
            <div class="level">
                <div class="level-left"></div>
                <div class="level-right">
                    <div class="level-item">
                        <div class="field">
                            <div class="control">
                            <button class="button is-info">Save</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endforeach

@endsection
