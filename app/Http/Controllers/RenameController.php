<?php
namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\Blog;
use duzun\hQuery;

class RenameController extends BaseController
{

    public function blogsByPostID()
    {
        $blogs = Blog::where('site_id', '=', 2)->get();
        foreach($blogs as $blog)
        {
           $base_url = "https://blog.shortletsmalta.com/?p=";
           $postid = $blog['postid'];
           $url = $base_url.$postid;
           echo  $url;
        }
    } 

    public function extractMetaFromUrl($url){
        //Defining a global var HTTP_CONTEXT variable - needed for plugin use
        $HTTP_CONTEXT = stream_context_create([
            'http'=>array( 
                'method'=>"GET", 
                'header'=>array("Accept-language: en", 
                                        "Cookie: foo=bar", 
                                        "Custom-Header: value") 
                ) 
        ]);
        //need to have a cache set up for this plugin to work
        hQuery::$cache_path = "/Users/leanne/Sites/php-script/image-script/imageScriptSLM/resources/cache";

        foreach($url as $current_url){
            $doc = hQuery::fromFile($currentURL, false, $HTTP_CONTEXT); 
            echo($doc);
        }
    }
}