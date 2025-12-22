<script src="{{ asset('assets/vendor/hightcharts/hightcharts.js') }}"></script>
<script src="{{ asset('assets/vendor/hightcharts/highcharts-3d.js') }}"></script>
<script src="{{ asset('assets/vendor/hightcharts/exporting.js') }}"></script>
{{-- <script src="https://code.highcharts.com/modules/accessibility.js"></script> --}}
<script>
    Highcharts.setOptions({
        lang: {
            contextButtonTitle: 'Menu',
            viewFullscreen: 'Plein écran',
            printChart: 'Imprimer le graphique',
            downloadPNG: 'Télécharger en PNG',
            downloadJPEG: 'Télécharger en JPEG',
            downloadPDF: 'Télécharger en PDF',
            downloadSVG: 'Télécharger en SVG',
            downloadCSV: 'Télécharger en CSV',
            downloadXLS: 'Télécharger en Excel'
        }
    });
</script>
<style>
    /* Bouton tools (context menu) */
    .highcharts-contextbutton {
        background-color: #6ca0d9 !important;
        border: 1px solid #6ca0d9 !important;
        border-radius: 4px;
        fill: #fff !important;
        /* couleur de l’icône (svg) */
        cursor: pointer;
    }

    .highcharts-contextbutton:hover {
        background-color: #5a90cc !important;
        border-color: #5a90cc !important;
        fill: #fff !important;
    }

    .highcharts-contextbutton:active {
        background-color: #4f84bf !important;
        border-color: #4f84bf !important;
        fill: #fff !important;
    }

    /* Menu déroulant */
    .highcharts-menu {
        background-color: #a8c8f0 !important;
        border: 1px solid #9cb9d8 !important;
        border-radius: 4px;
        padding: 4px 0;
    }

    /* Items du menu */
    .highcharts-menu-item {
        color: #1a3b5d !important;
        padding: 6px 12px;
        font-size: 14px;
        cursor: pointer;
    }

    .highcharts-menu-item:hover,
    .highcharts-menu-item:focus {
        background-color: #d4e3f5 !important;
        color: #1a3b5d !important;
    }
</style>
