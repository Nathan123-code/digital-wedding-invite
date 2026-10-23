@extends('layouts.wedding-invite')

@section('content')
    <section class="panel panel-plan">
        @if ($floorPlanUrl)
            <div class="uploaded-event-image">
                <img src="{{ $floorPlanUrl }}" alt="Wedding floor plan">
            </div>
        @else
            <div class="plan-card">
            <div class="plan-header">
                <span>{{ $eventSettings->bride_name }}'s Side</span>
                <span>{{ $eventSettings->groom_name }}'s Side</span>
            </div>

            <div class="plan-grid">
                <div class="table-group table-group-left">
                    @foreach ([16, 15, 14, 13, 17, 18, 19, 20, 21, 22, 23, 24] as $table)
                        <div class="table table-odd">{{ $table }}</div>
                    @endforeach
                </div>

                <div class="table-group table-group-right">
                    @foreach ([1, 2, 3, 4, 8, 7, 6, 5, 9, 10, 11, 12] as $table)
                        <div class="table table-even">{{ $table }}</div>
                    @endforeach
                </div>
            </div>

            <div class="dance-floor">The Hall</div>
            </div>
        @endif

    </section>
@endsection
