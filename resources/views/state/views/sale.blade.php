@extends('layouts.app')
@section('title', 'Ventes')
@section('bg-class', 'bg-img-3')
@section('body')
    <div class="container-fluid">
        <div class="d-flex justify-content-between">
            <div class="">
                <h2 class="font-weight-bold">Ventes | {{ $entity->shortname }} </h2>
                <p class="lead small m-0">Historiques des ventes (sorties) pour | {{ $entity->shortname }} </p>
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

        <div class="container-fluid">
            <div class="row">
                <div class="col-12 p-0">
                    <div class="carte d-block">
                        <div class="row g-3 mb-3">
                            <div class="col-6 col-md-6 col-12">
                                <div class="text-center">
                                    <i class="material-icons md-36 text-success mb-1">local_gas_station</i>
                                    <div class="font-weight-bold text-success">Volume Total LATA</div>
                                    <div class="h4 font-weight-bold text-success" style="font-size: 28px" totalLata>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 col-md-6 col-12">
                                <div class="text-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" height="48px" viewBox="0 -960 960 960"
                                        width="48px" fill="#FF3D55">
                                        <path
                                            d="M480-78q-142 0-242-97.71T138-415q0-68.14 27-130.77 27-62.63 75-108.73L480-892l240 237.5q48 46.1 75.5 108.73T823-415q0 141.58-100.5 239.29Q622-78 480-78ZM229-415h502q0-46-19-91.5T659-586L480-763 301-586q-34 34-53 79.54-19 45.54-19 91.46Z" />
                                    </svg>
                                    <div class="font-weight-bold text-danger">Volume Total L15</div>
                                    <div class="h4 font-weight-bold text-danger" style="font-size: 28px" totalL15>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 col-12">
                                <div class="">
                                    <h5 class="card-title text-center mb-4">Répartition des ventes par produit (M³)</h5>
                                    <div id="chart1"></div>
                                </div>
                            </div>
                            <div class="col-md-6 col-12">
                                <div class="">
                                    <h5 class="card-title text-center mb-4">Répartition des ventes par produit LATA</h5>
                                    <div id="chart2"></div>
                                </div>
                            </div>
                            <div class="col-12">
                                <x-dataloader />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 p-0">
                    <div class="card transparent">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-xs-12 col-sm-6">
                                    <h4 class="card-title font-weight-bold">
                                        Historique des toutes les ventes pour {{ $entity->shortname }}
                                    </h4>
                                </div>
                                @php
                                    $d = now()->startOfMonth()->toDateString();
                                    $d2 = now()->toDateString();
                                @endphp
                            </div>
                            <form id="ffilter" class="filters-form pull-right" role="form">
                                <div class="form-group mb-1">
                                    <label for="dv222" class="control-label d-block mb-0">Du</label>
                                    <input type="text" class="form-control flatpickr2" id="dv222" name="date1"
                                        value="{{ $d }}" style="min-width:120px;">
                                </div>
                                <div class="form-group mb-1">
                                    <label for="dv22" class="control-label d-block mb-0">Au</label>
                                    <input type="text" class="form-control flatpickr2" id="dv22" name="date2"
                                        value="{{ $d2 }}" style="min-width:120px;">
                                </div>
                                @if ($entity->user->user_role == 'logisticien')
                                    <div class="form-group mb-1">
                                        <label for="zone" class="control-label d-block mb-0">Type de vente</label>
                                        <select name="from_mutuality" id="from_mutuality" class="form-control select2"
                                            style="min-width:150px;">
                                            <option value="">Tous</option>
                                            <option value="0">Non mutualisé</option>
                                            <option value="1">Mutualisé</option>
                                        </select>
                                    </div>
                                @endif
                                @if ('edit' == $mode)
                                    <div class="form-group mb-1">
                                        <button type="button" class="btn btn-sm btn-primary mt-3" data-toggle="modal"
                                            data-target="#mdlChose">
                                            <i class="material-icons md-18">add_circle_outline</i> Nouvelle vente
                                        </button>
                                    </div>
                                @endif
                            </form>
                        </div>
                        <div class="card-body">
                            @if ('edit' == $mode)
                                <button style="display: none" type="button" class="btn btn-sm btn-danger mb-2"
                                    data-toggle="modal" data-target="#mdldelall" id="btnDelAll">
                                    <i class="material-icons md-18">delete</i> <span text></span>
                                </button>
                            @endif
                            <div class="table-responsive">
                                <table id="table" class="table table-striped table-hover text-center text-nowrap"
                                    style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>
                                                <div class="custom-control custom-checkbox mt-3">
                                                    <input type="checkbox" name="remember" class="custom-control-input"
                                                        id="selall">
                                                    <label class="custom-control-label" for="selall">
                                                    </label>
                                                </div>
                                            </th>
                                            <th>ID</th>
                                            <th>Terminal</th>
                                            <th>Date</th>
                                            <th>Localité</th>
                                            <th>Voie</th>
                                            <th class="text-nowrap">Produit</th>
                                            <th>Bon de livraison</th>
                                            <th>Programme de livraison</th>
                                            <th class="text-nowrap">Client</th>
                                            <th class="text-nowrap">Lata</th>
                                            <th class="text-nowrap">L15</th>
                                            <th class="text-nowrap">Densité</th>
                                            <th class="text-nowrap no-export">Factures</th>
                                            <th class="no-export"></th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    </div>
