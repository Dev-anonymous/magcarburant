@extends('layouts.app')
@section('title', 'Ventes')
@section('bg-class', 'bg-img-3')
@section('body')
    <div class="container-fluid">
        <div class="d-flex justify-content-between">
            <div class="">
                <h2 class="font-weight-bold">Ventes ({{ $entity->shortname }}) </h2>
                <p class="lead small m-0">Historiques des ventes (sorties) pour ({{ $entity->shortname }}) </p>
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
                        <div class="row g-3 mb-3">
                            <div class="col-6 col-md-6 col-12">
                                <div class="text-center">
                                    <i class="material-icons md-36 text-success mb-1">local_gas_station</i>
                                    <div class="font-weight-bold text-success">Volume Total LATA</div>
                                    <div class="h4 font-weight-bold text-success" style="font-size: 28px" totalLata>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 col-md-6 col-12">
                                <div class="text-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" height="48px" viewBox="0 -960 960 960"
                                        width="48px" fill="#FF3D55">
                                        <path
                                            d="M480-78q-142 0-242-97.71T138-415q0-68.14 27-130.77 27-62.63 75-108.73L480-892l240 237.5q48 46.1 75.5 108.73T823-415q0 141.58-100.5 239.29Q622-78 480-78ZM229-415h502q0-46-19-91.5T659-586L480-763 301-586q-34 34-53 79.54-19 45.54-19 91.46Z" />
                                    </svg>
                                    <div class="font-weight-bold text-danger">Volume Total L15</div>
                                    <div class="h4 font-weight-bold text-danger" style="font-size: 28px" totalL15>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 col-12">
                                <div class="">
                                    <h5 class="card-title text-center mb-4">Répartition des ventes par produit (M³)</h5>
                                    <div id="chart1"></div>
                                </div>
                            </div>
                            <div class="col-md-6 col-12">
                                <div class="">
                                    <h5 class="card-title text-center mb-4">Répartition des ventes par produit LATA</h5>
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
                            <div class="row">
                                <div class="col-xs-12 col-sm-6">
                                    <h4 class="card-title font-weight-bold">
                                        Historique des toutes les ventes pour {{ $entity->shortname }}
                                    </h4>
                                </div>
                                @php
                                    $d = now()->startOfMonth()->toDateString();
                                    $d2 = now()->toDateString();
                                @endphp
                            </div>
                            <form id="ffilter" class="filters-form pull-right" role="form">
                                <div class="form-group mb-1">
                                    <label for="dv222" class="control-label d-block mb-0">Du</label>
                                    <input type="text" class="form-control flatpickr2" id="dv222" name="date1"
                                        value="{{ $d }}" style="min-width:120px;">
                                </div>
                                <div class="form-group mb-1">
                                    <label for="dv22" class="control-label d-block mb-0">Au</label>
                                    <input type="text" class="form-control flatpickr2" id="dv22" name="date2"
                                        value="{{ $d2 }}" style="min-width:120px;">
                                </div>
                                @if ($entity->user->user_role == 'logisticien')
                                    <div class="form-group mb-1">
                                        <label for="zone" class="control-label d-block mb-0">Type de vente</label>
                                        <select name="from_mutuality" id="from_mutuality" class="form-control select2"
                                            style="min-width:150px;">
                                            <option value="">Tous</option>
                                            <option value="0">Non mutualisé</option>
                                            <option value="1">Mutualisé</option>
                                        </select>
                                    </div>
                                @endif
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
                                            <th class="text-nowrap">L15</th>
                                            <th class="text-nowrap">Densité</th>
                                            <th class="text-nowrap no-export">Factures</th>
                                            <th class="no-export"></th>
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
        flatpickr(".flatpickr2", {
            maxDate: "today",
            locale: {
                firstDayOfWeek: 1
            }
        });

        $('[btnmdl]').click(function() {
            $('.modal.show').modal('hide');
            var t = $(this).data('target');
            $(`${t}`).modal('show');
        })

        var dtObj = $('#table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route('sale.index') }}',
                data: function(d) {
                    d.entity_id = '{{ @$entity->id }}';
                    d.date = $('[name="date1"]').val() + ' to ' + $('[name="date2"]').val();
                    d.from_mutuality = $('#from_mutuality').val();
                }
            },
            order: [
                [0, "desc"]
            ],
            columnDefs: [{
                targets: 0,
                width: '1%'
            }, {
                targets: 13,
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
                    data: 'l15',
                    name: 'l15'
                },
                {
                    data: 'density',
                    name: 'density'
                },
                {
                    data: 'salefile',
                    name: 'salefile',
                    orderable: false,
                    searchable: false,
                },
                {
                    data: 'action',
                    name: 'action',
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
            $('[bedit]').off('click').click(function() {
                var data = JSON.parse($(this).attr('data'));
                var mdl = $('#mdledit');
                var form = $('[fedit]', mdl);
                var dateinp = $('[name="date"]', form);
                var dob = dateinp.flatpickr();
                dob.destroy();
                dob = dateinp.flatpickr({
                    maxDate: "today",
                    locale: {
                        firstDayOfWeek: 1
                    }
                });
                dob.setDate(data.date);

                $('[name="id"]', form).val(data.id);
                $('[name="product"]', form).val(data.product);
                $('[name="terminal"]', form).val(data.terminal);
                $('[name="client"]', form).val(data.client);
                $('[name="way"]', form).val(data.way);
                $('[name="locality"]', form).val(data.locality);
                $('[name="delivery_note"]', form).val(data.delivery_note);
                $('[name="delivery_program"]', form).val(data.delivery_program);
                $('[name="lata"]', form).val(data.lata);
                $('[name="l15"]', form).val(data.l15);
                $('[name="density"]', form).val(data.density);
                mdl.modal('show');
            });
            $('[bdel]').off('click').click(function() {
                var data = JSON.parse($(this).attr('data'));
                var mdl = $('#mdldel');
                var form = $('[fdel]', mdl);
                $('[name="id"]', form).val(data.id);
                $('[shortname]', form).html(data.name);
                mdl.modal('show');
            });
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
                    type: 'sale',
                    from_mutuality: $('#from_mutuality').val(),
                    entity_id: '{{ @$entity->id }}',
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
                    return this.name + ' : ' + formatNumber(this.y) + ' M³';
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
