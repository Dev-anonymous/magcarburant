@extends('layouts.app')
@section('title', 'Structure des prix')
@section('body')
    <div class="container">
        <div class="d-flex justify-content-between">
            <div class="">
                <h2 class="font-weight-bold">Structures des prix | {{ $entity->shortname }}
                </h2>
                <p class="lead small m-0">Historique des structures des prix pour {{ $entity->shortname }}</p>
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
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title font-weight-bold">
                            Historique des structures des prix
                        </h4>

                    </div>
                    <div class="py-4">
                        <div class="table-responsive">
                            <table id="table" class="table table-striped table-hover text-nowrap" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>ID </th>
                                        <th>Structure</th>
                                        <th>Taux Structure</th>
                                        <th>Date validité du</th>
                                        <th>Date validité au</th>
                                        <th class="no-export"></th>
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

@section('script')
    <x-datatable />
    <x-flatpickr />

    <script>
        flatpickr(".flatpickr", {
            maxDate: "today",
            locale: {
                firstDayOfWeek: 1
            }
        });

        var dtObj = $('#table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route('structureprice.index') }}',
                data: function(d) {
                    d.entity_id = '{{ @$entity->id }}'
                }
            },
            order: [
                [0, "desc"]
            ],
            columnDefs: [{
                targets: 0,
                width: '1%'
            }, {
                targets: 4,
                width: '1%'
            }],
            columns: [{
                    data: 'id',
                    name: 'id',
                },
                {
                    data: 'name',
                    name: 'name',
                    className: 'text-nowrap',
                },
                {
                    data: 'tx',
                    name: 'tx',
                    orderable: false,
                    searchable: false,
                    className: 'text-nowrap',
                },
                {
                    data: 'from',
                    name: 'from',
                    className: 'text-nowrap',
                },
                {
                    data: 'to',
                    name: 'to',
                    className: 'text-nowrap',
                },
                {
                    data: 'view',
                    name: 'view',
                    orderable: false,
                    searchable: false,
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
                            let num = parseFloat(data.toString().replace(/ /g,
                                '').replace(',', '.'));
                            return isNaN(num) ? data : num;
                        }
                    }
                }
            }, ],
        }).on('draw.dt', function(e, settings, data, xhr) {

        });
    </script>
@endsection
