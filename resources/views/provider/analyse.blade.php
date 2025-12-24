@extends('layouts.app')
@section('title', 'Analyse')
@section('bg-class', 'bg-img-3')
@section('body')
    <div class="container">
        <div class="d-flex justify-content-between">
            <div class="">
                <h2 class="font-weight-bold">Analyse</h2>
                <p class="lead small m-0">Analyse et Bilan de vente des prodtuis</p>
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
                    {{-- <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title font-weight-bold">
                            Bilan d'analyse
                        </h4>
                        <div class="d-flex">
                            @php
                                $d = now()->startOfMonth()->toDateString();
                                $d2 = now()->toDateString();
                            @endphp
                            <div class="mr-2">
                                <label class="mb-0" for="dv222">Du</label>
                                <input class="form-control flatpickr2" id="dv222" value="{{ $d }}"
                                    name="date1" style="width: 100px">
                            </div>
                            <div class="mr-2">
                                <label class="mb-0" for="dv22">Au</label>
                                <input class="form-control flatpickr2" id="dv22" value="{{ $d2 }}"
                                    name="date2" style="width: 100px">
                            </div>
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
                    </div> --}}
                    <div class="card-header mb-3">
                        <div class="row">
                            <div class="col-xs-12 col-sm-6">
                                <h4 class="card-title font-weight-bold">Bilan d'analyse</h4>
                            </div>@php
                                $d = now()->startOfMonth()->toDateString();
                                $d2 = now()->toDateString();
                            @endphp
                            @if (auth()->user()->user_role === 'provider')
                                <div class="col-xs-12 col-sm-6 col-md-12">
                                    <form id="ffilter" class="form-inline filters-form pull-right" role="form">
                                        <input type="hidden" name="type" value="balance">
                                        <div class="form-group mb-1">
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
                                        </div>
                                        <div class="form-group mb-1">
                                            <div>
                                                <label class="justify-content-start" for="">Type</label>
                                                <select name="fuel_type" class="form-control select2">
                                                    <option>TERRESTRE</option>
                                                    <option>AVIATION</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group mb-1">
                                            <div>
                                                <label class="justify-content-start" for="">Zone</label>
                                                <select name="zone" class="form-control"></select>
                                            </div>
                                        </div>
                                        <div class="form-group mb-1">
                                            <div>
                                                <label class="justify-content-start">Produit</label>
                                                <select name="fuel" class="form-control"></select>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            @endif
                        </div>
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
    <style>
        .noneditable td {
            border-top: 2px solid #ccc !important;
            border-bottom: 2px solid #ccc !important;
        }
    </style>
    <script>
        flatpickr(".flatpickr2", {
            maxDate: "today",
            locale: {
                firstDayOfWeek: 1
            }
        });
        var ldr = $('[dataloader]');

        var ff = $('#ffilter');

        ff.change(function(e) {
            var e = $(e.target)
            if (e.attr('name') == 'fuel_type') {
                getExtra();
            } else {
                getData();
            }
        });

        function getExtra() {
            var sel = $('[name="fuel_type"]');
            var o = '';
            var o2 = '';

            if (sel.val() == 'TERRESTRE') {
                ['NORD', 'SUD', 'EST', 'OUEST'].forEach((e) => {
                    o += `<option>${e}</option>`;
                });
                ['ESSENCE', 'PETROLE', 'GASOIL', 'FOMI'].forEach((e) => {
                    o2 += `<option>${e}</option>`;
                });
            }
            if (sel.val() == 'AVIATION') {
                ['SUD', 'EST', 'OUEST'].forEach((e) => {
                    o += `<option>${e}</option>`;
                });
                ['JET'].forEach((e) => {
                    o2 += `<option>${e}</option>`;
                });
            }
            var sz = $('[name=zone]').html(o);
            var sf = $('[name=fuel]').html(o2);

            try {
                sz.select2('destroy');
            } catch (error) {}
            sz.select2();

            try {
                sf.select2('destroy');
            } catch (error) {}
            sf.select2();
            getData();
        }

        getExtra();

        function getData() {
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
                    <table id="table" class="table table-striped table-hover text-nowrap"
                        style="width:100%">
                        <thead>
                            <tr>
                                <td colspan=4>
                                    <div class='text-center p-2'>
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
                        var url =
                            '{!! route('provider.accounting', [
                                'item' => 'gb',
                                'stp' => 'STRUCTURENAME',
                                'f' => 'FUELNAME',
                                'z' => 'ZONENAME',
                                'tag' => 'TAGNAME',
                            ]) !!}';
                        d.forEach(line => {
                            var u = url.split('STRUCTURENAME').join(line.struct_price_id)
                                .split('FUELNAME').join(line.fuel)
                                .split('ZONENAME').join(line.zone)
                                .split('TAGNAME').join(line.tag);
                            h += `
                            <tr style="cursor:pointer" onclick="location.assign('${u}')" >
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

        // getData();
    </script>
@endsection
