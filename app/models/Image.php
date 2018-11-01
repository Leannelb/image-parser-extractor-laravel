<?php

namespace App\Models;

use DB;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    protected $fillable = array ('title', 'imgLink');
    protected $table = 'shortlets_images';
}




// ALTER TABLE `blog` ADD `postid` INT(11) NULL DEFAULT NULL AFTER `meta_description`;
