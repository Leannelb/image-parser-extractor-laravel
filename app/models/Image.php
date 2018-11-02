<?php

namespace App\Models;

use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Image extends Model
{
    use SoftDeletes;

    protected $fillable = array ('name', 'extension', 'title');

    protected $table = 'shortlets_images';

    public function getImageDirectoryAttribute()
    {
        return storage_path("image_local_links/{$this->name}.{$this->extension}");
    }

    public function blogs()
    {
        return $this->belongsTo('App\Models\Blog');
    }
}

