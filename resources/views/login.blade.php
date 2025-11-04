<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Connexion | {{ config('app.name') }}</title>
    <link type="text/css" href="{{ asset('assets/css/app.css') }}" rel="stylesheet">
    <style>
        .dots-loader span {
            display: inline-block;
            width: 8px;
            height: 8px;
            margin: 0 2px;
            background-color: #fff;
            border-radius: 50%;
            animation: bounce 1.2s infinite ease-in-out both;
        }

        .dots-loader span:nth-child(1) {
            animation-delay: -0.32s;
        }

        .dots-loader span:nth-child(2) {
            animation-delay: -0.16s;
        }

        @keyframes bounce {

            0%,
            80%,
            100% {
                transform: scale(0);
            }

            40% {
                transform: scale(1);
            }
        }

        .material-icons {
            vertical-align: middle !important;
        }
    </style>

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
                    <div class="card card-login mb-3">
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
                                </div>
                                <div id="rep" class="alert p-2" style="display: none"></div>
                                <button type="submit"
                                    class="btn btn-primary btn-block d-flex align-items-center justify-content-center">
                                    <span loader class="dots-loader" style="display:none;">
                                        <span></span><span></span><span></span>
                                    </span>
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
                    url: '/api/login',
                    method: 'POST',
                    data: data,
                    success: function(response) {

                    },
                    error: function(xhr) {

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
