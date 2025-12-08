@extends('layouts.app')
@section('title', 'Structure des prix')
@section('body')
    <div class="container-fluid">
        <div class="d-flex justify-content-between">
            <div class="">
                <h2 class="font-weight-bold">Voies et Structures des prix
                    | {{ $structure->entity->shortname }}
                </h2>
                <p class="lead small m-0">Gestion des structures des prix</p>
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
                <div class="bg-whitee d-flex justify-content-between align-items-center p-3">
                    <h4 class="text-dark font-weight-bold">
                        Structure
                        {{ "$structure->name du {$structure->from->format('d-m-Y')} " . (empty($structure->to) ? ' à maintenant' : " au {$structure->to->format('d-m-Y')}") }}
                    </h4>
                </div>
                <div class="card-body">
                    @foreach ($zones as $zone)
                        <div class="table-responsive d-flex">
                            <div class="carte autocalc m-2">
                                <div class="w-100" style="min-height: 820px">
                                    <p info class="mb-2 text-danger font-weight-bold text-right" style="display:none;">
                                        Vous pouvez maintenant modifier les prix
                                    </p>
                                    <h5 class="text-center font-weight-bold">ZONE {{ $zone->zone }}</h5>
                                    <h6 class="text-danger text-right">Les valeurs sont en USD</h6>
                                    <table class="table table-striped table-bordered table-hover" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th></th>
                                                <th></th>
                                                @foreach ($fuels as $fuel)
                                                    <th>{{ $fuel->fuel }}</th>
                                                @endforeach
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($labels as $label)
                                                @continue($zone->zone !== 'OUEST' && $label->tag === 'L')
                                                @php
                                                    $noedit = in_array($label->tag, noteditable());
                                                @endphp
                                                <tr tag="{{ $label->tag }}"
                                                    class="text-nowrap @if ($noedit) noneditable font-weight-bold @endif">
                                                    <td>{{ $label->tag }}</td>
                                                    <td>{{ $label->label }}</td>
                                                    @foreach ($fuels as $fuel)
                                                        @php
                                                            $price = optional(
                                                                $fuelprices[$zone->id][$fuel->id][$label->id] ?? null,
                                                            )->first();
                                                        @endphp
                                                        <td class="@if (!$noedit) editable-price @endif text-center @if (!$price) bg-danger @endif"
                                                            data-fuelprice-id="{{ $price->id }}"
                                                            data-zone="{{ $zone->zone }}" data-label="{{ $label->label }}"
                                                            data-tag="{{ $label->tag }}">
                                                            {{ $price->amount }}
                                                        </td>
                                                    @endforeach
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    <div class="">
                                        @if (empty($structure->to))
                                            @if (auth()->user()->user_role == 'provider')
                                                <div class="text-right">
                                                    <button class="btn btn-sm btn-edit-table">
                                                        <i class="material-icons md-18">edit</i>
                                                        Modifier les prix
                                                    </button>
                                                </div>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="carte m-2">
                                <div class="w-100" style="min-height: 820px">
                                    <h5 class="text-center font-weight-bold">ZONE {{ $zone->zone }}</h5>
                                    <h6 class="text-danger text-right">Les valeurs sont en CDF</h6>
                                    <table class="table table-striped table-bordered table-hover" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th></th>
                                                <th>{!! $tx
                                                    ? "<small class='text-danger'>1 USD = $tx->usd_cdf CDF</small>"
                                                    : "<small class='text-danger'>Aucun taux structure trouvé</small>" !!}</th>
                                                @foreach ($fuels as $fuel)
                                                    <th>{{ $fuel->fuel }}</th>
                                                @endforeach
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($labels as $label)
                                                @continue($zone->zone !== 'OUEST' && $label->tag === 'L')
                                                @php
                                                    $noedit = in_array($label->tag, noteditable());
                                                @endphp
                                                <tr tag="{{ $label->tag }}"
                                                    class="text-nowrap @if ($noedit) noneditable font-weight-bold @endif">
                                                    <td>{{ $label->tag }}</td>
                                                    <td>{{ $label->label }}</td>
                                                    @foreach ($fuels as $fuel)
                                                        @php
                                                            $price = optional(
                                                                $fuelprices[$zone->id][$fuel->id][$label->id] ?? null,
                                                            )->first();
                                                        @endphp
                                                        <td class="@if (!$noedit) editable-price @endif text-center @if (!$price) bg-danger @endif"
                                                            data-fuelprice-id="{{ $price->id }}"
                                                            data-zone="{{ $zone->zone }}"
                                                            data-label="{{ $label->label }}"
                                                            data-tag="{{ $label->tag }}">
                                                            @php
                                                                $v = $price->amount * (float) @$tx->usd_cdf;
                                                            @endphp
                                                            {{ $v ? $v : '' }}
                                                        </td>
                                                    @endforeach
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

    </div>
