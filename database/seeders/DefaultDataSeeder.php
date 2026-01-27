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
            ['TOTAL', 'TOTAL ENERGIES SA', 'petrolier'],
            ['ENGEN', 'ENGEN RDC SA', 'petrolier'],
            ['COBIL', 'COBIL SA', 'petrolier'],
            ['SONAHYDROC', 'Société Nationale des Hydrocarbures du Congo', 'petrolier'],
            ['LEREXCOM', 'LEREXCOM', 'logisticien', ['OUEST']],
            ['SEP CONGO', 'SEP CONGO', 'logisticien', ['NORD', 'SUD', 'EST', 'OUEST']],
            ['SPSA', 'SPSA COBIL', 'logisticien',  ['OUEST']],
            ['SOCIR', 'SOCIR', 'logisticien', ['OUEST']],
            ['GPDPP', 'GPDPP', 'etatique'],
            ['FEC', 'Fédération des Entreprises du Congo', 'etatique'],
            ['MINECO', 'Ministère de l\'Économie', 'etatique'],
            ['PRIMATURE', 'Primature (Cabinet du Premier Ministre)', 'etatique'],
            ['PRESIDENCE', 'Présidence de la République', 'etatique'],
            ['MINHYD', 'Ministère des Hydrocarbures', 'etatique'],
            ['DGDA', 'Direction Générale des Douanes et Accises', 'etatique'],
            ['DGI', 'Direction Générale des Impôts', 'etatique'],
            ['AUTHENTIX', 'AUTHENTIX', 'etatique'],
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
                    $u->user_role = $el[2];
                    $u->save();
                }

                $e->longname =  $el[1];
                $e->users_id = $u->id;
                $e->save();
            }

            $wz = (array) @$el[3];
            foreach ($wz as $w) {
                $zone = Zone::where('zone', $w)->firstOrFail();
                $ewz = $e->workingzones()->where('zone_id', $zone->id)->firstOrNew();
                $ewz->zone_id = $zone->id;
                $ewz->save();
            }
        }

        $zones = mainWays();
        foreach ($zones as $e) {
            Zone::firstOrCreate(['zone' => $e]);
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
            'Platts',
            'Premium/TM',
            'PMFC en TM',
            'Densité',
            'PMFC Ouest',
            'Différentiel de livraison à l\'intérieur',
            'PMFC en M3',

            'Charges SOCIR',
            'Charges Sep Congo',
            'Charges Sep Congo et Autres entrepots',
            'Charges SPSA-COBIL',
            'Charges capacités additionnelles SPSA',
            'Capacités additionnelles KPS',
            'Charges LEREXCOM PETROLEUM ET Appui Terrestre',
            'Charges d\'exploitation logisticiens (frais d\'entreprot)',
            'Charges d\'exploitation logisticien (Frais d\'entrepot)',
            'Charges d\'exploitation Sep Congo',

            'Frais & Services SOCIR',

            'Total frais des sociétés de logistique',

            'Charges d\'exploitation Sociétés commerciales',
            'Marges Sociétés Commerciales (10% PMF)',
            'Total frais des sociétés Commerciales',

            'Stock de sécurité 1',
            'Stock de sécurité 2',

            'Effort de reconstruction et Stock Stratégiques',
            'CRP & Comité de suivi des Prix des produits Petroliers',
            'Marquage moléculaire',
            'FONER (Fonds National d\'Entretien Routier)',
            'Interventions Economiques',

            'Total Parafiscalité',

            'PMF fiscal (PMFF=Ki*PMFC)',
            'TVA à la vente (TVAV) pour calcul',
            'Droits de douane (10% PMF Commercial)',
            'Droits de consommation (25%, 15%, 0% du PMFF)',
            'TVA à l\'importation (TVAI) = 16%(PMFC+DD+DC)',

            'Total Fiscalité 1',
            'TVA nette à l\'intérieur (TVAIr=TVAV-TVAI)',
            'Total Fiscalité 2',

            'Prix de référence réel (USD/M3)',
            'Prix de référence à appliquer (USD/L)',
        ];


        $alphabet = range('A', 'Z');
        $index = 0;

        foreach ($labels as $text) {
            $tag = '';
            $n = $index;
            do {
                $tag = $alphabet[$n % 26] . $tag;
                $n = intdiv($n, 26) - 1;
            } while ($n >= 0);

            Label::firstOrCreate(
                ['label' => $text],
                ['tag' => $tag]
            );
            $index++;
        }


        DB::commit();
    }
}
