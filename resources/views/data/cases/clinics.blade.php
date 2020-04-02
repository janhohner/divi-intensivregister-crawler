@extends('layouts.base')

@section('content')
    <section class="padd-section text-center">
        <div class="container">
            <h1>Fallzahlen</h1>
            <p>
                Die Daten werden stündlich zur halben Stunde aus der
                <a href="https://www.divi.de/register/kartenansicht" target="_blank">DIVI Intensivregister Kartenansicht</a>
                gesammelt und gespeichert.<br>
                @if ($lastUpdate)
                    Die neuesten Einträge sind vom {{ $lastUpdate->format('d.m.Y H:i') }} Uhr.<br>
                @endif
                Nutzung der Daten ist unter
                <a href="https://creativecommons.org/licenses/by-sa/3.0/de/" target="_blank">CC BY-SA 3.0</a>
                möglich. Zitatvorschlag:
                <i>
                    Hohner, J., Lengenfelder, L. (2020),
                    <a href="https://divi.hohner.dev">https://divi.hohner.dev</a>
                </i>
            </p>
            <a href="{{ route('data.cases.export', ['type' => 'json']) }}" class="btn-get-started mr-2 font-weight-bold">JSON</a>
            <a href="{{ route('data.cases.export', ['type' => 'csv']) }}" class="btn-get-started ml-2 font-weight-bold">CSV</a>
        </div>
    </section>

    <section class="padd-section">
        <div class="container text-center">
            <h2>Kliniken</h2>
            <p>Spalten sind per Klick auf den Spaltentitel sortierbar.</p>
        </div>

        <table class="table table-striped sortable" id="clinics">
            <thead class="thead-light">
            <tr>
                <th scope="col">#</th>
                <th scope="col">Name</th>
                <th scope="col">Bundesland</th>
                <th scope="col">Breitengrad</th>
                <th scope="col">Längengrad</th>
                <th scope="col">Anzahl Updates</th>
                <th scope="col">letzter Stand</th>
            </tr>
            </thead>
            <tbody>
            @foreach($clinics as $clinic)
                <tr>
                    <th scope="row">{{ $clinic['id'] }}</th>
                    <td sorttable_customkey="{{ $clinic['name'] }}">
                        <a href="{{ route('data.cases.clinic', ['id' => $clinic['id']]) }}">
                            {{ $clinic['name'] }}
                        </a>
                    </td>
                    <td>{{ $clinic['state'] }}</td>
                    <td>{{ $clinic['lat'] }}</td>
                    <td>{{ $clinic['lon'] }}</td>
                    <td>{{ $clinic['statuses']->count() }}</td>
                    <td sorttable_customkey="{{ $clinic['last_submit_at']->format('YmdHi') }}">
                        {{ $clinic['last_submit_at']->format('d.m.Y H:i') }}
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </section>
@endsection
