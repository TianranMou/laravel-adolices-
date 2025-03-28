<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class Product extends Model
{
    use HasFactory;
    protected $table = 'product';
    protected $primaryKey = 'product_id';
    public $timestamps = false;

    protected $fillable = [
        'quota_id',
        'shop_id',
        'withdrawal_method',
        'product_name',
        'subsidized_price',
        'price',
        'dematerialized'
    ];

    protected $casts = [
        'price' => 'float',
        'subsidized_price' => 'float',
        'dematerialized' => 'boolean'
    ];

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class, 'shop_id', 'shop_id');
    }

    public function quota(): BelongsTo
    {
        return $this->belongsTo(Quota::class, 'quota_id', 'quota_id');
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'product_id', 'product_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            $validator = Validator::make($model->getAttributes(), [
                'quota_id' => 'required|exists:quota,quota_id',
                'shop_id' => 'required|exists:shop,shop_id',
                'withdrawal_method' => 'required|in:pickup,delivery,digital',
                'product_name' => 'required|string|max:250',
                'subsidized_price' => 'required|numeric|min:0',
                'price' => 'required|numeric|min:0|gte:subsidized_price',
                'dematerialized' => 'required|boolean'
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }
        });
    }
}