@endsection
@section('modals')
    <div class="modal fade" id="mdladd" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="defaultModalLabel">Nouvelle vente</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form class="was-validated" fadd>
                    <div class="modal-body">
                        <h2 class="mb-3 text-center">
                            Veuillez enregistrer la vente de <b>{{ $entity->shortname }}</b>
                        </h2>
                        <input type="hidden" name="entity_id" value="{{ $entity->id }}">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <label class="mb-0">Produit</label>
                                    <select name="product" id="" class="form-control" required>
                                        <option value="">Sélectionnez un produit</option>
                                        @foreach (mainfuels() as $e)
                                            <option>{{ $e }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <label class="mb-0" for="dv1">Date de la vente</label>
                                    <input class="form-control flatpickr" id="dv1" required name="date">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-2">
                                    <label class="mb-0">Terminal</label>
                                    <input class="form-control" required name="terminal">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <label class="mb-0">Voie</label>
                                    <select name="way" id="" class="form-control" required>
                                        <option value="">Sélectionnez une voie</option>
                                        @foreach (mainWays() as $e)
                                            <option>{{ $e }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <label class="mb-0">Localité</label>
                                    <input class="form-control" required name="locality">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <label class="mb-0">Client</label>
                                    <input class="form-control" required name="client">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <label class="mb-0">Bon de livraison</label>
                                    <input class="form-control" required name="delivery_note">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <label class="mb-0">Programmme de livraison</label>
                                    <input class="form-control" required name="delivery_program">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <label class="mb-0">LATA</label>
                                    <input type="number" min="0" step="0.001" class="form-control" required
                                        name="lata">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <label class="mb-0">L15</label>
                                    <input type="number" min="0" step="0.001" class="form-control" required
                                        name="l15">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <label class="mb-0">Densité</label>
                                    <input type="number" min="0.001" step="0.001" class="form-control" required
                                        name="density">
                                </div>
                            </div>
                        </div>
                        <p class="mt-2">Pièces jointes : factures ou documents (.pdf)</p>
                        <div class="mb-2">
                            <label class="mb-0">Vous pouvez sélectionner plusieurs fichiers à la fois</label>
                            <input type="file" multiple class="form-control" name="salefile[]">
                        </div>
                        <x-alert />
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn" data-dismiss="modal">
                            <i class="material-icons md-18 mr-1 m-0 p-0">highlight_off</i>
                            Fermer
                        </button>
                        <button type="submit" class="btn btn-primary d-flex align-items-center justify-content-center">
                            <x-loader />
                            <span text>
                                <i class="material-icons md-18 mr-1 m-0 p-0">save</i>
                                Valider
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="mdledit" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="defaultModalLabel">Modification de la vente</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form class="was-validated" fedit>
                    <input type="hidden" name="id">
                    <input type="hidden" name="action" value="update">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <label class="mb-0">Produit</label>
                                    <select name="product" id="" class="form-control" required>
                                        @foreach (mainfuels() as $e)
                                            <option>{{ $e }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <label class="mb-0" for="dv1">Date de la vente</label>
                                    <input class="form-control flatpickr" id="dv1" required name="date">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-2">
                                    <label class="mb-0">Terminal</label>
                                    <input class="form-control" required name="terminal">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <label class="mb-0">Voie</label>
                                    <select name="way" id="" class="form-control" required>
                                        @foreach (mainWays() as $e)
                                            <option>{{ $e }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <label class="mb-0">Localité</label>
                                    <input class="form-control" required name="locality">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <label class="mb-0">Client</label>
                                    <input class="form-control" required name="client">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <label class="mb-0">Bon de livraison</label>
                                    <input class="form-control" required name="delivery_note">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <label class="mb-0">Programmme de livraison</label>
                                    <input class="form-control" required name="delivery_program">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <label class="mb-0">LATA</label>
                                    <input type="number" min="0" step="0.001" class="form-control" required
                                        name="lata">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <label class="mb-0">L15</label>
                                    <input type="number" min="0" step="0.001" class="form-control" required
                                        name="l15">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <label class="mb-0">Densité</label>
                                    <input type="number" min="0.001" step="0.001" class="form-control" required
                                        name="density">
                                </div>
                            </div>
                        </div>
                        <p class="mt-2">Pièces jointes : factures ou documents (.pdf)</p>
                        <div class="mb-2">
                            <label class="mb-0">Vous pouvez sélectionner plusieurs fichiers à la fois</label>
                            <input type="file" multiple class="form-control" name="salefile[]">
                        </div>
                        <x-alert />
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn" data-dismiss="modal">
                            <i class="material-icons md-18 mr-1 m-0 p-0">highlight_off</i>
                            Fermer
                        </button>
                        <button type="submit" class="btn btn-primary d-flex align-items-center justify-content-center">
                            <x-loader />
                            <span text>
                                <i class="material-icons md-18 mr-1 m-0 p-0">save</i>
                                Valider
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="mdldel" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form class="was-validated" fdel>
                    <input type="hidden" name="id">
                    <div class="modal-body">
                        <div class="mb-2 text-center">
                            <h3 class="text-danger">
                                Voulez-vous supprimer cette vente ?
                            </h3>
                        </div>
                        <x-alert />
                    </div>
                    <div class="w-100 d-flex justify-content-center p-3">
                        <div class="">
                            <button type="button" class="btn btn-sm m-2" data-dismiss="modal">
                                <i class="material-icons md-18 mr-1 m-0 p-0">highlight_off</i>
                                NON
                            </button>
                        </div>
                        <div class="">
                            <button type="submit"
                                class="btn  btn-sm btn-danger d-flex m-2 align-items-center justify-content-center">
                                <x-loader />
                                <span text>
                                    <i class="material-icons md-18 mr-1 m-0 p-0">delete</i>
                                    OUI JE CONFIRME
                                </span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="mdldelall" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form class="was-validated" fdel fdel2>
                    <input type="hidden" name="id">
                    <input type="hidden" name="ids">
                    <input type="hidden" name="action" value="bulk">
                    <div class="modal-body">
                        <div class="mb-2 text-center">
                            <h3 class="text-danger">
                                Voulez-vous <span deltext></span> ?
                            </h3>
                        </div>
                        <x-alert />
                    </div>
                    <div class="w-100 d-flex justify-content-center p-3">
                        <div class="">
                            <button type="button" class="btn btn-sm m-2" data-dismiss="modal">
                                <i class="material-icons md-18 mr-1 m-0 p-0">highlight_off</i>
                                NON
                            </button>
                        </div>
                        <div class="">
                            <button type="submit"
                                class="btn  btn-sm btn-danger d-flex m-2 align-items-center justify-content-center">
                                <x-loader />
                                <span text>
                                    <i class="material-icons md-18 mr-1 m-0 p-0">delete</i>
                                    OUI JE CONFIRME
                                </span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="mdladd2" role="dialog" style="overflow-y: auto;">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="defaultModalLabel">Importation des données de vente</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form class="was-validated" fadd>
                    <input type="hidden" name="action" value="import">
                    <div class="modal-body">
                        <h2 class="mb-3 text-center">
                            Veuillez importer le fichier Excel des ventes de <b>{{ $entity->shortname }}</b>
                        </h2>
                        <input type="hidden" name="entity_id" value="{{ $entity->id }}">
                        <div class="text-center p-3">
                            <h5>Vous pouvez importer la liste de toutes les ventes disponibles depuis un fichier Excel en
                                respectant les colonnes et le format des données.</h5>
                        </div>
                        <p class="mt-2">Sélectionnez le fichier excel à importer</p>
                        <div class="mb-2">
                            <input type="file" required multiple class="form-control" name="file">
                        </div>
                        <div style="max-height: 100px; overflow: auto">
                            <x-alert />
                        </div>
                        <div class="mt-3">
                            <a href="{{ asset('ModeleImportationVente.xlsx') }}" class="btn text-danger">
                                <i class="material-icons md-18">insert_drive_file</i>
                                Télécharger le modèle
                            </a>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn" data-dismiss="modal">
                            <i class="material-icons md-18 mr-1 m-0 p-0">highlight_off</i>
                            Fermer
                        </button>
                        <button type="submit" class="btn btn-primary d-flex align-items-center justify-content-center">
                            <x-loader />
                            <span text>
                                <i class="material-icons md-18 mr-1 m-0 p-0">save</i>
                                Valider
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="mdlChose" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body text-center p-5">
                    <h4 class="mb-3">Quelle action voulez-vous ? </h4>
                    <button class="btn btn-sm btn-primary m-2" btnmdl data-target="#mdladd">
                        <i class="material-icons md-18">edit</i>
                        Saisir une vente
                    </button>
                    <button class="btn btn-sm btn-outline-primary m-2 mr-2" btnmdl data-target="#mdladd2">
                        <i class="material-icons md-18">insert_drive_file</i>
                        Importer les ventes
                    </button>
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
    <x-datatable />
    <x-flatpickr />
    <x-chart />
    <x-select />

    <script>
        flatpickr(".flatpickr", {
            maxDate: "today",
            locale: {
                firstDayOfWeek: 1
            }
        });
        flatpickr(".flatpickr2", {
            maxDate: "today",
            locale: {
                firstDayOfWeek: 1
            }
        });

        $('[btnmdl]').click(function() {
            $('.modal.show').modal('hide');
            var t = $(this).data('target');
            $(`${t}`).modal('show');
        });

        function formatFr(val) {
            if (val === '' || val === null || isNaN(val)) return '';
            return parseFloat(val).toLocaleString('fr-FR', {
                minimumFractionDigits: 3,
                maximumFractionDigits: 3
            });
        }

        var dtObj = $('#table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route('sale.index') }}',
                data: function(d) {
                    d.entity_id = '{{ @$entity->id }}';
                    d.date = $('[name="date1"]').val() + ' to ' + $('[name="date2"]').val();
                    d.from_mutuality = $('#from_mutuality').val();
                }
            },
            order: [
                [1, "desc"]
            ],
            columnDefs: [{
                targets: 1,
                width: '1%'
            }, {
                targets: 14,
                width: '1%'
            }],
            columns: [{
                    data: 'selall',
                    orderable: false,
                    searchable: false,
                }, {
                    data: 'id',
                    name: 'id',
                },
                {
                    data: 'terminal',
                    name: 'terminal',
                },
                {
                    data: 'date',
                    name: 'date'
                },
                {
                    data: 'locality',
                    name: 'locality'
                },
                {
                    data: 'way',
                    name: 'way'
                },
                {
                    data: 'product',
                    name: 'product'
                },
                {
                    data: 'delivery_note',
                    name: 'delivery_note'
                },
                {
                    data: 'delivery_program',
                    name: 'delivery_program'
                },
                {
                    data: 'client',
                    name: 'client'
                },
                {
                    data: 'lata',
                    name: 'lata'
                },
                {
                    data: 'l15',
                    name: 'l15'
                },
                {
                    data: 'density',
                    name: 'density'
                },
                {
                    data: 'salefile',
                    name: 'salefile',
                    orderable: false,
                    searchable: false,
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false,
                },
            ],
            dom: 'Blfrtip',
            buttons: [{
                extend: 'excelHtml5',
                title: 'Export Excel',
                exportOptions: {
                    columns: ':not(.no-export)',
                    format: {
                        body: function(data, row, column, node) {
                            if (!data) return data;
                            let text = $('<div>').html(data).text().trim();
                            if ($(node).find('input[type="checkbox"]').length > 0) {
                                return '';
                            }
                            let cleaned = text.replace(/\s+/g, '').replace(',', '.');
                            let num = Number(cleaned);
                            return isNaN(num) ? text : num;
                        }
                    }
                }
            }, ],

        }).on('draw.dt', function(e, settings, data, xhr) {
            sell[0].checked = false;
            canshow();
            @if ('view' == $mode)
                $('#selall,.selall').closest('div').hide();
            @endif

            $('[bedit]').off('click').click(function() {
                var data = JSON.parse($(this).attr('data'));
                var mdl = $('#mdledit');
                var form = $('[fedit]', mdl);
                var dateinp = $('[name="date"]', form);
                var dob = dateinp.flatpickr();
                dob.destroy();
                dob = dateinp.flatpickr({
                    maxDate: "today",
                    locale: {
                        firstDayOfWeek: 1
                    }
                });
                dob.setDate(data.date);

                $('[name="id"]', form).val(data.id);
                $('[name="product"]', form).val(data.product);
                $('[name="terminal"]', form).val(data.terminal);
                $('[name="client"]', form).val(data.client);
                $('[name="way"]', form).val(data.way);
                $('[name="locality"]', form).val(data.locality);
                $('[name="delivery_note"]', form).val(data.delivery_note);
                $('[name="delivery_program"]', form).val(data.delivery_program);
                $('[name="lata"]', form).val(data.lata);
                $('[name="l15"]', form).val(data.l15);
                $('[name="density"]', form).val(data.density);
                mdl.modal('show');
            });
            $('[bdel]').off('click').click(function() {
                var data = JSON.parse($(this).attr('data'));
                var mdl = $('#mdldel');
                var form = $('[fdel]', mdl);
                $('[name="id"]', form).val(data.id);
                $('[shortname]', form).html(data.name);
                mdl.modal('show');
            });
        });

        var sell = $('#selall');

        function canshow() {
            var show = false;
            var n = 0;
            var ids = [];
            $('.selall').each((i, s) => {
                if (s.checked) {
                    ids.push(s.value);
                    show = true;
                    n++;
                }
            });
            if (show) {
                var t = `Supprimer ${n} élément` + (n > 1 ? 's' : '');
                $('#btnDelAll').slideDown().find('[text]').html(t);
                $('[deltext]').html(t.toLowerCase());
                var f = $('[fdel2]');
                $('[name="ids"]', f).val(JSON.stringify(ids));
                $('[name="id"]', f).val(ids[0]);
            } else {
                $('#btnDelAll').slideUp();
                $('[name="id"]', f).val('');
                $('[name="ids"]', f).val('');
            }
        }
        canshow();
        sell.change(function() {
            var e = this;
            $('.selall').each((i, s) => {
                s.checked = e.checked;
            });
            canshow();
        });

        $(document).on('click', '.selall', function() {
            canshow();
        })

        $('.flatpickr2').change(function() {
            dtObj.ajax.reload(null, false);
            dashboard();
        });

        $('#mdledit').on('shown.bs.modal', function() {
            $('[name="from"], [name="to"]', this).each(function() {
                if (this._flatpickr) {
                    this._flatpickr.destroy();
                }
                flatpickr(this, {
                    maxDate: "today",
                    locale: {
                        firstDayOfWeek: 1
                    }
                });
            });
        });

        $('[fadd],[fedit]').on('submit', function(e) {
            e.preventDefault();
            var form = $(this);
            var btn = $(':submit', form);
            var rep = $('#rep', form);
            var data = new FormData(this);
            rep.hide();
            $(':input', form).attr('disabled', true);
            $('[loader]', btn).show();
            $('[text]', btn).hide();

            $.ajax({
                url: '{{ route('sale.store') }}',
                method: 'POST',
                data: data,
                contentType: false,
                processData: false,
                success: function(resp) {
                    var mess = resp?.message ?? "Erreur, veuillez réessayer !";
                    rep.html(mess).stop().removeClass().addClass(
                            'p-1 m-0 alert alert-success')
                        .show();
                    dtObj.ajax.reload(null, false);
                    dashboard();
                    form[0].reset();
                    setTimeout(() => {
                        rep.hide();
                        $('.modal.show').modal('hide');
                    }, 2000);
                },
                error: function(xhr, a, b) {
                    var resp = xhr.responseJSON;
                    var mess = resp?.message ?? "Erreur, veuillez réessayer !";
                    rep.html(mess).stop().removeClass().addClass(
                            'p-1 m-0 alert alert-danger')
                        .show();
                },
            }).always(function() {
                $(':input', form).attr('disabled', false);
                $('[loader]', btn).hide();
                $('[text]', btn).show();
            });
        });

        $('[fdel]').on('submit', function(e) {
            e.preventDefault();
            var form = $(this);
            var btn = $(':submit', form);
            var rep = $('#rep', form);
            var id = $('[name="id"]', form).val();
            rep.hide();
            var data = form.serialize();
            $(':input', form).attr('disabled', true);
            $('[loader]', btn).show();
            $('[text]', btn).hide();

            $.ajax({
                url: '{{ route('sale.index') }}/' + id,
                method: 'delete',
                data: data,
                success: function(resp) {
                    var mess = resp?.message ?? "Erreur, veuillez réessayer !";
                    rep.html(mess).stop().removeClass().addClass(
                            'p-1 m-0 text-center alert alert-success')
                        .show();
                    dtObj.ajax.reload(null, false);
                    dashboard();
                    setTimeout(() => {
                        rep.hide();
                        $('.modal.show').modal('hide');
                    }, 2000);
                },
                error: function(xhr, a, b) {
                    var resp = xhr.responseJSON;
                    var mess = resp?.message ?? "Erreur, veuillez réessayer !";
                    rep.html(mess).stop().removeClass().addClass(
                            'p-1 m-0 text-center alert alert-danger')
                        .show();
                },
            }).always(function() {
                $(':input', form).attr('disabled', false);
                $('[loader]', btn).hide();
                $('[text]', btn).show();
            })
        });

        var ff = $('#ffilter');

        let timer;
        ff.change(function(e) {
            clearTimeout(timer);
            var e = $(e.target);
            timer = setTimeout(() => {
                dtObj.ajax.reload(null, false);
                dashboard();
            }, 100);
        });

        function dashboard() {
            var ldr = $('[dataloader]');
            ldr.show();
            $.ajax({
                url: '{{ route('dashboard') }}',
                data: {
                    type: 'sale',
                    from_mutuality: $('#from_mutuality').val(),
                    entity_id: '{{ @$entity->id }}',
                    date: $('[name="date1"]').val() + ' to ' + $('[name="date2"]').val(),
                },
                success: function(data) {
                    $('[totalLata]').html(data.totalLata);
                    $('[totalL15]').html(data.totalL15);

                    chart1.series[0].setData(
                        data.chart1.labels.map((label, i) => [label, data.chart1.data[i]])
                    );

                    chart2.series[0].setData(
                        data.chart2.labels.map((label, i) => [label, data.chart2.data[i]])
                    );

                    setTimeout(() => {
                        chart1.update({}, true);
                        chart2.update({}, true);
                    }, 400);

                    ldr.hide();
                },
                error: function(xhr, a, b) {

                },
            })
        }

        function formatNumber(val) {
            return val
                .toFixed(3)
                .replace('.', ',')
                .replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
        }

        var chart1 = Highcharts.chart('chart1', {
            chart: {
                type: 'pie',
                height: 300,
                backgroundColor: 'transparent',
                options3d: {
                    enabled: true,
                    alpha: 45,
                    beta: 0,
                    depth: 50
                }
            },
            legend: {
                enabled: true,
                align: 'left',
                verticalAlign: 'middle',
                layout: 'vertical',
                itemMarginTop: 8,
                itemMarginBottom: 8,
                symbolRadius: 6,
                symbolHeight: 12,
                symbolWidth: 12,
                itemStyle: {
                    fontSize: '14px',
                    color: '#1a3b5d',
                    fontWeight: 'normal'
                },
                labelFormatter: function() {
                    return this.name + ' : ' + formatNumber(this.y) + ' M³';
                }
            },
            title: {
                text: ''
            },
            credits: {
                enabled: false
            },
            plotOptions: {
                pie: {
                    innerSize: '60%',
                    depth: 50,
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: true,
                        formatter: function() {
                            const total = this.series.data.reduce((sum, p) => sum + p.y, 0);
                            if (total === 0) return '';
                            const percent = (this.y / total * 100).toFixed(1);
                            return percent + '%';
                        },
                        style: {
                            fontWeight: 'bold',
                            color: '#000',
                            fontSize: '13px'
                        }
                    }
                }
            },
            tooltip: {
                pointFormatter: function() {
                    return 'Total M³ Vendus : ' + formatNumber(this.y);
                }
            },
            series: [{
                name: 'Ventes',
                showInLegend: true,
                data: [],
                colors: [
                    '#4e73df',
                    '#1cc88a',
                    '#36b9cc',
                    '#f6c23e',
                    '#5a90cc'
                ]
            }],
            responsive: {
                rules: [{
                    condition: {
                        maxWidth: 500
                    },
                    chartOptions: {
                        legend: {
                            align: 'center',
                            verticalAlign: 'bottom',
                            layout: 'horizontal'
                        },
                        chart: {
                            height: 400
                        }
                    }
                }]
            }
        });

        var chart2 = Highcharts.chart('chart2', {
            chart: {
                type: 'pie',
                height: 300,
                backgroundColor: 'transparent',
                options3d: {
                    enabled: true,
                    alpha: 45,
                    beta: 0,
                    depth: 50
                }
            },
            legend: {
                enabled: true,
                align: 'right',
                verticalAlign: 'middle',
                layout: 'vertical',
                itemMarginTop: 8,
                itemMarginBottom: 8,
                symbolRadius: 6,
                symbolHeight: 12,
                symbolWidth: 12,
                itemStyle: {
                    fontSize: '14px',
                    color: '#1a3b5d'
                },
                labelFormatter: function() {
                    return this.name + ' : ' + formatNumber(this.y);
                }
            },
            title: {
                text: ''
            },
            credits: {
                enabled: false
            },
            tooltip: {
                pointFormatter: function() {
                    return 'Total Vente LATA : ' + formatNumber(this.y);
                }
            },
            plotOptions: {
                pie: {
                    innerSize: '60%',
                    depth: 50,
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: true,
                        formatter: function() {
                            const total = this.series.data.reduce((sum, p) => sum + p.y, 0);
                            if (total === 0) return '';
                            const percent = (this.y / total * 100).toFixed(1);
                            return percent + '%';
                        },
                        style: {
                            fontWeight: 'bold',
                            color: '#000',
                            fontSize: '13px'
                        }
                    }
                }
            },
            series: [{
                name: 'Ventes LATA',
                showInLegend: true,
                data: [],
                colors: [
                    '#4e73df',
                    '#1cc88a',
                    '#36b9cc',
                    '#f6c23e',
                    '#5a90cc'
                ]
            }],
            responsive: {
                rules: [{
                    condition: {
                        maxWidth: 500
                    },
                    chartOptions: {
                        legend: {
                            align: 'center',
                            verticalAlign: 'bottom',
                            layout: 'horizontal'
                        },
                        chart: {
                            height: 400
                        }
                    }
                }]
            }
        });


        dashboard();
    </script>
@endsection
