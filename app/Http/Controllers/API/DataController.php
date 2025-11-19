<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DataController extends Controller
{
    function dashboard()
    {
        $user = auth()->user();

        $type = request('type');

        if ($user->user_role == 'provider') {
            if ($type == 'purchase') {
                $date = request('date');
                $date = explode(' to ', $date);
                $date = array_filter($date);
                $from = @$date[0] ?? nnow()->toDateString();
                $to = @$date[1] ?? $from;

                $entity = $user->entities()->first();
                $base = $entity->purchases()->whereBetween('date', [$from, $to]);

                $totalTm = v((clone $base)->sum('qtytm'));
                $totalM3 = v((clone $base)->sum('qtym3'));
                $totalAmount = v((clone $base)->sum(DB::raw('unitprice * qtytm')));
                $avgPrice = v((clone $base)->avg('unitprice'));

                $labels = [];
                $data = [];
                foreach (mainfuels() as $el) {
                    $data[] =  $entity->purchases()->where('product', $el)->whereBetween('date', [$from, $to])->sum(DB::raw('unitprice * qtytm'));
                    $labels[] = $el;
                }
                $chart1 = compact('labels', 'data');

                $labels = [];
                $data = [];
                foreach (mainfuels() as $el) {
                    $data[] =  $entity->purchases()->where('product', $el)->whereBetween('date', [$from, $to])->sum('qtym3');
                    $labels[] = $el;
                }
                $chart2 = compact('labels', 'data');

                return compact('totalTm', 'totalM3', 'totalAmount', 'avgPrice', 'chart1', 'chart2');
            }
        }
    }
}
