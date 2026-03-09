@extends('layouts.app')
@section('title', 'Dashboard')
@section('bg-class', 'bg-img-3')
@section('body')
    @php
        $isState = !in_array(Auth::user()->user_role, ['petrolier', 'logisticien']);
    @endphp
    <div class="container-fluid">
        <div class="d-flex justify-content-between">
            <div class="">
                <h2 class="font-weight-bold">Dashboard </h2>
                <p class="lead small m-0">Statistiques des sur les achats et ventes</p>
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
                    <div class="card transparent">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-xs-12 col-sm-6">
                                </div>
                                @php
                                    $d = now()->startOfMonth()->toDateString();
                                    $d2 = now()->toDateString();
                                @endphp
                                <div class="col-xs-12 col-sm-6">
                                    <form class="form-inline filters-form pull-right" role="form">
                                        <div class="form-group mb-1">
                                            <label class="mr-2" for="dv222">Du</label>
                                            <input class="form-control flatpickr" id="dv222" value="{{ $d }}"
                                                name="date1" style="width:100px" />
                                        </div>
                                        <div class="form-group mb-1">
                                            <label class="mr-2" for="dv22">Au</label>
                                            <input class="form-control flatpickr" id="dv22" value="{{ $d2 }}"
                                                name="date2" style="width:100px" />
                                        </div>
                                    </form>
                                </div>

                            </div>
                            <div class="row">
                                @if ($isState)
                                    <div class="col-md-12">
                                        <div class="">
                                            <div id="chart4"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="">
                                            <div id="chart6"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="">
                                            <div id="chart1"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="">
                                            <div id="chart2"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="">
                                            <div id="chart7"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="">
                                            <div id="chart3"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="">
                                            <div id="chart5"></div>
                                        </div>
                                    </div>
                                @else
                                    <div class="col-md-6">
                                        <div class="">
                                            <div id="chart1"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="">
                                            <div id="chart2"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="">
                                            <div id="chart3"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="">
                                            <div id="chart4"></div>
                                        </div>
                                    </div>
                                @endif
                                <div class="col-12">
                                    <x-dataloader />
                                </div>
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
    <x-flatpickr />
    <x-chart />

    <script>
        flatpickr(".flatpickr", {
            maxDate: "today",
            locale: {
                firstDayOfWeek: 1
            }
        });

        $('.flatpickr').change(() => dashboard());

        function dashboard() {
            var ldr = $('[dataloader]');
            ldr.show();
            $.ajax({
                url: '{{ route('dashboard') }}',
                data: {
                    type: 'dash',
                    date: $('[name="date1"]').val() + ' to ' + $('[name="date2"]').val(),
                },
                success: function(data) {
                    @if ($isState)
                        var achat = data.data.achat;
                        var vente = data.data.vente;
                        var livraison = data.data.livraison;
                        var vente_miniere = data.data.vente_miniere;

                        chart1.xAxis[0].setCategories(vente.categories, false);
                        while (chart1.series.length > 0) {
                            chart1.series[0].remove(false);
                        }
                        chart1.addSeries(vente.series, false);
                        chart1.redraw();

                        chart2.xAxis[0].setCategories(achat.categories, false);
                        while (chart2.series.length > 0) {
                            chart2.series[0].remove(false);
                        }
                        chart2.addSeries(achat.series, false);
                        chart2.redraw();

                        chart3.xAxis[0].setCategories(livraison.categories, false);
                        while (chart3.series.length > 0) {
                            chart3.series[0].remove(false);
                        }
                        chart3.addSeries(livraison.series, false);
                        chart3.redraw();

                        var vente_zone = data.data.vente_zone;
                        chart4.xAxis[0].setCategories(vente_zone.categories, false);
                        while (chart4.series.length > 0) {
                            chart4.series[0].remove(false);
                        }
                        vente_zone.series.forEach(function(serie) {
                            chart4.addSeries(serie, false);
                        });
                        chart4.redraw();

                        chart5.xAxis[0].setCategories(vente_miniere.categories, false);
                        while (chart5.series.length > 0) {
                            chart5.series[0].remove(false);
                        }
                        chart5.addSeries(vente_miniere.series, false);
                        chart5.redraw();

                        var vente_carburant = data.data.vente_carburant;
                        chart6.xAxis[0].setCategories(vente_carburant.categories, false);
                        while (chart6.series.length > 0) {
                            chart6.series[0].remove(false);
                        }
                        vente_carburant.series.forEach(function(serie) {
                            chart6.addSeries(serie, false);
                        });
                        chart6.redraw();

                        var livraison_zone = data.data.livraison_zone;
                        chart7.xAxis[0].setCategories(livraison_zone.categories, false);
                        while (chart7.series.length > 0) {
                            chart7.series[0].remove(false);
                        }
                        livraison_zone.series.forEach(function(serie) {
                            chart7.addSeries(serie, false);
                        });
                        chart7.redraw();
                    @else
                        chart1.xAxis[0].setCategories(data.chart1.categories, false);
                        while (chart1.series.length > 0) {
                            chart1.series[0].remove(false);
                        }
                        data.chart1.series.forEach(function(serie) {
                            chart1.addSeries({
                                name: serie.name,
                                data: serie.data
                            }, false);
                        });

                        chart2.xAxis[0].setCategories(data.chart2.categories, false);
                        while (chart2.series.length > 0) {
                            chart2.series[0].remove(false);
                        }
                        data.chart2.series.forEach(function(serie) {
                            chart2.addSeries({
                                name: serie.name,
                                data: serie.data
                            }, false);
                        });

                        chart3.xAxis[0].setCategories(data.chart3.categories, false);
                        while (chart3.series.length > 0) {
                            chart3.series[0].remove(false);
                        }
                        data.chart3.series.forEach(function(serie) {
                            chart3.addSeries({
                                name: serie.name,
                                data: serie.data
                            }, false);
                        });

                        chart4.xAxis[0].setCategories(data.chart4.categories, false);
                        while (chart4.series.length > 0) {
                            chart4.series[0].remove(false);
                        }
                        data.chart4.series.forEach(function(serie) {
                            chart4.addSeries({
                                name: serie.name,
                                data: serie.data
                            }, false);
                        });

                        chart1.redraw();
                        chart2.redraw();
                        chart3.redraw();
                        chart4.redraw();
                    @endif
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

        @if ($isState)
            var chart1 = Highcharts.chart('chart1', {
                chart: {
                    type: 'bar',
                    height: 400,
                    backgroundColor: 'transparent',
                },
                title: {
                    text: 'Statistiques des ventes par société'
                },
                credits: {
                    enabled: false
                },
                xAxis: {
                    categories: [],
                    crosshair: true
                },
                yAxis: {
                    min: 0,
                    title: {
                        text: 'Volume (M3)'
                    }
                },
                legend: {
                    enabled: true,
                    align: 'center',
                    verticalAlign: 'bottom',
                    layout: 'horizontal',
                    itemMarginTop: 8,
                    itemMarginBottom: 8,
                    symbolRadius: 6,
                    symbolHeight: 12,
                    symbolWidth: 12,
                    itemStyle: {
                        fontSize: '14px',
                        color: '#1a3b5d'
                    }
                },
                tooltip: {
                    shared: true,
                    valueSuffix: ' M3'
                },
                plotOptions: {
                    bar: {
                        borderRadius: 4,
                        pointPadding: 0.1,
                        groupPadding: 0.15,
                        dataLabels: {
                            enabled: true,
                            formatter: function() {
                                return formatNumber(this.y) + ' M3';
                            },
                            style: {
                                fontSize: '13px',
                                color: '#000'
                            }
                        }
                    }
                },
                series: [],
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
                    type: 'bar',
                    height: 400,
                    backgroundColor: 'transparent',
                },
                title: {
                    text: 'Statistiques des achats par société'
                },
                credits: {
                    enabled: false
                },
                xAxis: {
                    categories: [],
                    crosshair: true
                },
                yAxis: {
                    min: 0,
                    title: {
                        text: 'Volume (M3)'
                    }
                },
                legend: {
                    enabled: true,
                    align: 'center',
                    verticalAlign: 'bottom',
                    layout: 'horizontal',
                    itemMarginTop: 8,
                    itemMarginBottom: 8,
                    symbolRadius: 6,
                    symbolHeight: 12,
                    symbolWidth: 12,
                    itemStyle: {
                        fontSize: '14px',
                        color: '#1a3b5d'
                    }
                },
                tooltip: {
                    shared: true,
                    valueSuffix: ' M3'
                },
                plotOptions: {
                    bar: {
                        borderRadius: 4,
                        pointPadding: 0.1,
                        groupPadding: 0.15,
                        dataLabels: {
                            enabled: true,
                            formatter: function() {
                                return formatNumber(this.y) + ' M3';
                            },
                            style: {
                                fontSize: '13px',
                                color: '#000'
                            }
                        }
                    }
                },
                series: [],
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

            var chart3 = Highcharts.chart('chart3', {
                chart: {
                    type: 'bar',
                    height: 400,
                    backgroundColor: 'transparent',
                },
                title: {
                    text: 'Statistiques des livraisons excédentaires par société'
                },
                credits: {
                    enabled: false
                },
                xAxis: {
                    categories: [],
                    crosshair: true
                },
                yAxis: {
                    min: 0,
                    title: {
                        text: 'Volume (M3)'
                    }
                },
                legend: {
                    enabled: true,
                    align: 'center',
                    verticalAlign: 'bottom',
                    layout: 'horizontal',
                    itemMarginTop: 8,
                    itemMarginBottom: 8,
                    symbolRadius: 6,
                    symbolHeight: 12,
                    symbolWidth: 12,
                    itemStyle: {
                        fontSize: '14px',
                        color: '#1a3b5d'
                    }
                },
                tooltip: {
                    shared: true,
                    valueSuffix: ' M3'
                },
                plotOptions: {
                    bar: {
                        borderRadius: 4,
                        pointPadding: 0.1,
                        groupPadding: 0.15,
                        dataLabels: {
                            enabled: true,
                            formatter: function() {
                                return formatNumber(this.y) + ' M3';
                            },
                            style: {
                                fontSize: '13px',
                                color: '#000'
                            }
                        }
                    }
                },
                series: [],
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

            var chart4 = Highcharts.chart('chart4', {
                chart: {
                    type: 'column',
                    height: 400,
                    backgroundColor: 'transparent',
                },
                title: {
                    text: 'Statistiques des ventes par zone'
                },
                credits: {
                    enabled: false
                },
                xAxis: {
                    categories: [],
                    crosshair: true
                },
                yAxis: {
                    min: 0,
                    title: {
                        text: 'Volume (M3)'
                    }
                },
                legend: {
                    enabled: true,
                    align: 'center',
                    verticalAlign: 'bottom',
                    layout: 'horizontal',
                    itemMarginTop: 8,
                    itemMarginBottom: 8,
                    symbolRadius: 6,
                    symbolHeight: 12,
                    symbolWidth: 12,
                    itemStyle: {
                        fontSize: '14px',
                        color: '#1a3b5d'
                    }
                },
                tooltip: {
                    shared: true,
                    valueSuffix: ' M3'
                },
                plotOptions: {
                    column: {
                        borderRadius: 4,
                        pointPadding: 0.1,
                        groupPadding: 0.15,
                        dataLabels: {
                            enabled: true,
                            formatter: function() {
                                return formatNumber(this.y) + ' M3';
                            },
                            style: {
                                fontSize: '13px',
                                color: '#000'
                            }
                        }
                    }
                },
                series: [],
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

            var chart5 = Highcharts.chart('chart5', {
                chart: {
                    type: 'bar',
                    height: 400,
                    backgroundColor: 'transparent',
                },
                title: {
                    text: 'Statistiques des ventes liées aux sociétés minières'
                },
                credits: {
                    enabled: false
                },
                xAxis: {
                    categories: [],
                    crosshair: true
                },
                yAxis: {
                    min: 0,
                    title: {
                        text: 'Volume (M3)'
                    }
                },
                legend: {
                    enabled: true,
                    align: 'center',
                    verticalAlign: 'bottom',
                    layout: 'horizontal',
                    itemMarginTop: 8,
                    itemMarginBottom: 8,
                    symbolRadius: 6,
                    symbolHeight: 12,
                    symbolWidth: 12,
                    itemStyle: {
                        fontSize: '14px',
                        color: '#1a3b5d'
                    }
                },
                tooltip: {
                    shared: true,
                    valueSuffix: ' M3'
                },
                plotOptions: {
                    bar: {
                        borderRadius: 4,
                        pointPadding: 0.1,
                        groupPadding: 0.15,
                        dataLabels: {
                            enabled: true,
                            formatter: function() {
                                return formatNumber(this.y) + ' M3';
                            },
                            style: {
                                fontSize: '13px',
                                color: '#000'
                            }
                        }
                    }
                },
                series: [],
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

            var chart6 = Highcharts.chart('chart6', {
                chart: {
                    type: 'column',
                    height: 400,
                    backgroundColor: 'transparent',
                },
                title: {
                    text: 'Statistiques des ventes par carburant'
                },
                credits: {
                    enabled: false
                },
                xAxis: {
                    categories: [],
                    crosshair: true
                },
                yAxis: {
                    min: 0,
                    title: {
                        text: 'Volume (M3)'
                    }
                },
                legend: {
                    enabled: true,
                    align: 'center',
                    verticalAlign: 'bottom',
                    layout: 'horizontal',
                    itemMarginTop: 8,
                    itemMarginBottom: 8,
                    symbolRadius: 6,
                    symbolHeight: 12,
                    symbolWidth: 12,
                    itemStyle: {
                        fontSize: '14px',
                        color: '#1a3b5d'
                    }
                },
                tooltip: {
                    shared: true,
                    valueSuffix: ' M3'
                },
                plotOptions: {
                    column: {
                        borderRadius: 4,
                        pointPadding: 0.1,
                        groupPadding: 0.15,
                        dataLabels: {
                            enabled: true,
                            formatter: function() {
                                return formatNumber(this.y) + ' M3';
                            },
                            style: {
                                fontSize: '13px',
                                color: '#000'
                            }
                        }
                    }
                },
                series: [],
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

            var chart7 = Highcharts.chart('chart7', {
                chart: {
                    type: 'column',
                    height: 400,
                    backgroundColor: 'transparent',
                },
                title: {
                    text: 'Statistiques des livraisons excédentaires par zone'
                },
                credits: {
                    enabled: false
                },
                xAxis: {
                    categories: [],
                    crosshair: true
                },
                yAxis: {
                    min: 0,
                    title: {
                        text: 'Volume (M3)'
                    }
                },
                legend: {
                    enabled: true,
                    align: 'center',
                    verticalAlign: 'bottom',
                    layout: 'horizontal',
                    itemMarginTop: 8,
                    itemMarginBottom: 8,
                    symbolRadius: 6,
                    symbolHeight: 12,
                    symbolWidth: 12,
                    itemStyle: {
                        fontSize: '14px',
                        color: '#1a3b5d'
                    }
                },
                tooltip: {
                    shared: true,
                    valueSuffix: ' M3'
                },
                plotOptions: {
                    column: {
                        borderRadius: 4,
                        pointPadding: 0.1,
                        groupPadding: 0.15,
                        dataLabels: {
                            enabled: true,
                            formatter: function() {
                                return formatNumber(this.y) + ' M3';
                            },
                            style: {
                                fontSize: '13px',
                                color: '#000'
                            }
                        }
                    }
                },
                series: [],
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
        @else
            @php
                $islog = auth()->user()->user_role == 'logisticien';
                $title2 = 'Statistiques des ventes liées aux Sociétés Minières';
                if ($islog) {
                    $title = 'Statistiques des ventes';
                } else {
                    $title = 'Statistiques des achats, ventes et livraisons excédentaires';
                }
            @endphp
            var chart1 = Highcharts.chart('chart1', {
                chart: {
                    type: 'column',
                    height: 400,
                    backgroundColor: 'transparent',
                    options3d: {
                        enabled: true,
                        alpha: 20,
                        beta: 0,
                        depth: 50,
                        viewDistance: 25
                    }
                },
                title: {
                    text: '{{ $title }} par carburant'
                },
                credits: {
                    enabled: false
                },
                xAxis: {
                    categories: [],
                    crosshair: true
                },
                yAxis: {
                    min: 0,
                    title: {
                        text: 'Volume (M3)'
                    }
                },
                legend: {
                    enabled: true,
                    align: 'center',
                    verticalAlign: 'bottom',
                    layout: 'horizontal',
                    itemMarginTop: 8,
                    itemMarginBottom: 8,
                    symbolRadius: 6,
                    symbolHeight: 12,
                    symbolWidth: 12,
                    itemStyle: {
                        fontSize: '14px',
                        color: '#1a3b5d'
                    }
                },

                tooltip: {
                    shared: true,
                    valueSuffix: ' M3'
                },
                plotOptions: {
                    column: {
                        depth: 25,
                        borderRadius: 4,
                        pointPadding: 0.1,
                        groupPadding: 0.15,
                        dataLabels: {
                            enabled: true,
                            formatter: function() {
                                return formatNumber(this.y) + ' M3';
                            },
                            style: {
                                fontSize: '13px',
                                color: '#000'
                            }
                        }
                    },
                },

                series: [
                    // {
                    //     name: "Achats",
                    //     data: []
                    // },
                    // {
                    //     name: "Ventes",
                    //     data: []
                    // },
                    // {
                    //     name: "Livraisons excédentaires",
                    //     data: []
                    // }
                ],

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
                    type: 'column',
                    height: 400,
                    backgroundColor: 'transparent',
                    options3d: {
                        enabled: true,
                        alpha: 20,
                        beta: 0,
                        depth: 50,
                        viewDistance: 25
                    }
                },
                title: {
                    text: '{{ $title }} par zone'
                },
                credits: {
                    enabled: false
                },
                xAxis: {
                    categories: [],
                    crosshair: true
                },
                yAxis: {
                    min: 0,
                    title: {
                        text: 'Volume (M3)'
                    }
                },
                legend: {
                    enabled: true,
                    align: 'center',
                    verticalAlign: 'bottom',
                    layout: 'horizontal',
                    itemMarginTop: 8,
                    itemMarginBottom: 8,
                    symbolRadius: 6,
                    symbolHeight: 12,
                    symbolWidth: 12,
                    itemStyle: {
                        fontSize: '14px',
                        color: '#1a3b5d'
                    }
                },

                tooltip: {
                    shared: true,
                    valueSuffix: ' M3'
                },
                plotOptions: {
                    column: {
                        depth: 25,
                        borderRadius: 4,
                        pointPadding: 0.1,
                        groupPadding: 0.15,
                        dataLabels: {
                            enabled: true,
                            formatter: function() {
                                return formatNumber(this.y) + ' M3';
                            },
                            style: {
                                fontSize: '13px',
                                color: '#000'
                            }
                        }
                    },
                },
                series: [],
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

            var chart3 = Highcharts.chart('chart3', {
                chart: {
                    type: 'column',
                    height: 400,
                    backgroundColor: 'transparent',
                    options3d: {
                        enabled: true,
                        alpha: 20,
                        beta: 0,
                        depth: 50,
                        viewDistance: 25
                    }
                },
                title: {
                    text: '{{ $title2 }} par carburant'
                },
                credits: {
                    enabled: false
                },
                xAxis: {
                    categories: [],
                    crosshair: true
                },
                yAxis: {
                    min: 0,
                    title: {
                        text: 'Volume (M3)'
                    }
                },
                legend: {
                    enabled: true,
                    align: 'center',
                    verticalAlign: 'bottom',
                    layout: 'horizontal',
                    itemMarginTop: 8,
                    itemMarginBottom: 8,
                    symbolRadius: 6,
                    symbolHeight: 12,
                    symbolWidth: 12,
                    itemStyle: {
                        fontSize: '14px',
                        color: '#1a3b5d'
                    }
                },
                tooltip: {
                    shared: true,
                    valueSuffix: ' M3'
                },
                plotOptions: {
                    column: {
                        depth: 25,
                        borderRadius: 4,
                        pointPadding: 0.1,
                        groupPadding: 0.15,
                        dataLabels: {
                            enabled: true,
                            formatter: function() {
                                return formatNumber(this.y) + ' M3';
                            },
                            style: {
                                fontSize: '13px',
                                color: '#000'
                            }
                        }
                    },
                },
                series: [],
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

            var chart4 = Highcharts.chart('chart4', {
                chart: {
                    type: 'column',
                    height: 400,
                    backgroundColor: 'transparent',
                    options3d: {
                        enabled: true,
                        alpha: 20,
                        beta: 0,
                        depth: 50,
                        viewDistance: 25
                    }
                },
                title: {
                    text: '{{ $title2 }} par zone'
                },
                credits: {
                    enabled: false
                },
                xAxis: {
                    categories: [],
                    crosshair: true
                },
                yAxis: {
                    min: 0,
                    title: {
                        text: 'Volume (M3)'
                    }
                },
                legend: {
                    enabled: true,
                    align: 'center',
                    verticalAlign: 'bottom',
                    layout: 'horizontal',
                    itemMarginTop: 8,
                    itemMarginBottom: 8,
                    symbolRadius: 6,
                    symbolHeight: 12,
                    symbolWidth: 12,
                    itemStyle: {
                        fontSize: '14px',
                        color: '#1a3b5d'
                    }
                },

                tooltip: {
                    shared: true,
                    valueSuffix: ' M3'
                },
                plotOptions: {
                    column: {
                        depth: 25,
                        borderRadius: 4,
                        pointPadding: 0.1,
                        groupPadding: 0.15,
                        dataLabels: {
                            enabled: true,
                            formatter: function() {
                                return formatNumber(this.y) + ' M3';
                            },
                            style: {
                                fontSize: '13px',
                                color: '#000'
                            }
                        }
                    },
                },
                series: [],
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
        @endif

        dashboard();
    </script>
@endsection
