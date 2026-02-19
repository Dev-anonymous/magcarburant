<?php

namespace App\Models\Traits;

use App\Models\AccountingClosure;
use Carbon\Carbon;

trait HasAccountingLock
{
    protected static function bootHasAccountingLock()
    {
        static::saving(fn($model) => $model->checkPeriodClosure());
        static::deleting(fn($model) => $model->checkPeriodClosure());
    }

    public function checkPeriodClosure()
    {
        $lastClosure = AccountingClosure::lastClosedDate($this->entity_id);

        if (!$lastClosure) {
            return;
        }

        $closureDate = Carbon::parse($lastClosure)->startOfDay();
        $fromDate = $this->date;

        if ($fromDate->lte($closureDate)) {
            $d = Carbon::parse($lastClosure);
            throw new \Exception("Vous ne pouvez plus ajouter, modifier ni supprimer les données avant le {$d->format('d-m-Y')}, car cette période est déjà clôturée après réconciliation.");
        }
    }
}
