@extends('layouts.base')

@section('content')
    <h1>DIVI Intensivregister Data</h1>
    <h2>Kliniken</h2>
    <p>Spalten sind per Klick auf den Spaltentitel sortierbar.</p>

    <table class="table table-striped sortable" id="clinics">
        <thead class="thead-light">
            <tr>
                <th scope="col">#</th>
                <th scope="col">Name</th>
                <th scope="col">Adresse</th>
                <th scope="col">Ort</th>
                <th scope="col">Bundesland</th>
                <th scope="col">Anzahl Updates</th>
                <th scope="col">letzter Stand</th>
            </tr>
        </thead>
        <tbody>
            @foreach($clinics as $clinic)
                <tr>
                    <th scope="row">{{ $clinic['id'] }}</th>
                    <td sorttable_customkey="{{ $clinic['name'] }}">
                        <a href="{{ route('clinic', ['id' => $clinic['id']]) }}">
                            {{ $clinic['name'] }}
                        </a>
                    </td>
                    <td>{{ $clinic['address'] }}</td>
                    <td>{{ $clinic['city'] }}</td>
                    <td>{{ $clinic['state'] }}</td>
                    <td>{{ $clinic['statuses']->count() }}</td>
                    <td sorttable_customkey="{{ $clinic['last_submit_at']->format('YmdHi') }}">
                        {{ $clinic['last_submit_at']->format('d.m.Y H:i') }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
