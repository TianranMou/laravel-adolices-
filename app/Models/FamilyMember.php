<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class FamilyMember extends Model
{
    use HasFactory;

    protected $table = 'family_members';
    protected $primaryKey = 'member_id';
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'name_member',
        'birth_date_member',
        'first_name_member',
        'relation'
    ];

    protected $casts = [
        'birth_date_member' => 'date'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            $validator = Validator::make($model->getAttributes(), [
                'user_id' => 'required|exists:users,user_id',
                'name_member' => 'required|string|max:255',
                'first_name_member' => 'required|string|max:255',
                'birth_date_member' => 'nullable|date',
                'relation' => 'required|string|max:255'
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }
        });
    }
}
