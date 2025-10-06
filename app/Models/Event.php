<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    // ✅ Allow these fields to be mass assignable
    protected $fillable = [
        'title',
        'description',
        'starts_at',
        'location',
        'capacity',
        'organiser_id'
    ];

    // ✅ Relationships
    public function organiser()
    {
        return $this->belongsTo(User::class, 'organiser_id');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
