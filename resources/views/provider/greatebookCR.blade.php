@extends('layouts.app')
@section('title', 'Grand livre C.C.')
@section('bg-class', 'bg-img-3')
@section('body')
    <div class="container-fluid">
        <div class="d-flex justify-content-between">
            <div class="">
                <h2 class="font-weight-bold">Grand livre Croisement des Créances</h2>
                <p class="lead small m-0">Grand Livre de Croisement des Créances des ventes des produits</p>
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
                    <div class="card-header">
                        @php
                            $d = now()->startOfMonth()->toDateString();
                            $d2 = now()->toDateString();
                        @endphp
                        <form id="ffilter" class="filters-form pull-right" role="form">
                            <input type="hidden" name="type" value="greatbookcr">
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
                                <label for="items" class="control-label d-block mb-0">Items</label>
                                <select name="items" id="items" class="form-control select2" style="min-width:150px;">
                                    <option value="">Tous</option>
                                    @foreach (itemsCR() as $e)
                                        <option value="{{ $e->val }}">{{ $e->label }}</option>
                                    @endforeach
                                </select>
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
                    <div class="card-body" estyle="min-height: 100vh">
                        <div class="table-responsive" data></div>
                        <div class="my-3 text-danger" errdiv></div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
@section('modals')

@endsection

@section('script')
    <x-datatable />
    <x-flatpickr />
    <x-select />


    <style>
        .table td,
        .table th {
            padding: 4px !important;
        }

        .noneditable td {
            border-top: 2px solid #ccc !important;
            border-bottom: 2px solid #ccc !important;
        }

        .bigtitle {
            text-align: center;
        }
    </style>
    <script>
        flatpickr(".flatpickr", {
            maxDate: "today",
            locale: {
                firstDayOfWeek: 1
            }
        });

        var table = null;

        function getData() {
            var ldr = $('[dataloader]');
            ldr.show();

            var data = $('#ffilter').serialize();
            var rep = $('#rep');
            $.ajax({
                url: '{{ route('dashboard') }}',
                data: data,
                success: function(data) {
                    var th = '';
                    data.head.forEach(e => {
                        var t = e.title ? `tooltip title="${e.title}"` : '';
                        th +=
                            `<th class='${e.class??''}' ${t} >${e.label}</th>`;
                    });
                    var td = '';
                    data.rows.forEach(e => {
                        td += `<tr>`;
                        e.forEach(ee => {
                            var t = ee.title ? `tooltip title="${ee.title}"` : '';
                            td +=
                                `<td class='${ee.class??''}' ${t}">${ee.v}</td>`;
                        });
                        td += '</tr>';
                    });

                    var h = `
                    <table table class="table table-striped table-hover text-nowrap text-center"
                        style="width:100%">
                        <thead>
                            <tr>${th}</tr>
                        </thead>
                        <tbody>${td}</tbody>
                    </table>
                    `;
                    $('[data]').html(h);
                    $('[data]').css('opacity', 1);
                    rep.hide();

                    let $table = $('[table]');
                    $table.on('draw.dt', function() {
                        var only = '{{ request('tag') }}';
                        if (only.trim().length) {
                            // let table = $(this).DataTable();
                            // table.columns().every(function(idx) {
                            //     let th = table.column(idx).header();
                            //     let tag = th.getAttribute('tag');
                            //     let visible = (tag === only || tag === null);
                            //     table.column(idx).visible(visible);
                            // });
                        }
                    });

                    table = $table.DataTable({
                        dom: 'Blfrtip',
                        // responsive: true,
                        scrollX: true,
                        // fixedColumns: {
                        //     leftColumns: 2
                        // },
                        buttons: [
                            // {
                            //     extend: 'colvis',
                            //     text: 'Filtrer les paramètres',
                            //     collectionLayout: 'fixed four-column',
                            //     collectionTitle: 'Affichage des colonnes',
                            // },
                            {
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
                            },
                        ],
                    });

                    $('.tooltip').remove();
                    $('[tooltip]').tooltip();

                    var e = '';
                    if (data.errors) {
                        data.errors.forEach(el => {
                            e +=
                                `<p class='m-0 font-weight-bold'><i class="material-icons md-18 align-middle">error_outline</i> ${el}</p>`;
                        });
                    }
                    $('[errdiv]').html(e);

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


        var ff = $('#ffilter');

        let timer;
        ff.change(function(e) {
            clearTimeout(timer);
            var e = $(e.target);
            timer = setTimeout(() => {
                getData();
            }, 100);
        });

        var date1 = '{{ request('date1') }}';
        var date2 = '{{ request('date2') }}';
        var item = '{{ request('el') }}';
        var fuel = '{{ request('fuel') }}';
        if (date1.length) {
            $('[name="date1"]')[0]._flatpickr.setDate(date1, true);
        }
        if (date2.length) {
            $('[name="date2"]')[0]._flatpickr.setDate(date2, true);
        }
        if (item.length) {
            $('[name="items"]').val(item).change();
        }
        if (fuel.length) {
            $('[name="fuel[]"]').val([fuel]).change();
            $('[name="fuel[]"]').multiselect('refresh');
        }

        getData();
    </script>
@endsection
