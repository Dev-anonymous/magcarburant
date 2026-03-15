<?php

namespace App\Models\Traits;

use App\Models\AccountingClosure;
use App\Models\AverageFuelPrice;
use App\Models\Delivery;
use App\Models\Deliveryfile;
use App\Models\Fuelprice;
use App\Models\MiningSale;
use App\Models\MiningSaleFile;
use App\Models\Purchase;
use App\Models\Purchasefile;
use App\Models\Rate;
use App\Models\Role;
use App\Models\Sale;
use App\Models\Salefile;
use App\Models\SecurityStock;
use App\Models\Securitystockfile;
use App\Models\StateFuelprice;
use App\Models\StateStructureprice;
use App\Models\Structureprice;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

trait HasAuditLogs
{
    protected static function bootHasAuditLogs()
    {
        static::created(function ($model) {
            $user = Auth::user();
            $tableName = self::mapTableName($model);
            AuditService::log(
                'ajout',
                $model,
                null,
                $model->getAttributes(),
                sprintf("%s a ajouté une donnée (%s [ID:%d])", $user->name ?? 'Système', $tableName, $model->id)
            );
        });

        static::updated(function ($model) {
            $user = Auth::user();
            $old = array_intersect_key($model->getOriginal(), $model->getChanges());
            $new = $model->getChanges();

            if (!empty($new)) {
                $tableName = self::mapTableName($model);
                AuditService::log(
                    'modification',
                    $model,
                    $old,
                    $new,
                    sprintf("%s a modifié une donnée (%s [ID:%d])", $user->name ?? 'Système', $tableName, $model->id)
                );
            }
        });

        static::deleted(function ($model) {
            $user = Auth::user();
            $tableName = self::mapTableName($model);
            AuditService::log(
                'suppression',
                $model,
                $model->getOriginal(),
                null,
                sprintf("%s a supprimé une donnée (%s [ID:%d])", $user->name ?? 'Système', $tableName, $model->id)
            );
        });
    }

    private static function mapTableName(Model $model): string
    {
        if ($model instanceof User) {
            return "Utilisateur";
        }

        if ($model instanceof AccountingClosure) {
            return 'Réconciliation';
        }
        if ($model instanceof AverageFuelPrice) {
            return "Prix Moyen d'Achat";
        }

        if ($model instanceof Delivery) {
            return "Livraison excédentaire";
        }
        if ($model instanceof Deliveryfile) {
            return "Pièce jointe de la livraison excédentaire";
        }

        if ($model instanceof Fuelprice) {
            return "Prix Carburant";
        }

        if ($model instanceof MiningSale) {
            return "Ventes liées aux Stes. Minières";
        }
        if ($model instanceof MiningSaleFile) {
            return "Pièce jointe de la vente liée aux Stes. Minières";
        }

        if ($model instanceof Purchase) {
            return "Achat";
        }
        if ($model instanceof Purchasefile) {
            return "Pièce jointe de l'achat ";
        }

        if ($model instanceof Sale) {
            return "Vente";
        }
        if ($model instanceof Salefile) {
            return "Pièce jointe de la vente ";
        }

        if ($model instanceof Rate) {
            return "Taux réel";
        }

        if ($model instanceof StateFuelprice) {
            return "Prix Carburant";
        }

        if ($model instanceof Rate) {
            return "Taux réel";
        }

        if ($model instanceof StateStructureprice) {
            return "Structure de prix";
        }

        if ($model instanceof Structureprice) {
            return "Structure de prix";
        }

        if ($model instanceof SecurityStock) {
            return "Stock de sécurité collecté reversé";
        }

        if ($model instanceof Securitystockfile) {
            return "Pièce jointe du Stock de sécurité collecté reversé";
        }

        if ($model instanceof Role) {
            return "Rôle";
        }


        return strtolower(class_basename($model));
    }
}
