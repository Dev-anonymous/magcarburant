@extends('layouts.app')
@section('title', 'Comptabilité')
@section('bg-class', 'bg-img-3')
@section('body')
    <div class="container">
        <h2 class="font-weight-bold">Comptabilité</h2>
        <p class="lead small m-0">Gestion de la comptabilité</p>
        <hr />

        <div class="row">
            <div class="col-md-6">
                <div class="carte" style="cursor: pointer;min-height: 120px"
                    onclick="location.assign('{{ route('provider.analyse') }}')">
                    <svg xmlns="http://www.w3.org/2000/svg" height="48px" viewBox="0 -960 960 960" width="48px"
                        fill="#000000">
                        <path
                            d="M180-120q-24 0-42-18t-18-42v-660h60v660h660v60H180Zm75-135v-334h119v334H255Zm198 0v-540h119v540H453Zm194 0v-170h119v170H647Z" />
                    </svg>
                    <div class="p-2">
                        <h4 class="font-weight-bold">Analyse</h4>
                        {{-- <p class="m-0">Gestion des achats</p> --}}
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="carte" style="cursor: pointer;min-height: 120px"
                    onclick="location.assign('{{ route('provider.accounting', ['item' => 'pricestr']) }}')">
                    <div class="w-100">
                        <div class="d-flex align-items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" height="48px" viewBox="0 -960 960 960" width="48px"
                                fill="#000000">
                                <path
                                    d="M450-201h60v-40h60q12.75 0 21.38-8.63Q600-258.25 600-271v-130q0-12.75-8.62-21.38Q582.75-431 570-431H420v-70h180v-60h-90v-40h-60v40h-60q-12.75 0-21.37 8.62Q360-543.75 360-531v130q0 12.75 8.63 21.37Q377.25-371 390-371h150v70H360v60h90v40ZM220-80q-24 0-42-18t-18-42v-680q0-24 18-42t42-18h361l219 219v521q0 24-18 42t-42 18H220Zm311-581v-159H220v680h520v-521H531ZM220-820v159-159 680-680Z" />
                            </svg>
                            <div class="p-2">
                                <h4 class="font-weight-bold">Structure des prix</h4>
                                {{-- <p class="m-0">Gestion de la comptabilité</p> --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="carte" style="cursor: pointer;min-height: 120px"
                    onclick="location.assign('{{ route('provider.accounting', ['item' => 'rtx']) }}')">
                    <svg xmlns="http://www.w3.org/2000/svg" height="48px" viewBox="0 -960 960 960" width="48px"
                        fill="#000000">
                        <path
                            d="M480-40q-112 0-216-66T100-257v137H40v-240h240v60H143q51 77 145.5 138.5T480-100q78 0 147.5-30t121-81.5Q800-263 830-332.5T860-480h60q0 91-34.5 171T791-169q-60 60-140 94.5T480-40Zm-29-153v-54q-45-12-75.5-38.5T324-358l51-17q12 38 42.5 60t69.5 22q40 0 66.5-19.5T580-364q0-33-25-55.5T463-470q-60-25-90-54t-30-78q0-44 30-75t80-38v-51h55v51q38 4 66 24t45 55l-48 23q-15-28-37-42t-52-14q-39 0-61.5 18T398-602q0 32 26 51t84 43q69 29 98 61t29 83q0 25-9 46t-25.5 36Q584-267 560-257.5T506-245v52h-55ZM40-480q0-91 34.5-171T169-791q60-60 140-94.5T480-920q112 0 216 66t164 151v-137h60v240H680v-60h137q-51-77-145-138.5T480-860q-78 0-147.5 30t-121 81.5Q160-697 130-627.5T100-480H40Z" />
                    </svg>
                    <div class="p-2">
                        <h4 class="font-weight-bold">Taux réels</h4>
                        {{-- <p class="m-0">Gestion des ventes</p> --}}
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="carte" style="cursor: pointer;min-height: 120px"
                    onclick="location.assign('{{ route('provider.accounting', ['item' => 'stx']) }}')">
                    <svg xmlns="http://www.w3.org/2000/svg" height="48px" viewBox="0 -960 960 960" width="48px"
                        fill="#000000">
                        <path
                            d="M480-40q-112 0-216-66T100-257v137H40v-240h240v60H143q51 77 145.5 138.5T480-100q78 0 147.5-30t121-81.5Q800-263 830-332.5T860-480h60q0 91-34.5 171T791-169q-60 60-140 94.5T480-40Zm-29-153v-54q-45-12-75.5-38.5T324-358l51-17q12 38 42.5 60t69.5 22q40 0 66.5-19.5T580-364q0-33-25-55.5T463-470q-60-25-90-54t-30-78q0-44 30-75t80-38v-51h55v51q38 4 66 24t45 55l-48 23q-15-28-37-42t-52-14q-39 0-61.5 18T398-602q0 32 26 51t84 43q69 29 98 61t29 83q0 25-9 46t-25.5 36Q584-267 560-257.5T506-245v52h-55ZM40-480q0-91 34.5-171T169-791q60-60 140-94.5T480-920q112 0 216 66t164 151v-137h60v240H680v-60h137q-51-77-145-138.5T480-860q-78 0-147.5 30t-121 81.5Q160-697 130-627.5T100-480H40Z" />
                    </svg>
                    <div class="p-2">
                        <h4 class="font-weight-bold">Taux structures</h4>
                        {{-- <p class="m-0">Gestion des statistiques</p> --}}
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="carte" style="cursor: pointer;min-height: 120px"
                    onclick="location.assign('{{ route('provider.accounting', ['item' => 'gb']) }}')">
                    <svg xmlns="http://www.w3.org/2000/svg" height="48px" viewBox="0 -960 960 960" width="48px"
                        fill="#000000">
                        <path
                            d="M132-120q-24 0-42-18t-18-42v-600q0-24 18-42t42-18h696q24 0 42 18t18 42v600q0 24-18 42t-42 18H132Zm0-60h696v-600H132v600Zm68-100h200v-80H200v80Zm382-80 198-198-57-57-141 142-57-57-56 57 113 113Zm-382-80h200v-80H200v80Zm0-160h200v-80H200v80Zm-68 420v-600 600Z" />
                    </svg>
                    <div class="p-2">
                        <h4 class="font-weight-bold">Grand Livre</h4>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
