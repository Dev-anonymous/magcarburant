<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Entity;
use App\Models\StateStructureprice;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class TxStructure extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (isPetroUser() || isLogUser()) {
            can('Structure des prix - Lire', true);
            $item = request('item');
            $entity = gentity();
            abort_if(!$entity, 422, "No entity");
            if ($item == 'stx') {
                $data = $entity->structureprices();
            } else {
                $data = $entity->structurepriceminings();
            }
        } elseif (isEtaUser()) {
            can('Configuration - Lire', true);

            if (from_state() || ('view' === rmode() && null == request('entity_id'))) { // mode edit
                $data  = StateStructureprice::query();
            } else {
                $entity = Entity::findOrFail(request('entity_id'));
                $data = $entity->structureprices();
            }
        } else {
            abort(403);
        }

        return DataTables::of($data)
            ->addIndexColumn()
            ->editColumn('from', function ($row) {
                return $row->from->format('d-m-Y');
            })
            ->editColumn('to', function ($row) {
                return $row->to?->format('d-m-Y') ?? '-';
            })
            ->addColumn('rate', function ($row) {
                return "<span>1 USD = $row->usd_cdf CDF</span>";
            })
            ->rawColumns(['rate'])
            ->make(true);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
