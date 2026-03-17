<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Réinitialisation du mot de passe | {{ config('app.name') }}</title>
    <link type="text/css" href="{{ asset('assets/css/app.css') }}" rel="stylesheet">
    <link type="text/css" href="{{ asset('assets/css/custom.css') }}" rel="stylesheet">
    <style>
        body {
            background-image: url("{{ asset('assets/images/bg.jpg') }}") !important;
            background-size: cover !important;
            background-position: center !important;
            background-repeat: no-repeat !important;
            background-attachment: fixed !important;
            min-height: 100vh !important;
        }

        .transparent {
            background: rgba(255, 255, 255, 0.4) !important;
        }
    </style>
</head>

<body>
    <x-preloader />

    <div class="mdk-drawer-layout js-mdk-drawer-layout" data-fullbleed data-push data-has-scrolling-region>
        <div class="mdk-drawer-layout__content mdk-header-layout__content--scrollable" style="overflow-y: auto;"
            data-simplebar data-simplebar-force-enabled="true">
            <div class="container h-vh d-flex justify-content-center align-items-center flex-column">
                <div class="d-flex justify-content-center align-items-center mb-3">
                    <a href="{{ route('login') }}" class="drawer-brand-circle mr-2">MC</a>
                    <h4 style="cursor: pointer" onclick="location.assign('{{ route('login') }}')"
                        class="ml-2 text-bg mb-0"><strong>{{ config('app.name') }}</strong></h2>
                </div>
                <div class="row w-100 justify-content-center">
                    <div class="col-12 col-md-6">
                        <div class="card mb-3 transparent" style="border-radius: 20px;">
                            <div class="card-body w-100">
                                @if ($message)
                                    <div class="text-center">
                                        <h3 class="text-danger">{{ $message }}</h3>
                                        <div class="mt-5">
                                            <div class="">
                                                <a href="{{ route('login') }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" height="12px"
                                                        viewBox="0 -960 960 960" width="12px" fill="#000">
                                                        <path d="M423-59 2-480l421-421 78 79-342 342 342 342-78 79Z" />
                                                    </svg>
                                                    Retourner à la page de connexion
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @elseif ($canreset)
                                    <h3 class="text-center mb-4">Réinitialisation du mot de passe</h3>
                                    <p class="text-center text-dark my-3">
                                        Entrez votre nouveau mot de passe pour
                                        réinitialiser votre compte.
                                    </p>
                                    <form freset>
                                        <input type="hidden" name="token" value="{{ $token }}">
                                        <div class="form-group">
                                            <label>Nouveau mot de passe</label>
                                            <div class="input-group input-group--inline">
                                                <div class="input-group-addon">
                                                    <i class="material-icons">lock</i>
                                                </div>
                                                <input type="password" class="form-control" name="password"
                                                    placeholder="Nouveau mot de passe" required>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label>Confirmer le mot de passe</label>
                                            <div class="input-group input-group--inline">
                                                <div class="input-group-addon">
                                                    <i class="material-icons">lock</i>
                                                </div>
                                                <input type="password" class="form-control" name="password_confirmation"
                                                    placeholder="Confirmer le mot de passe" required>
                                            </div>
                                        </div>
                                        <x-alert />
                                        <button type="submit"
                                            class="btn btn-primary btn-block d-flex align-items-center justify-content-center">
                                            <x-loader />
                                            <span text>
                                                <i class="material-icons md-18 mr-1 m-0 p-0">lock_open</i>
                                                Valider
                                            </span>
                                        </button>
                                        <div class="mt-3">
                                            <div class="">
                                                <a href="{{ route('login') }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" height="12px"
                                                        viewBox="0 -960 960 960" width="12px" fill="#000">
                                                        <path d="M423-59 2-480l421-421 78 79-342 342 342 342-78 79Z" />
                                                    </svg>
                                                    Retourner à la page de connexion
                                                </a>
                                            </div>
                                        </div>
                                    </form>
                                @else
                                    <form action="#" flog>
                                        <div class="py-3">
                                            <h3 class="text-center">Réinitialisation du mot de passe
                                            </h3>
                                            <p class="text-center text-dark mb-0">Entrez votre adresse email pour
                                                recevoir
                                                les instructions de réinitialisation de votre mot de passe.</p>
                                        </div>
                                        <div class="form-group">
                                            <label>Email</label>
                                            <div class="input-group input-group--inline">
                                                <div class="input-group-addon">
                                                    <i class="material-icons">account_circle</i>
                                                </div>
                                                <input type="text" class="form-control" name="email"
                                                    placeholder="Votre email" required>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="d-flex">
                                                <span class="ml-auto">
                                                    <a href="{{ route('login') }}">
                                                        Retourner à la page de connexion
                                                    </a>
                                                </span>
                                            </div>
                                        </div>
                                        <x-alert />
                                        <button type="submit"
                                            class="btn btn-primary btn-block d-flex align-items-center justify-content-center">
                                            <x-loader />
                                            <span text>
                                                <i class="material-icons md-18 mr-1 m-0 p-0">lock_open</i>
                                                Vérifier
                                            </span>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    </div>

    <script src="{{ asset('assets/js/jq.js') }}"></script>
    <script>
        $(function() {
            'use strict';

            $('[flog]').on('submit', function(e) {
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
                    url: '{{ route('recovery.verify') }}',
                    method: 'POST',
                    data: data,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function(resp) {
                        var mess = resp?.message ?? "Erreur, veuillez réessayer !";
                        rep.html(mess).stop().removeClass().addClass(
                                'p-1 text-center alert alert-success')
                            .show();
                        setTimeout(() => {
                            $(':input', form).attr('disabled', true);
                        }, 800);
                        setTimeout(() => {
                            location.assign('{{ route('login') }}');
                        }, 3000);
                    },
                    error: function(xhr, a, b) {
                        var resp = xhr.responseJSON;
                        var mess = resp?.message ?? "Erreur, veuillez réessayer !";
                        rep.html(mess).stop().removeClass().addClass(
                                'p-1 text-center alert alert-danger')
                            .show();
                        if (419 == xhr.status) {
                            location.reload();
                        }
                    },
                }).always(function() {
                    $(':input', form).attr('disabled', false);
                    $('[loader]', btn).hide();
                    $('[text]', btn).show();
                })
            });

            @if ($canreset)
                $('[freset]').on('submit', function(e) {
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
                        url: '{{ route('recovery.reset') }}',
                        method: 'POST',
                        data: data,
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        success: function(resp) {
                            var mess = resp?.message ?? "Erreur, veuillez réessayer !";
                            rep.html(mess).stop().removeClass().addClass(
                                    'p-1 text-center alert alert-success')
                                .show();
                            localStorage.setItem('_token', resp.token);
                            setTimeout(() => {
                                $(':input', form).attr('disabled', true);
                            }, 800);
                            setTimeout(() => {
                                location.assign('{{ route('login') }}');
                            }, 3000);
                        },
                        error: function(xhr, a, b) {
                            var resp = xhr.responseJSON;
                            var mess = resp?.message ?? "Erreur, veuillez réessayer !";
                            rep.html(mess).stop().removeClass().addClass(
                                    'p-1 text-center alert alert-danger')
                                .show();
                            if (419 == xhr.status) {
                                location.reload();
                            }
                        },
                    }).always(function() {
                        $(':input', form).attr('disabled', false);
                        $('[loader]', btn).hide();
                        $('[text]', btn).show();
                    })
                });
            @endif
        });
    </script>
</body>

</html>
