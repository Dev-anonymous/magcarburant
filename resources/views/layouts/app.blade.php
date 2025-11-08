<!DOCTYPE html>
<html lang="fr" dir="ltr">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>@yield('title') | {{ config('app.name') }}</title>
    <x-css />
</head>

<body>
    <div class="mdk-drawer-layout js-mdk-drawer-layout" data-fullbleed data-push data-responsive-width="992px"
        data-has-scrolling-region>
        <div class="mdk-drawer-layout__content">
            <div
                class="mdk-header-layout js-mdk-header-layout mdk-header--fixed mdk-header-layout__content--scrollable">
                <div class="mdk-header js-mdk-header bg-primary" data-fixed>
                    <div class="mdk-header__content">
                        <nav class="navbar navbar-expand-md d-flex-none bg-white">
                            <button class="btn btn-link appcol pl-0" type="button" data-toggle="sidebar">
                                <i class="material-icons align-middle md-36">short_text</i>
                            </button>
                            <div class="page-title m-0"></div>
                            <div class="collapse navbar-collapse" id="mainNavbar">
                                <ul class="navbar-nav ml-auto align-items-center">
                                    <li class="nav-item nav-divider"></li>
                                    <li class="nav-item">
                                        <a href="#"
                                            class="nav-link dropdown-toggle dropdown-clear-caret appcol font-weight-bold"
                                            data-toggle="sidebar" data-target="#user-drawer">
                                            {{ auth()->user()->name }}
                                            <img src="{{ userimg() }}" class="img-fluid rounded-circle ml-1"
                                                width="35" />
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
                    <div class="h-100">
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
                            <div class="py-2 drawer-heading">Menu</div>
                            <ul class="drawer-menu" id="mainMenu" data-children=".drawer-submenu">
                                @php
                                    $role = auth()->user()->user_role;
                                @endphp
                                @if ($role === 'sudo')
                                    <li class="drawer-menu-item">
                                        <a href="{{ route('sudo.home') }}">
                                            <i class="material-icons">dashboard</i>
                                            <span class="drawer-menu-text"> Dashboard</span>
                                        </a>
                                    </li>
                                    <li class="drawer-menu-item drawer-submenu">
                                        <a data-toggle="collapse" data-parent="#mainMenu" href="#"
                                            data-target="#uiComponentsMenu" aria-controls="uiComponentsMenu"
                                            aria-expanded="true">
                                            <i class="material-icons">domain</i>
                                            <span class="drawer-menu-text"> Gestion fournisseurs</span>
                                        </a>
                                        <ul class="collapse show" id="uiComponentsMenu">
                                            <li class="drawer-menu-item active">
                                                <a href="{{ route('sudo.provider') }}">Fournisseurs</a>
                                            </li>
                                        </ul>
                                    </li>
                                @endif
                            </ul>
                        </nav>
                    </div>
                    <a href="#" class="nav-link dropdown-toggle dropdown-clear-caret appcol" data-toggle="sidebar"
                        data-target="#user-drawer">
                        <img src="{{ userimg() }}" class="img-fluid rounded-circle ml-1" width="35" />
                        Profile
                    </a>
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
    @yield('modals')
    <x-js />
    @yield('script')
</body>

</html>
