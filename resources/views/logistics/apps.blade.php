@extends('layouts.app')
@section('title', 'Apps')
@section('bg-class', 'bg-img-1')
@section('body')
    <div class="container">
        <h2 class="font-weight-bold">{{ $entity->shortname }}</h2>
        <p class="lead small m-0">{{ $entity->longname }}</p>
        <hr />
        <div class="row">
            @canlocal('Vente - Lire')
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
            @endcanlocal

            @canlocal('Comptabilité - Lire')
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
                    </div>
                </div>
            </div>
            @endcanlocal

            @canlocal('Tableau de bord - Lire')
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
            @endcanlocal

            @canlocal('Gestion des utilisateurs - Lire')
            <div class="col-md-6">
                <div class="carte" style="min-height: 120px"">
                    <div class="w-100">
                        <div class="d-flex align-items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" height="48px" viewBox="0 -960 960 960" width="48px"
                                fill="#000">
                                <path
                                    d="M292-527q-42-42-42-108t42-108q42-42 108-42t108 42q42 42 42 108t-42 108q-42 42-108 42t-108-42ZM80-164v-94q0-35 17.5-63t50.5-43q72-32 133.5-46T400-424h23q-6 14-9 27.5t-5 32.5h-9q-58 0-113.5 12.5T172-310q-16 8-24 22.5t-8 29.5v34h269q5 18 12 32.5t17 27.5H80Zm587 44-10-66q-17-5-34.5-14.5T593-222l-55 12-25-42 47-44q-2-9-2-25t2-25l-47-44 25-42 55 12q12-12 29.5-21.5T657-456l10-66h54l10 66q17 5 34.5 14.5T795-420l55-12 25 42-47 44q2 9 2 25t-2 25l47 44-25 42-55-12q-12 12-29.5 21.5T731-186l-10 66h-54Zm85-143q22-22 22-58t-22-58q-22-22-58-22t-58 22q-22 22-22 58t22 58q22 22 58 22t58-22ZM464.5-570.5Q490-596 490-635t-25.5-64.5Q439-725 400-725t-64.5 25.5Q310-674 310-635t25.5 64.5Q361-545 400-545t64.5-25.5ZM400-635Zm9 411Z" />
                            </svg>
                            <div class="p-2">
                                <h4 class="font-weight-bold">Utilisateurs & Rôles</h4>
                            </div>
                        </div>
                        <div class="text-center">
                            <a class="text-primary" href="{{ route('users') }}">Gestion des utilisateurs </a>
                            <span>|</span>
                            <a class="text-primary" href="{{ route('roles') }}">Gestion des rôles</a>
                        </div>
                    </div>
                </div>
            </div>
            @endcanlocal

            @canlocal('Audit - Lire')
            <div class="col-md-6">
                <div class="carte" style="cursor: pointer;min-height: 120px;"
                    onclick="location.assign('{{ route('applogs') }}')">
                    <svg xmlns="http://www.w3.org/2000/svg" height="48px" viewBox="0 -960 960 960" width="48px"
                        fill="#000" style="vertical-align: middle;">
                        <path
                            d="M321-296.08q9-8.88 9-21t-9-21.12q-9-9-21-9t-21 9q-9 9-9 21.12 0 12.12 9 21t21 8.88q12 0 21-8.88ZM321-459q9-9 9-21t-9-21q-9-9-21-9t-21 9q-9 9-9 21t9 21q9 9 21 9t21-9Zm0-162.92q9-8.88 9-21t-9-21.12q-9-9-21-9t-21 9q-9 9-9 21.12 0 12.12 9 21t21 8.88q12 0 21-8.88ZM432-287.2h244v-60H432v60Zm0-162.8h244v-60H432v60Zm0-163.04h244v-60H432v60ZM182.15-114.02q-27.6 0-47.86-20.27-20.27-20.26-20.27-47.86v-595.7q0-27.7 20.27-48.03 20.26-20.34 47.86-20.34h595.7q27.7 0 48.03 20.34 20.34 20.33 20.34 48.03v595.7q0 27.6-20.34 47.86-20.33 20.27-48.03 20.27h-595.7Zm0-68.13h595.7v-595.7h-595.7v595.7Zm0-595.7v595.7-595.7Z" />
                    </svg>
                    <div class="p-2">
                        <h4 class="font-weight-bold">Audits</h4>
                        <p class="m-0">Historique des modifications systèmes</p>
                    </div>
                </div>
            </div>
            @endcanlocal

        </div>
    @endsection
