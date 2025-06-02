<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'description', 'pdf_path', 'user_id', 'approved'];

    // protected $casts = [
    //     'approved' => 'boolean',
    // ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'book_category');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class)->where('approved', true);

    }
}
