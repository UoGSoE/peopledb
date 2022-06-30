@extends('layouts.app')

@section('content')

<h3 class="title is-3">
    Outstanding Tasks <span class="has-text-grey is-size-4">(By Task)</span>
</h3>

<table class="table is-fullwidth is-striped is-hoverable">
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
            <tr>
                <td>
                    {{ $task->task->description }}
                </td>
                <td>
                    <a href="{{ route('people.show', $task->person) }}">{{ $task->person->full_name }}</a>
                    <span class="tag">{{ $task->person->type?->name }}</span>
                </td>
                <td>
                    <span class="tag has-text-weight-semibold {{ $task->task->css_class_tag_colour }}">{{ $task->task->unit->name }}</span>
                </td>
                <td>
                    {{ $task->created_at->format('d/m/Y') }}
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
@endsection
