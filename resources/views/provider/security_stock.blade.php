@extends('layouts.app')
@section('title', 'Stock de sécurité')
@section('bg-class', 'bg-img-3')
@section('body')
    <div class="container">
        <div class="d-flex justify-content-between">
            <div class="">
                <h2 class="font-weight-bold">Stock de sécurité collecté reversé | {{ $entity->shortname }} </h2>
                <p class="lead small m-0">Configuration des stocks de sécurité mensuels pour {{ $entity->shortname }} </h2>
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
        <div class="container">
            <div class="row">
                <div class="col-12 p-0">
                    <div class="card transparent">
                        <div class="card-header">
                            <form id="ffilter" class="filters-form pull-right" role="form">
                                <div class="form-group mb-1">
                                    <label for="fuel" class="control-label d-block mb-0">Année</label>
                                    <select id="year" class="form-control select2" style="min-width:150px;">
                                        @foreach ($years as $e)
                                            <option>{{ $e }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </form>
                        </div>
                        <div class="py-4">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="table" class="table table-striped table-hover text-nowrap"
                                        style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>Mois</th>
                                                <th class="text-center">Stock de sécurité collecté reversé (USD)</th>
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

    </div>
@endsection
@section('modals')
    <div class="modal fade" id="mdledit" role="dialog">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="defaultModalLabel">Configuration du Stock de sécurité collecté reversé</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form class="was-validated" fedit>
                    <input type="hidden" name="id">
                    <div class="modal-body">
                        <div class="py-3">
                            <h4 month></h4>
                        </div>
                        <div class="mb-2">
                            <label class="mb-0">Montant Stock de sécurité collecté reversé</label>
                            <input type="number" min="0" step="0.001" class="form-control" required
                                name="amount">
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
    <x-select />

    <script>
        var ff = $('#ffilter');

        let timer;
        ff.change(function(e) {
            clearTimeout(timer);
            var e = $(e.target);
            timer = setTimeout(() => {
                dtObj.ajax.reload(null, false);
            }, 100);
        });

        $(document).on('click', '.editdata', function() {
            var data = $(this).data('data');
            var mdl = $('#mdledit');
            $('[month]', mdl).html('Mois : ' + data.monthname);
            $('[name="amount"]', mdl).val(data.amount);
            $('[name="id"]', mdl).val(data.id);
            mdl.modal('show');
        });

        $('[fedit]').on('submit', function(e) {
            e.preventDefault();
            var form = $(this);
            var btn = $(':submit', form);
            var rep = $('#rep', form);
            var data = form.serialize();
            rep.hide();
            $(':input', form).attr('disabled', true);
            $('[loader]', btn).show();
            $('[text]', btn).hide();
            var id = $('[name=id]', form).val();

            $.ajax({
                url: '{{ route('securitystock.index') }}/' + id,
                method: 'put',
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

        var dtObj = $('#table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route('securitystock.index') }}',
                data: function(d) {
                    d.entity_id = '{{ @$entity->id }}';
                    d.year = $('#year').val();
                }
            },
            order: [
                [0, "asc"]
            ],
            columnDefs: [{
                targets: 0,
                width: '1%'
            }, {
                targets: 2,
                width: '1%'
            }],
            columns: [{
                    data: 'month',
                    name: 'month',
                },
                {
                    data: 'amount',
                    name: 'amount',
                    className: 'text-center'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false,
                },
            ],
            dom: 'Brt',
            ordering: false,
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

        }).on('draw.dt', function(e, settings, data, xhr) {});
    </script>
@endsection
