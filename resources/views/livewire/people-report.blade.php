<div>
    <div class="level">
        <div class="level-left">
            <div class="level-item">
                <h3 class="title is-3">
                    People
                </h3>
            </div>
            <div class="level-item">
                <button class="button is-small" wire:click.prevent="toggleFilterDisplay">@if ($showAllFilters) Hide @else Show @endif filters...</button>
            </div>
        </div>
    </div>

    </h3>
    <div class="box">

        @if ($showAllFilters)
            <div class="columns">
                <div class="column is-one-third">
                    <div class="field">
                        <label class="label">Type</label>
                        <div class="control">
                        <div class="select">
                            <select wire:model="filterType">
                            <option value=""></option>
                            @foreach ($possibleTypes as $type)
                                <option value="{{ $type->value }}">{{ $type->value }}</option>
                            @endforeach
                            </select>
                        </div>
                        </div>
                    </div>
                </div>
                <div class="column is-one-third">
                    <div class="field">
                    <label class="label">Group</label>
                    <div class="control">
                        <div class="select">
                        <select wire:model="filterGroup">
                            <option value=""></option>
                            @foreach ($possibleGroups as $group)
                            <option value="{{ $group }}">{{ $group }}</option>
                            @endforeach
                        </select>
                        </div>
                    </div>
                    </div>
                </div>
                <div class="column is-one-third">
                    <div class="field">
                    <label class="label">Reports To</label>
                    <div class="control">
                        <div class="select">
                        <select wire:model="filterReportsTo">
                            <option value=""></option>
                            @foreach ($possibleReportsTo as $reportsTo)
                            <option value="{{ $reportsTo->id }}">{{ $reportsTo->full_name }}</option>
                            @endforeach
                        </select>
                        </div>
                    </div>
                    </div>
                </div>
            </div>

            <div class="columns">
                <div class="column is-one-third">
                    <label class="label">Arriving in the next</label>
                    <div class="field has-addons">
                        <div class="control">
                        <input class="input" type="text" wire:model="filterArrivingInDays">
                        </div>
                        <p class="control">
                            <a class="button is-static">
                            Days
                            </a>
                        </p>
                    </div>
                </div>
                <div class="column is-one-third"></div>
                <div class="column is-one-third">
                    <label class="label">Leaving in the next</label>
                    <div class="field has-addons">
                        <div class="control">
                            <input class="input" type="text" wire:model="filterLeavingInDays">
                        </div>
                        <p class="control">
                            <a class="button is-static">
                                Days
                            </a>
                        </p>
                    </div>
                </div>
            </div>

            <div class="columns">
                <div class="column is-one-third">
                    <label class="label">Arrived in the last</label>
                    <div class="field has-addons">
                        <div class="control">
                        <input class="input" type="text" wire:model="filterArrivedInDays">
                        </div>
                        <p class="control">
                            <a class="button is-static">
                            Days
                            </a>
                        </p>
                    </div>
                </div>
                <div class="column is-one-third"></div>
                <div class="column is-one-third">
                    <label class="label">Left in the last</label>
                    <div class="field has-addons">
                        <div class="control">
                            <input class="input" type="text" wire:model="filterLeftInDays">
                        </div>
                        <p class="control">
                            <a class="button is-static">
                                Days
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        @endif
        <div class="columns">
            <div class="column">
                <label class="label">Search...</label>
                <div class="field is-grouped">
                    <div class="control is-expanded">
                        <input class="input" type="text" wire:model="filterSearch">
                    </div>
                    <p class="control">
                        <button class="button" wire:click.prevent="exportExcel">Export</button>
                    </p>
                </div>
            </div>
        </div>
    </div>
    <table class="table is-striped is-fullwidth is-hoverable">
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
