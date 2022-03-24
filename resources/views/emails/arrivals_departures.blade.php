@component('mail::message')
# Upcoming Arrivals and Departures

## Arrivals

@component('mail::table')
| Name          | Email        | Arrives | Type | Reports To |
|:------------- |:-------------|:--------|:-----|:-----------|
@foreach($arrivals as $person)
| [{{ $person->full_name }}]({{ route('people.show', $person) }}) | {{ $person->email }} | {{ $person->start_at?->format('d/m/Y') }} | {{ $person->type->value }} | {{ $person->reportsTo?->full_name }} |
@endforeach
@endcomponent

## Departures

@component('mail::table')
| Name          | Email        | Leaves  | Type | Reports To |
|:------------- |:-------------|:--------|:-----|:-----------|
@foreach($departures as $person)
| [{{ $person->full_name }}]({{ route('people.show', $person) }}) | {{ $person->email }} | {{ $person->end_at?->format('d/m/Y') }} | {{ $person->type->value }} |  {{ $person->reportsTo?->full_name }} |
@endforeach
@endcomponent


@component('mail::button', ['url' => route('home')])
Log In
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
