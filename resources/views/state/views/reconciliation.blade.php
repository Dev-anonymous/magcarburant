@extends('layouts.app')
@section('title', 'Réconciliation')
@section('bg-class', 'bg-img-3')
@section('body')
    <div class="container-fluid">
        <div class="d-flex justify-content-between">
            <div class="">
                <h2 class="font-weight-bold">Réconciliation des données : {{ $me->shortname }} - {{ $entity->shortname }}
                </h2>
                <p class="lead small m-0">Rapprochement des données que <b>VOUS</b> avez encodé et celles dont
                    <b>{{ $entity->shortname }}</b> a encodé
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
                    <div class="card transparent">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-xs-12 col-sm-6">
                                    <h4 class="card-title font-weight-bold">
                                        Tableaux de rapprochement : {{ $me->shortname }} - {{ $entity->shortname }}
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
                                    <input type="text" class="form-control flatpickr" id="dv222" name="date1"
                                        value="{{ $d }}" style="min-width:120px;">
                                </div>
                                <div class="form-group mb-1">
                                    <label for="dv22" class="control-label d-block mb-0">Au</label>
                                    <input type="text" class="form-control flatpickr" id="dv22" name="date2"
                                        value="{{ $d2 }}" style="min-width:120px;">
                                </div>
                                <div class="form-group mb-1">
                                    <label for="zone" class="control-label d-block mb-0">Item</label>
                                    <select id="item" name="item" multiple class="form-control"
                                        style="min-width:150px;">
                                        <option selected value="achat">Achats</option>
                                        <option selected value="vente">Ventes</option>
                                        <option selected value="livraison">Livraisons excédentaires</option>
                                        <option selected value="taux">Taux</option>
                                    </select>
                                </div>
                            </form>
                        </div>
                        <div class="py-4">
                            <div class="table-responsive">

                            </div>
                        </div>
                    </div>
                    <div class="card transparent">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-xs-12 col-sm-6">
                                    <h4 class="card-title font-weight-bold">
                                        Réconciliation des achats entre {{ auth()->user()->name }} et
                                        {{ $entity->shortname }}
                                    </h4>
                                </div>
                                <div class="col-xs-12 col-sm-6">

                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <x-dataloader />
                            <x-alert />
                            <div class="card-body" style="min-height: 300px">
                                <div data class="row"></div>
                                <div class="my-3 text-danger" errdiv></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <style>
        .bgred {
            font-weight: bold;
            background: #ccc;
        }

        .bodld {}
    </style>
    <x-datatable />
    <x-flatpickr />
    <x-select />

    <script>
        flatpickr(".flatpickr", {
            maxDate: "today",
            locale: {
                firstDayOfWeek: 1
            }
        });
        $('#item').multiselect({
            includeSelectAllOption: true,
            nonSelectedText: 'Aucun filtre',
            nSelectedText: 'zones sélectionnées',
            allSelectedText: 'Tous les items',
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
                dashboard();
            }, 100);
        });

        function dashboard() {
            var ldr = $('[dataloader]');
            ldr.show();
            var rep = $('#rep');

            $.ajax({
                url: '{{ route('reconciliation') }}',
                data: {
                    item: $('#item').val(),
                    entity_id: '{{ @$entity->id }}',
                    date: $('[name="date1"]').val() + ' to ' + $('[name="date2"]').val(),
                },
                success: function(data) {
                    var h = `
                    <div class='table-responsive'><div class='col-md-6'>
                    <table id="table" class="table table-striped table-hover text-nowrap" style="width:100%">
                    `;

                    h += '<thead>';
                    var tit = data.head || [];
                    tit.forEach(ttr => {
                        var tr = '<tr>';
                        ttr.map(th => {
                            var csp = th.colspan;
                            tr +=
                                `<th ${csp?`colspan=${csp}`:''} class="${th.class??""}">${th.label}</th>`
                        });
                        tr += '</tr>';
                        h += tr;
                    });
                    h += '</thead><tbody>';

                    data.body?.forEach(row => {
                        h += `<tr>`
                        row.map(e => {
                            h += `<td ${e?.title?'title="'+e?.title+'"':''}  ${e?.href?'href="'+e?.href+'"':''} class="${e.class??""}">${e.label}</td>`
                        })
                        h += '</tr>'
                    });

                    h += '</tbody></table></div></div>';

                    $('[data]').html(h);
                    $('[data]').css('opacity', 1);
                    rep.hide();

                    try {
                        $('#table').DataTable({
                            dom: 'Brt',
                            ordering: false,
                            buttons: [{
                                extend: 'excelHtml5',
                                title: 'Export Excel',
                                exportOptions: {
                                    format: {
                                        body: function(data, row, column, node) {
                                            if (!data) return data;
                                            let cleaned = data.toString().replace(/\s+/g,
                                                '');
                                            cleaned = cleaned.replace(',', '.');
                                            let num = parseFloat(cleaned);
                                            return isNaN(num) ? data : num;
                                        }
                                    }
                                }
                            }, ],
                        });
                    } catch (error) {
                        console.log(error);
                    }

                    $('.tooltip').remove();
                    $('td[title]').tooltip();
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

        dashboard();
    </script>
@endsection
