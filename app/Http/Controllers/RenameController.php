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
        $array_all_urls= [];
        $blogs = Blog::where('site_id', '=', 2)->where('postid', '>', 0)->limit(1)->get();
        foreach($blogs as $blog)
        {
           $base_url = "https://blog.shortletsmalta.com/?p=".$blog['postid'];
        //    $postid = $blog['postid'];
        //    $url = $base_url.$postid;
           $array_all_urls[] = $base_url; //to push each new $url onto the array
        }
        // print_r($array_all_urls);
        // // return;
        // $meta = $this.$imageMetaLinks;
        $meta = $this->extractMetaFromUrl($array_all_urls);
        foreach($meta as $currentMeta)
        {
            $path = "images/blog/";
            $insertPath = $path.$currentMeta;
            echo($insertPath);
            // print_r($currentMeta[0]);
            //  $b = Blog::where('postid', $blog['postid'])->first();
             if ($blogs!=null){
                $newBlog = new Blog();
                $newBlog->fill([
                    'image_url' => $currentMeta
                ])->save(); 
             }
        }


    } 

    public function extractMetaFromUrl($array_all_urls){
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
        foreach($array_all_urls as $current_url){
            $imageMetaLinks = [];
            $doc = hQuery::fromFile($current_url, false, $HTTP_CONTEXT); 
            // Find all meta:og image tags
            $metaTags = $doc->find('meta:property=og:image');
            foreach($metaTags as $key=>$meta)
            {
                //once found DO SOMETHING with it
                $metaContent = $meta->attr('content');
                if(strpos($metaContent,'http') > -1 && strpos($metaContent,'jpg') > -1 || strpos($metaContent,'png') > -1){
                    $proccesedLink['imageLink']= $metaContent;
                    $imageMetaLinks[] = $metaContent; 
                }
            }
        }
        return $imageMetaLinks;
    }
}