@extends('layouts.app')
@section('title', 'Prix moyens d\'achat ')
@section('bg-class', 'bg-img-3')
@section('body')
    <div class="container-fluid">
        <div class="d-flex justify-content-between">
            <div class="">
                <h2 class="font-weight-bold">Prix moyen d'achat des carburants</h2>
                <p class="lead small m-0">Configuration des prix moyens d'achats des carburants
                </p>
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
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 p-0">
                    <div class="card transparent">
                        <div class="card-header">
                            <form id="ffilter" class="filters-form pull-right" role="form">
                                <div class="form-group mb-1">
                                    <label for="fuel" class="control-label d-block mb-0">Année</label>
                                    <select id="year" class="form-control select2" style="min-width:150px;">
                                        @foreach ($years as $e)
                                            <option>{{ $e }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </form>
                        </div>
                        <div class="py-4">
                            <div class="card-body">
                                <x-dataloader />
                                <div class="row" id="avg-price-container"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    </div>
@endsection
@section('modals')
    <div class="modal fade" id="mdledit" role="dialog">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="defaultModalLabel">Configuration du prix moyen d'achat</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form class="was-validated" fedit>
                    <div class="modal-body">
                        <div class="py-3">
                            <h4 product></h4>
                            <h4 month></h4>
                        </div>
                        <div class="mb-2">
                            <label class="mb-0">Prix Moyen d'achat au NORD</label>
                            <input type="hidden" name="nord_id">
                            <input type="number" min="0" step="0.001" class="form-control" required
                                name="nord">
                        </div>
                        <div class="mb-2">
                            <label class="mb-0">Prix Moyen d'achat au SUD</label>
                            <input type="hidden" name="sud_id">
                            <input type="number" min="0" step="0.001" class="form-control" required
                                name="sud">
                        </div>
                        <div class="mb-2">
                            <label class="mb-0">Prix Moyen d'achat à l'EST</label>
                            <input type="hidden" name="est_id">
                            <input type="number" min="0" step="0.001" class="form-control" required
                                name="est">
                        </div>
                        <div class="mb-2">
                            <label class="mb-0">Prix Moyen d'achat à l'OUEST</label>
                            <input type="hidden" name="ouest_id">
                            <input type="number" min="0" step="0.001" class="form-control" required
                                name="ouest">
                        </div>
                        <x-alert />
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn" data-dismiss="modal">
                            <i class="material-icons md-18 mr-1 m-0 p-0">highlight_off</i>
                            Fermer
                        </button>
                        <button type="submit" class="btn btn-primary d-flex align-items-center justify-content-center">
                            <x-loader />
                            <span text>
                                <i class="material-icons md-18 mr-1 m-0 p-0">save</i>
                                Valider
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <x-datatable />
    <x-flatpickr />
    <x-select />

    <script>
        var ff = $('#ffilter');

        let timer;
        ff.change(function(e) {
            clearTimeout(timer);
            var e = $(e.target);
            timer = setTimeout(() => {
                getdata();
            }, 100);
        });

        $(document).on('click', '.editdata', function() {
            var tr = $(this);
            var data = tr.data('data');
            var id = data.id;
            var product = data.NORD.prod;
            var month = tr.closest('[col]').find('[mlabel]').text().trim();

            var mdl = $('#mdledit');
            $('[product]', mdl).html('Carburant : ' + product);
            $('[month]', mdl).html('Mois : ' + month);
            $('[name="nord_id"]', mdl).val(data.NORD.id);
            $('[name="nord"]', mdl).val(data.NORD.v);
            $('[name="sud_id"]', mdl).val(data.SUD.id);
            $('[name="sud"]', mdl).val(data.SUD.v);
            $('[name="est_id"]', mdl).val(data.EST.id);
            $('[name="est"]', mdl).val(data.EST.v);
            $('[name="ouest_id"]', mdl).val(data.OUEST.id);
            $('[name="ouest"]', mdl).val(data.OUEST.v);
            mdl.modal('show');
        });

        $('[fedit]').on('submit', function(e) {
            e.preventDefault();
            var form = $(this);
            var btn = $(':submit', form);
            var rep = $('#rep', form);
            var data = form.serialize();
            rep.hide();
            $(':input', form).attr('disabled', true);
            $('[loader]', btn).show();
            $('[text]', btn).hide();

            $.ajax({
                url: '{{ route('avgprice.index') }}',
                method: 'post',
                data: data,
                success: function(resp) {
                    var mess = resp?.message ?? "Erreur, veuillez réessayer !";
                    rep.html(mess).stop().removeClass().addClass(
                            'p-1 m-0 alert alert-success')
                        .show();
                    getdata();
                    form[0].reset();
                    setTimeout(() => {
                        rep.hide();
                        $('.modal.show').modal('hide');
                    }, 2000);
                },
                error: function(xhr, a, b) {
                    var resp = xhr.responseJSON;
                    var mess = resp?.message ?? "Erreur, veuillez réessayer !";
                    rep.html(mess).stop().removeClass().addClass(
                            'p-1 m-0 alert alert-danger')
                        .show();
                },
            }).always(function() {
                $(':input', form).attr('disabled', false);
                $('[loader]', btn).hide();
                $('[text]', btn).show();
            });
        });

        function getdata() {
            var ldr = $('[dataloader]');
            ldr.show();
            $.ajax({
                url: '{{ route('avgprice.index') }}',
                data: {
                    year: $('#year').val(),
                },
                success: function(data) {
                    const monthNames = {
                        1: 'Janvier',
                        2: 'Février',
                        3: 'Mars',
                        4: 'Avril',
                        5: 'Mai',
                        6: 'Juin',
                        7: 'Juillet',
                        8: 'Août',
                        9: 'Septembre',
                        10: 'Octobre',
                        11: 'Novembre',
                        12: 'Décembre'
                    };

                    const container = document.getElementById('avg-price-container');
                    container.innerHTML = '';

                    const year = data.year;
                    const months = data.months;
                    var tab = [];

                    for (let m = 1; m <= 12; m++) {
                        var id = 'tbl' + Math.random().toString().split('.').join('');
                        tab.push(id);

                        const products = data.months[m] || {};

                        let html = `
                                <div class="col-md-3" col>
                                <h4 class="mt-4" mlabel>${monthNames[m]} ${data.year}</h4>
                                <div class='table-responsive'>
                                <table id="${id}" class="table table-striped table-hover text-nowrap table-sm">
                                    <thead>
                                        <tr>
                                            <th>Produit</th>
                                            <th>Nord</th>
                                            <th>Sud</th>
                                            <th>Est</th>
                                            <th>Ouest</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                            `;

                        if (Object.keys(products).length === 0) {
                            html += `
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">
                                            Aucune donnée
                                        </td>
                                    </tr>
                                `;
                        } else {
                            for (const product in products) {
                                const z = products[product];
                                html += `
                                        <tr class='cursor editdata' data-data='${JSON.stringify(z)}'>
                                            <td>${product}</td>
                                            <td>${z.NORD.price }</td>
                                            <td>${z.SUD.price }</td>
                                            <td>${z.EST.price }</td>
                                            <td>${z.OUEST.price }</td>
                                        </tr>
                                    `;
                            }
                        }

                        html += `</tbody></table></div></div>`;
                        container.insertAdjacentHTML('beforeend', html);

                    }

                    tab.forEach(e => {
                        var table = $('#' + e);
                        const tbody = table.find("tbody");
                        table.DataTable({
                            dom: 'Bfrt',
                            searching: false,
                            ordering: false,
                            buttons: [{
                                extend: 'excelHtml5',
                                title: 'Export Excel',
                                exportOptions: {
                                    columns: ':not(.no-export)',
                                    format: {
                                        body: function(data, row, column, node) {
                                            if (!data) return data;
                                            let cleaned = data.toString()
                                                .replace(
                                                    /\s+/g,
                                                    '');
                                            cleaned = cleaned.replace(',', '.');
                                            let num = Number(cleaned);
                                            return isNaN(num) ? data : num;
                                        }
                                    }
                                }
                            }, ],
                        });
                    });

                    ldr.hide();
                },
                error: function(xhr, a, b) {

                },
            })
        }

        getdata();
    </script>
@endsection
