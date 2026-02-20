@extends('layouts.app')
@section('title', 'Dashboard')
@section('bg-class', 'bg-img-3')
@section('body')
    <div class="container-fluid">
        <div class="d-flex justify-content-between">
            <div class="">
                <h2 class="font-weight-bold">Dashboard </h2>
                <p class="lead small m-0">Statistiques générales sur les achats et ventes</p>
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
                                <div class="col-md-12">
                                    <div class="">
                                        <div id="chart1"></div>
                                    </div>
                                </div>
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
                    chart1.redraw();
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
                type: 'column',
                height: 600,
                backgroundColor: 'transparent',
                options3d: {
                    enabled: true,
                    alpha: 35,
                    beta: 0,
                    depth: 50,
                    viewDistance: 25
                }
            },
            title: {
                text: 'Statistiques générales sur les achats, les ventes et les livraisons excédentaires'
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
                        format: '{point.y} M3',
                        style: {
                            fontSize: '13px',
                            color: '#000'
                        }
                    }
                },
            },

            series: [
                // {
                //     name: "",
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


        dashboard();
    </script>
@endsection
