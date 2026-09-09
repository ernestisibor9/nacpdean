<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /*
    |--------------------------------------------------------------------------
    | Mass Assignable Attributes
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
        'name',
        'username',
        'email',
        'phone',
        'password',
        'member_type',
        'membership_category_id',
        'role',
        'user_type',
        'status',
    ];

    /*
    |--------------------------------------------------------------------------
    | Hidden Attributes
    |--------------------------------------------------------------------------
    */

    protected $hidden = [
        'password',
        'remember_token',
        'otp',
    ];

    /*
    |--------------------------------------------------------------------------
    | Attribute Casting
    |--------------------------------------------------------------------------
    */

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'otp_expires_at' => 'datetime',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Payments
    |--------------------------------------------------------------------------
    */

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Profile
    |--------------------------------------------------------------------------
    */

    public function profile()
    {
        return $this->hasOne(MemberProfile::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Member Fees
    |--------------------------------------------------------------------------
    */

    public function memberFees()
    {
        return $this->hasMany(MemberFee::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Membership
    |--------------------------------------------------------------------------
    */

    public function membership()
    {
        return $this->hasOne(Membership::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Transactions
    |--------------------------------------------------------------------------
    */

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Membership Category
    |--------------------------------------------------------------------------
    */

    public function membershipCategory()
    {
        return $this->belongsTo(
            MembershipCategory::class
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Generated Documents
    |--------------------------------------------------------------------------
    */

    public function generatedDocuments()
    {
        return $this->hasMany(
            GeneratedDocument::class
        );
    }


    /*
|--------------------------------------------------------------------------
| VIOLATIONS
|--------------------------------------------------------------------------
*/

public function violations(): HasMany
{
    return $this->hasMany(
        Violation::class
    );
}

}
