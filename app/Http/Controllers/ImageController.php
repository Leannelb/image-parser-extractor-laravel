<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use duzun\hQuery;

class ImageController extends BaseController
{
    function getImageLinks(){

        //
        hQuery::$cache_path = "/Users/leanne/Sites/php-script/image-script/imageScriptSLM/resources/cache";

        // Extract links and images
        $links  = array();
        $images = array();
        $titles = array();

        //set up roxy
        $http_context = stream_context_create([
            'http'=>array( 
                'method'=>"GET", 
                'header'=>array("Accept-language: en", 
                                       "Cookie: foo=bar", 
                                       "Custom-Header: value") 
              ) 
        ]);

        $doc = hQuery::fromFile('https://blog.shortletsmalta.com/', false, $http_context);
        // dump($doc);

        // Find all headings with the class entry-title
        $links = $doc->find('h2.entry-title');
        foreach($links as $link){
            if(!$link->hasClass('href')){
                $href = $link->find('href')[0]; // ArrayAccess
                if ( $href ) $images[$link] = $href->src;
                dd( $href->src);
            }
            
        }
       

      


    }
}
