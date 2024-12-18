<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'package_id',
        'name',
        'number_of_beds',
        'number_of_bathrooms',
        'day_deposit',
        'weekly_deposit',
        'monthly_deposit'
    ];

    public function package()
    {
        return $this->belongsTo(Package::class);
    }
    public function prices()
    {
        return $this->hasMany(RoomPrice::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function roomPrices()
    {
        return $this->hasMany(RoomPrice::class);
    }

    public function bookingRoomPrices()
    {
        return $this->hasMany(BookingRoomPrice::class);
    }
}
