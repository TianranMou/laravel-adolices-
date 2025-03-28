<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class Status extends Model
{
    use HasFactory;

    protected $primaryKey = 'status_id';
    protected $table = 'status';
    public $timestamps = false;

    protected $fillable = [
        'status_label'
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'status_id', 'status_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            $validator = Validator::make($model->getAttributes(), [
                'status_label' => 'required|string|max:255'
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }
        });
    }
}
