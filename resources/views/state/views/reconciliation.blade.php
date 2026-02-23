@extends('layouts.app')
@section('title', 'Réconciliation')
@section('bg-class', 'bg-img-3')
@section('body')
    <div class="container-fluid">
        <div class="d-flex justify-content-between">
            <div class="">
                <h2 class="font-weight-bold">Réconciliation des données : {{ $me->shortname }} - {{ $entity->shortname }}
                </h2>
                <p class="lead small m-0">Rapprochement des données que <b>VOUS</b> avez encodé et celles dont
                    <b>{{ $entity->shortname }}</b> a encodé
                </p>
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
                    <div class="card transparent">
                        <div class="card-header">
                            <div class="row mb-4">
                                <div class="col-xs-12 col-sm-6">
                                    <h4 class="card-title font-weight-bold">
                                        Tableaux de rapprochement : {{ $me->shortname }} - {{ $entity->shortname }}
                                    </h4>
                                </div>
                                <div class="col-sm-6 text-md-right">
                                    <div class="">
                                        <button data-toggle="modal" data-target="#warnmdl" class="btn btn-danger btn-sm "
                                            type="button">
                                            <i class="material-icons align-middle md-18">done_all</i>
                                            Clôturer la cession
                                        </button>
                                        <button id="btnhisto" class="btn btn-primary btn-sm appcol" type="button">
                                            <i class="material-icons his md-18">history</i> Historique
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <form id="ffilter" class="filters-form pull-right" role="form">
                                @php
                                    $d = now()->startOfMonth()->toDateString();
                                    $d2 = now()->toDateString();
                                @endphp
                                <div class="form-group mb-1">
                                    <label for="dv222" class="control-label d-block mb-0">Du</label>
                                    <input type="text" class="form-control flatpickr" id="dv222" name="date1"
                                        value="{{ $d }}" style="min-width:120px;">
                                </div>
                                <div class="form-group mb-1">
                                    <label for="dv22" class="control-label d-block mb-0">Au</label>
                                    <input type="text" class="form-control flatpickr" id="dv22" name="date2"
                                        value="{{ $d2 }}" style="min-width:120px;">
                                </div>
                                <div class="form-group mb-1">
                                    <label for="zone" class="control-label d-block mb-0">Item</label>
                                    <select id="item" name="item" multiple class="form-control"
                                        style="min-width:150px;">
                                        <option selected value="achat">Achats</option>
                                        <option selected value="vente">Ventes</option>
                                        <option selected value="livraison">Livraisons excédentaires</option>
                                        <option selected value="taux">Taux</option>
                                        <option selected value="structure">Structure des prix</option>
                                    </select>
                                </div>
                            </form>
                        </div>
                        <div class="card-body">
                            <x-dataloader />
                            <x-alert />
                            <div class="card-body" style="min-height: 300px">
                                <div data class="row"></div>
                                <div class="my-3 text-danger" errdiv></div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection

