<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'occupation',
        'avatar',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
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
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // user memiliki banyak courses
    // pada model course berada di pivotable course_students
    public function courses(){ 
        return $this->belongsToMany(Course::class, 'course_students');
    }

    // satu pengguna dapat langganan berkali-kali
    public function subscribe_transactions(){
        return $this->hasMany(SubscribeTransaction::class);
    }

    // check, langganan terakhirnya masih active atau tidaa
    // case: dia sudah bayar atau belum, diambil dari data updated_at satu saja yang paling terakhir (first).
    public function hasActiveSubscription(){
        $latestSubscription = $this->subscribe_transactions()
        ->where('is_paid', true)
        ->latest('updated_at')
        ->first();

        if (!$latestSubscription) {
            return false;
        }

        // dicheck apakah dia masih aktif atau tidak setelah 30 hari
        $subscriptionEndDate = Carbon::parse($latestSubscription->subscription_start_date)->addMonths(1);
        return Carbon::now()->lessThanOrEqualTo($subscriptionEndDate); // true == dia berlangganan
    }

    // dry, don't repeat urself
}
