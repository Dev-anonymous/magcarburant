<script src="{{ asset('assets/vendor/select2.full.min.js') }}"></script>
<script src="{{ asset('assets/vendor/bsmultiselect/bs-multiselect.js') }}"></script>

<link rel="stylesheet" href="{{ asset('assets/vendor/bsmultiselect/bs-mutliselect.css') }}">

<script>
    $('.select2').select2();
    $('.select22').select2({
        minimumResultsForSearch: Infinity
    });
</script>
