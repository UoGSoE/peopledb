@extends('layouts.app')

@section('content')

<h3 class="title is-3">Outstanding Tasks <span class="has-text-grey is-size-4">(By Person)</span></h3>

<table class="table is-fullwidth is-striped is-hoverable">
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
                        <li>
                            <span @class([
                                'tag',
                                'has-text-weight-semibold',
                                'is-danger' => $task->pivot->created_at->diffInDays(now()) > 14,
                            ])>{{ $task->created_at->format('d/m/Y') }}</span>
                            <span class="tag has-text-weight-semibold {{ $task->css_class_tag_colour }}">{{ $task->unit->name }}</span>
                            {{ $task->description }}
                        </li>
                    @endforeach
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
@endsection
