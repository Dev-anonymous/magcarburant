@extends('layouts.app')
@section('title', 'Audits')
@section('bg-class', 'bg-img-3')
@section('body')
    <div class="container-wide">
        <div class="d-flex justify-content-between">
            <div class="">
                <h2 class="font-weight-bold">Audits systèmes</h2>
                <p class="lead small m-0">Toutes les modifications apportées a votre compte seront enregistrées ici. </p>
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
                                <label for="evente" class="control-label d-block mb-0">Evènement</label>
                                <select name="event[]" id="evente" class="form-control" multiple
                                    style="min-width:150px;">
                                    @foreach (logEvents() as $el)
                                        <option selected value="{{ $el->value }}">{{ $el->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </form>
                    </div>
                    <div class="card-body" style="min-height: 300px">
                        <div class="table-responsive">
                            <table id="table" class="table table-striped table-hover" style="width:100%">
                                <thead>
                                    <tr>
                                        <th class="text-nowrap">Date</th>
                                        <th class="text-nowrap">Utilisateur</th>
                                        <th class="text-nowrap">Evènement</th>
                                        <th class="text-nowrap">Anciennes données</th>
                                        <th class="text-nowrap">Nouvelles données</th>
                                        <th class="text-nowrap">Adresse IP</th>
                                        <th class="text-nowrap">Navigateur</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
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

    <script>
        flatpickr(".flatpickr", {
            maxDate: "today",
            locale: {
                firstDayOfWeek: 1
            }
        });
        var ff = $('#ffilter');

        let timer;
        ff.change(function(e) {
            clearTimeout(timer);
            var e = $(e.target);
            timer = setTimeout(() => {
                dtObj.ajax.reload(null, false);
            }, 100);
        });

        $('[name="event[]"]').multiselect({
            includeSelectAllOption: true,
            nonSelectedText: 'Aucun filtre',
            nSelectedText: 'Evènements sélectionnés',
            allSelectedText: 'Toutes les zones',
            numberDisplayed: 1, // affiche 1 élément puis "n zones sélectionnées"
            selectAllText: 'Tous',
            buttonWidth: '100%',
            buttonClass: 'btn btn-primary'
        });

        var dtObj = $('#table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route('audit.index') }}',
                data: function(d) {
                    d.date = $('[name="date1"]').val() + ' to ' + $('[name="date2"]').val();
                    d.event = $('[name="event[]"]').val();
                }
            },
            order: [
                [0, "desc"]
            ],
            columnDefs: [{
                targets: 0,
                width: '1%'
            }, {
                targets: 3,
                width: '1%'
            }],
            columns: [{
                    data: 'created_at',
                    name: 'created_at',
                    className: 'text-nowrap',
                },
                {
                    data: 'username',
                    name: 'username',
                    className: 'text-nowrap font-weight-bold',
                },
                {
                    data: 'event',
                    name: 'event',
                    className: '',
                },
                {
                    data: 'old_values',
                    name: 'old_values',
                    className: '',
                },
                {
                    data: 'new_values',
                    name: 'new_values',
                    className: '',
                },
                {
                    data: 'ip_address',
                    name: 'ip_address',
                    className: '',
                },
                {
                    data: 'user_agent',
                    name: 'user_agent',
                    className: 'text-nowrap',
                },
            ],
            dom: 'Blfrtip',
            buttons: [{
                extend: 'excelHtml5',
                title: 'Export Excel',
                exportOptions: {
                    columns: ':not(.no-export)',
                    format: {
                        body: function(data, row, column, node) {
                            if (!data) return data;
                            let cleaned = data.toString().replace(/\s+/g,
                                '');
                            cleaned = cleaned.replace(',', '.');
                            let num = Number(cleaned);
                            return isNaN(num) ? data : num;
                        }
                    }
                }
            }, ],
        }).on('draw.dt', function(e, settings, data, xhr) {

        });
    </script>
@endsection
