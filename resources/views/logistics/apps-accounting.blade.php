@extends('layouts.app')
@section('title', 'Comptabilité')
@section('bg-class', 'bg-img-3')
@section('body')
    <div class="container">
        <div class="d-flex justify-content-between">
            <div class="">
                <h2 class="font-weight-bold">Comptabilité</h2>
                <p class="lead small m-0">Gestion de la comptabilité</p>
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
            @canlocal('Bilan manque à gagner - Lire')
            <div class="col-md-6">
                <div class="carte" style="cursor: pointer;min-height: 120px"
                    onclick="location.assign('{{ route('logistics.analyse') }}')">
                    <svg xmlns="http://www.w3.org/2000/svg" height="48px" viewBox="0 -960 960 960" width="48px"
                        fill="#000000">
                        <path
                            d="M180-120q-24 0-42-18t-18-42v-660h60v660h660v60H180Zm75-135v-334h119v334H255Zm198 0v-540h119v540H453Zm194 0v-170h119v170H647Z" />
                    </svg>
                    <div class="p-2">
                        <h4 class="font-weight-bold">Manque à gagner</h4>
                    </div>
                </div>
            </div>
            @endcanlocal

            @canlocal('Grand livre manque à gagner - Lire')
            <div class="col-md-6">
                <div class="carte" style="cursor: pointer;min-height: 120px"
                    onclick="location.assign('{{ route('logistics.accounting', ['item' => 'gb']) }}')">
                    <svg xmlns="http://www.w3.org/2000/svg" height="48px" viewBox="0 -960 960 960" width="48px"
                        fill="#000000">
                        <path
                            d="M132-120q-24 0-42-18t-18-42v-600q0-24 18-42t42-18h696q24 0 42 18t18 42v600q0 24-18 42t-42 18H132Zm0-60h696v-600H132v600Zm68-100h200v-80H200v80Zm382-80 198-198-57-57-141 142-57-57-56 57 113 113Zm-382-80h200v-80H200v80Zm0-160h200v-80H200v80Zm-68 420v-600 600Z" />
                    </svg>
                    <div class="p-2">
                        <h4 class="font-weight-bold">Grand livre MAG</h4>
                    </div>
                </div>
            </div>
            @endcanlocal

        </div>

    </div>
@endsection
