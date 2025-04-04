<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Shop extends Model
{
    use HasFactory;

    protected $table = 'shop';

    protected $primaryKey = 'shop_id';

    protected $fillable = [
        'shop_name',
        'short_description',
        'long_description',
        'min_limit',
        'end_date',
        'is_active',
        'thumbnail',
        'doc_link',
        'bc_link',
        'ha_link',
        'photo_link',
    ];

    protected $casts = [
        'end_date' => 'date',
        'is_active' => 'boolean',
        'min_limit' => 'integer',
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'shop_id', 'shop_id');
    }

    public static function getAllAvailableShops()
    {
        return self::where('is_active', true)
                    ->where(function ($query) {
                        $query->where('end_date', '>=', now())
                              ->orWhereNull('end_date');
                    })->get();
    }

    public function administrators(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'administrator', 'shop_id', 'user_id')
                    ->withTimestamps();
    }

    public static function findOrCreateByName(string $name, array $attributes = []): self
    {
        return static::firstOrCreate(
            ['shop_name' => $name],
            $attributes
        );
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            $validator = Validator::make($model->getAttributes(), [
                'shop_name' => 'required|string|max:250',
                'short_description' => 'required|string',
                'long_description' => 'required|string',
                'min_limit' => 'required|integer|min:0',
                'end_date' => 'nullable|date',
                'is_active' => 'required|boolean',
                'thumbnail' => 'nullable|string',
                'doc_link' => 'nullable|url',
                'bc_link' => 'nullable|url',
                'ha_link' => 'nullable|url',
                'photo_link' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }
        });
    }
}
