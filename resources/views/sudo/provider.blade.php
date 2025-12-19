@extends('layouts.app')
@section('title', 'Fournisseurs')
@section('body')
    <div class="container-fluid">
        <h2>Fournisseurs (<span nb></span>)</h2>
        <div class="d-flex justify-content-between">
            <p class="lead">Gesion des fournisseurs</p>
            <div class="m-2">
                <div class="d-flex">
                    <div class="mr-2">
                        <label class="mb-0" style="color: #000">Recherche</label>
                        <input type="text" class="form-control" id="search" placeholder="Ex : Total energies">
                    </div>
                    <div class="">
                        <button class="btn btn-sm btn-primary mt-3" data-toggle="modal" data-target="#mdladd">
                            <i class="material-icons md-24">add_circle_outline</i>
                            Nouveau fournisseur
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <hr />
        <x-dataloader />
        <div class="w-100">
            <h6 id="searchResult" class="text-danger text-center"></h6>
        </div>
        <div class="row row-projects mt-2" data>
            {{-- <div class="col">
                    <i class="material-icons text-link-color md-36">dvr</i>
                    <div class="mb-1">Total Projects</div>
                    <h4 class="mb-0">6</h4>
                </div> --}}
        </div>
    </div>
@endsection

@section('modals')
    <div class="modal fade" id="mdladd" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="defaultModalLabel">Nouveau fournisseur</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form class="was-validated" fadd>
                    <div class="modal-body">
                        <div class="mb-2">
                            <label class="mb-0" for="validationCustom01">Sigle du founisseur (Shortname) </label>
                            <input type="text" class="form-control" id="validationCustom01" placeholder="Ex: ENGEN"
                                name="shortname" required>
                        </div>
                        <div class="mb-2">
                            <label class="mb-0" for="validationCustom01">Nom complet du founisseur </label>
                            <input type="text" class="form-control" id="validationCustom01"
                                placeholder="Ex: ENGEN DRC SA" name="longname" required>
                        </div>
                        <div class="mb-2">
                            <label class="mb-0" for="validationCustom01">Email du founisseur </label>
                            <input type="email" class="form-control" id="validationCustom01"
                                placeholder="Ex: admin@engensa.com" name="email" required>
                        </div>
                        <div class="mb-2">
                            <label class="mb-0" for="validationCustom01">Mot de passe de connexion</label>
                            <input type="text" class="form-control" id="validationCustom01" name="password"
                                value="mdp@123" required>
                        </div>
                        <div class="mb-2">
                            <label class="mb-0" for="validationCustom01">Logo du founisseur (optionnel)</label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="customFile" name="logo">
                                <label class="custom-file-label" for="customFile">Logo</label>
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
    <div class="modal fade" id="mdledit" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="defaultModalLabel">Modification infos fournisseur</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form class="was-validated" fedit>
                    <input type="hidden" name="id">
                    <input type="hidden" name="action" value="update">
                    <div class="modal-body">
                        <div class="mb-2">
                            <label class="mb-0" for="validationCustom01">Sigle du founisseur (Shortname) </label>
                            <input type="text" class="form-control" id="validationCustom01" placeholder="Ex: ENGEN"
                                name="shortname" required>
                        </div>
                        <div class="mb-2">
                            <label class="mb-0" for="validationCustom01">Nom complet du founisseur </label>
                            <input type="text" class="form-control" id="validationCustom01"
                                placeholder="Ex: ENGEN DRC SA" name="longname" required>
                        </div>
                        <div class="mb-2">
                            <label class="mb-0" for="validationCustom01">Email du founisseur </label>
                            <input type="email" class="form-control" id="validationCustom01"
                                placeholder="Ex: admin@engensa.com" name="email" required>
                        </div>
                        <div class="mb-2">
                            <label class="mb-0" for="validationCustom01">Logo du founisseur (optionnel) </label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="customFile" name="logo">
                                <label class="custom-file-label" for="customFile">Logo</label>
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
    <div class="modal fade" id="mdldel" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form class="was-validated" fdel>
                    <input type="hidden" name="id">
                    <div class="modal-body">
                        <div class="mb-2 text-center">
                            <h3 class="text-danger">
                                Voulez-vous supprimer le fournisseur <span shortname></span> et toutes ses informations ?
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
    <script>
        $(function() {

            document.querySelectorAll('.custom-file-input')
                .forEach(element => {
                    element.addEventListener('change', function(e) {
                        var fileName = e.target.files[0] ? e.target.files[0].name : 'Choisir un logo';
                        e.target.nextElementSibling.textContent = fileName;
                    });
                });

            function loaddata() {
                var ldr = $('[dataloader]');
                ldr.show();
                $.ajax({
                    url: '{{ route('entity.index') }}',
                    success: function(data) {
                        $('span[nb]').html(data.length);
                        var t = '';
                        var url = '{{ route('sudo.provider') }}';
                        data.forEach(e => {
                            t += `
                            <div class="col-md-3 col-sm-4">
                                <div class="carte d-block" style="cursor:pointer;" onclick="if (!event.target.closest('.dropdown')) location.assign('${url}?item=${e.shortname}')">
                                    <div class="text-right p-2">
                                        <div class="dropdown">
                                            <a
                                                class="btn btn-primary btn-sm"
                                                href="#"
                                                role="button"
                                                data-toggle="dropdown"
                                                aria-haspopup="true"
                                                aria-expanded="false"
                                            >
                                                <i class="material-icons md-18 align-middle"
                                                >more_vert</i
                                                >
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                <a class="dropdown-item" href="#" bedit data="${JSON.stringify(e).replace(/"/g, '&quot;')}">
                                                    <i class="material-icons md-14 align-middle">edit</i>
                                                    <span class="align-middle">Modifier</span>
                                                </a>
                                                <div class="dropdown-divider"></div>
                                                <a class="dropdown-item text-danger" href="#" bdel data="${JSON.stringify(e).replace(/"/g, '&quot;')}">
                                                    <i class="material-icons md-14 align-middle" >delete</i>
                                                    <span class="align-middle">Supprimer</span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-img text-center p-2">
                                        <img src="${e.logo}" class="img-responsive center-block">
                                    </div>
                                    <div class="card-caption text-center">
                                        <h4>${e.shortname}</h4>
                                        <p>${e.longname}</p>
                                    </div>
                                </div>
                            </div>
                            `;
                        });
                        $('[data]').html(t);

                        $('[bedit]').off('click').click(function() {
                            var data = JSON.parse($(this).attr('data'));
                            var mdl = $('#mdledit');
                            var form = $('[fedit]', mdl);
                            $('[name="id"]', form).val(data.id);
                            $('[name="shortname"]', form).val(data.shortname);
                            $('[name="longname"]', form).val(data.longname);
                            $('[name="email"]', form).val(data.user.email);
                            mdl.modal('show');
                        });
                        $('[bdel]').off('click').click(function() {
                            var data = JSON.parse($(this).attr('data'));
                            var mdl = $('#mdldel');
                            var form = $('[fdel]', mdl);
                            $('[name="id"]', form).val(data.id);
                            $('[shortname]', form).html(data.shortname);
                            mdl.modal('show');
                        });
                    },
                    error: function(xhr) {
                        var resp = xhr.responseJSON;
                        var mess = resp?.message ?? "Erreur, veuillez réessayer !";

                    },
                }).always(function() {
                    ldr.hide();
                })
            }

            loaddata();


            function initsearch() {

            }
            document.getElementById('search').addEventListener('input', function() {
                const q = this.value.toLowerCase().trim();
                let count = 0;

                document.querySelectorAll('.carte').forEach(card => {
                    const container = card.parentElement; // col-md-3
                    const text = card.innerText.toLowerCase();

                    if (text.includes(q)) {
                        container.style.display = '';
                        count++;
                    } else {
                        container.style.display = 'none';
                    }
                });
                const resultSpan = document.getElementById('searchResult');
                if (q === '') {
                    resultSpan.textContent = '';
                } else if (count === 0) {
                    resultSpan.textContent = "Aucun élément trouvé";
                } else if (count === 1) {
                    resultSpan.textContent = "1 élément trouvé";
                } else {
                    resultSpan.textContent = `${count} éléments trouvés`;
                }
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
                    url: '{{ route('entity.store') }}',
                    method: 'POST',
                    data: data,
                    contentType: false,
                    processData: false,
                    success: function(resp) {
                        var mess = resp?.message ?? "Erreur, veuillez réessayer !";
                        rep.html(mess).stop().removeClass().addClass(
                                'p-1 m-0 text-center alert alert-success')
                            .show();
                        loaddata();
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
                                'p-1 m-0 text-center alert alert-danger')
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
                    url: '{{ route('entity.index') }}/' + id,
                    method: 'delete',
                    success: function(resp) {
                        var mess = resp?.message ?? "Erreur, veuillez réessayer !";
                        rep.html(mess).stop().removeClass().addClass(
                                'p-1 m-0 text-center alert alert-success')
                            .show();
                        loaddata();
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
        })
    </script>
@endsection
