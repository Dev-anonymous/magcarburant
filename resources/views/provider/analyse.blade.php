@extends('layouts.app')
@section('title', 'Analyse')
@section('bg-class', 'bg-img-3')
@section('body')
    <div class="container">
        <div class="d-flex justify-content-between">
            <div class="">
                <h2 class="font-weight-bold">Manque à Gagner des Sociétés Commerciales</h2>
                <p class="lead small m-0">Analyse et Bilan MAG de tous les prodtuis et toutes les zones </p>
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

        <div class="row">
            <div class="col-md-12">
                <div class="card transparent">
                    <input type="hidden" name="type" value="balance">
                    <div class="card-header">
                        @php
                            $d = now()->startOfMonth()->toDateString();
                            $d2 = now()->toDateString();
                        @endphp
                        <form id="ffilter" class="filters-form pull-right" role="form">
                            <input type="hidden" name="type" value="balance">
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
                    <x-dataloader />
                    <x-alert />
                    <div class="card-body" style="min-height: 300px">
                        <div class="table-responsive" data>

                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
@section('modals')

@endsection

@section('script')
    <x-flatpickr />
    <x-select />
    <x-datatable />
    <style>
        .title {
            font-weight: bold;
            background: #ccc;
        }

        .title1 {
            font-weight: bold;
            background: #cccccc70;
        }
    </style>
    <script>
        flatpickr(".flatpickr", {
            maxDate: "today",
            locale: {
                firstDayOfWeek: 1
            }
        });
        var ldr = $('[dataloader]');

        var ff = $('#ffilter');

        let timer;
        ff.change(function(e) {
            clearTimeout(timer);
            var e = $(e.target);
            timer = setTimeout(() => {
                getData();
            }, 100);
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

        function getData() {
            ldr.show();

            var data = $('#ffilter').serialize();
            var rep = $('#rep');
            $.ajax({
                url: '{{ route('dashboard') }}',
                data: data,
                success: function(data) {
                    var h = `
                    <h6 class='text-center font-weight-bold'>MANQUE A GAGNER SOCIETES COMMERCIALES USD</h6>
                    <table id="table" class="table table-striped table-hover text-nowrap" style="width:100%">
                    `;

                    h += '<thead><tr>';
                    var tit = data.rows.shift();
                    tit.map(e => {
                        h += `<td class="${e.class??""}">${e.label}</td>`
                    })
                    h += '</tr></thead><tbody>';

                    data.rows.forEach(row => {
                        h += '<tr>'
                        row.map(e => {
                            h += `<td class="${e.class??""}">${e.label}</td>`
                        })
                        h += '</tr>'
                    });

                    h += '</tbody></table>'

                    $('[data]').html(h);
                    $('[data]').css('opacity', 1);
                    rep.hide();

                    $('#table').DataTable({
                        dom: 'Brt',
                        ordering: false,
                        buttons: [{
                            extend: 'excelHtml5',
                            title: 'Export Excel',
                            exportOptions: {
                                format: {
                                    body: function(data, row, column, node) {
                                        let num = parseFloat(data.toString().replace(/ /g,
                                            '').replace(',', '.'));
                                        return isNaN(num) ? data : num;
                                    }
                                }
                            }
                        }, ],
                    });
                },
                error: function(xhr, a, b) {
                    var resp = xhr.responseJSON;
                    var mess = resp?.message ?? "Erreur, veuillez réessayer !";
                    rep.html(mess).stop().removeClass().addClass(
                            'p-1 m-0 alert alert-danger')
                        .show();
                    $('[data]').css('opacity', 0.1);

                },
            }).always(function() {
                ldr.hide();
            })
        }

        getData();
    </script>
@endsection
