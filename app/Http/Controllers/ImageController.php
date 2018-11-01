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
use App\Models\Image;

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
        //To use  global var from outside function, write global infront of the var you intend to use as a global (not when initally defining it).
        global $HTTP_CONTEXT;
        //This array holds (a)->href, the string inside the href, i.e. an array of the blog links.
        $proccesedLinks = [];

        $doc = hQuery::fromFile('https://blog.shortletsmalta.com/page1/', false, $HTTP_CONTEXT);

        // Find all h2 with the class entry-title, i.e. links to blog posts
        $links = $doc->find('h2.entry-title');

        //loop each h2.entry-title
        foreach($links as $key=> $link){
            //access the href and put into array $proccesedLinks[]
            $href = $link->find('a')->href; // ArrayAccess
            $proccesedLinks[] = ['title' => $link->find('a')->text(),'link'=>$href];

        }

        //pulls the img link for each blog post
        foreach($proccesedLinks as $link){
            $this->getImageFromBlog($link);
            // $content = $link->link;

            // dd($content);
            
         }

        
    }
    //funtion to pull the img link for each blog post
    function getImageFromBlog(&$proccesedLink){
        global $HTTP_CONTEXT;
        $doc = hQuery::fromFile($proccesedLink['link'], false, $HTTP_CONTEXT);
        // dd($doc);
        $metaTags = $doc->find('meta:property=og:image');
        foreach($metaTags as $key=>$meta){
            $metaContent = $meta->attr('content');
            if(strpos($metaContent,'http') > -1 && strpos($metaContent,'jpg') > -1 || strpos($metaContent,'png') > -1){
                // echo $key.' '.$meta->attr('content').'<br>';
                $proccesedLink['imageLink']= $metaContent;
                $this->saveImage($key,$proccesedLink['imageLink']);
                break;
            }
        }

       
        // $url = 'http://example.com/image.php';
        // $img = '/my/folder/flower.gif';
        // file_put_contents($img, file_get_contents($url));
        // $processedMetaImages[] = ['image'=>$doc->find('meta.og:image')->text()];
        // dd($processedMetaImages);
    }
    
    function saveImage($key,$imageLink){
        $imageContent = file_get_contents($imageLink);
        $imagePath = '/Users/leanne/Sites/php-script/image-script/imageScriptSLM/resources/image-local-links';
        $imageName = "$key.".(strpos($imageLink,'png') ? 'png' : 'jpg' );
        file_put_contents($imagePath.'/'.$imageName, $imageContent);
    }
}

// <meta property="og:image" content="https://shortletsmalta.files.wordpress.com/2018/07/ffm.jpg?w=1200">