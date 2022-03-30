@extends('layouts.app')

@section('content')

<h3 class="title is-3">Site options</h3>

<form action="{{ route('options.update') }}" method="post">
    @csrf
    <div class="field">
        <label class="label">Days that count as 'recent' for arrivals</label>
        <div class="control">
          <input class="input" name="recent_days_arriving" type="number" value="{{ old('recent_days_arriving', $recent_days_arriving) }}" min="1" required>
        </div>
        @error('recent_days_arriving')
            <p class="help is-danger">{{ $message }}</p>
        @enderror
    </div>
    <div class="field">
        <label class="label">Days that count as 'recent' for departures</label>
        <div class="control">
          <input class="input" name="recent_days_leaving" type="number" value="{{ old('recent_days_leaving', $recent_days_leaving) }}" min="1" required>
        </div>
        @error('recent_days_arriving')
            <p class="help is-danger">{{ $message }}</p>
        @enderror
    </div>
    <div class="field">
        <label class="label">Comma seperated email addresses of people who get the recent arrivals/departures mail</label>
        <div class="control">
          <input class="input" name="arrivals_departures_recipients" type="text" value="{{ old('arrivals_departures_recipients', $arrivals_departures_recipients) }}" placeholder="office1@example.ac.uk, admin@example.ac.uk" required>
        </div>
        @error('arrivals_departures_recipients')
            <p class="help is-danger">{{ $message }}</p>
        @enderror
        @error('email')
            <p class="help is-danger">{{ $message }}</p>
        @enderror
    </div>
    <hr>
    <div class="field">
        <div class="control">
          <button class="button">Update</button>
        </div>
    </div>
</form>
@endsection
