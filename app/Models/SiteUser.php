<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class SiteUser extends Model
{
    use HasFactory;

    protected $table = 'site_user';
    protected $primaryKey = ['site_id', 'user_id'];
    public $incrementing = false;
    public $timestamps = true;

    protected $fillable = [
        'site_id',
        'user_id',
    ];

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class, 'site_id', 'site_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            $validator = Validator::make($model->getAttributes(), [
                'site_id' => 'required|exists:site,site_id',
                'user_id' => 'required|exists:users,user_id'
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }
        });
    }

    public static function createTable()
    {
        Schema::create('site_user', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('site_id');
            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->foreign('site_id')->references('site_id')->on('site')->onDelete('cascade');
            $table->primary(['user_id', 'site_id']);
        });
    }
}
