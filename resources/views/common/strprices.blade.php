@extends('layouts.app')
@section('title', 'Structure des prix')
@section('body')
    <div class="container">
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
                <div class="bg-white d-flex justify-content-between align-items-center">
                    <h4 class="text-muted font-weight-bold">
                        Structure
                        {{ "$structure->name du {$structure->from->format('d-m-Y')} " . (empty($structure->to) ? ' à maintenant' : " au {$structure->to->format('d-m-Y')}") }}
                    </h4>
                </div>
                <div class="py-4">
                    @foreach ($zones as $zone)
                        <div class="carte">
                            <div class="w-100">
                                <div class="table-responsive p-1">
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
                                                @continue($zone->zone !== 'OUEST' && $label->tag === 'H')
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
                                </div>
                                @if (empty($structure->to))
                                    @if (auth()->user()->user_role == 'provider')
                                        <div class="p-2 text-right">
                                            <button class="btn btn-sm btn- btn-edit-table">
                                                <i class="material-icons md-18">edit</i>
                                                Modifier les prix
                                            </button>
                                        </div>
                                    @endif
                                @endif
                            </div>
                        </div>
                    @endforeach
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

        const formulas = {
            D: "C-E-F",
            K: "A+B+C+G+H+I+J+L",
            S: "P+Q+R",
            T: "O-R",
            U: "S+T",
            V: "A+K+M+U",
            W: "V*1000",
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

        $('.carte table td[contenteditable="true"]').on('input', calculate);

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
                        `Erreur enregistrement sur la zone ${zoneName}, [${tag}] ${labelName} ${m? ": "+m : ''}`
                    );
                }
            });
        });
    </script>
@endsection
