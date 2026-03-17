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
                            <div class="card-body">
                                <form action="#" flog>
                                    <div class="py-3">
                                        <h3 class="text-center">Réinitialisation du mot de passe</h3>
                                        <p class="text-center text-dark mb-0">Entrez votre adresse email pour recevoir
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
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{-- <div class="d-flex justify-content-center">
                    <span class="mr-2">Don't have an account?</span>
                    <a href="signup.html">Sign Up</a>
                </div> --}}
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
                            // $(':input', form).attr('disabled', true);
                        }, 800);
                        // setTimeout(() => {
                        //     location.reload();
                        // }, 1000);
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
        });
    </script>
</body>

</html>
