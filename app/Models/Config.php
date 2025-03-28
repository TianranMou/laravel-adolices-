<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class Config extends Model
{
    use HasFactory;

    protected $table = 'config';
    protected $primaryKey = 'config_id';
    protected $fillable = ['config_label', 'config_value'];
    public $timestamps = false;


    public static function findByLabel($label)
    {
        return static::where('config_label', $label)->first();
    }
    
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            $validator = Validator::make($model->getAttributes(), [
                'config_label' => 'required|string|max:50',
                'config_value' => 'required|string|max:255'
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }
        });
    }
}
