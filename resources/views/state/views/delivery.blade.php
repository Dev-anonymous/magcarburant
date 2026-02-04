@extends('layouts.app')
@section('title', 'Livraisons')
@section('bg-class', 'bg-img-3')
@section('body')
    <div class="container-fluid">
        <div class="d-flex justify-content-between">
            <div class="">
                <h2 class="font-weight-bold">Livraisons excédentaires | {{ $entity->shortname }}</h2>
                <p class="lead small m-0">Historiques des livraisons excédentaires du carburant pour {{ $entity->shortname }}
                </p>
            </div>
            <div class="m-2">
                <button onclick="history.back()" class="btn btn-sm btn-primary d-flex align-items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" height="12px" viewBox="0 -960 960 960" width="12px"
                        fill="#fff">
                        <path d="M423-59 2-480l421-421 78 79-342 342 342 342-78 79Z" />
                    </svg>
                    Retour
                </button>
            </div>
        </div>
        <hr />

        <div class="container-fluid">
            <div class="row">
                <div class="col-12 p-0">
                    <div class="carte d-block">
                        <div class="row">
                            <div class="col-md-6 col-12">
                                <div class="">
                                    <h5 class="card-title text-center mb-4">Répartition des livraisons par produit (LATA)
                                    </h5>
                                    <div id="chart1"></div>
                                </div>
                            </div>
                            <div class="col-md-6 col-12">
                                <div class="">
                                    <h5 class="card-title text-center mb-4">Répartition des livraisons par zone (LATA)</h5>
                                    <div id="chart2"></div>
                                </div>
                            </div>
                            <div class="col-12">
                                <x-dataloader />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 p-0">
                    <div class="card transparent">
                        <div class="card-header">
                            @php
                                $d = now()->startOfMonth()->toDateString();
                                $d2 = now()->toDateString();
                            @endphp
                            <form id="ffilter" class="filters-form pull-right" role="form">
                                <input type="hidden" name="type" value="greatbook">
                                <div class="form-group mb-1">
                                    <label for="dv222" class="control-label d-block mb-0">Du</label>
                                    <input type="text" class="form-control flatpickr" id="dv222" name="date1"
                                        value="{{ $d }}" style="min-width:120px;">
                                </div>
                                <div class="form-group mb-1">
                                    <label for="dv22" class="control-label d-block mb-0">Au</label>
                                    <input type="text" class="form-control flatpickr" id="dv22" name="date2"
                                        value="{{ $d2 }}" style="min-width:120px;">
                                </div>
                                <div class="form-group mb-1">
                                    <label for="zone" class="control-label d-block mb-0">Zone</label>
                                    <select name="zone[]" id="zone" class="form-control" multiple
                                        style="min-width:150px;">
                                        @foreach (mainWays() as $e)
                                            <option selected>{{ $e }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group mb-1">
                                    <label for="fuel" class="control-label d-block mb-0">Produit</label>
                                    <select name="fuel[]" id="fuel" class="form-control" multiple
                                        style="min-width:150px;">
                                        @foreach (mainfuels() as $e)
                                            <option selected>{{ $e }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </form>
                        </div>

                        <div class="py-4">
                            <div class="table-responsive">
                                <table id="table" class="table table-striped table-hover text-center text-nowrap"
                                    style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Terminal</th>
                                            <th>Date</th>
                                            <th>Localité</th>
                                            <th>Voie</th>
                                            <th class="text-nowrap">Produit</th>
                                            <th>Bon de livraison</th>
                                            <th>Programme de livraison</th>
                                            <th class="text-nowrap">Client</th>
                                            <th class="text-nowrap">LATA</th>
                                            <th class="text-nowrap">Prix unitaire LATA</th>
                                            <th class="text-nowrap">Total</th>
                                            <th class="text-nowrap no-export">Factures</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    </div>
@endsection

@section('script')
    <x-datatable />
    <x-flatpickr />
    <x-chart />
    <x-select />

    <script>
        flatpickr(".flatpickr", {
            maxDate: "today",
            locale: {
                firstDayOfWeek: 1
            }
        });

        $('[name="zone[]"]').multiselect({
            includeSelectAllOption: true,
            nonSelectedText: 'Aucun filtre',
            nSelectedText: 'zones sélectionnées',
            allSelectedText: 'Toutes les zones',
            numberDisplayed: 1, // affiche 1 élément puis "n zones sélectionnées"
            selectAllText: 'Toutes',
            buttonWidth: '100%',
            buttonClass: 'btn btn-primary'
        });

        $('[name="fuel[]"]').multiselect({
            includeSelectAllOption: true,
            nonSelectedText: 'Aucun filtre',
            nSelectedText: 'produits sélectionnés',
            allSelectedText: 'Tous les produits',
            numberDisplayed: 1,
            selectAllText: 'Tous',
            buttonWidth: '100%',
            buttonClass: 'btn btn-primary'
        });

        $('[btnmdl]').click(function() {
            $('.modal.show').modal('hide');
            var t = $(this).data('target');
            $(`${t}`).modal('show');
        });


        var date1 = '{{ request('date1') }}';
        var date2 = '{{ request('date2') }}';
        var fuel = '{{ request('fuel') }}';
        if (date1.length) {
            $('[name="date1"]')[0]._flatpickr.setDate(date1, true);
        }
        if (date2.length) {
            $('[name="date2"]')[0]._flatpickr.setDate(date2, true);
        }
        if (fuel.length) {
            $('[name="fuel[]"]').val([fuel]).change();
            $('[name="fuel[]"]').multiselect('refresh');
        }

        var dtObj = $('#table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route('delivery.index') }}',
                data: function(d) {
                    d.zones = $('[name="zone[]"]').val();
                    d.fuels = $('[name="fuel[]"]').val();
                    d.entity_id = '{{ @$entity->id }}';
                    d.date = $('[name="date1"]').val() + ' to ' + $('[name="date2"]').val();
                }
            },
            order: [
                [0, "desc"]
            ],
            columnDefs: [{
                targets: 0,
                width: '1%'
            }, {
                targets: 12,
                width: '1%'
            }],
            columns: [{
                    data: 'id',
                    name: 'id',
                },
                {
                    data: 'terminal',
                    name: 'terminal',
                },
                {
                    data: 'date',
                    name: 'date'
                },
                {
                    data: 'locality',
                    name: 'locality'
                },
                {
                    data: 'way',
                    name: 'way'
                },
                {
                    data: 'product',
                    name: 'product'
                },
                {
                    data: 'delivery_note',
                    name: 'delivery_note'
                },
                {
                    data: 'delivery_program',
                    name: 'delivery_program'
                },
                {
                    data: 'client',
                    name: 'client'
                },
                {
                    data: 'lata',
                    name: 'lata'
                },
                {
                    data: 'unitprice',
                    name: 'unitprice'
                },
                {
                    data: 'total',
                    name: 'total',
                    orderable: false,
                    searchable: false,
                    className: 'cursor'
                },
                {
                    data: 'deliveryfile',
                    name: 'deliveryfile',
                    orderable: false,
                    searchable: false,
                },
            ],
            dom: 'Blfrtip',
            buttons: [{
                extend: 'excelHtml5',
                title: 'Export Excel',
                exportOptions: {
                    columns: ':not(.no-export)',
                    format: {
                        body: function(data, row, column, node) {
                            let num = parseFloat(data.toString().replace(/ /g,
                                '').replace(',', '.'));
                            return isNaN(num) ? data : num;
                        }
                    }
                }
            }, ],

        }).on('draw.dt', function(e, settings, data, xhr) {
            $('.tooltip').remove();
            $('[tooltip]').tooltip();
        });

        var ff = $('#ffilter');

        let timer;
        ff.change(function(e) {
            clearTimeout(timer);
            var e = $(e.target);
            timer = setTimeout(() => {
                dtObj.ajax.reload(null, false);
                dashboard();
            }, 100);
        });

        function dashboard() {
            var ldr = $('[dataloader]');
            ldr.show();
            $.ajax({
                url: '{{ route('dashboard') }}',
                data: {
                    type: 'delivery',
                    entity_id: '{{ @$entity->id }}',
                    zones: $('[name="zone[]"]').val(),
                    fuels: $('[name="fuel[]"]').val(),
                    date: $('[name="date1"]').val() + ' to ' + $('[name="date2"]').val(),
                },
                success: function(data) {
                    $('[totalLata]').html(data.totalLata);
                    $('[totalL15]').html(data.totalL15);

                    chart1.series[0].setData(
                        data.chart1.labels.map((label, i) => [label, data.chart1.data[i]])
                    );

                    chart2.series[0].setData(
                        data.chart2.labels.map((label, i) => [label, data.chart2.data[i]])
                    );

                    setTimeout(() => {
                        chart1.update({}, true);
                        chart2.update({}, true);
                    }, 400);

                    ldr.hide();
                },
                error: function(xhr, a, b) {

                },
            })
        }

        function formatNumber(val) {
            return val
                .toFixed(3)
                .replace('.', ',')
                .replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
        }

        var chart1 = Highcharts.chart('chart1', {
            chart: {
                type: 'pie',
                height: 300,
                backgroundColor: 'transparent',
                options3d: {
                    enabled: true,
                    alpha: 45,
                    beta: 0,
                    depth: 50
                }
            },
            legend: {
                enabled: true,
                align: 'left',
                verticalAlign: 'middle',
                layout: 'vertical',
                itemMarginTop: 8,
                itemMarginBottom: 8,
                symbolRadius: 6,
                symbolHeight: 12,
                symbolWidth: 12,
                itemStyle: {
                    fontSize: '14px',
                    color: '#1a3b5d',
                    fontWeight: 'normal'
                },
                labelFormatter: function() {
                    return this.name + ' : ' + formatNumber(this.y);
                }
            },
            title: {
                text: ''
            },
            credits: {
                enabled: false
            },
            plotOptions: {
                pie: {
                    innerSize: '60%',
                    depth: 50,
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: true,
                        formatter: function() {
                            const total = this.series.data.reduce((sum, p) => sum + p.y, 0);
                            if (total === 0) return '';
                            const percent = (this.y / total * 100).toFixed(1);
                            return percent + '%';
                        },
                        style: {
                            fontWeight: 'bold',
                            color: '#000',
                            fontSize: '13px'
                        }
                    }
                }
            },
            tooltip: {
                pointFormatter: function() {
                    return 'Total M³ Vendus : ' + formatNumber(this.y);
                }
            },
            series: [{
                name: 'Ventes',
                showInLegend: true,
                data: [],
                colors: [
                    '#4e73df',
                    '#1cc88a',
                    '#36b9cc',
                    '#f6c23e',
                    '#5a90cc'
                ]
            }],
            responsive: {
                rules: [{
                    condition: {
                        maxWidth: 500
                    },
                    chartOptions: {
                        legend: {
                            align: 'center',
                            verticalAlign: 'bottom',
                            layout: 'horizontal'
                        },
                        chart: {
                            height: 400
                        }
                    }
                }]
            }
        });

        var chart2 = Highcharts.chart('chart2', {
            chart: {
                type: 'pie',
                height: 300,
                backgroundColor: 'transparent',
                options3d: {
                    enabled: true,
                    alpha: 45,
                    beta: 0,
                    depth: 50
                }
            },
            legend: {
                enabled: true,
                align: 'right',
                verticalAlign: 'middle',
                layout: 'vertical',
                itemMarginTop: 8,
                itemMarginBottom: 8,
                symbolRadius: 6,
                symbolHeight: 12,
                symbolWidth: 12,
                itemStyle: {
                    fontSize: '14px',
                    color: '#1a3b5d'
                },
                labelFormatter: function() {
                    return this.name + ' : ' + formatNumber(this.y);
                }
            },
            title: {
                text: ''
            },
            credits: {
                enabled: false
            },
            tooltip: {
                pointFormatter: function() {
                    return 'Total Vente LATA : ' + formatNumber(this.y);
                }
            },
            plotOptions: {
                pie: {
                    innerSize: '60%',
                    depth: 50,
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: true,
                        formatter: function() {
                            const total = this.series.data.reduce((sum, p) => sum + p.y, 0);
                            if (total === 0) return '';
                            const percent = (this.y / total * 100).toFixed(1);
                            return percent + '%';
                        },
                        style: {
                            fontWeight: 'bold',
                            color: '#000',
                            fontSize: '13px'
                        }
                    }
                }
            },
            series: [{
                name: 'Ventes LATA',
                showInLegend: true,
                data: [],
                colors: [
                    '#4e73df',
                    '#1cc88a',
                    '#36b9cc',
                    '#f6c23e',
                    '#5a90cc'
                ]
            }],
            responsive: {
                rules: [{
                    condition: {
                        maxWidth: 500
                    },
                    chartOptions: {
                        legend: {
                            align: 'center',
                            verticalAlign: 'bottom',
                            layout: 'horizontal'
                        },
                        chart: {
                            height: 400
                        }
                    }
                }]
            }
        });


        dashboard();
    </script>
@endsection
