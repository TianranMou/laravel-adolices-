<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class Adhesion extends Model
{
    use HasFactory;
    protected $table='adhesion';
    protected $primaryKey = 'adhesion_id';
    protected $connection = 'mysql';


    protected $fillable = [
        'user_id',
        'date_adhesion',
    ];

    protected $casts = [
        'date_adhesion' => 'date'
    ];

    public $timestamps = false;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function setDateAdhesionAttribute($value)
    {
        if (is_null($value)) {
            $this->attributes['date_adhesion'] = null;
            return;
        }

        try {
            if (!$value instanceof Carbon) {
                $value = Carbon::parse($value);
            }
            $this->attributes['date_adhesion'] = $value->format('Y-m-d');
        } catch (\Exception $e) {
            throw ValidationException::withMessages([
                'date_adhesion' => ['The date format is invalid.']
            ]);
        }
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            $validator = Validator::make($model->getAttributes(), [
                'user_id' => 'required|exists:users,user_id',
                'date_adhesion' => 'nullable'
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }
        });
    }

    /**
     * Check if a user has a valid adhesion for the current school year
     *
     * @param int $userId
     * @return bool
     */
    public static function isValid($userId): bool
    {
        $now = Carbon::now();

        $startOfSchoolYear = Carbon::create($now->month >= 9 ? $now->year : $now->year - 1, 9, 1, 0, 0, 0);
        $endOfSchoolYear = Carbon::create($now->month >= 9 ? $now->year + 1 : $now->year, 8, 31, 23, 59, 59);

        return static::where('user_id', $userId)
            ->whereBetween('date_adhesion', [$startOfSchoolYear, $endOfSchoolYear])
            ->exists();
    }

    /**
     * Create a new adhesion for a user
     *
     * @param int $userId
     * @param mixed $date_adhesion Optional date (defaults to current date if null)
     * @return Adhesion
     */
    public static function createForUser($userId, $date_adhesion = null): self
    {
        $adhesion = new static();
        $adhesion->user_id = $userId;
        $adhesion->date_adhesion = $date_adhesion ?: Carbon::now();
        $adhesion->save();

        return $adhesion;
    }
}
