@extends('layouts.app')

@section('content')


<div class="box">
    <h3 class="title is-3">
        {{ $person->full_name }} <span class="has-text-weight-light has-text-grey">({{ $person->username }})</span>
    </h3>
    <div class="columns">
        <div class="column">
            <h4 class="title is-4  has-text-grey">Email</h4>
            <p class="subtitle has-text-grey-dark has-text-weight-medium">
                <a href="mailto:{{ $person->email }}">{{ $person->email }}</a>
            </p>
        </div>
        <div class="column">
            <h4 class="title is-4  has-text-grey">Phone</h4>
            <p class="subtitle has-text-grey-dark has-text-weight-medium">
                @if ($person->phone)
                    <a href="tel:{{ $person->phone }}">{{ $person->phone }}</a>
                @else
                    N/A
                @endif
            </p>
        </div>
        <div class="column">
            <h4 class="title is-4  has-text-grey">Type</h4>
            <p class="subtitle has-text-grey-dark has-text-weight-medium">
                {{ $person->type->value }}
            </p>
        </div>
        <div class="column">
            <h4 class="title is-4  has-text-grey">Group</h4>
            <p class="subtitle has-text-grey-dark has-text-weight-medium">
                {{ $person->group }}
            </p>
        </div>
    </div>
    <div class="columns">
        <div class="column">
            <h4 class="title is-4  has-text-grey">@if ($person->start_at?->isBefore(now())) Started @else Starts @endif</h4>
            <p class="subtitle has-text-grey-dark has-text-weight-medium">
                {{ $person->start_at?->format('d/m/Y') }}
            </p>
        </div>
        <div class="column">
            <h4 class="title is-4  has-text-grey">@if ($person->end_at?->isBefore(now())) Left @else Ends @endif</h4>
            <p class="subtitle has-text-grey-dark has-text-weight-medium">
                {{ $person->end_at?->format('d/m/Y') }}
            </p>
        </div>
        <div class="column">
            <h4 class="title is-4  has-text-grey">Reports To</h4>
            <p class="subtitle has-text-grey-dark has-text-weight-medium">
                @if ($person->reportsTo)
                    <a href="{{ route('people.show', $person->reportsTo) }}">{{ $person->reportsTo->full_name }}</a>
                @else
                    N/A
                @endif
            </p>
        </div>
        <div class="column">
            <h4 class="title is-4  has-text-grey">MS Teams</h4>
            <p class="subtitle has-text-grey-dark has-text-weight-medium">
                <span><a class="button is-small" href="callto:{{ $person->email }}">Call</a></span>
                <span><a class="button is-small" href="https://teams.microsoft.com/l/chat/0/0?users={{ $person->email }}">Chat</a></span>
            </p>
        </div>
    </div>
    @if (count($person->reportees) > 0)
    <hr>
    <h4 class="title is-4  has-text-grey">Reportees</h4>
    <table class="table is-fullwidth is-striped is-hoverable">
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
