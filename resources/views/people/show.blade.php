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
                {{ $person->type->name }}
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
    @if (count($person->tasks) > 0)
    <h4 class="title is-4 has-text-grey">Tasks</h4>
    <hr>
    <table class="table is-fullwidth is-striped is-hoverable">
        <thead>
            <tr>
                <th>Unit</th>
                <th>Task</th>
                <th>Type</th>
                <th>Completed At</th>
                <th>Completed By</th>
                <th>Notes</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($person->tasks as $task)
                <tr x-data="{ showNotesModal: false }">
                    <td>
                        <span class="tag has-text-weight-semibold {{ $task->css_class_tag_colour }}">{{ $task->unit->name }}</span>
                    </td>
                    <td>
                        {{ $task->description }}
                    </td>
                    <td>{{ $task->isOnboarding() ? 'Onboarding' : 'Leaving' }}</td>
                    <td>
                        <form action="{{ route('person.task.update', $person) }}" method="post">
                            @csrf
                            <input type="hidden" name="task_id" value="{{ $task->pivot->task_id }}">
                            <div class="field has-addons">
                                <div class="control is-small">
                                    <input @class([
                                        'input',
                                        'is-small',
                                        'has-text-weight-semibold',
                                        'has-background-warning' => is_null($task->pivot->completed_at) && !$task->is_optional,
                                    ]) name="task_completed_at" type="date" value="{{ $task->pivot->completed_at?->format('Y-m-d') }}" pattern="\d{4}-\d{2}-\d{2}" placeholder="Y-m-d">
                                </div>
                                <div class="control is-small">
                                    <button type="submit" class="button is-small">Update</button>
                                </div>
                            </div>
                        </form>
                    </td>
                    <td>{{ $task->pivot->completer?->full_name }}</td>
                    <td>
                        <button @click="showNotesModal = true" @class([
                            'button',
                            'is-small',
                            'is-info' => $task->pivot->notes != '',
                        ])>✏️</button>
                        <!-- modal start -->
                        <div class="modal" :class="showNotesModal ? 'is-active' : ''">
                            <div class="modal-background"></div>
                            <form x-ref="notesform" action="{{ route('person.task.update', $person) }}" method="post">
                                @csrf
                                <input type="hidden" name="task_id" value="{{ $task->pivot->task_id }}">
                                <input type="hidden" name="task_completed_at" value="{{ $task->pivot->completed_at?->format('Y-m-d') }}">
                                <input type="hidden" name="task_completed_by" value="{{ $task->pivot->completed_by }}">
                                <div class="modal-card">
                                <header class="modal-card-head">
                                    <p class="modal-card-title">Notes</p>
                                    <button @click.prevent="showNotesModal = false; $refs.notesform.reset()" class="delete" aria-label="close"></button>
                                </header>
                                <section class="modal-card-body">
                                    <div class="field">
                                        <div class="control">
                                            <textarea class="textarea" name="task_notes" id="notes-{{ $task->id }}" cols="30" rows="10">{{ $task->pivot->notes }}</textarea>
                                        </div>
                                    </div>
                                </section>
                                <footer class="modal-card-foot">
                                    <button type="submit" class="button is-success">Save changes</button>
                                    <button @click.prevent="showNotesModal = false; $refs.notesform.reset()" class="button">Cancel</button>
                                </footer>
                                </div>
                            </form>
                          </div>
                        </div>
                        <!-- modal end -->
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    @endif
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
                    <td>{{ $reportee->type->name }}</td>
                    <td>{{ $reportee->start_at?->format('d/m/Y') }}</td>
                    <td @if ($reportee->end_at?->isPast()) class="has-background-danger-light" @endif>{{ $reportee->end_at?->format('d/m/Y') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    @endif
</div>

@endsection
