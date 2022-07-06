@extends('layouts.app')

@section('content')

<div class="level">
    <div class="level-left">
        <div class="level-item">
            <h3 class="title is-3">Outstanding Tasks <span class="has-text-grey is-size-4">(By Person)</span></h3>
        </div>
    </div>
    <div class="level-right">
        <div class="level-item">
            <a class="button" href="{{ route('reports.tasks.outstanding.export') }}">Export</a>
        </div>
    </div>
</div>

@if (count($people) == 0)
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
                    <th>Name</th>
                    <th>Outstanding Tasks</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($people as $person)
                    <tr>
                        <td>
                            <span class="is-size-4">
                                <a href="{{ route('people.show', $person) }}">{{ $person->full_name }}</a>
                                <span class="tag is-medium">{{ $person->type->name }}</span>
                            </span>
                        </td>
                        <td>
                            @foreach ($person->tasks as $task)
                                <li x-show="unit ? '{{ $task->unit->name }}' == unit : true"">
                                    <span @class([
                                        'tag',
                                        'has-text-weight-semibold',
                                        'is-danger' => $task->pivot->created_at->diffInDays(now()) > 14,
                                    ])>{{ $task->created_at->format('d/m/Y') }}</span>
                                    <span @click="unit ? unit = '' : unit = '{{ $task->unit->name }}'" class="tag is-clickable has-text-weight-semibold {{ $task->css_class_tag_colour }}">{{ $task->unit->name }}</span>
                                    {{ $task->description }}
                                </li>
                            @endforeach
                        </td>
                    </tr>
                @endforeach
            </tbody>
    </table>
    </div>
@endif
@endsection
