@extends('layouts.app')
@section('title', 'Livraisons')
@section('bg-class', 'bg-img-3')
@section('body')
    <div class="container-fluid">
        <div class="d-flex justify-content-between">
            <div class="">
                <h2 class="font-weight-bold">Livraisons excédentaires</h2>
                <p class="lead small m-0">Gestion des livraisons excédentaires du carburant</p>
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
                        {{-- <div class="row g-3 mb-3">
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
                        </div> --}}
                        <div class="row">
                            <div class="col-md-6 col-12">
                                <div class="">
                                    <h5 class="card-title text-center mb-4">Répartition des livraisons par produit (LATA)</h5>
                                    <div id="chart1"></div>
                                </div>
                            </div>
                            <div class="col-md-6 col-12">
                                <div class="">
                                    <h5 class="card-title text-center mb-4">Répartition des livraisons par zone (LATA)</h5>
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
                                    <h4 class="card-title font-weight-bold">Historique des toutes les livraisons
                                        excédentaires</h4>
                                </div>@php
                                    $d = now()->startOfMonth()->toDateString();
                                    $d2 = now()->toDateString();
                                @endphp
                                @if (auth()->user()->user_role === 'petrolier')
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
                                            <div class="form-group mb-1">
                                                <button type="button" class="btn btn-sm btn-primary" data-toggle="modal"
                                                    data-target="#mdlChose">
                                                    <i class="material-icons md-18">add_circle_outline</i> Nouvelle
                                                    livraison excédentaire
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="py-4">
                            <div class="table-responsive">
                                <table id="table" class="table table-striped table-hover text-center text-nowrap"
                                    style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Terminal</th>
                                            <th>Date</th>
                                            <th>Localité</th>
                                            <th>Voie</th>
                                            <th class="text-nowrap">Produit</th>
                                            <th>Bon de livraison</th>
                                            <th>Programme de livraison</th>
                                            <th class="text-nowrap">Client</th>
                                            <th class="text-nowrap">LATA</th>
                                            <th class="text-nowrap">Prix unitaire /M3</th>
                                            <th class="text-nowrap">Total</th>
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
                    <h5 class="modal-title" id="defaultModalLabel">Nouvelle livraison excédentaire</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form class="was-validated" fadd>
                    <div class="modal-body">
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
                                    <label class="mb-0">Quantité livrée en LATA</label>
                                    <input type="number" min="0" step="0.001" class="form-control" required
                                        name="lata">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <label class="mb-0">Prix unitaire /M3</label>
                                    <input type="number" min="0" step="0.001" class="form-control" required
                                        name="unitprice">
                                </div>
                            </div>
                        </div>
                        <p class="mt-2">Pièces jointes : factures ou documents (.pdf)</p>
                        <div class="mb-2">
                            <label class="mb-0">Vous pouvez sélectionner plusieurs fichiers à la fois</label>
                            <input type="file" multiple class="form-control" name="deliveryfile[]">
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
                    <h5 class="modal-title" id="defaultModalLabel">Modification de la livraison excédentaire</h5>
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
                                    <label class="mb-0">Quantité livrée en LATA</label>
                                    <input type="number" min="0" step="0.001" class="form-control" required
                                        name="lata">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <label class="mb-0">Prix unitaire /M3</label>
                                    <input type="number" min="0" step="0.001" class="form-control" required
                                        name="unitprice">
                                </div>
                            </div>
                        </div>
                        <p class="mt-2">Pièces jointes : factures ou documents (.pdf)</p>
                        <div class="mb-2">
                            <label class="mb-0">Vous pouvez sélectionner plusieurs fichiers à la fois</label>
                            <input type="file" multiple class="form-control" name="deliveryfile[]">
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
                                Voulez-vous supprimer cette livraison excédentaire ?
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
                    <h5 class="modal-title" id="defaultModalLabel">Importation des données de livraison excédentaire</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form class="was-validated" fadd>
                    <input type="hidden" name="action" value="import">
                    <div class="modal-body">
                        <div class="text-center p-3">
                            <h5>Vous pouvez importer la liste de toutes vos livraisons excédentaires disponibles depuis un
                                fichier Excel en
                                respectant les colonnes et le format des données.</h5>
                        </div>
                        <p class="mt-2">Sélectionnez le fichier excel à importer</p>
                        <div class="mb-2">
                            <input type="file" required multiple class="form-control" name="file">
                        </div>
                        <x-alert />
                        <div class="mt-3">
                            <a href="{{ asset('ModeleImportationLivraisonsExcedentaires.xlsx') }}"
                                class="btn text-danger">
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
                        Saisir une livraison excédentaire
                    </button>
                    <button class="btn btn-sm btn-outline-primary m-2 mr-2" btnmdl data-target="#mdladd2">
                        <i class="material-icons md-18">insert_drive_file</i>
                        Importer les livraisons excédentaires
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
        })

        var dtObj = $('#table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route('delivery.index') }}',
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
                targets: 13,
                width: '1%'
            }],
            columns: [{
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
                    data: 'unitprice',
                    name: 'unitprice'
                },
                {
                    data: 'total',
                    name: 'total',
                    orderable: false,
                    searchable: false,
                    className: 'cursor'
                },
                {
                    data: 'deliveryfile',
                    name: 'deliveryfile',
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
                            let num = parseFloat(data.toString().replace(/ /g,
                                '').replace(',', '.'));
                            return isNaN(num) ? data : num;
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
                $('[name="terminal"]', form).val(data.terminal);
                $('[name="client"]', form).val(data.client);
                $('[name="way"]', form).val(data.way);
                $('[name="locality"]', form).val(data.locality);
                $('[name="delivery_note"]', form).val(data.delivery_note);
                $('[name="delivery_program"]', form).val(data.delivery_program);
                $('[name="lata"]', form).val(data.lata);
                $('[name="unitprice"]', form).val(data.unitprice);
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

            $('.tooltip').remove();
            $('[tooltip]').tooltip();
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
                url: '{{ route('delivery.store') }}',
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
                url: '{{ route('delivery.index') }}/' + id,
                method: 'delete',
                success: function(resp) {
                    var mess = resp?.message ?? "Erreur, veuillez réessayer !";
                    rep.html(mess).stop().removeClass().addClass(
                            'p-1 m-0 text-center alert alert-success')
                        .show();
                    dtObj.ajax.reload(null, false);
                    setTimeout(() => {
                        rep.hide();
                        $('#mdldel').modal('hide');
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
                    type: 'delivery',
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
                    return this.name + ' : ' + formatNumber(this.y) + ' M³';
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
