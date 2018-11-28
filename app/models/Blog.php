<?php

namespace App\Models;

use DB;
use Illuminate\Database\Eloquent\Model;
use Faker\Provider\Image;

class Blog extends Model
{
    protected $fillable = array 
    (
        'id', 
        'postid', 
        'published_at', 
        'title', 
        'content', 
        'slug', 
        'client_id', 
        'status_id', 
        'site_id', 
        'author_id'
    );

    protected $table = 'blog';

     public function images()
    {
        return $this->hasOne(Image::class, 'title', 'title');
    }
}