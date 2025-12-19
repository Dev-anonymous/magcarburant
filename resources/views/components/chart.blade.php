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
