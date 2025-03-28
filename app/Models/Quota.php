<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            $validator = Validator::make($model->getAttributes(), [
                'value' => 'required|integer',
                'duration' => 'required|integer'
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }
        });
    }
}
