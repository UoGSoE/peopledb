@component('mail::message')
# Arrivals and Departures

---

## Arrived in the past {{ config('peopledb.recent_days_arriving') }} days
@if (count($arrivalsAndDepartures->arrived) > 0)
@component('mail::table')
| Name          | Email        | Arrived | Type | Reports To |
|:------------- |:-------------|:--------|:-----|:-----------|
@foreach($arrivalsAndDepartures->arrived as $person)
| [{{ $person->full_name }}]({{ route('people.show', $person) }}) | [{{ \Str::before($person->email, '@') }}](mailto:{{ $person->email }}) | {{ $person->start_at?->format('d/m/Y') }} | {{ $person->type->value }} | {{ $person->reportsTo?->full_name }} |
@endforeach
@endcomponent
@else
No recent arrivals.
@endif

## Left in the past {{ config('peopledb.recent_days_arriving') }} days
@if (count($arrivalsAndDepartures->departed) > 0)

@component('mail::table')
| Name          | Email        | Left    | Type | Reports To |
|:------------- |:-------------|:--------|:-----|:-----------|
@foreach($arrivalsAndDepartures->departed as $person)
| [{{ $person->full_name }}]({{ route('people.show', $person) }}) | [{{ \Str::before($person->email, '@') }}](mailto:{{ $person->email }}) | {{ $person->end_at?->format('d/m/Y') }} | {{ $person->type->value }} | {{ $person->reportsTo?->full_name }} |
@endforeach
@endcomponent
@else
No recent departures.
@endif

---

## Arriving in the next {{ config('peopledb.recent_days_arriving') }} days
@if (count($arrivalsAndDepartures->arrivals) > 0)

@component('mail::table')
| Name          | Email        | Arrives | Type | Reports To |
|:------------- |:-------------|:--------|:-----|:-----------|
@foreach($arrivalsAndDepartures->arrivals as $person)
| [{{ $person->full_name }}]({{ route('people.show', $person) }}) | [{{ \Str::before($person->email, '@') }}](mailto:{{ $person->email }}) | {{ $person->start_at?->format('d/m/Y') }} | {{ $person->type->value }} | {{ $person->reportsTo?->full_name }} |
@endforeach
@endcomponent
@else
No upcoming arrivals.
@endif

## Departing in the next {{ config('peopledb.recent_days_arriving') }} days
@if (count($arrivalsAndDepartures->departures) > 0)

@component('mail::table')
| Name          | Email        | Leaves  | Type | Reports To |
|:------------- |:-------------|:--------|:-----|:-----------|
@foreach($arrivalsAndDepartures->departures as $person)
| [{{ $person->full_name }}]({{ route('people.show', $person) }}) | [{{ \Str::before($person->email, '@') }}](mailto:{{ $person->email }}) | {{ $person->end_at?->format('d/m/Y') }} | {{ $person->type->value }} | {{ $person->reportsTo?->full_name }} |
@endforeach
@endcomponent
@else
No upcoming departures.
@endif

---

@component('mail::button', ['url' => route('home')])
Log In
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
