@extends('layouts.app')
@section('title', 'Fournisseurs')
@section('body')
    <div class="container-fluid">
        <h2>Fournisseurs</h2>
        <div class="d-flex justify-content-between">
            <p class="lead">Gesion des fournisseurs</p>
            <div class="m-2">
                <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#mdladd">
                    Ajouter un fournisseur
                </button>
            </div>
        </div>
        <hr />
        <x-dataloader />
        <div class="row row-projects" data>
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
                            <label class="mb-0" for="validationCustom01">Sigle du founisseur </label>
                            <input type="text" class="form-control" id="validationCustom01"
                                placeholder="Sigle, Ex : ENGEN" name="shortname" required>
                        </div>
                        <div class="mb-2">
                            <label class="mb-0" for="validationCustom01">Nom complet du founisseur </label>
                            <input type="text" class="form-control" id="validationCustom01"
                                placeholder="Ex : ENGEN DRC SA" name="longname" required>
                        </div>
                        <div class="">
                            <label class="mb-0" for="validationCustom01">Logo du founisseur </label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="customFile" name="logo">
                                <label class="custom-file-label" for="customFile">Logo</label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-white" data-dismiss="modal">Fermer</button>
                        <button type="submit" class="btn btn-primary">
                            <x-loader />
                            Valider
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <style>
        .custom-card {
            background: #fff;
            border-radius: 6px;
            overflow: hidden;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
            transition: all 0.3s ease;
        }

        .custom-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.25);
        }

        .card-img img {
            width: auto;
            max-width: 100%;
            height: 100px;
            object-fit: cover;
            border-radius: 4px;
        }

        .card-caption {
            padding: 15px;
        }

        .card-caption h4 {
            margin-top: 0;
            font-size: 16px;
            font-weight: bold;
        }

        .card-caption p {
            margin: 5px 0 0;
            color: #777;
            font-size: 13px;
        }
    </style>
    <script>
        $(function() {

            document.querySelector('.custom-file-input').addEventListener('change', function(e) {
                var fileName = e.target.files[0] ? e.target.files[0].name : 'Choisir un logo';
                e.target.nextElementSibling.textContent = fileName;
            });

            function loaddata() {
                var ldr = $('[dataloader]');
                ldr.show();

                $.ajax({
                    url: '{{ route('entity.index') }}',
                    success: function(data) {
                        var t = '';
                        data.forEach(e => {
                            t += `
                            <div class="col-md-2">
                                <div class="custom-card">
                                    <div class="card-img text-center p-2">
                                        <img src="${e.logo}" alt="Image" class="img-responsive">
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
        })
    </script>
@endsection
