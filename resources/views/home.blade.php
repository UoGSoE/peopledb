@extends('layouts.app')

@section('content')

<div class="columns box">
    <div class="column has-text-centered">
        <h3 class="title is-5 has-text-weight-light">Academics</h3>
        <p class="subtitle is-size-3 has-text-weight-semibold">
            <span class="is-size-5 has-text-weight-light">&darr;{{ $stats['academics_leaving_count'] }}</span>
            {{ $stats['academics_count'] }}
            <span class="is-size-5 has-text-weight-light">&uarr;{{ $stats['academics_arriving_count'] }}</span>
        </p>
    </div>
    <div class="column has-text-centered">
        <h3 class="title is-5 has-text-weight-light">PhDs</h3>
        <p class="subtitle is-size-3 has-text-weight-semibold">
            <span class="is-size-5 has-text-weight-light">&darr;{{ $stats['phds_leaving_count'] }}</span>
            {{ $stats['phds_count'] }}
            <span class="is-size-5 has-text-weight-light">&uarr;{{ $stats['phds_arriving_count'] }}</span>
        </p>
    </div>
    <div class="column has-text-centered">
        <h3 class="title is-5 has-text-weight-light">MPAs</h3>
        <p class="subtitle is-size-3 has-text-weight-semibold">
            <span class="is-size-5 has-text-weight-light">&darr;{{ $stats['mpas_leaving_count'] }}</span>
            {{ $stats['mpas_count'] }}
            <span class="is-size-5 has-text-weight-light">&uarr;{{ $stats['mpas_arriving_count'] }}</span>
        </p>
    </div>
    <div class="column has-text-centered">
        <h3 class="title is-5 has-text-weight-light">Technicians</h3>
        <p class="subtitle is-size-3 has-text-weight-semibold">
            <span class="is-size-5 has-text-weight-light">&darr;{{ $stats['technicians_leaving_count'] }}</span>
            {{ $stats['technicians_count'] }}
            <span class="is-size-5 has-text-weight-light">&uarr;{{ $stats['technicians_arriving_count'] }}</span>
        </p>
    </div>
    <div class="column has-text-centered">
        <h3 class="title is-5 has-text-weight-light">Total</h3>
        <p class="subtitle is-size-3 has-text-weight-semibold">
            <span class="is-size-5 has-text-weight-light">&darr;{{ $stats['total_leaving_count'] }}</span>
            {{ $stats['total_count'] }}
            <span class="is-size-5 has-text-weight-light">&uarr;{{ $stats['total_arriving_count'] }}</span>
        </p>
    </div>
</div>
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
                <a href="{{ route('export.arrivals_departures') }}" class="button is-pulled-right">
                    <span>Export</span>
                </a>
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
