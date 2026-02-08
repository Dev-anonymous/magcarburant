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
                <h3 class="text-center font-weight-bold my-3">Sélectionnez le mode d'utilisation</h3>
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
                    <div class="my-3">
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
