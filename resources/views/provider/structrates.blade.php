@extends('layouts.app')
@section('title', 'Taux Structures')
@section('bg-class', 'bg-img-3')
@section('body')
    <div class="container">
        <div class="d-flex justify-content-between">
            <div class="">
                <h2 class="font-weight-bold">Taux structure | {{ $entity->shortname }} </h2>
                <p class="lead small m-0">Gestion de taux structure</p>
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
                            Historique des taux
                        </h4>
                        <div class="">

                        </div>
                    </div>
                    <div class="py-4">
                        <div class="table-responsive">
                            <table id="table" class="table table-striped table-hover text-nowrap" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Structure</th>
                                        <th>Taux</th>
                                        <th>Date validité du</th>
                                        <th>Date validité au</th>
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

    <script>
        var dtObj = $('#table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route('tx-structure.index') }}',
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
                    name: 'name'
                },
                {
                    data: 'rate',
                    name: 'rate',
                    className: 'text-nowrap font-weight-bold',
                    orderable: false,
                    searchable: false,
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
                            let num = parseFloat(cleaned);
                            return isNaN(num) ? data : num;
                        }
                    }
                }
            }, ],
        }).on('draw.dt', function(e, settings, data, xhr) {

        });
    </script>
@endsection
