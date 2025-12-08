<link type="text/css" href="{{ asset('assets/css/vendor-bootstrap-datatables.css') }}" rel="stylesheet">
<script src="{{ asset('assets/vendor/datatable/jquery.dataTables.js') }}"></script>
<script src="{{ asset('assets/vendor/datatable/dataTables.bootstrap4.js') }}"></script>

<link rel="stylesheet" href="{{ asset('assets/vendor/datatable/buttons.dataTables.min.css') }}">
<script src="{{ asset('assets/vendor/datatable/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('assets/vendor/datatable/jszip.min.js') }}"></script>
<script src="{{ asset('assets/vendor/datatable/buttons.html5.min.js') }}"></script>
<script src="{{ asset('assets/vendor/datatable/pdfmake.min.js') }}"></script>
<script src="{{ asset('assets/vendor/datatable/vfs_fonts.js') }}"></script>
<script src="{{ asset('assets/vendor/datatable/buttons.print.min.js') }}"></script>

<link rel="stylesheet" href="{{ asset('assets/vendor/datatable/fixedcolumn.css') }}">
<script src="{{ asset('assets/vendor/datatable/fixedcolumn.js') }}"></script>

<link rel="stylesheet" href="{{ asset('assets/vendor/datatable/responsive.css') }}">
<script src="{{ asset('assets/vendor/datatable/responsive.js') }}"></script>

<script src="{{ asset('assets/vendor/datatable/colvis.js') }}"></script>

<script>
    $.extend(true, $.fn.dataTable.defaults, {
        language: {
            // decimal: ",",
            thousands: " ",
            processing: "Traitement en cours...",
            search: "Rechercher:",
            lengthMenu: "Afficher _MENU_ lignes",
            info: "Affichage de _START_ à _END_ sur _TOTAL_ lignes",
            infoEmpty: "Aucune ligne à afficher",
            infoFiltered: "(filtré parmi _MAX_ lignes au total)",
            loadingRecords: "Chargement...",
            zeroRecords: "Aucune ligne correspondant trouvée",
            emptyTable: "Aucune donnée disponible",
            paginate: {
                first: "Premier",
                previous: "Précédent",
                next: "Suivant",
                last: "Dernier"
            },
            aria: {
                sortAscending: ": activer pour trier la colonne par ordre croissant",
                sortDescending: ": activer pour trier la colonne par ordre décroissant"
            }
        },
        pageLength: 25,
        lengthMenu: [
            [25, 50, 100, 200, 500, 1000],
            [25, 50, 100, 200, 500, 1000]
        ],
    });
</script>
