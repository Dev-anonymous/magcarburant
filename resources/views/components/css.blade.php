<style>
    ::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }

    ::-webkit-scrollbar-thumb {
        background: rgba(0, 0, 0, 0.1);
        border-radius: 16px;
    }

    ::-webkit-scrollbar-track {
        background: rgba(0, 0, 0, 0.05);
    }

    * {
        -ms-overflow-style: 8px;
        scrollbar-width: thin;
        scrollbar-color: rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.05);
    }
</style>
<link type="text/css" href="{{ asset('assets/css/app.css') }}" rel="stylesheet" />
<link type="text/css" href="{{ asset('assets/css/app.rtl.css') }}" rel="stylesheet" />
<link type="text/css" href="{{ asset('assets/vendor/simplebar.css') }}" rel="stylesheet" />
<link type="text/css" href="{{ asset('assets/css/custom.css') }}" rel="stylesheet" />

<style>
    .filters-form.pull-right {
        float: right;
        margin-top: 6px;
    }

    .filters-form .form-group {
        margin-right: 10px;
        display: inline-block;
        vertical-align: middle;
    }

    @media (max-width: 767px) {
        .filters-form.pull-right {
            float: none !important;
            display: block;
            width: 100%;
            text-align: left;
            margin-top: 10px;
        }

        .filters-form .form-group {
            display: block;
            width: 100%;
            margin-right: 0;
            margin-bottom: 10px;
        }

        .filters-form .form-control,
        .filters-form .btn {
            width: 100% !important;
        }
    }
</style>


<style>
    body {
        background-image: url("{{ asset('assets/images/bg.jpg') }}") !important;
        background-size: cover !important;
        background-position: center !important;
        background-repeat: no-repeat !important;
        background-attachment: fixed !important;
        min-height: 100vh !important;
    }

    nav.navbar {
        background: rgba(255, 255, 255, 0.1) !important;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1) !important;
    }

    div.mdk-drawer__inner {
        background: #CCC;
        background: radial-gradient(circle, rgba(204, 204, 204, 1) 22%, rgba(255, 255, 255, 1) 55%);
    }

    a.nav-link {
        color: #000 !important;
    }

    .transparent {
        background: rgba(255, 255, 255, 0.85) !important;
        border-radius: 10px !important;
    }
</style>
