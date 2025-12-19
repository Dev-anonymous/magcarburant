@extends('layouts.app')
@section('title', 'Taux Réels')
@section('body')
    <div class="container">
        <div class="d-flex justify-content-between">
            <div class="">
                <h2 class="font-weight-bold">Taux Réels | {{ $entity->shortname }} </h2>
                <p class="lead small m-0">Gestion des taux réels</p>
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
                <div class="card transparent">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title font-weight-bold">
                            Historique des taux
                        </h4>
                        @if (auth()->user()->user_role === 'provider')
                            <div class="">
                                <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#mdladd">
                                    <i class="material-icons md-24">add_circle_outline</i>
                                    Nouveau taux
                                </button>
                            </div>
                        @endif
                    </div>
                    <div class="py-4">
                        <div class="table-responsive">
                            <table id="table" class="table table-striped table-hover" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Taux</th>
                                        <th>Date validité du</th>
                                        <th>Date validité au</th>
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
@endsection
@section('modals')
    <div class="modal fade" id="mdladd" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="defaultModalLabel">Nouveau taux</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form class="was-validated" fadd>
                    <input type="hidden" name="type" value="RÉEL">
                    <div class="modal-body">
                        <div class="mb-2">
                            <label class="mb-0" for="dv1">Date validité du </label>
                            <input type="text" class="form-control flatpickr" id="dv1" required name="from">
                        </div>
                        <div class="mb-2">
                            <div class="input-group">
                                <div class="input-group-append">
                                    <div class="input-group-text">1 CDF = </div>
                                </div>
                                <input type="number" min="0.00000001" step="0.00000001" name="cdf_usd" class="form-control"
                                    required>
                                <div class="input-group-preppend">
                                    <div class="input-group-text"> USD</div>
                                </div>
                            </div>
                        </div>
                        <div class="mb-2">
                            <div class="input-group">
                                <div class="input-group-append">
                                    <div class="input-group-text">1 USD = </div>
                                </div>
                                <input type="number" min="0.00000001" step="0.00000001" name="usd_cdf" class="form-control"
                                    required>
                                <div class="input-group-preppend">
                                    <div class="input-group-text"> CDF</div>
                                </div>
                            </div>
                        </div>
                        <p class="m-0 text-danger mb-3">
                            Si vous créez un nouveau taux sans pour autant spécifier la date de clôture du
                            taux actuel, la date clôture du taux actuel sera la date début du
                            taux que vous créez – 1.
                        </p>
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
                    <h5 class="modal-title" id="defaultModalLabel">Modification taux</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form class="was-validated" fedit>
                    <input type="hidden" name="id">
                    <input type="hidden" name="action" value="update">
                    <div class="modal-body">
                        <div class="mb-2">
                            <label class="mb-0" for="dv1">Date validité du </label>
                            <input type="text" class="form-control" id="dv1" required name="from">
                        </div>
                        <div class="mb-2">
                            <label class="mb-0">Date validité au (optionnel)</label>
                            <input type="text" class="form-control" name="to">
                        </div>
                        <p class="m-0 text-danger">
                            Une fois la <b>"date validité au"</b> renseignée, vous ne pouvez plus modifier les taux,
                            rassurez-vous donc de configurer les taux avant de renseigner la date de clôture
                        </p>
                        <div class="mb-2 mt-2">
                            <div class="input-group">
                                <div class="input-group-append">
                                    <div class="input-group-text">1 CDF = </div>
                                </div>
                                <input type="number" min="0.00000001" step="0.00000001" name="cdf_usd"
                                    class="form-control" required>
                                <div class="input-group-preppend">
                                    <div class="input-group-text"> USD</div>
                                </div>
                            </div>
                        </div>
                        <div class="mb-2">
                            <div class="input-group">
                                <div class="input-group-append">
                                    <div class="input-group-text">1 USD = </div>
                                </div>
                                <input type="number" min="0.00000001" step="0.00000001" name="usd_cdf"
                                    class="form-control" required>
                                <div class="input-group-preppend">
                                    <div class="input-group-text"> CDF</div>
                                </div>
                            </div>
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
                url: '{{ route('rate.index') }}',
                data: function(d) {
                    d.entity_id = '{{ $entity->id }}';
                    d.type = 'RÉEL';
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
                    data: 'rate',
                    name: 'rate',
                    className: 'text-nowrap font-weight-bold',
                    orderable: false,
                    searchable: false,
                },
                {
                    data: 'from',
                    name: 'from'
                },
                {
                    data: 'to',
                    name: 'to'
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
                    columns: ':not(.no-export)'
                }
            }, ],
        }).on('draw.dt', function(e, settings, data, xhr) {
            $('[bedit]').off('click').click(function() {
                var data = JSON.parse($(this).attr('data'));
                var mdl = $('#mdledit');
                var form = $('[fedit]', mdl);
                $('[name="id"]', form).val(data.id);
                $('[name="from"]', form).val(data.from);
                $('[name="to"]', form).val(data.to);
                $('[name="cdf_usd"]', form).val(data.cdf_usd);
                $('[name="usd_cdf"]', form).val(data.usd_cdf);
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
            var data = form.serialize();
            rep.hide();
            $(':input', form).attr('disabled', true);
            $('[loader]', btn).show();
            $('[text]', btn).hide();

            $.ajax({
                url: '{{ route('rate.store') }}',
                method: 'POST',
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
    </script>
@endsection
