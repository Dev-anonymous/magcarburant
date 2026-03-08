<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Entity;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Yajra\DataTables\DataTables;

class AuditController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = request()->user();
        // abort_if(!in_array($user->user_role, ['petrolier', 'etatique']), 403, "No permission");

        $date = request('date');
        $date = explode(' to ', $date);
        $date = array_filter($date);
        $from = @$date[0] ?? nnow()->toDateString();
        $to = @$date[1] ?? $from;

        $from = Carbon::parse($from)->startOfDay();
        $to   = Carbon::parse($to)->endOfDay();

        $event = (array) request('event');

        $logs = AuditLog::whereBetween('created_at', [$from, $to]);
        if (count($event)) {
            $logs->whereIn('event', $event);
        }

        if (in_array($user->user_role, ['petrolier', 'logisticien'])) {
            $entity  = $user->entities()->first();
            $logs->where('entity_id', $entity->id);
        } else {
            // abort(403);
        }


        return DataTables::of($logs)
            ->addIndexColumn()
            ->editColumn('event', function ($row) {
                return ucfirst($row->event);
            })
            ->editColumn('old_values', function ($row) {
                if (!($row->old_values)) {
                    return '-';
                }
                return $row->old_values;
            })
            ->editColumn('new_values', function ($row) {
                if (!$row->new_values) {
                    return '-';
                }
                return $row->new_values;
            })
            ->rawColumns([])
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
    public function show(AuditLog $auditLog)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AuditLog $auditLog)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AuditLog $auditLog)
    {
        //
    }
}
