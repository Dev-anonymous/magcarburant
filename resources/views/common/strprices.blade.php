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
                <div class="bg-whitee d-flex justify-content-between align-items-center">
                    <h4 class="text-dark font-weight-bold">
                        Structure
                        {{ "$structure->name du {$structure->from->format('d-m-Y')} " . (empty($structure->to) ? ' à maintenant' : " au {$structure->to->format('d-m-Y')}") }}
                    </h4>
                </div>
                <div class="card-body">
                    @foreach ($grouped as $type => $zones)
                        <div class="row no-gutter">
                            <div class="col-12">
                                <b class="text-white bold">CARBURANT {{ strtoupper($type) }}</b>
                            </div>
                            @foreach ($zones as $zoneName => $fuels)
                                @php
                                    $rnd = rand(10000, 90000);
                                @endphp
                                <div class="col-md-6 mb-3">
                                    <div class="carte m-2 d-block" type="{{ $type }}" zone="{{ $zoneName }}">
                                        <div class="div_a_{{ $rnd }}">
                                            <p info class="mb-2 text-danger font-weight-bold text-right"
                                                style="display:none;">
                                                Vous pouvez maintenant modifier les prix
                                            </p>
                                            <h5 class="text-center font-weight-bold">ZONE {{ $zoneName }}</h5>
                                            <h6 class="text-danger text-right">Les valeurs sont en USD</h6>
                                            <div class="table-responsive">
                                                <table class="table table-striped table-hover" style="width:100%">
                                                    <thead>
                                                        <tr>
                                                            <th></th>
                                                            <th></th>
                                                            @foreach ($fuels as $fuelName => $labels)
                                                                <th>{{ $fuelName }}</th>
                                                            @endforeach
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @php
                                                            // Récupérer tous les labels uniques pour cette zone
                                                            $allLabels = [];
                                                            foreach ($fuels as $labels) {
                                                                foreach ($labels as $labelName => $data) {
                                                                    $allLabels[$labelName] = $data['tag']; // associer label → tag
                                                                }
                                                            }
                                                        @endphp

                                                        @foreach ($allLabels as $labelName => $tag)
                                                            @php
                                                                $noedit = in_array(
                                                                    $labelName,
                                                                    noteditable($type, $zoneName),
                                                                );
                                                            @endphp
                                                            <tr tag="{{ $tag }}"
                                                                class="text-nowrap @if ($noedit) noneditable font-weight-bold @endif">
                                                                <td>{{ $tag }}</td>
                                                                <td class="text-nowrap">{{ $labelName }}</td>
                                                                @foreach ($fuels as $fuelName => $labels)
                                                                    @php
                                                                        $v = $labels[$labelName]['amount'] ?? 0;
                                                                        $fpi = $labels[$labelName]['id'] ?? 0;
                                                                    @endphp
                                                                    <td class="@if (!$noedit) editable-price @endif text-center @if (!$fpi) bg-danger @endif"
                                                                        data-fuelprice-id="{{ $fpi }}"
                                                                        data-zone="{{ $zoneName }}"
                                                                        data-label="{{ $labelName }}"
                                                                        data-tag="{{ $tag }}">{{ $v ? $v : '' }}
                                                                    </td>
                                                                @endforeach
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
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
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="carte m-2 d-block" type="{{ $type }}" zone="{{ $zoneName }}">
                                        <div class="div_b_{{ $rnd }}">
                                            <h5 class="text-center font-weight-bold">ZONE {{ $zoneName }}</h5>
                                            <h6 class="text-danger text-right">Les valeurs sont en CDF</h6>
                                            <div class="table-responsive">
                                                <table class="table table-striped table-hover" style="width:100%">
                                                    <thead>
                                                        <tr>
                                                            <th></th>
                                                            <th>
                                                                <small class='text-danger'>
                                                                    1 USD = {{ $structure->usd_cdf ?? 0 }} CDF
                                                                </small>
                                                            </th>
                                                            @foreach ($fuels as $fuelName => $labels)
                                                                <th>{{ $fuelName }}</th>
                                                            @endforeach
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @php
                                                            // Récupérer tous les labels uniques pour cette zone
                                                            $allLabels = [];
                                                            foreach ($fuels as $labels) {
                                                                foreach ($labels as $labelName => $data) {
                                                                    $allLabels[$labelName] = $data['tag']; // associer label → tag
                                                                }
                                                            }
                                                        @endphp

                                                        @foreach ($allLabels as $labelName => $tag)
                                                            @php
                                                                $noedit = in_array(
                                                                    $labelName,
                                                                    noteditable($type, $zoneName),
                                                                );
                                                            @endphp
                                                            <tr tag="{{ $tag }}"
                                                                class="text-nowrap @if ($noedit) noneditable font-weight-bold @endif">
                                                                <td>{{ $tag }}</td>
                                                                <td class="text-nowrap">{{ $labelName }}</td>
                                                                @foreach ($fuels as $fuelName => $labels)
                                                                    @php
                                                                        $v = $labels[$labelName]['amount'] ?? 0;
                                                                        $v *= (float) $structure->usd_cdf;
                                                                    @endphp
                                                                    <td>{{ $v ? $v : '' }}</td>
                                                                @endforeach
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endforeach


                    {{-- @foreach ($zones = [] as $zone)
                            <div class="col-md-6 mb-3">
                                <div class="carte autocalc m-2">
                                    <div class="w-100" style="min-height: 820px">
                                        <p info class="mb-2 text-danger font-weight-bold text-right" style="display:none;">
                                            Vous pouvez maintenant modifier les prix
                                        </p>
                                        <h5 class="text-center font-weight-bold">ZONE {{ $zone->zone }}</h5>
                                        <h6 class="text-danger text-right">Les valeurs sont en USD</h6>
                                        <div class="table-responsive">
                                            <table class="table table-striped table-hover" style="width:100%">
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
                                                                        $fuelprices[$zone->id][$fuel->id][$label->id] ??
                                                                            null,
                                                                    )->first();
                                                                @endphp
                                                                <td class="@if (!$noedit) editable-price @endif text-center @if (!$price) bg-danger @endif"
                                                                    data-fuelprice-id="{{ $price->id }}"
                                                                    data-zone="{{ $zone->zone }}"
                                                                    data-label="{{ $label->label }}"
                                                                    data-tag="{{ $label->tag }}">
                                                                    {{ $price->amount }}
                                                                </td>
                                                            @endforeach
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>

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
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="carte m-2 d-block">
                                    <div class="w-100" style="min-height: 820px">
                                        <h5 class="text-center font-weight-bold">ZONE {{ $zone->zone }}</h5>
                                        <h6 class="text-danger text-right">Les valeurs sont en CDF</h6>
                                        <div class="table-responsive">
                                            <table class="table table-striped table-hover" style="width:100%">
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
                                                                        $fuelprices[$zone->id][$fuel->id][$label->id] ??
                                                                            null,
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
                            </div>
                        @endforeach --}}
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

        .table td,
        .table th {
            padding: 4px !important;
        }

        .noneditable td {
            border-top: 2px solid #ccc !important;
            border-bottom: 2px solid #ccc !important;
        }

        .col-md-6 {
            padding-left: 5px;
            padding-right: 5px;
        }

        .row.no-gutter>[class*="col-"] {
            padding-left: 5px;
            padding-right: 5px;
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

        $('[class^="div_a_"]').each(function() {
            var divA = $(this);
            // Récupérer le suffixe (par exemple "001", "002", ...)
            var classes = divA.attr('class').split(/\s+/);
            var suffix = '';
            $.each(classes, function(i, cls) {
                if (cls.startsWith('div_a_')) {
                    suffix = cls.replace('div_a_', '');
                }
            });
            // Trouver le div_b correspondant
            var divB = $('.div_b_' + suffix);
            // Appliquer la hauteur
            if (divB.length) {
                divB.height(divA.height());
            }
        });

        const Glfomulas = {
            terrestre_zone_nord: {
                S: "J",
                V: "T+U",
                AD: "W+X+AA+AB",
                AJ: "AG+AH+AI",
                AK: "AF-AI",
                AL: "AJ+AK",
                AM: "G+S+V+AD+AL",
                AN: "AM/1000"
            },
            terrestre_zone_sud: {
                S: "O",
                V: "T+U",
                AD: "W+X+AA+AB+Y+AC",
                AJ: "AG+AH+AI",
                AK: "AF-AI",
                AL: "AJ+AK",
                AM: "G+S+V+AD+AL",
                AN: "AM/1000"
            },
            terrestre_zone_est: {
                S: "O+M",
                V: "T+U",
                AD: "W+X+AA+AB+Y+AC",
                AJ: "AG+AH+AI",
                AK: "AF-AI",
                AL: "AJ+AK",
                AM: "G+S+V+AD+AL",
                AN: "AM/1000"
            },
            terrestre_zone_ouest: {
                S: "H+I+K+N",
                V: "T+U",
                AD: "W+X+Y+Z+AA+AB+AC",
                AJ: "AG+AH+AI",
                AK: "AF-AI",
                AL: "AJ+AK",
                AM: "G+S+V+AD+AL",
                AN: "AM/1000"
            },
            aviation_zone_sud: {
                S: "P",
                V: "T+U",
                AJ: "AG+AH+AI",
                AK: "AF-AI",
                AL: "AJ+AK",
                AM: "G+S+V+AL",
                AN: "AM/1000"
            },
            aviation_zone_est: {
                S: "P",
                V: "T+U",
                AJ: "AG+AH+AI",
                AK: "AF-AI",
                AL: "AJ+AK",
                AM: "G+S+V+AL",
                AN: "AM/1000"
            },
            aviation_zone_ouest: {
                S: "R+Q+L",
                V: "T+U",
                AJ: "AG+AH+AI",
                AK: "AF-AI",
                AL: "AJ+AK",
                AM: "G+S+V+AL",
                AN: "AM/1000"
            }
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
                var carte = $(carte);
                var table = $('table', carte);
                var type = carte.attr('type');
                var zone = carte.attr('zone').toString().toLowerCase();
                const rows = {}; // stocke toutes les lignes lues
                // Lire toutes les lignes de A à Z (ou celles existantes)
                $('tbody tr', table).each(function() {
                    const tag = $(this).attr('tag');
                    rows[tag] = getRowValues(tag, table);
                });

                var ind = `${type}_zone_${zone}`;
                var formulas = Glfomulas[ind];
                if (!formulas) {
                    console.error("FORMULE NON TROUVEE ->" + ind);
                    return;
                }

                // Parcourir les formules
                Object.keys(formulas).forEach(tag => {
                    const formula = formulas[tag]; //
                    const operators = formula.match(/[\+\-\*\/]/g) || []; // ["-", "-"]
                    const operands = formula.split(/[\+\-\*\/]/); // ["C","A","B"]

                    const length = rows[operands[0]]?.length || 0;
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

                    // console.log(formula, length);

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
