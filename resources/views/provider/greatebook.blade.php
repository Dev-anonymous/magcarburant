@extends('layouts.app')
@section('title', 'Grand livre')
@section('bg-class', 'bg-img-3')
@section('body')
    <div class="container-fluid">
        <div class="d-flex justify-content-between">
            <div class="">
                <h2 class="font-weight-bold">Grand livre</h2>
                <p class="lead small m-0">Grand livre des ventes des prodtuis</p>
            </div>
            <div class="m-2">
                <button onclick="history.back()" class="btn btn-sm btn-light d-flex align-items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" height="12px" viewBox="0 -960 960 960" width="12px"
                        fill="#000">
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
                        <div class="row">
                            <div class="col-xs-12 col-sm-6">
                                <h4 class="card-title font-weight-bold">Grand livre</h4>
                            </div>
                            @php
                                $d = now()->startOfMonth()->toDateString();
                                $d2 = now()->toDateString();
                            @endphp
                            @if (auth()->user()->user_role === 'provider')
                                <div class="col-xs-12 col-sm-6 col-md-12">
                                    <form id="ffilter" class="form-inline filters-form pull-right" role="form">
                                        <input type="hidden" name="type" value="greatbook">
                                        {{-- <div class="form-group mb-1">
                                            <div class="">
                                                <label class="justify-content-start" for="dv222">Du</label>
                                                <input class="form-control flatpickr2" id="dv222"
                                                    value="{{ $d }}" name="date1" style="width:100px" />
                                            </div>
                                        </div>
                                        <div class="form-group mb-1">
                                            <div>
                                                <label class="justify-content-start" for="dv22">Au</label>
                                                <input class="form-control flatpickr2" id="dv22"
                                                    value="{{ $d2 }}" name="date2" style="width:100px" />
                                            </div>
                                        </div> --}}
                                        <div class="form-group mb-1">
                                            <div>
                                                <label class="justify-content-start" for="">Structure de
                                                    prix</label>
                                                <select name="structure" class="form-control">
                                                    @foreach ($ps as $e)
                                                        @php
                                                            $au = $e->to;
                                                            if ($au) {
                                                                $au = "au {$au->format('d-m-Y')}";
                                                            } else {
                                                                $au = '';
                                                            }
                                                        @endphp
                                                        <option value="{{ $e->id }}"
                                                            @if (request('stp') == $e->id) selected @endif>
                                                            {{ "$e->name : Du {$e->from?->format('d-m-Y')} $au" }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group mb-1">
                                            <div>
                                                <label class="justify-content-start" for="">Zone</label>
                                                <select name="zone" class="form-control">
                                                    <option value="">Toutes</option>
                                                    @foreach (mainWays() as $e)
                                                        <option @if (request('z') == $e) selected @endif>
                                                            {{ $e }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group mb-1">
                                            <div>
                                                <label class="justify-content-start">Produit</label>
                                                <select name="fuel" class="form-control">
                                                    <option value="">Tous</option>
                                                    @foreach (mainfuels() as $e)
                                                        <option @if (request('f') == $e) selected @endif>
                                                            {{ $e }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>
                    <x-dataloader />
                    <x-alert />
                    <div class="card-body" style="min-height: 100vh">
                        <div class="table-responsive" data></div>
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

    <style>
        .table td,
        .table th {
            padding: 4px !important;
        }

        .noneditable td {
            border-top: 2px solid #ccc !important;
            border-bottom: 2px solid #ccc !important;
        }
    </style>
    <script>
        $('#ffilter').change(function() {
            getData();
        })
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
                        var t = e.tag ? `tag="${e.tag}"` : '';
                        th += `<th ${t}>${e.label}</th>`;
                    });
                    var td = '';
                    data.rows.forEach(e => {
                        td += `<tr>`;
                        e.forEach(ee => {
                            td += `<td>${ee}</td>`;
                        });
                        td += '</tr>';
                    });

                    var h = `
                    <table table class="table table-striped table-hover text-nowrap display nowrap"
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
                            let table = $(this).DataTable();
                            table.columns().every(function(idx) {
                                let th = table.column(idx).header();
                                let tag = th.getAttribute('tag');
                                let visible = (tag === only || tag === null);
                                table.column(idx).visible(visible);
                            });
                        }
                    });

                    table = $table.DataTable({
                        dom: 'Blfrtip',
                        // responsive: true,
                        scrollX: true,
                        fixedColumns: {
                            leftColumns: 2
                        },
                        buttons: [{
                                extend: 'colvis',
                                text: 'Filtrer les paramètres',
                                collectionLayout: 'fixed four-column',
                                collectionTitle: 'Affichage des colonnes',
                            },
                            {
                                extend: 'excelHtml5',
                                title: 'Export Excel',
                            },
                        ],
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