@endsection
@section('modals')
    <div class="modal fade" id="mdlinfo" role="dialog">
        <div class="modal-dialog modal-fullscreen" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-uppercase" id="defaultModalLabel">Structure des prix en CDF</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form finfo="">
                    <input type="hidden" name="structure">
                    <input type="hidden" name="zone">
                </form>
                <div class="modal-body">
                    <x-dataloader />
                    <x-alert />
                    <div class="w-100" repdata>
                        <form formfilter="">
                            <div class="d-flex">
                                <div class="mr-2">
                                    <span for="">Produit</span>
                                    <select name="fuel" class="form-control">
                                        @foreach (mainfuels() as $e)
                                            <option>{{ $e }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mr-2">
                                    <span for="">Au taux</span>
                                    <select name="ratetype" class="form-control">
                                        <option>RÉEL</option>
                                        <option>STRUCTURE</option>
                                    </select>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="table-responsiver mt-3">
                        <div data=""></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn" data-dismiss="modal">
                        <i class="material-icons md-18 mr-1 m-0 p-0">highlight_off</i>
                        Fermer
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <x-datatable />
    <x-flatpickr />

    <style>
        .modal-dialog.modal-fullscreen {
            width: 100% !important;
            max-width: 100% !important;
            height: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        .modal-dialog.modal-fullscreen .modal-content {
            height: 100% !important;
            border-radius: 0 !important;
        }

        .modal-dialog.modal-fullscreen .modal-body {
            overflow-y: auto !important;
        }
    </style>

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
        $('.btn-edit-table').on('click', function() {
            let card = $(this).closest('.carte');
            card.find('.editable-price').attr('contenteditable', 'true');
            card.find('.editable-price').css('background-color', '#fff8c4');

            var info = card.find('[info]');

            $('html, body').stop().animate({
                scrollTop: card.offset().top - 10
            }, 800, function() {
                info.stop().fadeIn();
                setTimeout(() => {
                    info.fadeOut();
                }, 5000);
            });
        });

        var formfilter = $('[formfilter]');

        // formfilter.change(() => loadpt());
        // function loadpt() {
        //     var mdl = $('#mdlinfo');
        //     var ldr = $('[dataloader]', mdl);
        //     var rep = $('#rep', mdl);
        //     var form = $('[finfo]', mdl);
        //     var data = form.serialize() + '&' + formfilter.serialize();
        //     ldr.show();
        //     $.ajax({
        //         url: `route('pricestructure') }}`,
        //         data: data,
        //         success: function(data) {
        //             var k = Object.keys(data);
        //             var h = '';
        //             k.forEach(key => {
        //                 h += `
    //             <table id="table" class="table table-striped table-bordered table-hover text-nowrap"
    //                 style="width:100%">
    //                 <thead>
    //                     <tr>
    //                         <td colspan=4>
    //                             <div class='text-center p-4'>
    //                                 DATATITLE
    //                             </div>
    //                         </td>
    //                     </tr>
    //                     <tr>
    //                         <th>ITEM</th>
    //                         <th class='text-center'>Prix Structure de Prix</th>
    //                         <th class='text-center'>Volume Vendu M3</th>
    //                         <th class='text-center'>MONTANT</th>
    //                     </tr>
    //                 </thead>
    //                 <tbody>
    //             `;
        //                 var d = data[key];

        //                 var li = null;
        //                 d.forEach(line => {
        //                     h += `
    //                     <tr>
    //                         <td>${line.label}</td>
    //                         <td class='text-center'>${line.struct_price}</td>
    //                         <td class='text-center'>${line.vol}</td>
    //                         <td class='text-center font-weight-bold'>${line.tot}</td>
    //                     </tr>
    //                         `
        //                     li = line;
        //                 });
        //                 h += `</tbody>
    //                 </table>`;
        //                 if (li) {
        //                     var ti = `<h4>${li.fuel} | ${li.date}</h4>`;
        //                     h = h.replace('DATATITLE', ti);
        //                 }
        //             });

        //             $('[data]', mdl).html(h);
        //             $('[repdata]').css('opacity', 1);
        //             ldr.hide();
        //             rep.hide();

        //         },
        //         error: function(xhr, a, b) {
        //             ldr.hide();
        //             var resp = xhr.responseJSON;
        //             var mess = resp?.message ?? "Erreur, veuillez réessayer !";
        //             rep.html(mess).stop().removeClass().addClass(
        //                     'p-1 m-0 text-center alert alert-danger')
        //                 .show();
        //             $('[repdata]').css('opacity', 0.1);
        //         }
        //     });
        // }

        const formulas = {
            D: "C-E-F-G-H-I-J",
            O: "A+B+C+K+L+M+N+P",
            W: "T+U+V",
            X: "S-V",
            Y: "W+X",
            Z: "A+O+Q+Y",
            AA: "Z*1000",
        };

        function getRowValues(tag, table) {
            return $(`tr[tag="${tag}"] td`, table).slice(2).map((i, td) => parseFloat($(td).text()) || 0).get();
        }

        function setRowValues(tag, values, table) {
            const row = $(`tr[tag="${tag}"] td`, table).slice(2);
            row.each(function(i) {
                $(this).text(values[i].toFixed(2));
            });
        }

        function calculate() {
            $('.carte').each(function(i, carte) {
                var table = $('table', $(carte));
                const rows = {}; // stocke toutes les lignes lues
                // Lire toutes les lignes de A à Z (ou celles existantes)
                $('tbody tr', table).each(function() {
                    const tag = $(this).attr('tag');
                    rows[tag] = getRowValues(tag, table);
                });

                // Parcourir les formules
                Object.keys(formulas).forEach(tag => {
                    const formula = formulas[tag]; // ex: "C-A-B"
                    const operators = formula.match(/[\+\-\*\/]/g) || []; // ["-", "-"]
                    const operands = formula.split(/[\+\-\*\/]/); // ["C","A","B"]

                    const length = rows[operands[0]].length;
                    const result = [];
                    for (let i = 0; i < length; i++) {
                        let value = rows[operands[0]] ?
                            parseFloat(rows[operands[0]][i]) || 0 :
                            parseFloat(operands[0]) || 0;

                        for (let j = 1; j < operands.length; j++) {
                            const op = operators[j - 1];
                            const val = rows[operands[j]] ?
                                parseFloat(rows[operands[j]][i]) || 0 :
                                parseFloat(operands[j]) || 0;

                            if (!rows[operands[j]]) {
                                // console.log(rows, '---', operands, '---', j, operands[j], '==');
                            }

                            if (op === "+") value += val;
                            else if (op === "-") value -= val;
                            else if (op === "*") value *= val;
                            else if (op === "/") value /= val;
                        }
                        result.push(value);
                    }

                    setRowValues(tag, result, table);
                    rows[tag] = result; // mise à jour pour les formules suivantes
                    $(`tr[tag="${tag}"]`, table).attr('title', `${tag}=${formula}`).off('tooltip')
                        .tooltip();
                });
            });
        }
        calculate();

        $('.carte.autocalc table td[contenteditable="true"]').on('input', calculate);

        $('.editable-price').on('blur', function() {
            let td = $(this);
            let price = td.text().trim();
            let fuelpriceId = td.data('fuelprice-id');
            let zoneName = td.data('zone');
            let labelName = td.data('label');
            let tag = td.data('tag');

            function isNumeric(value) {
                return value !== null && value !== '' && !isNaN(value);
            }
            if (!price) {
                td.css('background-color', '#fff8c4');
                return;
            }

            if (!isNumeric(price)) {
                td.css('background-color', '#ffb3b3');
                alert(`Valeur "${price}" est invalide sur la zone ${zoneName}, [${tag}] ${labelName}`);
                return;
            }

            calculate();

            td.css('background-color', 'lightblue');
            td.css('opacity', '0.5');

            $.ajax({
                url: `{{ route('fuelprice.index') }}/${fuelpriceId}`,
                type: 'PUT',
                data: {
                    price
                },
                success: function(response) {
                    td.css({
                        'background-color': '#54ee78',
                        'opacity': '1'
                    });
                    setTimeout(() => td.css('background-color', '#fff8c4'), 2000);
                },
                error: function(err) {
                    td.css({
                        'background-color': '#ffb3b3',
                        'opacity': '1'
                    });
                    var m = err.responseJSON?.message;
                    alert(
                        `Erreur enregistrement sur la zone ${zoneName}, [${tag}] ${labelName} ${m? ": "+m : ''}. (Pensez à vérifier votre connexion internet) `
                    );
                }
            });
        });
    </script>
@endsection
