<!DOCTYPE html>
<html lang="fr" dir="ltr">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>@yield('title') | {{ config('app.name') }}</title>
    <x-css />
</head>

<body class="@yield('bg-class', 'bg-img-1')">
    <x-preloader />

    <div class="mdk-drawer-layout js-mdk-drawer-layout" data-fullbleed data-push data-responsive-width="992px"
        data-has-scrolling-region>
        <div class="mdk-drawer-layout__content">
            <div
                class="mdk-header-layout js-mdk-header-layout mdk-header--fixed mdk-header-layout__content--scrollable">
                <div class="mdk-header js-mdk-header" data-fixed>
                    <div class="mdk-header__content">
                        <nav class="navbar navbar-expand-md d-flex-none">
                            <button class="btn btn-link appcol pl-0" type="button" data-toggle="sidebar" sidebarbtn>
                                <i class="material-icons align-middle md-36">short_text</i>
                            </button>
                            <button onclick="location.assign('{{ route('login') }}')"
                                class="btn btn-primary btn-sm appcol" type="button">
                                <i class="material-icons align-middle md-18">home</i>
                                Accueil
                            </button>
                            <div class="page-title m-0"></div>
                            <div class="collapse navbar-collapse" id="mainNavbar">
                                <ul class="navbar-nav ml-auto align-items-center">
                                    @if (Route::is('provider.accounting') || Route::is('provider.analyse'))
                                        <li class="nav-item dropdown nav-language d-flex align-items-center">
                                            <a href="{{ route('provider.analyse') }}" class="nav-link"
                                                aria-expanded="false">
                                                <i class="material-icons md-18 align-middle">trending_up</i>
                                                MAG
                                            </a>
                                            <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown"
                                                aria-expanded="false">
                                                <i class="material-icons md-18 align-middle">settings</i>
                                                Configuration
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                <ul class="list-unstyled">
                                                    <small class="font-italic text-muted ml-2">Gestions des prix</small>
                                                    <li>
                                                        <a href="{{ route('provider.accounting', ['item' => 'pricestr']) }}"
                                                            class="dropdown-item d-flex">
                                                            <svg xmlns="http://www.w3.org/2000/svg" height="18px"
                                                                viewBox="0 -960 960 960" width="18px" fill="#000000">
                                                                <path
                                                                    d="m421-298 283-283-46-45-237 237-120-120-45 45 165 166Zm59 218q-82 0-155-31.5t-127.5-86Q143-252 111.5-325T80-480q0-83 31.5-156t86-127Q252-817 325-848.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 82-31.5 155T763-197.5q-54 54.5-127 86T480-80Zm0-60q142 0 241-99.5T820-480q0-142-99-241t-241-99q-141 0-240.5 99T140-480q0 141 99.5 240.5T480-140Zm0-340Z" />
                                                            </svg>
                                                            Structure des prix
                                                        </a>
                                                    </li>
                                                    <small class="font-italic text-muted ml-2">Gestions des taux</small>
                                                    <li>
                                                        <a href="{{ route('provider.accounting', ['item' => 'rtx']) }}"
                                                            class="dropdown-item d-flex">
                                                            <svg xmlns="http://www.w3.org/2000/svg" height="18px"
                                                                viewBox="0 -960 960 960" width="18px" fill="#000000">
                                                                <path
                                                                    d="m421-298 283-283-46-45-237 237-120-120-45 45 165 166Zm59 218q-82 0-155-31.5t-127.5-86Q143-252 111.5-325T80-480q0-83 31.5-156t86-127Q252-817 325-848.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 82-31.5 155T763-197.5q-54 54.5-127 86T480-80Zm0-60q142 0 241-99.5T820-480q0-142-99-241t-241-99q-141 0-240.5 99T140-480q0 141 99.5 240.5T480-140Zm0-340Z" />
                                                            </svg>
                                                            Taux réels
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="{{ route('provider.accounting', ['item' => 'stx']) }}"
                                                            class="dropdown-item d-flex">
                                                            <svg xmlns="http://www.w3.org/2000/svg" height="18px"
                                                                viewBox="0 -960 960 960" width="18px" fill="#000000">
                                                                <path
                                                                    d="m421-298 283-283-46-45-237 237-120-120-45 45 165 166Zm59 218q-82 0-155-31.5t-127.5-86Q143-252 111.5-325T80-480q0-83 31.5-156t86-127Q252-817 325-848.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 82-31.5 155T763-197.5q-54 54.5-127 86T480-80Zm0-60q142 0 241-99.5T820-480q0-142-99-241t-241-99q-141 0-240.5 99T140-480q0 141 99.5 240.5T480-140Zm0-340Z" />
                                                            </svg>
                                                            Taux structures
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </li>
                                    @endif
                                    <li class="nav-item nav-divider"></li>
                                    <li class="nav-item">
                                        <a href="#"
                                            class="nav-link dropdown-toggle dropdown-clear-caret appcol font-weight-bold"
                                            data-toggle="sidebar" data-target="#user-drawer">
                                            {{ auth()->user()->name }}
                                            <img src="{{ userimg() }}" style="border: 2px solid #ccc"
                                                class="img-fluid rounded-circle ml-1" width="35" />
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </nav>
                    </div>
                </div>

                <div class="mdk-header-layout__content top-navbar mdk-header-layout__content--scrollable h-100">
                    @yield('body')
                </div>
            </div>
        </div>

        <div class="mdk-drawer js-mdk-drawer" id="default-drawer">
            <div class="mdk-drawer__content">
                <div class="mdk-drawer__inner" data-simplebar data-simplebar-force-enabled="true">
                    <nav class="drawer drawer--light">
                        <div class="drawer-spacer">
                            <div class="media align-items-center">
                                <a href="{{ route('login') }}" class="drawer-brand-circle mr-2 text-white">MC</a>
                                <div class="media-body">
                                    <a href="{{ route('login') }}"
                                        class="h6 font-weight-bold m-0 text-link">{{ config('app.name') }}</a>
                                </div>
                            </div>
                        </div>
                        <ul class="drawer-menu" id="mainMenu" data-children=".drawer-submenu">
                            @php
                                $role = auth()->user()->user_role;
                            @endphp
                            @if ($role === 'sudo')
                                <li class="drawer-menu-item @if (Route::is('sudo.home')) active @endif">
                                    <a href="{{ route('sudo.home') }}">
                                        <i class="material-icons">dashboard</i>
                                        <span class="drawer-menu-text"> Dashboard</span>
                                    </a>
                                </li>
                                <li class="drawer-menu-item drawer-submenu">
                                    <a data-toggle="collapse" data-parent="#mainMenu" href="#"
                                        data-target="#lizone1" aria-controls="lizone1" aria-expanded="false"
                                        class="collapsed">
                                        <i class="material-icons">domain</i>
                                        <span class="drawer-menu-text"> Gestion d'utilisateurs</span>
                                    </a>
                                    <ul class="collapse" id="lizone1">
                                        <li class="drawer-menu-item @if (Route::is('sudo.provider')) active @endif">
                                            <a href="{{ route('sudo.provider') }}">Utilisateurs</a>
                                        </li>
                                    </ul>
                                </li>
                            @endif
                            @if ($role === 'petrolier')
                                {{-- <li class="drawer-menu-item @if (Route::is('provider.home')) active @endif">
                                    <a href="{{ route('provider.home') }}">
                                        <i class="material-icons">dashboard</i>
                                        <span class="drawer-menu-text"> Dashboard</span>
                                    </a>
                                </li>
                                <li class="drawer-menu-item @if (Route::is('provider.home')) active @endif">
                                    <a href="{{ route('provider.apps') }}">
                                        <i class="material-icons">apps</i>
                                        <span class="drawer-menu-text"> Applications</span>
                                    </a>
                                </li> --}}
                            @endif
                            <li class="drawer-menu-item drawer-fixed-bottom">
                                <a href="#" class="nav-link dropdown-toggle dropdown-clear-caret appcol"
                                    data-toggle="sidebar" data-target="#user-drawer">
                                    <img src="{{ userimg() }}" class="img-fluid rounded-circle ml-1"
                                        width="35" />
                                    Profil
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>

        <div class="mdk-drawer js-mdk-drawer" id="user-drawer" data-position="right" data-align="end">
            <div class="mdk-drawer__content">
                <div class="mdk-drawer__inner" data-simplebar data-simplebar-force-enabled="true">
                    <nav class="drawer drawer--light">
                        <div class="drawer-spacer drawer-spacer-border">
                            <div class="media align-items-center">
                                <img src="{{ userimg() }}" class="img-fluid rounded-circle mr-2" width="35"
                                    alt="" />
                                <div class="media-body">
                                    <a class="h5 m-0">{{ auth()->user()->name }}</a>
                                    <div>{{ ucfirst(auth()->user()->user_role) }}</div>
                                </div>
                            </div>
                        </div>
                        <ul class="drawer-menu" id="userMenu" data-children=".drawer-submenu">
                            <li class="drawer-menu-item">
                                <a href="#" logout onclick="event.preventDefault();">
                                    <i class="material-icons">exit_to_app</i>
                                    <span class="drawer-menu-text"> Se déconnecter</span>
                                    <x-loader color="ok" />
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <style>
        .mdk-drawer {
            position: fixed !important;
        }

        .drawer-menu {
            padding-bottom: 50px;
        }

        .drawer-fixed-bottom {
            position: absolute !important;
            bottom: 20px;
            width: 100%;
            background: #fff;
        }

        .drawer-fixed-bottom a {
            display: flex;
            align-items: center;
            padding: 12px 20px;
        }
    </style>
    @yield('modals')
    <x-js />
    @yield('script')

</body>

</html>
