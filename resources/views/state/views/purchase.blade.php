@extends('layouts.app')
@section('title', 'Achats')
@section('bg-class', 'bg-img-3')
@section('body')
    <div class="container-fluid">
        <div class="d-flex justify-content-between">
            <div class="">
                <h2 class="font-weight-bold">Achats | {{ $entity->shortname }} </h2>
                <p class="lead small m-0">Historique des achats (entrées) pour {{ $entity->shortname }} </p>
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
                            <div class="col-6 col-md-4 col-12">
                                <div class="text-center">
                                    <i class="material-icons md-36 text-success mb-1">receipt</i>
                                    <div class="font-weight-bold text-success">Total Achat (USD)</div>
                                    <div class="h4 font-weight-bold text-success" style="font-size: 28px" totalAmount>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 col-md-4 col-12">
                                <div class="text-center">
                                    <i class="material-icons md-36 text-primary mb-1">local_gas_station</i>
                                    <div class="font-weight-bold text-primary">Volume Total (TM)</div>
                                    <div class="h4 font-weight-bold text-primary" style="font-size: 28px" totalTm></div>
                                </div>
                            </div>
                            <div class="col-6 col-md-4 col-12">
                                <div class="text-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" height="48px" viewBox="0 -960 960 960"
                                        width="48px" fill="#FF3D55">
                                        <path
                                            d="M480-78q-142 0-242-97.71T138-415q0-68.14 27-130.77 27-62.63 75-108.73L480-892l240 237.5q48 46.1 75.5 108.73T823-415q0 141.58-100.5 239.29Q622-78 480-78ZM229-415h502q0-46-19-91.5T659-586L480-763 301-586q-34 34-53 79.54-19 45.54-19 91.46Z" />
                                    </svg>
                                    <div class="font-weight-bold text-danger">Volume Total (M³)</div>
                                    <div class="h4 font-weight-bold text-danger" style="font-size: 28px" totalM3></div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 col-12">
                                <div class="">
                                    <h5 class="card-title text-center mb-4">Répartition des achats par produit (USD)</h5>
                                    <div id="chart1"></div>
                                </div>
                            </div>
                            <div class="col-md-6 col-12">
                                <div class="">
                                    <h5 class="card-title text-center mb-4">Répartition des achats par produit (M³)</h5>
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
                                        Historique des tous les achats pour {{ $entity->shortname }}
                                    </h4>
                                </div>
                                @php
                                    $d = now()->startOfMonth()->toDateString();
                                    $d2 = now()->toDateString();
                                @endphp
                                <div class="col-xs-12 col-sm-6">
                                    <form class="form-inline filters-form pull-right" role="form">
                                        <div class="form-group mb-1">
                                            <label class="mr-2" for="dv222">Du</label>
                                            <input class="form-control flatpickr2" id="dv222"
                                                value="{{ $d }}" name="date1" style="width:100px" />
                                        </div>
                                        <div class="form-group mb-1">
                                            <label class="mr-2" for="dv22">Au</label>
                                            <input class="form-control flatpickr2" id="dv22"
                                                value="{{ $d2 }}" name="date2" style="width:100px" />
                                        </div>
                                        @if ('edit' == $mode)
                                            <div class="form-group mb-1">
                                                <button type="button" class="btn btn-sm btn-primary" data-toggle="modal"
                                                    data-target="#mdlChose">
                                                    <i class="material-icons md-18">add_circle_outline</i> Nouvel achat
                                                </button>
                                            </div>
                                        @endif
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="py-4">
                            <div class="table-responsive">
                                <table id="table" class="table table-striped table-hover text-center text-nowrap"
                                    style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Voie</th>
                                            <th class="text-nowrap">Produit</th>
                                            <th>Fournisseur</th>
                                            <th class="text-nowrap">N° Facture</th>
                                            <th class="text-nowrap">Prix Unitaire (USD)</th>
                                            <th class="text-nowrap">Qte TM</th>
                                            <th class="text-nowrap">Qte M3</th>
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
@endsection
@section('modals')
    <div class="modal fade" id="mdladd" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="defaultModalLabel">Nouvel achat</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form class="was-validated" fadd>
                    <div class="modal-body">
                        <h2 class="mb-3 text-center">
                            Veuillez enregistrer l'achat de <b>{{ $entity->shortname }}</b>
                        </h2>
                        <input type="hidden" name="entity_id" value="{{ $entity->id }}">
                        <div class="row">
                            <div class="col-md-12">
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
                                    <label class="mb-0" for="dv1">Date de l'achat</label>
                                    <input class="form-control flatpickr" id="dv1" required name="date">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <label class="mb-0">Fournisseur</label>
                                    <input class="form-control" required name="provider">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <label class="mb-0">N° Facture</label>
                                    <input class="form-control" required name="billnumber">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <label class="mb-0">Prix Unitaire (en USD)</label>
                                    <input type="number" min="0.001" step="0.001" class="form-control" required
                                        name="unitprice">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <label class="mb-0">Qantité TM</label>
                                    <input type="number" min="0.001" step="0.001" class="form-control" required
                                        name="qtytm">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <label class="mb-0">Qantité M3</label>
                                    <input type="number" min="0.001" step="0.001" class="form-control" required
                                        name="qtym3">
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
                            <input type="file" multiple class="form-control" name="purchasefile[]">
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
                    <h5 class="modal-title" id="defaultModalLabel">Modification de l'achat</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form class="was-validated" fedit>
                    <h2 class="mb-3 text-center">
                        Veuillez modifier l'achat de <b>{{ $entity->shortname }}</b>
                    </h2>
                    <input type="hidden" name="entity_id" value="{{ $entity->id }}">
                    <input type="hidden" name="id">
                    <input type="hidden" name="action" value="update">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
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
                                    <label class="mb-0" for="dv1">Date de l'achat</label>
                                    <input class="form-control flatpickr" id="dv1" required name="date">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <label class="mb-0">Fournisseur</label>
                                    <input class="form-control" required name="provider">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <label class="mb-0">N° Facture</label>
                                    <input class="form-control" required name="billnumber">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <label class="mb-0">Prix Unitaire (en USD)</label>
                                    <input type="number" min="0.001" step="0.001" class="form-control" required
                                        name="unitprice">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <label class="mb-0">Qantité TM</label>
                                    <input type="number" min="0.001" step="0.001" class="form-control" required
                                        name="qtytm">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <label class="mb-0">Qantité M3</label>
                                    <input type="number" min="0.001" step="0.001" class="form-control" required
                                        name="qtym3">
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
                            <input type="file" multiple class="form-control" name="purchasefile[]">
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
                                Voulez-vous supprimer cet achat ?
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
                    <h5 class="modal-title" id="defaultModalLabel">Importation des données d'achat</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form class="was-validated" fadd>
                    <input type="hidden" name="action" value="import">
                    <div class="modal-body">
                        <h2 class="mb-3 text-center">
                            Veuillez importer le fichier Excel des achats de <b>{{ $entity->shortname }}</b>
                        </h2>
                        <input type="hidden" name="entity_id" value="{{ $entity->id }}">
                        <div class="text-center p-3">
                            <h5>Vous pouvez importer la liste de tous les achats disponibles depuis un fichier Excel en
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
                            <a href="{{ asset('ModeleImportationAchat.xlsx') }}" class="btn text-danger">
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
                        Saisir un achat
                    </button>
                    <button class="btn btn-sm btn-outline-primary m-2 mr-2" btnmdl data-target="#mdladd2">
                        <i class="material-icons md-18">insert_drive_file</i>
                        Importer les achats
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
                url: '{{ route('purchase.index') }}',
                data: function(d) {
                    d.entity_id = '{{ @$entity->id }}';
                    d.date = $('[name="date1"]').val() + ' to ' + $('[name="date2"]').val();
                }
            },
            order: [
                [0, "desc"]
            ],
            columnDefs: [{
                targets: 0,
                width: '1%'
            }, {
                targets: 1,
                width: '1%'
            }, {
                targets: 10,
                width: '1%'
            }],
            columns: [{
                    data: 'date',
                    name: 'date'
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
                    data: 'provider',
                    name: 'provider'
                },
                {
                    data: 'billnumber',
                    name: 'billnumber'
                },
                {
                    data: 'unitprice',
                    name: 'unitprice'
                },
                {
                    data: 'qtytm',
                    name: 'qtytm'
                },
                {
                    data: 'qtym3',
                    name: 'qtym3'
                },
                {
                    data: 'density',
                    name: 'density'
                },
                {
                    data: 'purchasefile',
                    name: 'purchasefile',
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
                $('[name="way"]', form).val(data.way);
                $('[name="provider"]', form).val(data.provider);
                $('[name="billnumber"]', form).val(data.billnumber);
                $('[name="unitprice"]', form).val(data.unitprice);
                $('[name="qtytm"]', form).val(data.qtytm);
                $('[name="qtym3"]', form).val(data.qtym3);
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
                url: '{{ route('purchase.store') }}',
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
                        $('#mdladd,#mdledit').modal('hide');
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
            $(':input', form).attr('disabled', true);
            $('[loader]', btn).show();
            $('[text]', btn).hide();

            $.ajax({
                url: '{{ route('purchase.index') }}/' + id,
                method: 'delete',
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

        function dashboard() {
            var ldr = $('[dataloader]');
            ldr.show();
            $.ajax({
                url: '{{ route('dashboard') }}',
                data: {
                    type: 'purchase',
                    entity_id: '{{ @$entity->id }}',
                    date: $('[name="date1"]').val() + ' to ' + $('[name="date2"]').val(),
                },
                success: function(data) {
                    $('[totalAmount]').html(data.totalAmount);
                    $('[totalTm]').html(data.totalTm);
                    $('[totalM3]').html(data.totalM3);

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
            title: {
                text: ''
            },
            credits: {
                enabled: false
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
                    color: '#1a3b5d'
                },
                labelFormatter: function() {
                    return this.name + ' : ' + formatNumber(this.y) + ' USD';
                }
            },
            tooltip: {
                pointFormatter: function() {
                    return 'Total Achat USD : ' + formatNumber(this.y);
                }
            },
            plotOptions: {
                pie: {
                    innerSize: '70%',
                    depth: 50,
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: true,
                        formatter: function() {
                            const total = this.series.data.reduce((sum, p) => sum + p.y, 0);
                            if (total === 0) return '';
                            return ((this.y / total) * 100).toFixed(1) + '%';
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
                name: 'Achats USD',
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
            title: {
                text: ''
            },
            credits: {
                enabled: false
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
                    return this.name + ' : ' + formatNumber(this.y) + ' M³';
                }
            },
            tooltip: {
                pointFormatter: function() {
                    return 'Total M³ Achetés : ' + formatNumber(this.y);
                }
            },
            plotOptions: {
                pie: {
                    innerSize: '70%',
                    depth: 50,
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: true,
                        formatter: function() {
                            const total = this.series.data.reduce((sum, p) => sum + p.y, 0);
                            if (total === 0) return '';
                            return ((this.y / total) * 100).toFixed(1) + '%';
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
                name: 'Achats M³',
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
