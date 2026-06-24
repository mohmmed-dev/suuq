<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'slug', 'description', 'price', 'covers', 'stock', 'is_active', 'is_deleted'])]
class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory;

    public function users()
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    public function likes()
    {
        return $this->belongsToMany(User::class, 'likes')->withTimestamps();
    }

    public function orders()
    {
        $this->belongsToMany(Order::class)->withPivot('')->withTimestamps();
    }

    public function Comments()
    {
        return $this->hasMany(Comment::class);
    }
}
