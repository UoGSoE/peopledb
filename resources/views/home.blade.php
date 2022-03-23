@extends('layouts.app')

@section('content')

<div class="columns">
    <div class="column">
        <div class="box">

            <h3 class="title is-3 has-text-grey">
                <span>Arrived/Arriving</span>
            </h3>
            <table class="table is-full-width is-striped is-hoverable">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Starts</th>
                        <th>Type</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($recentishArrivals as $user)
                        <tr @if ($user->start_at?->isPast()) class="has-background-success-light" @endif>
                            <td>
                                <a href="{{ route('people.show', $user) }}">
                                    {{ $user->full_name }}
                                </a>
                            </td>
                            <td>
                                <a href="mailto:{{ $user->email }}">{{ $user->email }}</a>
                            </td>
                            <td>
                                {{ $user->start_at?->format('d/m/Y') }}
                            </td>
                            <td>{{ $user->type->value }}</td>
                        </tr>
                    @endforeach
            </table>
        </div>
    </div>
    <div class="column">
        <div class="box">

            <h3 class="title is-3 has-text-grey">
                <span>Left/Leaving</span>
            </h3>
            <table class="table is-full-width is-striped is-hoverable">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Leaves</th>
                        <th>Type</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($recentishDepartures as $user)
                        <tr @if ($user->end_at?->isPast()) class="has-background-danger-light" @endif>
                            <td>
                                <a href="{{ route('people.show', $user) }}">
                                    {{ $user->full_name }}
                                </a>
                            </td>
                            <td>
                                <a href="mailto:{{ $user->email }}">{{ $user->email }}</a>
                            </td>
                            <td>
                                {{ $user->end_at?->format('d/m/Y') }}
                            </td>
                            <td>{{ $user->type->value }}</td>
                        </tr>
                    @endforeach
            </table>
        </div>
    </div>
</div>

@endsection
