@extends('layouts.app')
@section('title', 'Ventes')
@section('body')
    <div class="container-fluid">
        <div class="d-flex justify-content-between">
            <div class="">
                <h2 class="font-weight-bold">Ventes </h2>
                <p class="lead small m-0">Gestion des ventes (sorties) </p>
            </div>
            <div class="m-2">
                <button onclick="history.back()" class="btn btn-sm btn-light d-flex align-items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" height="12px" viewBox="0 -960 960 960" width="12px"
                        fill="#000">
                        <path d="M423-59 2-480l421-421 78 79-342 342 342 342-78 79Z" />
                    </svg>
                    Retour
                </button>
            </div>
        </div>
        <hr />

        <div class="row">
            <div class="col-md-12">
                <div class="carte">
                    <div class="container-fluid m-0">
                        <div class="row g-3">
                            <div class="col-6 col-md-3 col-12">
                                <div class="card shadow-sm border-0">
                                    <div class="card-body text-center">
                                        <i class="material-icons md-36 text-primary mb-1">receipt</i>
                                        <div class="font-weight-bold text-secondary">Total Densité</div>
                                        <div class="h4 font-weight-bold text-primary" style="font-size: 28px" totalDensity>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 col-md-3 col-12">
                                <div class="card shadow-sm border-0">
                                    <div class="card-body text-center">
                                        <i class="material-icons md-36 text-success mb-1">local_gas_station</i>
                                        <div class="font-weight-bold text-secondary">Volume Total LATA</div>
                                        <div class="h4 font-weight-bold text-success" style="font-size: 28px" totalLata>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 col-md-3 col-12">
                                <div class="card shadow-sm border-0">
                                    <div class="card-body text-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" height="48px" viewBox="0 -960 960 960"
                                            width="48px" fill="#F5B666 ">
                                            <path
                                                d="M480-78q-142 0-242-97.71T138-415q0-68.14 27-130.77 27-62.63 75-108.73L480-892l240 237.5q48 46.1 75.5 108.73T823-415q0 141.58-100.5 239.29Q622-78 480-78ZM229-415h502q0-46-19-91.5T659-586L480-763 301-586q-34 34-53 79.54-19 45.54-19 91.46Z" />
                                        </svg>
                                        <div class="font-weight-bold text-warning">Volume Total L15</div>
                                        <div class="h4 font-weight-bold text-warning" style="font-size: 28px" totalL15>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 col-md-3 col-12">
                                <div class="card shadow-sm border-0">
                                    <div class="card-body text-center">
                                        <i class="material-icons md-36 text-danger mb-1">payment</i>
                                        <div class="font-weight-bold text-secondary">Densité de vente Moyenne</div>
                                        <div class="h4 font-weight-bold text-danger" style="font-size: 28px" avgDensity>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-12">
                                <div class="">
                                    <h5 class="card-title text-center mb-4">Répartition des ventes par densité (M³)</h5>
                                    <div style="height: 300px;" />
                                    <canvas id="chart1"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-12">
                            <div class="">
                                <h5 class="card-title text-center mb-4">Répartition des ventes par LATA</h5>
                                <div style="height: 300px;">
                                    <canvas id="chart2"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <x-dataloader />
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title font-weight-bold">
                        Historique des toutes les ventes
                    </h4>
                    @if (auth()->user()->user_role === 'provider')
                        <div class="d-flex">
                            @php
                                $d = now()->startOfMonth()->toDateString() . ' to ' . now()->toDateString();
                            @endphp
                            <div class="mr-2">
                                <label class="mb-0" for="dv22">Date</label>
                                <input class="form-control flatpickr2" id="dv22" value="{{ $d }}"
                                    name="date">
                            </div>
                            <div class="">
                                <button class="btn btn-sm btn-outline-primary mt-3 mr-2" data-toggle="modal"
                                    data-target="#mdladd2">
                                    <i class="material-icons md-18">insert_drive_file</i>
                                    Importer
                                </button>
                                <button class="btn btn-sm btn-primary mt-3" data-toggle="modal" data-target="#mdladd">
                                    <i class="material-icons md-18">add_circle_outline</i>
                                    Nouvelle vente
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
                <div class="py-4">
                    <div class="table-responsive">
                        <table id="table" class="table table-striped table-bordered table-hover" style="width:100%">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Date</th>
                                    <th>Localité</th>
                                    <th>Voie</th>
                                    <th class="text-nowrap">Produit</th>
                                    <th>Bon de livraison</th>
                                    <th>Programme de livraison</th>
                                    <th class="text-nowrap">Client</th>
                                    <th class="text-nowrap">LATA</th>
                                    <th class="text-nowrap">L15</th>
                                    <th class="text-nowrap">Densité</th>
                                    <th class="text-nowrap">Factures</th>
                                    <th></th>
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
                                    <label class="mb-0">LAT</label>
                                    <input type="number" min="0" step="0.0001" class="form-control" required
                                        name="lata">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <label class="mb-0">L15</label>
                                    <input type="number" min="0" step="0.0001" class="form-control" required
                                        name="l15">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <label class="mb-0">Densité</label>
                                    <input type="number" min="1" step="0.0001" class="form-control" required
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
                                    <label class="mb-0">LAT</label>
                                    <input type="number" min="0" step="0.0001" class="form-control" required
                                        name="lata">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <label class="mb-0">L15</label>
                                    <input type="number" min="0" step="0.0001" class="form-control" required
                                        name="l15">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <label class="mb-0">Densité</label>
                                    <input type="number" min="1" step="0.0001" class="form-control" required
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
    <div class="modal fade" id="mdladd2" role="dialog">
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
                        <div class="text-center bg-light rounded-lg p-3">
                            <h5>Vous pouvez importer la liste de toutes vos ventes disponibles depuis un fichier Excel en
                                respectant les colonnes et le format des données.</h5>
                        </div>
                        <p class="mt-2">Sélectionnez le fichier excel à importer</p>
                        <div class="mb-2">
                            <input type="file" required multiple class="form-control" name="file">
                        </div>
                        <x-alert />
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
@endsection

