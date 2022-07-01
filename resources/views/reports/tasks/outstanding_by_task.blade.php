@extends('layouts.app')

@section('content')

<h3 class="title is-3">
    Outstanding Tasks <span class="has-text-grey is-size-4">(By Task)</span>
</h3>
@if (count($tasks) == 0)
    <p>No outstanding tasks found. Hurrah.</p>
@else
    <div x-data="{ unit: '' }" x-init="$watch('unit', value => value == '' ? $refs.allbutton.focus() : '')">
        <div class="field is-grouped">
            <p class="control">
                <button @click="unit = ''" x-ref="allbutton" class="button" :class="unit == '' ? 'is-light' : ''">All</button>
            </p>
            @foreach ($units as $unit)
                <p class="control">
                    <button @click="unit == '{{ $unit->name }}' ? unit = '' : unit = '{{ $unit->name }}'" class="button" :class="unit == '{{ $unit->name }}' ? '{{ $unit->css_class_tag_colour }}' : ''">{{ $unit->name }}</button>
                </p>
            @endforeach
        </div>
        <table class="table is-fullwidth is-striped is-hoverable" x-cloak>
            <thead>
                <tr>
                    <th>Task</th>
                    <th>For</th>
                    <th>Unit</th>
                    <th>Created</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($tasks as $task)
                    <tr x-show="unit ? '{{ $task->task->unit->name }}' == unit : true">
                        <td>
                            {{ $task->task->description }}
                        </td>
                        <td>
                            <a href="{{ route('people.show', $task->person) }}">{{ $task->person->full_name }}</a>
                            <span class="tag">{{ $task->person->type?->name }}</span>
                        </td>
                        <td>
                            <span @click="unit ? unit = '' : unit = '{{ $task->task->unit->name }}'" class="tag is-clickable has-text-weight-semibold {{ $task->task->css_class_tag_colour }}">{{ $task->task->unit->name }}</span>
                        </td>
                        <td>
                            {{ $task->created_at->format('d/m/Y') }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
@endsection
