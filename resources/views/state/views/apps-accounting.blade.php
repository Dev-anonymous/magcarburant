@extends('layouts.app')
@section('title', 'Comptabilité')
@section('bg-class', 'bg-img-3')
@section('body')
    <div class="container">
        <h2 class="font-weight-bold">Comptabilité | {{ $entity->shortname }}</h2>
        <p class="lead small m-0">Toute la partie fiscalité & comptabilité pour {{ $entity->shortname }} </p>
        <hr />

        <div class="row">
            <div class="col-md-6">
                <div class="carte" style="cursor: pointer;min-height: 120px"
                    onclick="location.assign('{{ route('state.view.analyse', $entity->id) }}')">
                    <svg xmlns="http://www.w3.org/2000/svg" height="48px" viewBox="0 -960 960 960" width="48px"
                        fill="#000000">
                        <path
                            d="M180-120q-24 0-42-18t-18-42v-660h60v660h660v60H180Zm75-135v-334h119v334H255Zm198 0v-540h119v540H453Zm194 0v-170h119v170H647Z" />
                    </svg>
                    <div class="p-2">
                        <h4 class="font-weight-bold">Manque à gagner</h4>
                        {{-- <p class="m-0">Gestion des achats</p> --}}
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="carte" style="cursor: pointer;min-height: 120px"
                    onclick="location.assign('{{ route('state.view.accounting', ['item' => 'gb', 'entity' => $entity->id]) }}')">
                    <svg xmlns="http://www.w3.org/2000/svg" height="48px" viewBox="0 -960 960 960" width="48px"
                        fill="#000000">
                        <path
                            d="M132-120q-24 0-42-18t-18-42v-600q0-24 18-42t42-18h696q24 0 42 18t18 42v600q0 24-18 42t-42 18H132Zm0-60h696v-600H132v600Zm68-100h200v-80H200v80Zm382-80 198-198-57-57-141 142-57-57-56 57 113 113Zm-382-80h200v-80H200v80Zm0-160h200v-80H200v80Zm-68 420v-600 600Z" />
                    </svg>
                    <div class="p-2">
                        <h4 class="font-weight-bold">Grand livre MAG</h4>
                    </div>
                </div>
            </div>
            @if ($entity->user->user_role == 'petrolier')
                <div class="col-md-6">
                    <div class="carte" style="cursor: pointer;min-height: 120px"
                        onclick="location.assign('{{ route('state.view.claim', $entity->id) }}')">
                        <div class="w-100">
                            <div class="d-flex align-items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" height="48px" viewBox="0 -960 960 960"
                                    width="48px" fill="#000000">
                                    <path
                                        d="M318-420v-295h90v295l-45-45-45 45Zm194 91v-551h90v461l-90 90ZM124-229v-320h90v230l-90 90Zm-4 111 246-246 149 132 262-262h-88v-60h191v190h-60v-88L517-149 368-280 206-118h-86Z" />
                                </svg>
                                <div class="p-2">
                                    <h4 class="font-weight-bold">Croisement des créances</h4>
                                    {{-- <p class="m-0">Gestion de la comptabilité</p> --}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="carte" style="cursor: pointer;min-height: 120px"
                        onclick="location.assign('{{ route('state.view.accounting', ['item' => 'cc', 'entity' => $entity->id]) }}')">
                        <svg xmlns="http://www.w3.org/2000/svg" height="48px" viewBox="0 -960 960 960" width="48px"
                            fill="#000000">
                            <path
                                d="M132-120q-24 0-42-18t-18-42v-600q0-24 18-42t42-18h696q24 0 42 18t18 42v600q0 24-18 42t-42 18H132Zm0-60h696v-600H132v600Zm68-100h200v-80H200v80Zm382-80 198-198-57-57-141 142-57-57-56 57 113 113Zm-382-80h200v-80H200v80Zm0-160h200v-80H200v80Zm-68 420v-600 600Z" />
                        </svg>
                        <div class="p-2">
                            <h4 class="font-weight-bold">Grand livre CC</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="carte" style="cursor: pointer;min-height: 120px"
                        onclick="location.assign('{{ route('state.view.taxation', $entity->id) }}')">
                        <svg xmlns="http://www.w3.org/2000/svg" height="48px" viewBox="0 -960 960 960" width="48px"
                            fill="#000000">
                            <path
                                d="M120-120v-76l60-60v136h-60Zm165 0v-236l60-60v296h-60Zm165 0v-296l60 61v235h-60Zm165 0v-235l60-60v295h-60Zm165 0v-396l60-60v456h-60ZM120-356v-85l280-278 160 160 280-281v85L560-474 400-634 120-356Z" />
                        </svg>
                        <div class="p-2">
                            <h4 class="font-weight-bold">Fiscalité et parafiscalité</h4>
                            {{-- <p class="m-0">Gestion des ventes</p> --}}
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="carte" style="cursor: pointer;min-height: 120px"
                        onclick="location.assign('{{ route('state.view.accounting', ['item' => 'pf', 'entity' => $entity->id]) }}')">
                        <svg xmlns="http://www.w3.org/2000/svg" height="48px" viewBox="0 -960 960 960" width="48px"
                            fill="#000000">
                            <path
                                d="M132-120q-24 0-42-18t-18-42v-600q0-24 18-42t42-18h696q24 0 42 18t18 42v600q0 24-18 42t-42 18H132Zm0-60h696v-600H132v600Zm68-100h200v-80H200v80Zm382-80 198-198-57-57-141 142-57-57-56 57 113 113Zm-382-80h200v-80H200v80Zm0-160h200v-80H200v80Zm-68 420v-600 600Z" />
                        </svg>
                        <div class="p-2">
                            <h4 class="font-weight-bold">Grand livre fiscalité</h4>
                        </div>
                    </div>
                </div>
            @endif
        </div>

    </div>
@endsection
