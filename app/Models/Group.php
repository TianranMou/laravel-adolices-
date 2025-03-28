<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class Group extends Model
{
    use HasFactory;

    protected $primaryKey = 'group_id';
    protected $table = 'group';
    public $timestamps = false;

    protected $fillable = [
        'label_group'
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'group_id', 'group_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            $validator = Validator::make($model->getAttributes(), [
                'label_group' => 'required|string|max:255'
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }
        });
    }
}
