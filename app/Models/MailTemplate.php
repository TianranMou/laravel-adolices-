<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class MailTemplate extends Model
{
    protected $table = 'template_mail';
    protected $primaryKey = 'mail_template_id';
    public $timestamps = false;

    protected $fillable = [
        'shop_id',
        'subject',
        'content',
    ];

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class, 'shop_id', 'shop_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            $validator = Validator::make($model->getAttributes(), [
                'shop_id' => 'exists:shop,shop_id',
                'subject' => 'required|string|max:100',
                'content' => 'required|string'
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }
        });
    }
}
