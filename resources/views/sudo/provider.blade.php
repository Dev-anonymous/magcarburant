@extends('layouts.app')
@section('title', 'Fournisseurs')
@section('body')
    <div class="container-fluid">
        <h2>Fournisseurs</h2>
        <p class="lead">Gesion des fournisseurs</p>
        <hr />


        <div class="card card-body p-2">
            <div class="row row-projects">
                <div class="col">
                    <i class="material-icons text-link-color md-36">dvr</i>
                    <div class="mb-1">Total Projects</div>
                    <h4 class="mb-0">6</h4>
                </div>
                <div class="col">
                    <i class="material-icons text-success md-36">cast</i>
                    <div class="mb-1">Active Projects</div>
                    <h4 class="mb-0">4</h4>
                </div>
                <div class="col">
                    <i class="material-icons text-warning md-36">assistant_photo</i>
                    <div class="mb-1">Your Tickets</div>
                    <h4 class="mb-0">2</h4>
                </div>
                <div class="col">
                    <i class="material-icons text-primary md-36">contacts</i>
                    <div class="mb-1">Top Contacts</div>
                    <h4 class="mb-0">4</h4>
                </div>
                <div class="col">
                    <i class="material-icons text-muted md-36">notifications_paused</i>
                    <div class="mb-1">Offline Contacts</div>
                    <h4 class="mb-0">11</h4>
                </div>
            </div>
        </div>



    </div>
@endsection
