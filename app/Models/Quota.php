<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class Quota extends Model {

    use HasFactory;
    protected $table = 'quota';
    protected $primaryKey = 'quota_id';
    public $timestamps = false;
    protected $connection = 'mysql';
    protected $fillable = [
        'value',
        'duration',
    ];

    protected $casts = [
        'value' => 'integer',
        'duration' => 'integer'
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'quota_id', 'quota_id');
    }

    public static function findOrCreateByValueAndDuration(int $value, int $duration): self
    {
        return static::firstOrCreate([
            'value' => $value,
            'duration' => $duration
        ]);
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            $validator = Validator::make($model->getAttributes(), [
                'value' => 'required|integer|min:0',
                'duration' => 'required|integer|min:0'
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }
        });
    }
}