@section('modals')
    <div class="modal fade" id="warnmdl" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form class="was-validated" fcl>
                    <div class="modal-body">
                        <div class="mb-4">
                            <h3 class="text-danger text-center">
                                Etes-vous sur de vouloir clôturer la cession de réconciliation de {{ $entity->shortname }}?
                            </h3>
                            <h4>
                                Une fois la cession clôturée :
                            </h4>
                            <ul>
                                {{-- <li>Vous ne pouvez plus annuler cette opération,</li> --}}
                                <li>Il sera impossible d’enregistrer les données (achats, ventes, ... antérieures à la date
                                    de
                                    clôture). </li>
                            </ul>
                            <p class="text-danger mb-3">
                                Pour clôture la cession, entrez la date de clôture puis valider.
                            </p>
                            <div class="form-group mb-1">
                                <input type="hidden" name="entity_id" value="{{ $entity->id }}">
                                <label class="control-label d-block mb-0">Date de clôture</label>
                                <input required type="text" class="form-control flatpickr" name="closed_until">
                            </div>
                        </div>
                        <x-alert />
                    </div>
                    <div class="w-100 d-flex justify-content-center p-3">
                        <div class="">
                            <button type="button" class="btn btn-sm m-2" data-dismiss="modal">
                                <i class="material-icons md-18 mr-1 m-0 p-0">highlight_off</i>
                                Annuler
                            </button>
                        </div>
                        <div class="">
                            <button type="submit" class="btn btn-danger d-flex align-items-center justify-content-center">
                                <x-loader />
                                <span text>
                                    <i class="material-icons md-18 mr-1 m-0 p-0">done_all</i>
                                    OUI CLOTURER LA CESSION
                                </span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="editmdl" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form class="was-validated" fedit>
                    <div class="modal-body">
                        <div class="mb-4">
                            <h3 class="text-center mb-3">
                                Modification de la date de clôture
                            </h3>
                            <div class="form-group mb-1">
                                <input type="hidden" name="id">
                                <label class="control-label d-block mb-0">Date de clôture</label>
                                <input required type="text" class="form-control" name="closed_until" id="datecl">
                            </div>
                        </div>
                        <x-alert />
                    </div>
                    <div class="w-100 d-flex justify-content-center p-3">
                        <div class="">
                            <button type="button" class="btn btn-sm m-2" data-dismiss="modal">
                                <i class="material-icons md-18 mr-1 m-0 p-0">highlight_off</i>
                                Annuler
                            </button>
                        </div>
                        <div class="">
                            <button type="submit"
                                class="btn btn-primary d-flex align-items-center justify-content-center">
                                <x-loader />
                                <span text>
                                    <i class="material-icons md-18 mr-1 m-0 p-0">save</i>
                                    Valider
                                </span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="mdlhistory" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="defaultModalLabel">Historique des clôtures de cession</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form class="was-validated" fadd>
                    <div class="modal-body">
                        <div class="table-responsive">
                            <table id="tablecl" class="table table-striped table-hover text-center text-nowrap"
                                style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Date clôture</th>
                                        <th>Clôturé par</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn" data-dismiss="modal">
                            <i class="material-icons md-18 mr-1 m-0 p-0">highlight_off</i>
                            Fermer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <style>
        .bgred {
            font-weight: bold;
            background: #ccc;
        }

        .bodld {}
    </style>
    <x-datatable />
    <x-flatpickr />
    <x-select />

    <script>
        flatpickr(".flatpickr", {
            maxDate: "today",
            locale: {
                firstDayOfWeek: 1
            }
        });
        var datecl = flatpickr("#datecl", {
            maxDate: "today",
            locale: {
                firstDayOfWeek: 1
            }
        });

        $('#item').multiselect({
            includeSelectAllOption: true,
            nonSelectedText: 'Aucun filtre',
            nSelectedText: 'items sélectionnés',
            allSelectedText: 'Tous les items',
            numberDisplayed: 1,
            selectAllText: 'Tous',
            buttonWidth: '100%',
            buttonClass: 'btn btn-primary'
        });

        var ff = $('#ffilter');

        let timer;
        ff.change(function(e) {
            clearTimeout(timer);
            var e = $(e.target);
            timer = setTimeout(() => {
                dashboard();
            }, 100);
        });

        function dashboard() {
            var ldr = $('[dataloader]');
            ldr.show();
            var rep = $('#rep');

            $.ajax({
                url: '{{ route('reconciliation') }}',
                data: {
                    item: $('#item').val(),
                    entity_id: '{{ @$entity->id }}',
                    date: $('[name="date1"]').val() + ' to ' + $('[name="date2"]').val(),
                },
                success: function(data) {
                    var tabid = [];
                    var html = '';
                    var keys = Object.keys(data);
                    var errors = [];
                    keys.forEach(k => {
                        var _d = data[k];
                        var grid = k == 'structure' ? 8 : 4;
                        var id = 'table_' + Math.random().toString().split('.').join('');
                        tabid.push(id);
                        var h = `
                        <div class='col-md-6 col-lg-${grid} col-sm-12'>
                        <div class='table-responsive mb-5'>
                        <table id="${id}" class="table table-striped table-bordered table-hover text-nowrap" style="width:100%">
                        `;
                        h += '<thead>';
                        var tit = _d.head || [];
                        tit.forEach(ttr => {
                            var tr = '<tr>';
                            ttr.map(th => {
                                var csp = th.colspan;
                                tr +=
                                    `<th ${csp?`colspan=${csp}`:''} class="${th.class??""}">${th.label}</th>`
                            });
                            tr += '</tr>';
                            h += tr;
                        });
                        h += '</thead><tbody>';

                        _d.body?.forEach(row => {
                            h += `<tr>`
                            row.map(e => {
                                h += `<td ${e?.title?'title="'+e?.title+'"':''}  ${e?.href?'href="'+e?.href+'"':''} class="${e.class??""}">${e.label}</td>`
                            })
                            h += '</tr>'
                        });
                        h += '</tbody></table></div></div>';
                        html += h;

                        if (_d.errors) {
                            _d.errors.forEach(el => {
                                errors.push(
                                    `<p class='m-0 font-weight-bold'><i class="material-icons md-18 align-middle">error_outline</i> ${el}</p>`
                                );
                            });
                        }

                    });

                    if (html.length == 0) {
                        html =
                            '<h4 class="text-center text-danger w-100">Veuillez sélectionner un item dans le filtre ci-haut.</h4>';
                    }

                    $('[data]').html(html);
                    $('[data]').css('opacity', 1);
                    $('.tooltip').remove();
                    $('td[title]').tooltip();

                    errors = [...new Set(errors)];
                    $('[errdiv]').html(errors.join(''));

                    rep.hide();

                    tabid.forEach(id => {
                        try {
                            $('#' + id).DataTable({
                                dom: 'Brt',
                                ordering: false,
                                buttons: [{
                                    extend: 'excelHtml5',
                                    title: 'Export Excel',
                                    exportOptions: {
                                        format: {
                                            body: function(data, row, column, node) {
                                                if (!data) return data;
                                                let cleaned = data.toString()
                                                    .replace(
                                                        /\s+/g,
                                                        '');
                                                cleaned = cleaned.replace(',', '.');
                                                let num = Number(cleaned);
                                                return isNaN(num) ? data : num;
                                            }
                                        }
                                    }
                                }, ],
                            });
                        } catch (error) {
                            console.log(id, '--', error);
                        }
                    });
                },
                error: function(xhr, a, b) {
                    var resp = xhr.responseJSON;
                    var mess = resp?.message ?? "Erreur, veuillez réessayer !";
                    rep.html(mess).stop().removeClass().addClass(
                            'p-1 m-0 alert alert-danger')
                        .show();
                    $('[data]').css('opacity', 0.1);
                },
            }).always(function() {
                ldr.hide();
            })
        }

        dashboard();

        $('#btnhisto').click(function() {
            dtObj.ajax.reload(null, false);
            $('#mdlhistory').modal('show');
        });

        var dtObj = $('#tablecl').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route('accountingclosure.index') }}',
                data: function(d) {
                    d.entity_id = '{{ @$entity->id }}';
                }
            },
            columnDefs: [{
                targets: 0,
                width: '1%'
            }, {
                targets: 2,
                width: '1%'
            }, ],
            columns: [{
                    data: 'closed_until',
                    name: 'closed_until',
                },
                {
                    data: 'closed_by',
                    name: 'closed_by',
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
            }, ],
        });

        $(document).on('click', '[bedit]', function() {
            var data = $(this).data('data');
            var mdl = $('#editmdl');
            $('[name="id"]', mdl).val(data.id);
            datecl.setDate(data.closed_until);
            $('.modal.show').modal('hide');
            setTimeout(() => {
                mdl.modal('show');
            }, 300);
        });

        $('[fcl]').on('submit', function(e) {
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
                url: '{{ route('accountingclosure.store') }}',
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
        $('[fedit]').on('submit', function(e) {
            e.preventDefault();
            var form = $(this);
            var btn = $(':submit', form);
            var rep = $('#rep', form);
            var id = $('[name="id"]', form).val();
            var data = form.serialize();
            rep.hide();
            $(':input', form).attr('disabled', true);
            $('[loader]', btn).show();
            $('[text]', btn).hide();

            $.ajax({
                url: '{{ route('accountingclosure.index') }}/' + id,
                method: 'PUT',
                data: data,
                success: function(resp) {
                    var mess = resp?.message ?? "Erreur, veuillez réessayer !";
                    rep.html(mess).stop().removeClass().addClass(
                            'p-1 m-0 alert alert-success')
                        .show();
                    dtObj.ajax.reload(null, false);
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
    </script>
@endsection
