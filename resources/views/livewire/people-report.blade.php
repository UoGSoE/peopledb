<div>
    <h3 class="title is-3">People</h3>
    <table class="table is-fullwidth is-hoverable">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Type</th>
                <th>Group</th>
                <th>Starts</th>
                <th>Ends</th>
                <th>Reports To</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($people as $person)
                <tr>
                    <td>
                        <a href="{{ route('people.show', $person) }}">
                            {{ $person->full_name }}
                        </a>
                    </td>
                    <td>
                        <a href="mailto:{{ $person->email }}">{{ $person->email }}</a>
                    </td>
                    <td>{{ $person->type->value }}</td>
                    <td>{{ $person->group }}</td>
                    <td>{{ $person->start_at?->format('d/m/Y') }}</td>
                    <td>{{ $person->end_at?->format('d/m/Y') }}</td>
                    <td>
                        @if ($person->reportsTo)
                            <a href="{{ route('people.show', $person->reportsTo) }}">{{ $person->reportsTo->full_name }}</a>
                        @else
                            N/A
                        @endif
                    </td>
                </tr>
            @endforeach
    </table>
</div>
