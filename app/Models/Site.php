<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class Site extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'site';
    protected $primaryKey = 'site_id';
    protected $connection = 'mysql';

    protected $fillable = [
        'label_site',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            $validator = Validator::make($model->getAttributes(), [
                'label_site' => 'required|string|max:190'
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }
        });
    }
}
