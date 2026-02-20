@extends('layouts.app')
@section('title', 'Apps')
@section('bg-class', 'bg-img-1')
@section('body')
    <div class="container">
        <h2 class="font-weight-bold">{{ $entity->shortname }}</h2>
        <p class="lead small m-0">{{ $entity->longname }}</p>
        <hr />
        <div class="row">
            <div class="col-md-6">
                <div class="carte" style="cursor: pointer;min-height: 120px"
                    onclick="location.assign('{{ route('logistics.sale') }}')">
                    <svg xmlns="http://www.w3.org/2000/svg" height="48px" viewBox="0 -960 960 960" width="48px"
                        fill="#000000">
                        <path
                            d="M445-203h70v-40h55q16.28 0 25.64-9.07 9.36-9.08 9.36-24.68V-412q0-15.42-9.36-25.21-9.36-9.79-25.89-9.79H425v-65h180v-69h-90v-40h-70v40h-55q-16.27 0-25.64 9.5Q355-562 355-547v135.25q0 16.02 9.36 25.39 9.37 9.36 25.89 9.36H535v65H355v69h90v40ZM229-59q-35.78 0-63.39-26.91Q138-112.83 138-150v-660q0-37.59 27.61-64.79Q193.22-902 229-902h364l230 228v524q0 37.17-27.91 64.09Q767.19-59 731-59H229Zm307-614v-137H229v660h502v-523H536ZM229-810v187-187 660-660Z" />
                    </svg>
                    <div class="p-2">
                        <h4 class="font-weight-bold">Ventes</h4>
                        <p class="m-0">Gestion des ventes</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="carte" style="cursor: pointer;min-height: 120px"
                    onclick="location.assign('{{ route('logistics.accounting') }}')">
                    <div class="w-100">
                        <div class="d-flex align-items-center">
                            <span class="material-icons md-48 ml-2">account_balance</span>
                            <div class="p-2">
                                <h4 class="font-weight-bold">Comptabilité</h4>
                                <p class="m-0">Gestion de la comptabilité</p>
                            </div>
                        </div>
                        {{-- <div class="text-center">
                            <a href="{{ route('logistics.home.rates') }}">Taux réels</a>
                            <span>|</span>
                            <a href="{{ route('provider.prices') }}">Structures des prix</a>
                        </div> --}}
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="carte" style="cursor: pointer;min-height: 120px;"
                    onclick="location.assign('{{ route('logistics.dash') }}')">
                    <svg xmlns="http://www.w3.org/2000/svg" height="48px" viewBox="0 -960 960 960" width="48px"
                        fill="#000000">
                        <path
                            d="M313-407v-317h98v312l-49-45-49 50Zm198 88v-569h96v472l-96 97ZM116-212v-346h97v250l-97 96ZM97-111l267-267 149 131 265-265h-85v-80h222v220h-80v-84L513-135 367-270 209-111H97Z" />
                    </svg>
                    <div class="p-2">
                        <h4 class="font-weight-bold">Tableau de bord</h4>
                        <p class="m-0">Gestion des statistiques</p>
                    </div>
                </div>
            </div>

        </div>

    </div>
@endsection
