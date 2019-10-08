@extends('layouts.app')

@section('content')

<div class="panel panel-default">
  <div class="panel-heading">Full Calendar</div>

  <div class="panel-body">

    <div class="cal-container">
      @if (\Session::has('success'))
        <div class="alert alert-success">
          <p>{{ \Session::get('success') }}</p>
        </div><br />
      @endif
      <div class="panel panel-default">
            <!-- <div class="panel-heading">
                <h2>Laravel Full Calendar Tutorial</h2>
            </div> -->
            <div class="panel-body" >
              {!! $calendar->calendar() !!}
          </div>
      </div>
    </div>
  </div>
</div>
@stop


@section('scripts')
{!! $calendar->script() !!}
@endsection
<!-- createevent.blade.php -->
