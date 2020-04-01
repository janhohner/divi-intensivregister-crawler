@extends('layouts.base')

@section('content')
    <section id="hero" class="wow fadeIn">
        <div class="hero-container">
            <div class="container">
                <h1>Divi Intensivregister Daten</h1>
                <p class="lead">
                    Die Daten der Auslastung werden stündlich zur ganzen Stunde aus dem
                    <a href="https://www.divi.de/register/intensivregister" target="_blank">DIVI Intensivregister</a>
                    gesammelt und gespeichert. <!--Die Fallzahlen stammen aus der
                    <a href="https://www.divi.de/register/kartenansicht" target="_blank">DIVI Intensivregister Kartenansicht</a>
                    und werden stündlich zur halben Stunde gesammelt und gespeichert.-->
                    DIVI bezieht die Daten <!--für beide Seiten--> vom <a href="" target="_blank">Robert Koch Institut</a>, vom ARDS
                    Netzwerk und von der
                    <a href="https://www.dkgev.de/" target="_blank">Deutschen Krankenhaus Gesellschaft (DKG)</a>.
                    Auf der <!--jeweiligen--> Unterseite gibt es auch die Möglichkeit die Daten als JSON oder CSV herunterzuladen.
                </p>
                <a href="{{ route('clinics') }}" class="btn-get-started mr-2 font-weight-bold">Auslastung</a>
                <!--<a href="#" class="btn-get-started ml-2 font-weight-bold">Fallzahlen</a>-->
            </div>
        </div>
    </section>
@endsection
