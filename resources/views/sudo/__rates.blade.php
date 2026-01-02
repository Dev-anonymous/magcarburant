@extends('layouts.app')
@section('title', 'Gestion des taux réels')
@section('body')
    <div class="container">
        <div class="d-flex justify-content-between">
            <div class="">
                <h2 class="font-weight-bold">Taux Réels | {{ $entity->shortname }} </h2>
                <p class="lead small m-0">Gestion des taux réels</p>
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
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title font-weight-bold">
                            Historique des taux
                        </h4>
                    </div>
                    <div class="py-4">
                        <div class="table-responsive">
                            <table id="table" class="table table-striped table-hover text-nowrap" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Date validité du</th>
                                        <th>Date validité au</th>
                                        <th>Quantité UM = Quantité UM</th>
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
        var dtObj = $('#table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route('rate.index') }}',
                data: function(d) {
                    d.entity_id = '{{ $entity->id }}'
                }
            },
            order: [
                [0, "desc"]
            ],
            columnDefs: [{
                targets: 0,
                width: '1%'
            }],
            columns: [{
                    data: 'id',
                    name: 'id',
                },
                {
                    data: 'from',
                    name: 'from'
                },
                {
                    data: 'to',
                    name: 'to'
                },
                {
                    data: 'rate',
                    name: 'rate',
                    className: 'text-nowrap font-weight-bold',
                    orderable: false,
                    searchable: false,
                },
            ]
        }).on('draw.dt', function(e, settings, data, xhr) {

        });
    </script>
@endsection
