<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class Ticket extends Model {

    use HasFactory;
    protected $table = 'ticket';
    protected $primaryKey = 'ticket_id';
    public $timestamps = true;

    protected $fillable = [
        'product_id',
        'user_id',
        'site_id',
        'ticket_link',
        'partner_code',
        'partner_id',
        'validity_date',
        'purchase_date',
    ];

    protected $casts = [
        'validity_date' => 'date',
        'purchase_date' => 'date'
    ];

    public function produit(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class, 'site_id', 'site_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            $validator = Validator::make($model->getAttributes(), [
                'product_id' => 'required|exists:product,product_id',
                'user_id' => 'required|exists:users,user_id',
                'site_id' => 'required|exists:site,site_id',
                'ticket_link' => 'nullable|string',
                'partner_code' => 'nullable|string',
                'partner_id' => 'nullable|string',
                'validity_date' => 'nullable|date',
                'purchase_date' => 'nullable|date'
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }
        });
    }
}
