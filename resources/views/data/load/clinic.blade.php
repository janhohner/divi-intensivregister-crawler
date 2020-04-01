@extends('layouts.base')

@section('content')
    <h1>DIVI Intensivregister Data</h1>
    <h2>{{ $clinic['name'] }}</h2>
    <p>Folgende Stände sind gesammelt worden:</p>

    <table class="table table-striped sortable" id="clinic-statuses">
        <thead class="thead-light">
        <tr>
            <th scope="col">#</th>
            <th scope="col">ICU low care</th>
            <th scope="col">ICU high care</th>
            <th scope="col">ECMO</th>
            <th scope="col">Meldezeitpunkt</th>
        </tr>
        </thead>
        <tbody>
        @foreach($clinic['statuses'] as $status)
            <tr>
                <th scope="row">{{ count($clinic['statuses']) - $loop->iteration + 1 }}</th>
                <td sorttable_customkey="{{ \App\ClinicStatus::colourToNumber($status['icu_low_care']) }}">
                    {{ $status['icu_low_care'] }}
                </td>
                <td sorttable_customkey="{{ \App\ClinicStatus::colourToNumber($status['icu_high_care']) }}">
                    {{ $status['icu_high_care'] }}
                </td>
                <td sorttable_customkey="{{ \App\ClinicStatus::colourToNumber($status['ecmo']) }}">
                    {{ $status['ecmo'] }}
                </td>
                <td sorttable_customkey="{{ $status['submitted_at']->format('YmdHi') }}">
                    {{ $status['submitted_at']->format('d.m.Y H:i') }}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection
