<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class StateSub extends Model
{
    use HasFactory;
    protected $table = 'state_sub';
    protected $connection = 'mysql';

    protected $primaryKey = 'state_id';
    public $timestamps = false;

    protected $fillable = [
        'label_state',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            $validator = Validator::make($model->getAttributes(), [
                'label_state' => 'required|string|max:255'
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }
        });
    }
}
