@extends('layouts.base')

@section('content')
    <section class="padd-section text-center">
        <div class="container">
            <h1>Fallzahlen</h1>
            <h2>{{ $clinic['name'] }}</h2>
            <p>Folgende Stände sind gesammelt worden:</p>
        </div>
    </section>

    <table class="table table-striped sortable" id="clinic-statuses">
        <thead class="thead-light">
        <tr>
            <th scope="col">#</th>
            <th scope="col">Covid19 Fälle</th>
            <th scope="col">Meldezeitpunkt</th>
        </tr>
        </thead>
        <tbody>
        @foreach($clinic['statuses'] as $status)
            <tr>
                <th scope="row">{{ count($clinic['statuses']) - $loop->iteration + 1 }}</th>
                <th scope="row">{{ $status->covid19_cases }}</th>
                <td sorttable_customkey="{{ $status['submitted_at']->format('YmdHi') }}">
                    {{ $status['submitted_at']->format('d.m.Y H:i') }}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection
