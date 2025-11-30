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
