@extends('layouts.app')

@section('content')


<div class="box">
    <h3 class="title is-3">{{ $person->full_name }}</h3>
    <div class="columns">
        <div class="column">
            <h4 class="title is-4">Email</h4>
            <p class="subtitle">
                <a href="mailto:{{ $person->email }}">{{ $person->email }}</a>
            </p>
        </div>
        <div class="column">
            <h4 class="title is-4">Phone</h4>
            <p class="subtitle">
                @if ($person->phone)
                    <a href="tel:{{ $person->phone }}">{{ $person->phone }}</a>
                @else
                    N/A
                @endif
            </p>
        </div>
        <div class="column">
            <h4 class="title is-4">Type</h4>
            <p class="subtitle">
                {{ $person->type->value }}
            </p>
        </div>
        <div class="column">
            <h4 class="title is-4">Group</h4>
            <p class="subtitle">
                {{ $person->group }}
            </p>
        </div>
    </div>
    <div class="columns">
        <div class="column">
            <h4 class="title is-4">Starts</h4>
            <p class="subtitle">
                {{ $person->start_at?->format('d/m/Y') }}
            </p>
        </div>
        <div class="column">
            <h4 class="title is-4">Ends</h4>
            <p class="subtitle">
                {{ $person->end_at?->format('d/m/Y') }}
            </p>
        </div>
        <div class="column">
            <h4 class="title is-4">Reports To</h4>
            <p class="subtitle">
                @if ($person->reportsTo)
                    <a href="{{ route('people.show', $person->reportsTo) }}">{{ $person->reportsTo->full_name }}</a>
                @else
                    N/A
                @endif
            </p>
        </div>
        <div class="column"></div>
    </div>
    @if ($person->reportees)
    <h4 class="title is-4">Reportees</h4>
    <table class="table is-full-width is-striped is-hoverable">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Type</th>
                <th>Start</th>
                <th>End</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($person->reportees->sortBy('surname') as $reportee)
                <tr>
                    <td>
                        <a href="{{ route('people.show', $reportee) }}">
                            {{ $reportee->full_name }}
                        </a>
                    </td>
                    <td>
                        <a href="mailto:{{ $reportee->email }}">{{ $reportee->email }}</a>
                    </td>
                    <td>{{ $reportee->type->value }}</td>
                    <td>{{ $reportee->start_at?->format('d/m/Y') }}</td>
                    <td @if ($reportee->end_at?->isPast()) class="has-background-danger-light" @endif>{{ $reportee->end_at?->format('d/m/Y') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    @endif
</div>

@endsection
