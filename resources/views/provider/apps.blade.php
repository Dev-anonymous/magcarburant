@extends('layouts.app')
@section('title', 'Apps')
@section('bg-class', 'bg-img-1')
@section('body')
    <div class="container">
        <h2 class="font-weight-bold">{{ $entity->shortname }}</h2>
        <p class="lead small m-0">{{ $entity->longname }}</p>
        <hr />
        <div class="row">
            @canlocal('Achat - Lire')
            <div class="col-md-6">
                <div class="carte" style="cursor: pointer;min-height: 120px"
                    onclick="location.assign('{{ route('provider.purchase') }}')">
                    <svg xmlns="http://www.w3.org/2000/svg" height="48px" viewBox="0 -960 960 960" width="48px"
                        fill="#000000">
                        <path
                            d="M227-38q-58.92 0-97.96-39.75Q90-117.5 90-175.47V-321h139v-573l65 64 63.77-64 63.77 64 63.77-64 63.78 64 64.77-64 63.57 64 63.9-64 63.34 64L871-894v719q0 57.5-40.04 97.25T734-38H227Zm507.5-92q18.5 0 31.5-12.94 13-12.95 13-31.86V-759H320v438h369v146q0 19 13 32t32.5 13ZM374-597v-57h219v57H374Zm0 132v-57h219v57H374Zm312.42-132q-11.42 0-19.92-8.76-8.5-8.77-8.5-19.5 0-10.74 8.28-19.74 8.28-9 20-9t20.22 8.78q8.5 8.78 8.5 19.82 0 11.03-8.58 19.72-8.58 8.68-20 8.68Zm0 127q-11.42 0-19.92-8.88-8.5-8.89-8.5-19.8 0-10.92 8.28-19.62 8.28-8.7 20-8.7t20.22 8.7q8.5 8.7 8.5 19.62 0 10.91-8.58 19.8-8.58 8.88-20 8.88ZM226-130h372v-99H182v54q0 19 12.65 32T226-130Zm-44 0v-99 99Z" />
                    </svg>
                    <div class="p-2">
                        <h4 class="font-weight-bold">Achats</h4>
                        <p class="m-0">Gestion des achats</p>
                    </div>
                </div>
            </div>
            @endcanlocal

            @canlocal('Vente - Lire')
            <div class="col-md-6">
                <div class="carte" style="cursor: pointer;min-height: 120px"
                    onclick="location.assign('{{ route('provider.sale') }}')">
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
            @canlocal('Livraison excédentaire - Lire')
            <div class="col-md-6">
                <div class="carte" style="cursor: pointer;min-height: 120px"
                    onclick="location.assign('{{ route('provider.delivery') }}')">
                    <svg xmlns="http://www.w3.org/2000/svg" height="48px" viewBox="0 -960 960 960" width="48px"
                        fill="#000000">
                        <path
                            d="M160-120v-660q0-24 18-42t42-18h269q24 0 42 18t18 42v288h65q20.63 0 35.31 14.69Q664-462.63 664-442v219q0 21.68 15.5 36.34Q695-172 717-172t37.5-14.66Q770-201.32 770-223v-295q-11 6-23 9t-24 3q-39.48 0-66.74-27.26Q629-560.52 629-600q0-31.61 18-56.81Q665-682 695-690l-95-95 36-35 153 153q14 14 22.5 30.5T820-600v377q0 43.26-29.82 73.13-29.81 29.87-73 29.87Q674-120 644-149.87q-30-29.87-30-73.13v-219h-65v322H160Zm60-432h269v-228H220v228Zm503-4q18 0 31-13t13-31q0-18-13-31t-31-13q-18 0-31 13t-13 31q0 18 13 31t31 13ZM220-180h269v-312H220v312Zm269 0H220h269Z" />
                    </svg>
                    <div class="p-2">
                        <h4 class="font-weight-bold">Livraisons excédentaires</h4>
                    </div>
                </div>
            </div>
            @endcanlocal

            @canlocal('Vente liées aux STEs minières - Lire')
            <div class="col-md-6">
                <div class="carte" style="cursor: pointer;min-height: 120px"
                    onclick="location.assign('{{ route('provider.mining-sale') }}')">
                    <svg xmlns="http://www.w3.org/2000/svg" height="48px" viewBox="0 -960 960 960" width="48px"
                        fill="#000">
                        <path
                            d="M449.57-193h57.86v-51.04q60.53-6.76 94.77-37.26 34.23-30.5 34.23-82.7 0-51.24-29.2-83.84-29.21-32.59-98.83-61.59-57.68-24-83.32-43-25.65-19-25.65-50.53 0-29.8 21.91-47.2 21.9-17.41 59.7-17.41 29.76 0 51.88 14t37.12 42.48l50.63-23.95q-17.51-36.72-45.25-56.7-27.75-19.98-65.99-24.47V-766h-57.86v49.8q-51.72 7.48-80.86 38.36-29.14 30.87-29.14 75.75 0 49.81 30.71 79.41 30.72 29.59 92.87 55.07 64.61 27.04 89.01 49.2 24.41 22.15 24.41 54.41 0 31.52-25.67 50.54-25.66 19.03-66.02 19.03-38.88 0-69.02-22-30.14-22-41.9-60.48l-53.63 17.95q20.76 47.2 51.97 74.3 31.22 27.09 75.27 38.7V-193Zm30.46 118.98q-83.46 0-157.54-31.86t-129.41-87.2q-55.34-55.33-87.2-129.38-31.86-74.04-31.86-157.51 0-84.46 31.86-158.54t87.16-128.93q55.3-54.85 129.36-86.81 74.06-31.97 157.55-31.97 84.48 0 158.59 31.95 74.1 31.95 128.94 86.76 54.83 54.82 86.78 128.91 31.96 74.08 31.96 158.6 0 83.5-31.97 157.57-31.96 74.08-86.81 129.38-54.85 55.31-128.9 87.17-74.04 31.86-158.51 31.86Zm-.03-68.13q141.04 0 239.45-98.75 98.4-98.76 98.4-239.1 0-141.04-98.4-239.45-98.41-98.4-239.57-98.4-140.16 0-238.95 98.4-98.78 98.41-98.78 239.57 0 140.16 98.75 238.95 98.76 98.78 239.1 98.78ZM480-480Z" />
                    </svg>
                    <div class="p-2">
                        <h4 class="font-weight-bold">Ventes Sociétés Minières </h4>
                        <p class="m-0">Gestion des ventes liées aux sociétés minières</p>
                    </div>
                </div>
            </div>
            @endcanlocal

            @canlocal('Stock de sécurité collecté reversé - Lire')
            <div class="col-md-6">
                <div class="carte" style="cursor: pointer;min-height: 120px"
                    onclick="location.assign('{{ route('provider.security-stock') }}')">
                    <div class="w-100">
                        <div class="d-flex align-items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" height="48px" viewBox="0 -960 960 960" width="48px"
                                fill="#000">
                                <path
                                    d="M480-80q-140-35-230-162.5T160-522v-238l320-120 320 120v238q0 152-90 279.5T480-80Zm0-62q106-35 175.5-128.5T737-480H480v-335l-260 97v196q0 12 .5 20.5T223-480h257v338Z" />
                            </svg>
                            <div class="p-2">
                                <h4 class="font-weight-bold">Stock de sécurité collecté reversé</h4>
                                <p class="m-0">Voir le stock de sécurité collecté reversé
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endcanlocal

            @canlocal('Comptabilité - Lire')
            <div class="col-md-6">
                <div class="carte" style="cursor: pointer;min-height: 120px"
                    onclick="location.assign('{{ route('provider.accounting') }}')">
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
                    onclick="location.assign('{{ route('provider.dash') }}')">
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

            @if (can('Gestion des utilisateurs - Lire') || can('Gestion des rôles - Lire'))
                <div class="col-md-6">
                    <div class="carte" style="min-height: 120px"">
                        <div class="w-100">
                            <div class="d-flex align-items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" height="48px" viewBox="0 -960 960 960"
                                    width="48px" fill="#000">
                                    <path
                                        d="M292-527q-42-42-42-108t42-108q42-42 108-42t108 42q42 42 42 108t-42 108q-42 42-108 42t-108-42ZM80-164v-94q0-35 17.5-63t50.5-43q72-32 133.5-46T400-424h23q-6 14-9 27.5t-5 32.5h-9q-58 0-113.5 12.5T172-310q-16 8-24 22.5t-8 29.5v34h269q5 18 12 32.5t17 27.5H80Zm587 44-10-66q-17-5-34.5-14.5T593-222l-55 12-25-42 47-44q-2-9-2-25t2-25l-47-44 25-42 55 12q12-12 29.5-21.5T657-456l10-66h54l10 66q17 5 34.5 14.5T795-420l55-12 25 42-47 44q2 9 2 25t-2 25l47 44-25 42-55-12q-12 12-29.5 21.5T731-186l-10 66h-54Zm85-143q22-22 22-58t-22-58q-22-22-58-22t-58 22q-22 22-22 58t22 58q22 22 58 22t58-22ZM464.5-570.5Q490-596 490-635t-25.5-64.5Q439-725 400-725t-64.5 25.5Q310-674 310-635t25.5 64.5Q361-545 400-545t64.5-25.5ZM400-635Zm9 411Z" />
                                </svg>
                                <div class="p-2">
                                    <h4 class="font-weight-bold">Utilisateurs & Rôles</h4>
                                </div>
                            </div>
                            <div class="text-center">
                                @if (can('Gestion des utilisateurs - Lire'))
                                    <a class="text-primary" href="{{ route('users') }}">Gestion des utilisateurs</a>
                                @endif
                                @if (can('Gestion des rôles - Lire'))
                                    <span>|</span>
                                    <a class="text-primary" href="{{ route('roles') }}">Gestion des rôles</a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endif

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

    </div>
@endsection
