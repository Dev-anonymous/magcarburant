@extends('layouts.app')
@section('title', 'Analyse')
@section('body')
    <div class="container">
        <div class="d-flex justify-content-between">
            <div class="">
                <h2 class="font-weight-bold">Analyse</h2>
                <p class="lead small m-0">Analyse et Bilan de vente des prodtuis</p>
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
                <div class="carte">
                    <div class="w-100">
                        <form action="#" id="ffilter">
                            <input type="hidden" name="type" value="balance">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4 class="card-title font-weight-bold">
                                    Bilan d'analyse
                                </h4>
                                <div class="d-flex">
                                    @php
                                        $d = now()->startOfMonth()->toDateString() . ' to ' . now()->toDateString();
                                    @endphp
                                    <div class="mr-2">
                                        <label class="mb-0" for="dv22">Date</label>
                                        <input class="form-control flatpickr" value="{{ $d }}" name="date">
                                    </div>
                                    {{-- <div class="mr-2">
                                        <span for="">Devise</span>
                                        <select name="devise" id="devise" class="form-control">
                                            <option>CDF</option>
                                            <option>USD</option>
                                        </select>
                                    </div> --}}
                                    <div class="mr-2">
                                        <span for="">Zone</span>
                                        <select name="zone" class="form-control">
                                            @foreach (mainWays() as $e)
                                                <option>{{ $e }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mr-2">
                                        <span for="">Produit</span>
                                        <select name="fuel" class="form-control">
                                            @foreach (mainfuels() as $e)
                                                <option>{{ $e }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <x-dataloader />
                        <x-alert />
                        <div class="py-4">
                            <div class="table-responsive" data>

                            </div>
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
        flatpickr(".flatpickr", {
            maxDate: "today",
            mode: "range",
            locale: {
                firstDayOfWeek: 1
            }
        });



        $('#ffilter').change(function() {
            getData();
        })

        function getData() {
            var ldr = $('[dataloader]');
            ldr.show();

            var data = $('#ffilter').serialize();
            var rep = $('#rep');
            $.ajax({
                url: '{{ route('dashboard') }}',
                data: data,
                success: function(data) {
                    var k = Object.keys(data);
                    var h = '';
                    k.forEach(key => {
                        h += `
                    <table id="table" class="table table-striped table-bordered table-hover text-nowrap"
                        style="width:100%">
                        <thead>
                            <tr>
                                <td colspan=4>
                                    <div class='text-center p-4'>
                                        DATATITLE
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th>ITEM</th>
                                <th class='text-center'>Prix Structure de Prix</th>
                                <th class='text-center'>Volume Vendu M3</th>
                                <th class='text-center'>MONTANT</th>
                            </tr>
                        </thead>
                        <tbody>
                    `;
                        var d = data[key];

                        var li = null;
                        d.forEach(line => {
                            h += `
                            <tr>
                                <td>${line.label}</td>
                                <td class='text-center'>${line.struct_price}</td>
                                <td class='text-center'>${line.vol}</td>
                                <td class='text-center font-weight-bold'>${line.tot}</td>
                            </tr>
                                `
                            li = line;
                        });
                        h += `</tbody>
                        </table>`;
                        if (li) {
                            var ti = `<h4>${li.fuel} | ${li.date} | ZONE ${li.zone}</h4>`;
                            h = h.replace('DATATITLE', ti);
                        }
                    });

                    $('[data]').html(h);
                    $('[data]').css('opacity', 1);
                    rep.hide();
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
