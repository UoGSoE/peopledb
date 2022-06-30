@component('mail::message')
# One of your reports is leaving

This is an automated email to let you know about the upcoming leaving of a report.

* Name: {{ $supervisee->full_name }}
* Email: {{ $supervisee->email }}
* Leaves: {{ $supervisee->end_at?->format('d/m/Y') }}

If their leaving date is changing, please follow the link below to update it.  The link will expire in four weeks if no further action is needed.

@component('mail::button', ['url' => $link])
Update Leaving Date
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
