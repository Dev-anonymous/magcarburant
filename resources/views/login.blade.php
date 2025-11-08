<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Connexion | {{ config('app.name') }}</title>
    <link type="text/css" href="{{ asset('assets/css/app.css') }}" rel="stylesheet">
    

</head>

<body>
    <div class="mdk-drawer-layout js-mdk-drawer-layout" data-fullbleed data-push data-has-scrolling-region>
        <div class="mdk-drawer-layout__content mdk-header-layout__content--scrollable" style="overflow-y: auto;"
            data-simplebar data-simplebar-force-enabled="true">
            <div class="container h-vh d-flex justify-content-center align-items-center flex-column">
                <div class="d-flex justify-content-center align-items-center mb-3">
                    <a href="{{ route('login') }}" class="drawer-brand-circle mr-2">MC</a>
                    <h4 class="ml-2 text-bg mb-0"><strong>{{ config('app.name') }}</strong></h2>
                </div>
                <div class="row w-100 justify-content-center">
                    <div class="col-12 col-md-6">
                        <div class="card mb-3">
                            <div class="card-body">
                                <form action="#" flog>
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
                                            <label>Mot de passe</label>
                                            <span class="ml-auto">
                                                <a href="#">
                                                    J'ai oublié mon mot de passe
                                                </a>
                                            </span>
                                        </div>
                                        <div class="input-group input-group--inline">
                                            <div class="input-group-addon">
                                                <i class="material-icons">lock_outline</i>
                                            </div>
                                            <input type="password" class="form-control" name="password"
                                                placeholder="Votre mot de passe" required>
                                        </div>
                                        <div class="custom-control custom-checkbox mt-3">
                                            <input type="checkbox" name="remember" class="custom-control-input" id="customCheck1">
                                            <label class="custom-control-label" for="customCheck1">Rester connecté</label>
                                        </div>
                                    </div>
                                    <x-alert />
                                    <button type="submit"
                                        class="btn btn-primary btn-block d-flex align-items-center justify-content-center">
                                        <x-loader/>
                                        <span text>
                                            <i class="material-icons md-18 mr-1 m-0 p-0">lock_open</i>
                                            Connexion
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
                    url: '{{ route('api.login') }}',
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
                        setInterval(() => {
                            $(':input', form).attr('disabled', true);
                        }, 800);
                        setInterval(() => {
                            location.reload();
                        }, 3000);
                    },
                    error: function(xhr, a, b) {
                        var resp = xhr.responseJSON;
                        var mess = resp?.message ?? "Erreur, veuillez réessayer !";
                        rep.html(mess).stop().removeClass().addClass(
                                'p-1 text-center alert alert-danger')
                            .show();
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
