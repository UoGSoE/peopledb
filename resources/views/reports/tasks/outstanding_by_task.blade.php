@extends('layouts.app')

@section('content')

<h3 class="title is-3">
    Outstanding Tasks By Task
</h3>

<table class="table is-fullwidth is-striped is-hoverable">
    <thead>
        <tr>
            <th>Unit</th>
            <th>Task</th>
            <th>Created</th>
            <th>For</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($tasks as $task)
            <tr>
                <td>
                    <span class="tag has-text-weight-semibold {{ $task->task->css_class_tag_colour }}">{{ $task->task->unit->name }}</span>
                </td>
                <td>
                    {{ $task->task->description }}
                </td>
                <td>
                    {{ $task->created_at->format('d/m/Y') }}
                </td>
                <td>
                    <a href="{{ route('people.show', $task->person) }}">{{ $task->person->full_name }}</a>
                    <span class="tag">{{ $task->person->type?->name }}</span>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
@endsection
