<?php

namespace App\Models;

use Illuminate\Database\DBAL\TimestampType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class Subvention extends Model
{
    use HasFactory;

    protected $primaryKey = 'subvention_id';
    protected $table='subvention';
    public $timestamps = false;
    protected $connection = 'mysql';


    protected $fillable = [
        'user_id',
        'state_id',
        'name_asso',
        'RIB',
        'montant',
        'link_attestation',
        'motif_refus',
        'payment_subvention',
    ];

    protected $casts = [
        'montant' => 'decimal:2',
        'payment_subvention' => 'date'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function state(): BelongsTo
    {
        return $this->belongsTo(StateSub::class, 'state_id', 'state_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            $validator = Validator::make($model->getAttributes(), [
                'user_id' => 'required|exists:users,user_id',
                'state_id' => 'required|exists:state_sub,state_id',
                'name_asso' => 'required|string|max:255',
                'RIB' => 'required|string|max:255',
                'montant' => 'required|numeric|min:0',
                'link_attestation' => 'nullable|string|max:255',
                'motif_refus' => 'nullable|string|max:255',
                'payment_subvention' => 'nullable|date'
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }
        });
    }

    public static function getLastPendingSubventionForUser(int $userId): ?Subvention
    {
        return self::where('user_id', $userId)
            ->where('state_id', 1)
            ->orderBy('subvention_id', 'desc')
            ->first();
    }

    public static function getResolvedSubventionsForUser(int $userId): \Illuminate\Database\Eloquent\Collection
    {
        return self::where('user_id', $userId)
            ->whereNotNull('payment_subvention')
            ->orderBy('payment_subvention', 'desc')
            ->orderBy('subvention_id', 'desc')
            ->get();
    }
}
