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
                <form fedit action="#">
                    <div class="modal-body">
                        <input type="hidden" name="id">
                        <div class="mb-3">
                            <label class="form-label">Nom du rôle</label>
                            <input required class="form-control" placeholder="Ex. manager" name="name" maxlength="100">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Modules utilisables pour ce rôle</label>
                            <div class="border rounded p-3 mb-2" style="max-height: 300px; overflow-y: auto;">
                                <div class="row">
                                    @foreach ($permissions as $permission)
                                        <div class="col-12 col-md-3">
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" name="permissions[]"
                                                    value="{{ $permission->name }}" id="perm2_{{ $permission->id }}">
                                                <label class="form-check-label small" for="perm2_{{ $permission->id }}">
                                                    {{ $permission->name }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="mb-2">
                                <button type="button" class="btn btn-sm btn-outline-secondary checkAll">
                                    Tout cocher
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary uncheckAll">
                                    Tout décocher
                                </button>
                            </div>
                        </div>
                        <x-alert />
                    </div>
                </form>
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
    <style>
        #table tbody tr {
            cursor: pointer;
        }
    </style>

    <script>
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

            var mdl = $('#logModal');

            $('[name="name"]', mdl).val(raw.name);
            $('[name="id"]', mdl).val(raw.id);
            $('input[name="permissions[]"]', mdl).val(raw.perms);

            $('input[name="permissions[]"]', mdl).prop('checked', false);
            raw.perms.forEach(function(permId) {
                $('#perm2_' + permId, mdl).prop('checked', true);
            });

            mdl.modal('show');
        });
    </script>
@endsection
