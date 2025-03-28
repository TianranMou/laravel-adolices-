<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class Administrator extends Model
{
    protected $table = 'administrator';
    protected $primaryKey = ['shop_id', 'user_id'];
    public $incrementing = false;
    public $timestamps = true;

    protected $fillable = [
        'shop_id',
        'user_id',
    ];

    const UPDATED_AT = null;
    const CREATED_AT = null;

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class, 'shop_id', 'shop_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            $validator = Validator::make($model->getAttributes(), [
                'shop_id' => 'required|exists:shop,shop_id',
                'user_id' => 'required|exists:users,user_id'
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }
        });
    }
}
