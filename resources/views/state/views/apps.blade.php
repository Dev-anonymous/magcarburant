@extends('layouts.app')
@section('title', 'Apps')
@section('bg-class', 'bg-img-1')
@section('body')
    <div class="container">
        <div class="d-flex justify-content-between">
            <div class="">
                <h2 class="font-weight-bold">{{ $entity->shortname }} </h2>
                <p class="lead small m-0">{{ $entity->longname }}</p>
            </div>
            <div class="m-2">
                <button onclick="history.back()" class="btn btn-sm btn-primary d-flex align-items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" height="12px" viewBox="0 -960 960 960" width="12px"
                        fill="#fff">
                        <path d="M423-59 2-480l421-421 78 79-342 342 342 342-78 79Z" />
                    </svg>
                    Retour
                </button>
            </div>
        </div>
        <hr />
        <div class="row">
            @if ($entity->user->user_role == 'petrolier' && 'view' == $mode)
                <div class="col-md-6">
                    <div class="carte" style="cursor: pointer;min-height: 120px"
                        onclick="location.assign('{{ state_route('purchase', $entity) }}')">
                        <svg xmlns="http://www.w3.org/2000/svg" height="48px" viewBox="0 -960 960 960" width="48px"
                            fill="#000000">
                            <path
                                d="M227-38q-58.92 0-97.96-39.75Q90-117.5 90-175.47V-321h139v-573l65 64 63.77-64 63.77 64 63.77-64 63.78 64 64.77-64 63.57 64 63.9-64 63.34 64L871-894v719q0 57.5-40.04 97.25T734-38H227Zm507.5-92q18.5 0 31.5-12.94 13-12.95 13-31.86V-759H320v438h369v146q0 19 13 32t32.5 13ZM374-597v-57h219v57H374Zm0 132v-57h219v57H374Zm312.42-132q-11.42 0-19.92-8.76-8.5-8.77-8.5-19.5 0-10.74 8.28-19.74 8.28-9 20-9t20.22 8.78q8.5 8.78 8.5 19.82 0 11.03-8.58 19.72-8.58 8.68-20 8.68Zm0 127q-11.42 0-19.92-8.88-8.5-8.89-8.5-19.8 0-10.92 8.28-19.62 8.28-8.7 20-8.7t20.22 8.7q8.5 8.7 8.5 19.62 0 10.91-8.58 19.8-8.58 8.88-20 8.88ZM226-130h372v-99H182v54q0 19 12.65 32T226-130Zm-44 0v-99 99Z" />
                        </svg>
                        <div class="p-2">
                            <h4 class="font-weight-bold">Achats</h4>
                            <p class="m-0">Voir les achats de {{ $entity->shortname }}</p>
                        </div>
                    </div>
                </div>
            @endif
            <div class="col-md-6">
                <div class="carte" style="cursor: pointer;min-height: 120px"
                    onclick="location.assign('{{ state_route('sale', $entity) }}')">
                    <svg xmlns="http://www.w3.org/2000/svg" height="48px" viewBox="0 -960 960 960" width="48px"
                        fill="#000000">
                        <path
                            d="M445-203h70v-40h55q16.28 0 25.64-9.07 9.36-9.08 9.36-24.68V-412q0-15.42-9.36-25.21-9.36-9.79-25.89-9.79H425v-65h180v-69h-90v-40h-70v40h-55q-16.27 0-25.64 9.5Q355-562 355-547v135.25q0 16.02 9.36 25.39 9.37 9.36 25.89 9.36H535v65H355v69h90v40ZM229-59q-35.78 0-63.39-26.91Q138-112.83 138-150v-660q0-37.59 27.61-64.79Q193.22-902 229-902h364l230 228v524q0 37.17-27.91 64.09Q767.19-59 731-59H229Zm307-614v-137H229v660h502v-523H536ZM229-810v187-187 660-660Z" />
                    </svg>
                    <div class="p-2">
                        <h4 class="font-weight-bold">Ventes</h4>
                        <p class="m-0">Voir les ventes de {{ $entity->shortname }}</p>
                    </div>
                </div>
            </div>
            @if ($entity->user->user_role == 'petrolier')
                <div class="col-md-6">
                    <div class="carte" style="cursor: pointer;min-height: 120px"
                        onclick="location.assign('{{ state_route('delivery', $entity) }}')">
                        <svg xmlns="http://www.w3.org/2000/svg" height="48px" viewBox="0 -960 960 960" width="48px"
                            fill="#000000">
                            <path
                                d="M160-120v-660q0-24 18-42t42-18h269q24 0 42 18t18 42v288h65q20.63 0 35.31 14.69Q664-462.63 664-442v219q0 21.68 15.5 36.34Q695-172 717-172t37.5-14.66Q770-201.32 770-223v-295q-11 6-23 9t-24 3q-39.48 0-66.74-27.26Q629-560.52 629-600q0-31.61 18-56.81Q665-682 695-690l-95-95 36-35 153 153q14 14 22.5 30.5T820-600v377q0 43.26-29.82 73.13-29.81 29.87-73 29.87Q674-120 644-149.87q-30-29.87-30-73.13v-219h-65v322H160Zm60-432h269v-228H220v228Zm503-4q18 0 31-13t13-31q0-18-13-31t-31-13q-18 0-31 13t-13 31q0 18 13 31t31 13ZM220-180h269v-312H220v312Zm269 0H220h269Z" />
                        </svg>
                        <div class="p-2">
                            <h4 class="font-weight-bold">Livraisons excédentaires</h4>
                            <p class="m-0">Voir les livraisons excédentaires de {{ $entity->shortname }}</p>
                        </div>
                    </div>
                </div>
            @endif
            <div class="col-md-6">
                <div class="carte" style="cursor: pointer;min-height: 120px"
                    onclick="location.assign('{{ state_route('accounting', $entity) }}')">
                    <div class="w-100">
                        <div class="d-flex align-items-center">
                            <span class="material-icons md-48 ml-2">account_balance</span>
                            <div class="p-2">
                                <h4 class="font-weight-bold">Comptabilité</h4>
                                <p class="m-0">Voir les données comptables de {{ $entity->shortname }}</p>
                            </div>
                        </div>
                        {{-- <div class="text-center">
                            <a href="{{ state_route('rates') }}">Taux réels</a>
                            <span>|</span>
                            <a href="{{ state_route('prices') }}">Structures des prix</a>
                        </div> --}}
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
