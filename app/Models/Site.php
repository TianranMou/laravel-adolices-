<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
//use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class Site extends Model
{
    //use HasFactory;

    protected $table = 'site';
    protected $primaryKey = 'site_id';
    public $timestamps = false;

    protected $fillable = [
        'label_site',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'site_user', 'site_id', 'user_id');
    }

    public static function findByLabel(string $label): ?self
    {
        return static::where('label_site', $label)->first();
    }

    public static function findOrCreateByLabel(string $label): self
    {
        return static::firstOrCreate(['label_site' => $label]);
    }

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
