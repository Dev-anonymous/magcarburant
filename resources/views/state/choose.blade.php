@extends('layouts.app')
@section('title', 'Modes')
@section('bg-class', 'bg-img-1')
@section('body')
    <div class="container">
        <h2 class="font-weight-bold">{{ $entity->shortname }}</h2>
        <p class="lead small m-0">{{ $entity->longname }}</p>
        <hr />
        <div class="row">
            <div class="col-md-12">
                <h3 class="text-center font-weight-bold my-3">Sélectionnez un module</h3>
            </div>
            <div class="col-md-6">
                <div class="carte" mode="view" style="cursor: pointer;min-height: 145px">
                    <svg xmlns="http://www.w3.org/2000/svg" height="48px" viewBox="0 -960 960 960" width="48px"
                        fill="#000">
                        <path
                            d="M223-40H100q-24 0-42-18t-18-42v-123h60v123h123v60Zm514 0v-60h123v-123h60v123q0 24-18 42t-42 18H737ZM479.17-225Q360-225 264-293.5 168-362 119-480q49-119 145-187.5T479.5-736q119.5 0 216 68.5T840-480q-48 118-144.83 186.5-96.83 68.5-216 68.5Zm-.17-60q93 0 173-52.5T774-480q-42-90-122-143t-173-53q-93 0-172.5 53T185-480q42 90 121.5 142.5T479-285Zm1.22-63Q535-348 574-386.72t39-93.5Q613-535 574.07-574t-94-39q-55.07 0-93.57 38.93-38.5 38.93-38.5 94t38.72 93.57q38.72 38.5 93.5 38.5Zm-.22-60q-30 0-51-21t-21-51q0-30 21-51.5t51-21.5q30 0 51.5 21.29T553-480q0 30-21.29 51T480-408ZM40-737v-123q0-24 18-42t42-18h123v60H100v123H40Zm820 0v-123H737v-60h123q24 0 42 18t18 42v123h-60ZM480-481Z" />
                    </svg>
                    <div class="p-2">
                        <h4 class="font-weight-bold">Mode Lecture</h4>
                        <p class="m-0">Pour la lecture de données des sociétés logistiques et pétrolières</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="carte" mode="edit" style="cursor: pointer;min-height: 145px">
                    <div>
                        <svg xmlns="http://www.w3.org/2000/svg" height="48px" viewBox="0 -960 960 960" width="48px"
                            fill="#000">
                            <path
                                d="M360-640v-60h360v60H360Zm0 120v-60h360v60H360Zm140 380H180h320Zm0 60H225q-43.75 0-74.37-30.63Q120-141.25 120-185v-135h120v-560h600v381q-15-2-30.37-.03-15.38 1.97-29.63 7.03v-328H300v500h292l-60 60H180v75q0 19.12 13 32.06Q206-140 224-140h276v60Zm60 0v-123l221-220q9-9 20-13t22-4q12 0 23 4.5t20 13.5l37 37q9 9 13 20t4 22q0 11-4.5 22.5T902.09-300L683-80H560Zm300-263-37-37 37 37ZM620-140h38l121-122-18-19-19-18-122 121v38Zm141-141-19-18 37 37-18-19Z" />
                        </svg>
                    </div>
                    <div class="p-2">
                        <h4 class="font-weight-bold">Mode Ecriture</h4>
                        <p class="m-0">Pour l’ajout, la modification et la suppression de les données : taux réels,
                            achats, ventes, structures des prix et autres</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="carte" mode="recon" style="cursor: pointer;min-height: 145px">
                    <div>
                        <svg xmlns="http://www.w3.org/2000/svg" height="48px" viewBox="0 -960 960 960" width="48px"
                            fill="#000">
                            <path
                                d="M360-640v-60h360v60H360Zm0 120v-60h360v60H360Zm140 380H180h320Zm0 60H225q-43.75 0-74.37-30.63Q120-141.25 120-185v-135h120v-560h600v381q-15-2-30.37-.03-15.38 1.97-29.63 7.03v-328H300v500h292l-60 60H180v75q0 19.12 13 32.06Q206-140 224-140h276v60Zm60 0v-123l221-220q9-9 20-13t22-4q12 0 23 4.5t20 13.5l37 37q9 9 13 20t4 22q0 11-4.5 22.5T902.09-300L683-80H560Zm300-263-37-37 37 37ZM620-140h38l121-122-18-19-19-18-122 121v38Zm141-141-19-18 37 37-18-19Z" />
                        </svg>
                    </div>
                    <div class="p-2">
                        <h4 class="font-weight-bold">Réconciliation</h4>
                        <p class="m-0">Pour le rapprochement des données avec les sociétés</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="carte" style="cursor: pointer;min-height: 145px;"
                    onclick="location.assign('{{ route('state.dash') }}')">
                    <svg xmlns="http://www.w3.org/2000/svg" height="48px" viewBox="0 -960 960 960" width="48px"
                        fill="#000000">
                        <path
                            d="M313-407v-317h98v312l-49-45-49 50Zm198 88v-569h96v472l-96 97ZM116-212v-346h97v250l-97 96ZM97-111l267-267 149 131 265-265h-85v-80h222v220h-80v-84L513-135 367-270 209-111H97Z" />
                    </svg>
                    <div class="p-2">
                        <h4 class="font-weight-bold">Tableau de bord</h4>
                        <p class="m-0">Acceder aux statistiques de {{ $entity->shortname }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="carte" style="cursor: pointer;min-height: 145px"
                    onclick="location.assign('{{ route('state.config') }}')">
                    <div>
                        <svg xmlns="http://www.w3.org/2000/svg" height="48px" viewBox="0 -960 960 960" width="48px"
                            fill="#000">
                            <path
                                d="m388-80-20-126q-19-7-40-19t-37-25l-118 54-93-164 108-79q-2-9-2.5-20.5T185-480q0-9 .5-20.5T188-521L80-600l93-164 118 54q16-13 37-25t40-18l20-127h184l20 126q19 7 40.5 18.5T669-710l118-54 93 164-108 77q2 10 2.5 21.5t.5 21.5q0 10-.5 21t-2.5 21l108 78-93 164-118-54q-16 13-36.5 25.5T592-206L572-80H388Zm48-60h88l14-112q33-8 62.5-25t53.5-41l106 46 40-72-94-69q4-17 6.5-33.5T715-480q0-17-2-33.5t-7-33.5l94-69-40-72-106 46q-23-26-52-43.5T538-708l-14-112h-88l-14 112q-34 7-63.5 24T306-642l-106-46-40 72 94 69q-4 17-6.5 33.5T245-480q0 17 2.5 33.5T254-413l-94 69 40 72 106-46q24 24 53.5 41t62.5 25l14 112Zm44-210q54 0 92-38t38-92q0-54-38-92t-92-38q-54 0-92 38t-38 92q0 54 38 92t92 38Zm0-130Z" />
                        </svg>
                    </div>
                    <div class="p-2">
                        <h4 class="font-weight-bold">Configuration</h4>
                        <p class="m-0">Pour la configuration des taux réels,
                            prix d'achats moyen, structures des prix et autres</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="carte" style="cursor: pointer;min-height: 145px;"
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
        </div>

    </div>
@endsection

@section('modals')
    <div class="modal fade" id="mdlChose" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <h4 class="my-3">Sélectionnez une société </h4>
                    <input type="hidden" id="navmode">
                    <div class="my-3" style="max-height: 70vh;overflow-y: auto; overflow-x: hidden;">
                        <div class="row">
                            @foreach ($entities as $el)
                                <div class="col-md-6">
                                    <div class="carte text-left" entity="{{ $el->id }}"
                                        style="cursor: pointer;min-height: 145px">
                                        <img src="{{ userimg($el->user) }}"
                                            style="border: 2px solid #ccc; width: 50px!important"
                                            class="img-fluid rounded-circle ml-1" />
                                        <div class="p-2">
                                            <h4 class="font-weight-bold">{{ $el->shortname }}</h4>
                                            <p class="m-0 mb-2 font-italic">{{ $el->longname }}</p>
                                            <p class="m-0">Type société : <span
                                                    class="badge @if ($el->user->user_role == 'petrolier') badge-info @else badge-danger @endif">
                                                    {{ $el->user->user_role }}
                                                </span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn" data-dismiss="modal">
                        <i class="material-icons md-18 mr-1 m-0 p-0">highlight_off</i>
                        Fermer
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $('[mode]').click(function() {
            var mode = $(this).attr('mode');
            $('#navmode').val(mode);
            $('#mdlChose').modal('show');
        });
        $('[entity]').click(function() {
            var entity = $(this).attr('entity');
            if (entity) {
                var mode = $('#navmode').val();
                if ('view' == mode) {
                    var href = '{{ route('state.apps', ['mode' => 'view', 'entity' => 'DATA_ID']) }}/';
                } else if ('edit' == mode) {
                    var href = '{{ route('state.apps', ['mode' => 'edit', 'entity' => 'DATA_ID']) }}/';
                } else if ('recon' == mode) {
                    var href = '{{ route('state.reconciliation', 'DATA_ID') }}'
                } else {
                    return alert('Invalid mode');
                }

                href = href.split('DATA_ID').join(entity);
                setTimeout(() => {
                    $('#mdlChose').modal('hide');
                }, 300);
                location.href = href;
            }
        });
    </script>
@endsection
