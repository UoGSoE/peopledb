@extends('layouts.app')

@section('content')


<div class="box">
    <h3 class="title is-3">
        Edit leaving date for {{ $supervisee->full_name }}
    </h3>
    <form action="" method="post">
        @csrf
        <div class="field">
            <label class="label">Leaves</label>
            <div class="control">
              <input class="input" name="end_at" type="date" value="{{ $supervisee->end_at?->format('Y-m-d') }}" required>
            </div>
        </div>
        <hr>
        <div class="field">
            <div class="control">
                <button type="submit" class="button is-primary">Save</button>
            </div>
        </div>
    </form>
</div>

@endsection
