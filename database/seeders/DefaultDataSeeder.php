<?php

namespace Database\Seeders;

use App\Models\Entity;
use App\Models\Fuel;
use App\Models\Label;
use App\Models\User;
use App\Models\Zone;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DefaultDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $entities = [
            ['TOTAL', 'TOTAL ENERGIES SA'],
            ['ENGEN', 'ENGEN RDC SA'],
            ['COBIL', 'COBIL SA'],
            ['SONAHYDROC', 'Société Nationale des Hydrocarbures du Congo'],
            ['LEREXCOM', 'LEREXCOM'],
            ['SEP CONGO', 'SEP CONGO'],
            ['SPSA', 'SPSA COBIL'],
            ['SOCIR', 'SOCIR'],
            ['GPDPP', 'GPDPP'],
            ['FEC', 'Fédération des Entreprises du Congo'],
            ['MINECO', 'Ministère de l\'Économie'],
            ['PRIMATURE', 'Primature (Cabinet du Premier Ministre)'],
            ['PRESIDENCE', 'Présidence de la République'],
            ['MINHYD', 'Ministère des Hydrocarbures'],
            ['DGDA', 'Direction Générale des Douanes et Accises'],
            ['DGI', 'Direction Générale des Impôts'],
            ['AUTHENTIX', 'AUTHENTIX']
        ];

        DB::beginTransaction();
        foreach ($entities as $el) {
            $e = Entity::firstOrNew(['shortname' => $el[0]]);
            if (!$e->exists) {
                $email = strtolower($el[0]) . "@email.com";
                $u = User::where(['email' => $email])->firstOrNew();
                if (!$u->exists) {
                    $u->name = $el[0];
                    $u->email = $email;
                    $u->password = Hash::make('mdp@123');
                    $u->user_role = 'provider';
                    $u->save();
                }

                $e->longname =  $el[1];
                $e->users_id = $u->id;
                $e->save();
            }
        }

        $zones = mainWays();
        foreach ($zones as $e) {
            $z = Zone::firstOrNew(['zone' => $e]);
            $z->save();
        }

        $fuels = mainfuels();
        foreach ($fuels as $e) {
            $z = Fuel::firstOrNew(['fuel' => $e]);
            $z->save();
        }

        $fuels = [
            ['fuel' => 'ESSENCE', 'fuel_type' => 'terrestre'],
            ['fuel' => 'PETROLE', 'fuel_type' => 'terrestre'],
            ['fuel' => 'GASOIL', 'fuel_type' => 'terrestre'],
            ['fuel' => 'FOMI', 'fuel_type' => 'terrestre'],
            ['fuel' => 'JET', 'fuel_type' => 'aviation'],
        ];

        foreach ($fuels as $f) {
            Fuel::firstOrCreate($f);
        }

        $labels = [
            ['label' => 'Platts', 'tag' => 'A'],
            ['label' => 'Premium/TM', 'tag' => 'B'],
            ['label' => 'PMFC en TM', 'tag' => 'C'],
            ['label' => 'Densité', 'tag' => 'D'],
            ['label' => 'PMFC en M3', 'tag' => 'E'],
            ['label' => 'Charges SOCIR', 'tag' => 'F'],
            ['label' => 'Charges Sep Congo', 'tag' => 'G'],
            ['label' => 'Charges SPSA-COBIL', 'tag' => 'H'],
            ['label' => 'Charges LEREXCOM PETROLEUM ET Appui Terrestre', 'tag' => 'I'],
            ['label' => 'Total frais des sociétés de logistique', 'tag' => 'J'],
            ['label' => 'Charges d\'exploitation Sociétés commerciales', 'tag' => 'K'],
            ['label' => 'Marges Sociétés Commerciales (10% PMF)', 'tag' => 'L'],
            ['label' => 'Total frais des sociétés Commerciales', 'tag' => 'M'],
            ['label' => 'Stock de sécurité 1', 'tag' => 'N'],
            ['label' => 'Stock de sécurité 2', 'tag' => 'O'],
            ['label' => 'Effort de reconstruction et Stock Stratégiques', 'tag' => 'P'],
            ['label' => 'CRP & Comité de suivi des Prix des produits Petroliers', 'tag' => 'Q'],
            ['label' => 'Marquage moléculaire', 'tag' => 'R'],
            ['label' => 'FONER (Fonds National d\'Entretien Routier)', 'tag' => 'S'],
            ['label' => 'Interventions Economiques', 'tag' => 'T'],
            ['label' => 'Total Parafiscalité', 'tag' => 'U'],
            ['label' => 'PMF fiscal (PMFF=Ki*PMFC)', 'tag' => 'V'],
            ['label' => 'TVA à la vente (TVAV)', 'tag' => 'W'],
            ['label' => 'Droits de douane', 'tag' => 'X'],
            ['label' => 'Droits de consommation', 'tag' => 'Y'],
            ['label' => 'TVA à l\'importation', 'tag' => 'Z'],
            ['label' => 'Total Fiscalité 1', 'tag' => 'AA'],
            ['label' => 'TVA nette à l\'intérieur', 'tag' => 'AB'],
            ['label' => 'Total Fiscalité 2', 'tag' => 'AC'],
            ['label' => 'Prix de référence réel (USD/M3)', 'tag' => 'AD'],
            ['label' => 'Prix de référence à appliquer (USD/L)', 'tag' => 'AE'],
        ];

        function numberToExcelColumn($num)
        {
            $column = '';
            while ($num >= 0) {
                $column = chr($num % 26 + 65) . $column;
                $num = intval($num / 26) - 1;
            }
            return $column;
        }

        // $tagAscii = 65;
        // foreach ($labels as $index =>  $labelText) {
        //     $tag = numberToExcelColumn($index);
        //     Label::firstOrCreate(
        //         ['label' => $labelText],
        //         ['tag' => $tag]
        //     );
        //     $tagAscii++;
        // }

        foreach ($labels as $l) {
            Label::firstOrCreate($l);
        }


        DB::commit();
    }
}
