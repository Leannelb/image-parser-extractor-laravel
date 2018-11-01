<?php
/*
*   Used plugin https://github.com/duzun/hQuery.php for parsing HTML documents
*
*/

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
// use hQuery
use duzun\hQuery;

class ImageController extends BaseController
{
    function getImageLinks(){
        
        //Defining a global HTTP_CONTEXT variable - needed for plugin use
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

        // Extract links and images, define vars and arrays
        $links  = array();
        $images = array();
        $titles = array();
        //To use  global var from outside function, write global infromt of the var you intend to use as a global (not when initally defining it).
        global $HTTP_CONTEXT;
        //This array holds (a)->href, the string inside the href, i.e. an array of the blog links.
        $proccesedLinks = [];

        $doc = hQuery::fromFile('https://blog.shortletsmalta.com/', false, $HTTP_CONTEXT);

        // Find all h2 with the class entry-title, i.e. links to blog posts
        $links = $doc->find('h2.entry-title');

        //loop each h2.entry-title
        foreach($links as $link){
            //access the href and put into array $proccesedLinks[]
            $href = $link->find('a')->href; // ArrayAccess
            $proccesedLinks[] = ['title' => $link->find('a')->text(),'link'=>$href];
        }

        foreach($proccesedLinks as $link){
            $this->getImageFromBlog($link);
        }
        
        dd($proccesedLinks);
    }

    function getImageFromBlog(&$proccesedLink){
        global $HTTP_CONTEXT;
        $doc = hQuery::fromFile($proccesedLink['link'], false, $HTTP_CONTEXT);
        // dd($doc);
        $metaTags = $doc->find('meta:property=og:image');
        foreach($metaTags as $key=>$meta){
            $metaContent = $meta->attr('content');
            if(strpos($metaContent,'http') > -1 && strpos($metaContent,'jpg') > -1 ){
                // echo $key.' '.$meta->attr('content').'<br>';
                $proccesedLink['imageLink']= $metaContent;
                break;
            }
        }
        return $proccesedLink;
        // $processedMetaImages[] = ['image'=>$doc->find('meta.og:image')->text()];
        // dd($processedMetaImages);
    }

}

// <meta property="og:image" content="https://shortletsmalta.files.wordpress.com/2018/07/ffm.jpg?w=1200">