@section('script')
    <x-datatable />
    <x-flatpickr />

    <script src="{{ asset('assets/vendor/Chart.4.5.min.js') }}"></script>

    <script>
        flatpickr(".flatpickr", {
            maxDate: "today",
            locale: {
                firstDayOfWeek: 1
            }
        });
        flatpickr(".flatpickr2", {
            mode: "range",
            maxDate: "today",
            locale: {
                firstDayOfWeek: 1
            }
        });

        var dtObj = $('#table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route('sale.index') }}',
                data: function(d) {
                    d.entity_id = '{{ @$entity->id }}';
                    d.date = $('#dv22').val();
                }
            },
            order: [
                [0, "desc"]
            ],
            columnDefs: [{
                targets: 0,
                width: '1%'
            }, {
                targets: 4,
                width: '1%'
            }],
            columns: [{
                    data: 'id',
                    name: 'id',
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
            ]
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

        $('#dv22').change(function() {
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
                        $('#mdladd,#mdledit').modal('hide');
                    }, 3000);
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
                url: '{{ route('sale.index') }}/' + id,
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
                    }, 3000);
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
                    type: 'sale',
                    date: $('#dv22').val(),
                },
                success: function(data) {
                    $('[totalDensity]').html(data.totalDensity);
                    $('[totalLata]').html(data.totalLata);
                    $('[totalL15]').html(data.totalL15);
                    $('[avgDensity]').html(data.avgDensity);

                    chart1.data.labels = data.chart1.labels;
                    chart1.data.datasets[0].data = data.chart1.data;
                    chart1.update();

                    chart2.data.labels = data.chart2.labels;
                    chart2.data.datasets[0].data = data.chart2.data;
                    chart2.update();
                    ldr.hide();
                },
                error: function(xhr, a, b) {

                },
            })
        }

        var chart1 = new Chart($('#chart1')[0].getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: [],
                datasets: [{
                    // label: 'Montant Total d\'achat',
                    data: [],
                    backgroundColor: [
                        '#4e73df',
                        '#1cc88a',
                        '#36b9cc',
                        '#f6c23e',
                    ],
                    hoverOffset: 30,
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                            font: {
                                size: 14
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let value = context.raw;
                                return value.toLocaleString() + ' M³';
                            }
                        }
                    }
                },
                cutout: '70%',
                animation: {
                    animateRotate: true,
                    duration: 1500,
                    easing: 'easeOutBounce'
                }
            }
        });

        var chart2 = new Chart($('#chart2')[0].getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: [],
                datasets: [{
                    // label: 'Montant Total d\'achat',
                    data: [],
                    backgroundColor: [
                        '#4e73df',
                        '#1cc88a',
                        '#36b9cc',
                        '#f6c23e',
                    ],
                    hoverOffset: 30,
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                            font: {
                                size: 14
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let value = context.raw;
                                return 'LATA : ' + value.toLocaleString() + '';
                            }
                        }
                    }
                },
                cutout: '70%',
                animation: {
                    animateRotate: true,
                    duration: 1500,
                    easing: 'easeOutBounce'
                }
            }
        });

        dashboard();
    </script>
@endsection
