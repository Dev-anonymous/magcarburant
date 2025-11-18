@extends('layouts.app')
@section('title', 'Achats')
@section('body')
    <div class="container-fluid">
        <div class="d-flex justify-content-between">
            <div class="">
                <h2 class="font-weight-bold">Achats </h2>
                <p class="lead small m-0">Gestion des achats (entrées) </p>
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
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title font-weight-bold">
                            Historique des tous les achats
                        </h4>
                        @if (auth()->user()->user_role === 'provider')
                            <div class="">
                                <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#mdladd">
                                    <i class="material-icons md-24">add_circle_outline</i>
                                    Nouvel achat
                                </button>
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
                                        <th class="text-nowrap">Produit</th>
                                        <th>Fournisseur</th>
                                        <th class="text-nowrap">N° Facture</th>
                                        <th class="text-nowrap">Prix Unitaire</th>
                                        <th class="text-nowrap">Qte TM</th>
                                        <th class="text-nowrap">Qte M3</th>
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
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="defaultModalLabel">Nouvel achat</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form class="was-validated" fadd>
                    <div class="modal-body">
                        <div class="mb-2">
                            <label class="mb-0" for="dv1">Date de l'achat</label>
                            <input class="form-control flatpickr" id="dv1" required name="date">
                        </div>
                        <div class="mb-2">
                            <label class="mb-0">Produit</label>
                            <select name="product" id="" class="form-control" required>
                                <option value="">Sélectionnez un produit</option>
                                @foreach (mainfuels() as $e)
                                    <option>{{ $e }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-2">
                            <label class="mb-0">Fournisseur</label>
                            <input class="form-control" required name="provider">
                        </div>
                        <div class="mb-2">
                            <label class="mb-0">N° Facture</label>
                            <input class="form-control" required name="billnumber">
                        </div>
                        <div class="mb-2">
                            <label class="mb-0">Prix Unitaire (en USD)</label>
                            <input type="number" min="1" step="0.0001" class="form-control" required
                                name="unitprice">
                        </div>
                        <div class="mb-2">
                            <label class="mb-0">Qantité TM</label>
                            <input type="number" min="1" step="0.0001" class="form-control" required
                                name="qtytm">
                        </div>
                        <div class="mb-2">
                            <label class="mb-0">Qantité M3</label>
                            <input type="number" min="1" step="0.0001" class="form-control" required
                                name="qtym3">
                        </div>
                        <div class="mb-2">
                            <label class="mb-0">Densité</label>
                            <input type="number" min="1" step="0.0001" class="form-control" required
                                name="density">
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
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="defaultModalLabel">Modification structure</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form class="was-validated" fedit>
                    <input type="hidden" name="id">
                    <input type="hidden" name="action" value="update">
                    <div class="modal-body">
                        <div class="mb-2">
                            <label class="mb-0" for="dv2">Date de l'achat</label>
                            <input class="form-control flatpickr2" id="dv2" required name="date">
                        </div>
                        <div class="mb-2">
                            <label class="mb-0">Produit</label>
                            <select name="product" id="" class="form-control" required>
                                @foreach (mainfuels() as $e)
                                    <option>{{ $e }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-2">
                            <label class="mb-0">Fournisseur</label>
                            <input class="form-control" required name="provider">
                        </div>
                        <div class="mb-2">
                            <label class="mb-0">N° Facture</label>
                            <input class="form-control" required name="billnumber">
                        </div>
                        <div class="mb-2">
                            <label class="mb-0">Prix Unitaire (en USD)</label>
                            <input type="number" min="1" step="0.0001" class="form-control" required
                                name="unitprice">
                        </div>
                        <div class="mb-2">
                            <label class="mb-0">Qantité TM</label>
                            <input type="number" min="1" step="0.0001" class="form-control" required
                                name="qtytm">
                        </div>
                        <div class="mb-2">
                            <label class="mb-0">Qantité M3</label>
                            <input type="number" min="1" step="0.0001" class="form-control" required
                                name="qtym3">
                        </div>
                        <div class="mb-2">
                            <label class="mb-0">Densité</label>
                            <input type="number" min="1" step="0.0001" class="form-control" required
                                name="density">
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
                                Voulez-vous supprimer la structure <span shortname></span> et toutes ses informations ?
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
@endsection

@section('script')
    <x-datatable />
    <x-flatpickr />

    <script>
        flatpickr(".flatpickr", {
            maxDate: "today",
            locale: {
                firstDayOfWeek: 1
            }
        });

        var dtObj = $('#table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route('purchase.index') }}',
                data: function(d) {
                    d.entity_id = '{{ @$entity->id }}'
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
            })
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
    </script>
@endsection
