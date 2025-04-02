<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;
use App\Notifications\CustomResetPasswordNotification;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected $primaryKey = 'user_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'status_id',
        'group_id',
        'last_name',
        'first_name',
        'email',
        'email_imt',
        'password',
        'phone_number',
        'photo_release',
        'photo_consent',
        'photo', // Ensure this is included
        'is_admin',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'photo_release' => 'boolean',
        'photo_consent' => 'boolean',
        'is_admin' => 'boolean',
    ];

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new CustomResetPasswordNotification($token, $this->email));
    }

    public function adhesions(): HasMany
    {
        return $this->hasMany(Adhesion::class, 'user_id', 'user_id');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class, 'status_id', 'status_id');
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class, 'group_id', 'group_id');
    }

    public function sites(): BelongsToMany
    {
        return $this->belongsToMany(Site::class, 'site_user', 'user_id', 'site_id');
    }

    public function familyMembers(): HasMany
    {
        return $this->hasMany(FamilyMember::class, 'user_id', 'user_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            $validator = Validator::make($model->getAttributes(), [
                'status_id' => 'required|exists:status,status_id',
                'group_id' => 'required|exists:group,group_id',
                'last_name' => 'required|string|max:255',
                'first_name' => 'required|string|max:255',
                'email' => 'nullable|email|max:255|unique:users,email' . ($model->exists ? ',' . $model->user_id . ',user_id' : ''),
                'email_imt' => 'nullable|email|max:255|unique:users,email_imt' . ($model->exists ? ',' . $model->user_id . ',user_id' : ''),
                'password' => $model->exists ? 'nullable|string|min:8' : 'required|string|min:8',
                'phone_number' => 'nullable|string|max:255',
                'photo_release' => 'required|boolean',
                'photo_consent' => 'required|boolean',
                'is_admin' => 'required|boolean',
                'photo' => 'nullable|string|max:255'
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }
        });
    }

    public function hasUpToDateAdhesion(): bool
    {
        // Default to September 1st if not set
        $adhesionMonthDay = env('ADHESION_MONTH_DAY', '09-01');

        try {
            [$month, $day] = explode('-', $adhesionMonthDay);
            $month = (int)$month;
            $day = (int)$day;

            // Validate month and day
            if ($month < 1 || $month > 12 || $day < 1 || $day > 31) {
                // Default to September 1st if invalid
                $month = 9;
                $day = 1;
            }

            $startDate = Carbon::createFromDate(now()->year, $month, $day);
            $endDate = Carbon::now();

            foreach ($this->adhesions as $adhesion) {
                $adhesionDate = Carbon::parse($adhesion->date_adhesion);

                if ($adhesionDate->between($startDate, $endDate, true)) {
                    return true;
                }
            }

            return false;
        } catch (\Exception $e) {
            // Log the error and return false
            \Log::error("Error in hasUpToDateAdhesion: " . $e->getMessage());
            return false;
        }
    }

    public function getBoutiquesGerees()
    {
        $userId = $this->user_id;
        return Shop::whereHas('administrators', function ($query) use ($userId) {
            $query->where('administrator.user_id', $userId);
        })->get();
    }

    public static function getUsersWithAdhesionsByDateRange($startDate, $endDate)
    {
        return self::whereHas('adhesions', function ($query) use ($startDate, $endDate) {
            $query->whereBetween('date_adhesion', [$startDate, $endDate]);
        })
        ->with(['adhesions' => function($query) use ($startDate, $endDate) {
            $query->whereBetween('date_adhesion', [$startDate, $endDate])
                  ->orderBy('date_adhesion', 'desc');
        }])
        ->get();
    }


    public static function getUsersWithAdhesionsAndFamily()
    {
        return self::has('adhesions')
            ->with([
                'adhesions' => function ($query) {
                    $query->orderBy('date_adhesion', 'desc');
                },
                'familyMembers',
                'sites',
                'group'
            ])
            ->get()
            ->map(function ($user) {
                return [
                    'user_id' => $user->user_id,
                    'group_id' => $user->group_id,
                    'last_name' => $user->last_name,
                    'first_name' => $user->first_name,
                    'group' => [
                        'group_id' => $user->group->group_id,
                        'label_group' => $user->group->label_group
                    ],
                    'family_members' => [
                        'spouse' => $user->familyMembers->contains('relation_id', 2) ? 'true' : 'false',
                        'child_nb' => $user->familyMembers->where('relation_id', 1)->count()
                    ],
                    'sites' => $user->sites->map(function ($site) {
                        return [
                            'site_id' => $site->site_id,
                            'label_site' => $site->label_site
                        ];
                    }),
                    'adhesions' => $user->adhesions->map(function ($adhesion) {
                        return [
                            'date_adhesion' => $adhesion->date_adhesion
                        ];
                    })
                ];
            });
    }

    public static function getAllUsersWithRelations()
    {
        return self::with([
            'adhesions' => function ($query) {
                $query->orderBy('date_adhesion', 'desc');
            },
            'familyMembers',
            'sites',
            'group'
        ])
        ->get()
        ->map(function ($user) {
            return [
                'user_id' => $user->user_id,
                'group_id' => $user->group_id,
                'last_name' => $user->last_name,
                'first_name' => $user->first_name,
                'group' => [
                    'group_id' => $user->group->group_id,
                    'label_group' => $user->group->label_group
                ],
                'family_members' => [
                    'spouse' => $user->familyMembers->contains('relation_id', 2) ? 'true' : 'false',
                    'child_nb' => $user->familyMembers->where('relation_id', 1)->count()
                ],
                'sites' => $user->sites->map(function ($site) {
                    return [
                        'site_id' => $site->site_id,
                        'label_site' => $site->label_site
                    ];
                }),
                'adhesions' => $user->adhesions->map(function ($adhesion) {
                    return [
                        'date_adhesion' => $adhesion->date_adhesion
                    ];
                }),
                'is_adherent' => $user->adhesions->isNotEmpty()
            ];
        });
    }
}
