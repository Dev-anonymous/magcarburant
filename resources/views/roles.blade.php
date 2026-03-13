@extends('layouts.app')
@section('title', 'Rôles et permissions')
@section('bg-class', 'bg-img-3')
@section('body')
    <div class="container">
        <div class="d-flex justify-content-between">
            <div class="">
                <h2 class="font-weight-bold">Rôles et permissions</h2>
                <p class="lead small m-0">Gestions des rôles et permissions sur les modules</p>
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

        <div class="row">
            <div class="col-12 p-0">
                <div class="card transparent">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-xs-12 col-sm-6">
                                <h4 class="card-title font-weight-bold">

                                </h4>
                            </div>

                        </div>
                        <form id="ffilter" class="filters-form pull-right" role="form">

                            <div class="form-group mb-1">
                                <button type="button" class="btn btn-sm btn-primary mt-3" data-toggle="modal"
                                    data-target="#mdlChose">
                                    <i class="material-icons md-18">add_circle_outline</i> Nouveau role
                                </button>
                            </div>
                        </form>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="table" class="table table-striped table-hover text-center text-nowrap"
                                style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Rôle</th>
                                        <th>Permission</th>
                                        <th>Modules</th>
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
    <div class="modal fade" id="logModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Détails de l'audit #<span logid></span></h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <!-- contenu injecté par JS -->
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
    <x-flatpickr />
    <x-select />
    <x-datatable />
    <style>
        #table tr {
            cursor: pointer;
        }
    </style>

    <script>
        flatpickr(".flatpickr", {
            maxDate: "today",
            locale: {
                firstDayOfWeek: 1
            }
        });
        var ff = $('#ffilter');

        let timer;
        ff.change(function(e) {
            clearTimeout(timer);
            var e = $(e.target);
            timer = setTimeout(() => {
                dtObj.ajax.reload(null, false);
            }, 100);
        });

        $('[name="event[]"]').multiselect({
            includeSelectAllOption: true,
            nonSelectedText: 'Aucun filtre',
            nSelectedText: 'Evènements sélectionnés',
            allSelectedText: 'Tous les Evènements',
            numberDisplayed: 1, // affiche 1 élément puis "n zones sélectionnées"
            selectAllText: 'Tous',
            buttonWidth: '100%',
            buttonClass: 'btn btn-primary'
        });

        var dtObj = $('#table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route('role.index') }}',
                data: function(d) {

                }
            },
            order: [
                [0, "desc"]
            ],
            columnDefs: [{
                targets: 0,
                width: '1%'
            }, {
                targets: 2,
                width: '1%'
            }],
            columns: [{
                    data: 'name',
                    name: 'name',
                },
                {
                    data: 'permission',
                    name: 'permission',
                },
                {
                    data: 'module',
                    name: 'module',
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
                            let cleaned = data.toString().replace(/\s+/g,
                                '');
                            cleaned = cleaned.replace(',', '.');
                            let num = Number(cleaned);
                            return isNaN(num) ? data : num;
                        }
                    }
                }
            }, ],
        }).on('draw.dt', function(e, settings, data, xhr) {

        });

        $('#table tbody').on('click', 'tr', function() {
            let log = dtObj.row(this).data();

            let raw = {};
            try {
                raw = JSON.parse(log.raw_data);
            } catch (e) {}

            let oldValues = {};
            let newValues = {};
            try {
                oldValues = JSON.parse(raw.old_values) || {};
            } catch (e) {}
            try {
                newValues = JSON.parse(raw.new_values) || {};
            } catch (e) {}

            let oldHtml = Object.keys(oldValues).length ?
                JSON.stringify(oldValues, null, 2) :
                '<span class="badge badge-secondary">Vide</span>';

            let newHtml = Object.keys(newValues).length ?
                JSON.stringify(newValues, null, 2) :
                '<span class="badge badge-secondary">Vide</span>';

            $('[logid]').html(raw.id);
            let html = `
                <p class='m-0'>
                    <strong><i class="material-icons align-middle md-18">event</i> Description :</strong>
                    <b>${raw.title}</b>
                </p>
                <p class='m-0'>
                    <strong><i class="material-icons align-middle md-18">event</i> Événement :</strong>
                    <b>${raw.event}</b>
                </p>
                <p class='m-0'>
                    <strong><i class="material-icons align-middle md-18">person</i> De :</strong>
                    ${raw.username ?? ''}
                </p>
                <p class='m-0'>
                    <strong><i class="material-icons align-middle md-18">storage</i> Sur :</strong>
                    ${raw.entity ?? '-'}
                </p>
                <p class='m-0'>
                    <strong><i class="material-icons align-middle md-18">schedule</i> Le :</strong>
                    ${raw.date}
                </p>
                <hr/>
                <div class="row">
                    <div class="col-md-6">
                        <h6 style='color:#D70040' class="font-weight-bold">
                            <i class="material-icons align-middle md-18">history</i> Anciennes valeurs
                        </h6>
                        <pre class="bg-light p-2 rounded">${oldHtml}</pre>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-success font-weight-bold">
                            <i class="material-icons align-middle md-18">update</i> Nouvelles valeurs
                        </h6>
                        <pre class="bg-light p-2 rounded">${newHtml}</pre>
                    </div>
                </div>
                <div>
                    <p class='m-0 mt-5'>
                        <strong><i class="material-icons align-middle md-18">language</i> Navigateur :</strong>
                        ${raw.user_agent}
                    </p>
                    <p class='m-0'>
                        <strong><i class="material-icons align-middle md-18">public</i> IP :</strong>
                        ${raw.ip_address}
                    </p>
                </div>
            `;

            $('#logModal .modal-body').html(html);
            $('#logModal').modal('show');
        });
    </script>
@endsection
