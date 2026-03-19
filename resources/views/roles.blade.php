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
            <div class="col-12">
                <div class="card transparent">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="card-title font-weight-bold">
                                Liste des rôles
                            </h4>
                            <div class="">
                                <button type="button" class="btn btn-sm btn-primary" id="badd">
                                    <i class="material-icons md-18">add_circle_outline</i> Nouveau role
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="table" class="table table-striped table-hover text-nowrap" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Rôle</th>
                                        <th>Modules</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer">
                        <a href="{{ route('users') }}" class="btn-link">
                            <svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px"
                                fill="#000" style="vertical-align:middle;">
                                <path
                                    d="M292-527q-42-42-42-108t42-108q42-42 108-42t108 42q42 42 42 108t-42 108q-42 42-108 42t-108-42ZM80-164v-94q0-35 17.5-63t50.5-43q72-32 133.5-46T400-424h23q-6 14-9 27.5t-5 32.5h-9q-58 0-113.5 12.5T172-310q-16 8-24 22.5t-8 29.5v34h269q5 18 12 32.5t17 27.5H80Zm587 44-10-66q-17-5-34.5-14.5T593-222l-55 12-25-42 47-44q-2-9-2-25t2-25l-47-44 25-42 55 12q12-12 29.5-21.5T657-456l10-66h54l10 66q17 5 34.5 14.5T795-420l55-12 25 42-47 44q2 9 2 25t-2 25l47 44-25 42-55-12q-12 12-29.5 21.5T731-186l-10 66h-54Zm85-143q22-22 22-58t-22-58q-22-22-58-22t-58 22q-22 22-22 58t22 58q22 22 58 22t58-22ZM464.5-570.5Q490-596 490-635t-25.5-64.5Q439-725 400-725t-64.5 25.5Q310-674 310-635t25.5 64.5Q361-545 400-545t64.5-25.5ZM400-635Zm9 411Z" />
                            </svg>
                            <span style="vertical-align:middle;">Gestion des utilisateurs</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('modals')
    <div class="modal fade" id="mdlAdd" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" mlabel></h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form smartform>
                    <input type="hidden" name="id">
                    <input type="hidden" name="action">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nom du rôle</label>
                            <input required class="form-control" placeholder="Ex. manager" name="name" maxlength="100">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Modules utilisables et permissions pour ce rôle</label>
                            <div class="border rounded p-3 mb-2" style="max-height: 300px; overflow-y: auto;">
                                <div class="row">
                                    @foreach ($permissions as $permission)
                                        <div class="col-12 col-md-3">
                                            <div class="custom-control custom-checkbox mt-1">
                                                <input type="checkbox" name="permissions[]" value="{{ $permission->name }}"
                                                    id="perm2_{{ $permission->id }}" class="custom-control-input">
                                                <label class="custom-control-label" for="perm2_{{ $permission->id }}">
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
                                Voulez-vous supprimer ce rôle ?
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

    <script>
        $(document).on('click', '.checkAll', function() {
            var form = $(this).closest('form');
            $('input[name="permissions[]"]', form).prop('checked', true);
        });

        $(document).on('click', '.uncheckAll', function() {
            var form = $(this).closest('form');
            $('input[name="permissions[]"]', form).prop('checked', false);
        });

        $('#badd').click(function() {
            var mdl = $('#mdlAdd');
            $('[name="action"]', mdl).val('');
            $('[name="id"]', mdl).val('');
            $('[name="name"]', mdl).val('');
            $('[mlabel]').html('Nouveau Rôle');
            mdl.modal('show');
        })

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
                    className: 'text-left'
                },
                {
                    data: 'module',
                    name: 'module',
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

        $('#table tbody').on('click', '[bedit]', function() {
            let log = $(this).attr('data');
            let raw = {};
            try {
                raw = JSON.parse(log);
            } catch (e) {}

            var mdl = $('#mdlAdd');
            $('[name="name"]', mdl).val(raw.name);
            $('[name="id"]', mdl).val(raw.id);
            $('[name="action"]', mdl).val('update');
            $('input[name="permissions[]"]', mdl).val(raw.perms);
            $('[mlabel]').html('Modification du Rôle');

            $('input[name="permissions[]"]', mdl).prop('checked', false);
            raw.perms.forEach(function(permId) {
                $('#perm2_' + permId, mdl).prop('checked', true);
            });
            mdl.modal('show');
        });

        $('#table tbody').on('click', '[bdel]', function() {
            var data = JSON.parse($(this).attr('data'));
            var mdl = $('#mdldel');
            var form = $('[fdel]', mdl);
            $('[name="id"]', form).val(data.id);
            mdl.modal('show');
        });

        $('[smartform]').on('submit', function(e) {
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
                url: '{{ route('role.store') }}',
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
                url: '{{ route('role.index') }}/' + id,
                method: 'delete',
                data: data,
                success: function(resp) {
                    var mess = resp?.message ?? "Erreur, veuillez réessayer !";
                    rep.html(mess).stop().removeClass().addClass(
                            'p-1 m-0 text-center alert alert-success')
                        .show();
                    dtObj.ajax.reload(null, false);
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
    </script>
@endsection
