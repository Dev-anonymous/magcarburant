<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\AverageFuelPrice;
use App\Models\Entity;
use App\Models\Zone;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ReconciliationController extends Controller
{
    function reconciliation()
    {
        $user = auth()->user();
        abort_unless($user->user_role == 'etatique', 403, 'Not permit');
        $entity_id = request('entity_id');
        $entity = Entity::findOrFail($entity_id);
        $item = (array) request('item');
        $date = request('date');
        $date = explode(' to ', $date);
        $date = array_filter($date);
        $from = @$date[0] ?? nnow()->toDateString();
        $to = @$date[1] ?? $from;
        $me = $user->entities()->first();

        $date1 = new DateTime($from);
        $date2 = new DateTime($to);
        $sameMonth = $date1->format('Y-m') === $date2->format('Y-m');

        $data = [];

        if (in_array('achat', $item)) {
            // $ob = [];
            // foreach (mainfuels() as $el) {
            //     $m31 = round($entity->purchases()->where('from_state', 1)->where('product', $el)->whereBetween('date', [$from, $to])->sum('qtym3'), 3);
            //     $m32 = round($entity->purchases()->where('from_state', 0)->where('product', $el)->whereBetween('date', [$from, $to])->sum('qtym3'), 3);

            //     $tm1 = round($entity->purchases()->where('from_state', 1)->where('product', $el)->whereBetween('date', [$from, $to])->sum('qtytm'), 3);
            //     $tm2 = round($entity->purchases()->where('from_state', 0)->where('product', $el)->whereBetween('date', [$from, $to])->sum('qtytm'), 3);

            //     $t1 = round($entity->purchases()->where('from_state', 1)->where('product', $el)->whereBetween('date', [$from, $to])->sum(DB::raw('unitprice * qtytm')), 3);
            //     $t2 = round($entity->purchases()->where('from_state', 0)->where('product', $el)->whereBetween('date', [$from, $to])->sum(DB::raw('unitprice * qtytm')), 3);
            //     $ob[] = [
            //         'fuel' => $el,
            //         'm31' => $m31,
            //         'm32' => $m32,
            //         'tm1' => $tm1,
            //         'tm2' => $tm2,
            //         'usd1' => $t1,
            //         'usd2' => $t2,
            //     ];
            // }

            $errors = [];
            $head = [];
            $body = [];

            $fromObj = Carbon::parse($from);
            $toObj = Carbon::parse($to);

            abort_if(!$sameMonth, 403, "Pour afficher le prix moyen d'achat, les 2 dates ($from ... $to) doivent etre sur un meme mois.");

            // si meme mois ! ok
            $startOfMonth = $fromObj->copy()->startOfMonth();
            $endOfMonth   = $fromObj->copy()->endOfMonth();

            foreach (mainWays() as $k => $way) {
                $zoneObj = Zone::where('zone', $way)->first();
                if ($k == 0) {
                    $head[] = [['label' => "PRIX MOYEN D'ACHAT (USD)", 'class' => 'bold text-center', 'colspan' => 4]];
                    $head[] = [['label' => "VOIE $way", 'class' => 'bgred', 'colspan' => 4]];
                    $head[] = [
                        ['label' => 'PRODUIT', 'class' => 'title bgred'],
                        ['label' => $me->shortname, 'class' => 'title bgred'],
                        ['label' => $entity->shortname, 'class' => 'title bgred'],
                        ['label' => 'ECART', 'class' => 'title bgred']
                    ];
                } else {
                    $body[] = [
                        ['label' => "VOIE $way", 'class' => 'bgred'],
                        ['label' => '', 'class' => 'bgred',],
                        ['label' => '', 'class' => 'bgred',],
                        ['label' => '', 'class' => 'bgred',],
                    ];
                }
                foreach (mainfuels() as $fuel) {
                    $avg =  (float) AverageFuelPrice::whereYear('month', $fromObj->year)
                        ->whereMonth('month', $fromObj->month)
                        ->where('product', $fuel)
                        ->where('zone_id', $zoneObj->id)
                        ->first()?->avg_price;
                    $lab1 = "Prix moyen d'achat $fuel (zone $way) du {$startOfMonth->format('d-m-Y')} au {$endOfMonth->format('d-m-Y')}";
                    if (!$avg) {
                        $errors[] = "Aucun prix d'achat moyen n'a été trouvé à la date du {$fromObj->format('d-m-Y')} ($fuel, $way)";
                    }
                    $pmfc_reel = (float) ($entity->purchases()->where('from_state', 0)->where(function ($q) use ($fuel) {
                        $q->where('product', $fuel);
                    })->where('way', $way)->whereBetween('date', [$startOfMonth, $endOfMonth])->avg('unitprice') ?? 0);
                    $lab2 = "Moyenne mensuelle du prix unitaire d'achat $fuel (zone $way) du {$startOfMonth->format('d-m-Y')} au {$endOfMonth->format('d-m-Y')}";
                    $ecart = $avg - $pmfc_reel;

                    $t = [
                        ['label' => $fuel, 'class' => ''],
                        ['label' => v($avg), 'title' => $lab1],
                        ['label' => v($pmfc_reel), 'title' => $lab2],
                        ['label' => v($ecart), 'class' => $avg !== $pmfc_reel ? 'text-danger font-weight-bold' : '', 'title' => "$me->shortname - $entity->shortname"],
                    ];

                    $body[] = $t;
                }
            }

            return response()->json(['head' => $head, 'body' => $body, 'errors' => $errors]);
        }

        if (in_array('vente', $item)) {
            // $ob = [];
            // foreach (mainfuels() as $el) {
            //     $m31 = round($entity->sales()->where('from_state', 1)->where('product', $el)->whereBetween('date', [$from, $to])->sum(DB::raw('lata/1000')), 3);
            //     $m32 = round($entity->sales()->where('from_state', 0)->where('product', $el)->whereBetween('date', [$from, $to])->sum(DB::raw('lata/1000')), 3);

            //     $lata1 = round($entity->sales()->where('from_state', 1)->where('product', $el)->whereBetween('date', [$from, $to])->sum('lata'), 3);
            //     $lata2 = round($entity->sales()->where('from_state', 0)->where('product', $el)->whereBetween('date', [$from, $to])->sum('lata'), 3);

            //     $l151 = round($entity->sales()->where('from_state', 1)->where('product', $el)->whereBetween('date', [$from, $to])->sum('l15'), 3);
            //     $l152 = round($entity->sales()->where('from_state', 0)->where('product', $el)->whereBetween('date', [$from, $to])->sum('l15'), 3);

            //     $ob[] = [
            //         'fuel' => $el,
            //         'm31' => $m31,
            //         'm32' => $m32,
            //         'lata1' => $lata1,
            //         'lata2' => $lata2,
            //         'l151' => $l151,
            //         'l152' => $l152,
            //     ];
            // }
        }

        if (in_array('livraison', $item)) {
            // $ob = [];
            // foreach (mainfuels() as $el) {
            //     $lata1 = round($entity->deliveries()->where('from_state', 1)->where('product', $el)->whereBetween('date', [$from, $to])->sum('lata'), 3);
            //     $lata2 = round($entity->deliveries()->where('from_state', 0)->where('product', $el)->whereBetween('date', [$from, $to])->sum('lata'), 3);

            //     $t1 = round($entity->deliveries()->where('from_state', 1)->where('product', $el)->whereBetween('date', [$from, $to])->sum(DB::raw('lata*unitprice')), 3);
            //     $t2 = round($entity->deliveries()->where('from_state', 0)->where('product', $el)->whereBetween('date', [$from, $to])->sum(DB::raw('lata*unitprice')), 3);

            //     $ob[] = [
            //         'fuel' => $el,
            //         'lata1' => $lata1,
            //         'lata2' => $lata2,
            //         't1' => $t1,
            //         't2' => $t2,
            //     ];
            // }
        }
    }
}
