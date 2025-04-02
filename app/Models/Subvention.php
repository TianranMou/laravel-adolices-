<?php

namespace App\Models;

use Illuminate\Database\DBAL\TimestampType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Carbon;

class Subvention extends Model
{
    use HasFactory;

    protected $primaryKey = 'subvention_id';
    protected $table = 'subvention';
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

    /**
     * Retrieve the last pending subvention for a specific user.
     *
     * A pending subvention is identified by its `state_id` being 1.
     * The method returns the most recent subvention based on the `subvention_id` in descending order.
     *
     * @param int $userId The ID of the user.
     * @return Subvention|null The last pending subvention for the user, or null if none exists.
     */
    public static function getLastPendingSubventionForUser(int $userId): ?Subvention
    {
        return self::where('user_id', $userId)
            ->where('state_id', 1)
            ->orderBy('subvention_id', 'desc')
            ->first();
    }


    /**
     * Retrieve all resolved subventions for a specific user.
     *
     * A resolved subvention is identified by having a non-null `payment_subvention`.
     * The results are ordered by `payment_subvention` in descending order,
     * and then by `subvention_id` in descending order.
     *
     * @param int $userId The ID of the user.
     * @return \Illuminate\Database\Eloquent\Collection A collection of resolved subventions for the user.
     */
    public static function getResolvedSubventionsForUser(int $userId): \Illuminate\Database\Eloquent\Collection
    {
        return self::where('user_id', $userId)
            ->whereNotNull('payment_subvention')
            ->orderBy('payment_subvention', 'desc')
            ->orderBy('subvention_id', 'desc')
            ->get();
    }

    /**
     * Check if a subvention is available for a specific user.
     *
     * A subvention is considered unavailable if there exists a subvention for the user
     * within the current school year (determined by the `ADHESION_MONTH_DAY` environment variable)
     * that has a `state_id` of 3.
     *
     * @param int $userId The ID of the user.
     * @return bool True if a subvention is available, false otherwise.
     */
    public static function isSubventionAvailable(int $userId): bool
    {
        $now = Carbon::now();
        $adhesionCutoff = env('ADHESION_MONTH_DAY', '07-31');
        [$adhesionMonth, $adhesionDay] = explode('-', $adhesionCutoff);
        $adhesionMonth = (int)$adhesionMonth;
        $adhesionDay = (int)$adhesionDay;
        $startOfSchoolYear = Carbon::create($now->month >= $adhesionMonth+1 ? $now->year : $now->year - 1, $adhesionMonth+1, 1, 0, 0, 0);
        $endOfSchoolYear = Carbon::create($now->month >= $adhesionMonth ? $now->year + 1 : $now->year, $adhesionMonth, $adhesionDay, 23, 59, 59);

        return !self::where('user_id', $userId)
            ->whereBetween('payment_subvention', [$startOfSchoolYear, $endOfSchoolYear])
            ->where('state_id', 3)
            ->exists();
    }

    public function scopePending($query)
    {
        return $query->where('state_id', 1);
    }
}
