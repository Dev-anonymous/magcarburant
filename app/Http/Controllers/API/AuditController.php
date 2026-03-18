<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Entity;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;

class AuditController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = request()->user();

        $date = request('date');
        $date = explode(' to ', $date);
        $date = array_filter($date);
        $from = @$date[0] ?? nnow()->toDateString();
        $to = @$date[1] ?? $from;

        $from = Carbon::parse($from)->startOfDay();
        $to   = Carbon::parse($to)->endOfDay();

        $event = (array) request('event');

        $logs = AuditLog::whereBetween('created_at', [$from, $to]);
        $logs->whereIn('event', $event);

        if (in_array($user->user_role, ['petrolier', 'logisticien', 'etatique'])) {
            $logs->whereIn('user_id', childrenlist($user));
        } elseif ($user->user_role == 'sudo') {
            //
        } else {
            abort(403, "Not permit");
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
                return middleTruncate($row->old_values);
            })
            ->editColumn('new_values', function ($row) {
                if (!$row->new_values) {
                    return '-';
                }
                return middleTruncate($row->new_values);
            })
            ->editColumn('user_agent', function ($row) {
                return Str::limit($row->user_agent, 50);
            })
            ->addColumn('raw_data', function ($row) {
                $d = $row->toArray();
                $date = Carbon::parse($row->created_at);
                $d['date'] = ucfirst($date->dayName) . " le {$date->format('d-m-Y H:i:s P')}";
                $d['event'] = ucfirst($d['event']);
                $entity = Entity::find($d['entity_id']);
                $d['entity'] =  $ent = @$entity->shortname;
                if ($row->username == $ent) {
                    $d['entity'] = null;
                }
                return json_encode($d);
            })
            ->rawColumns(['raw_data'])
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
