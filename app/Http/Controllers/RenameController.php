<?php
namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\Blog;
use duzun\hQuery;
use Storage;

class RenameController extends BaseController
{

    public function blogsByPostID()
    {
        $array_all_urls= [];
        $blogs = Blog::where('site_id', '=', 2)->where('postid', '>', 0)->whereNull("image_url")->get();
        foreach($blogs as $blog)
        {
           $base_url = "https://blog.shortletsmalta.com/?p=".$blog['postid'];
           //$array_all_urls[] = $base_url; //to push each new $url onto the array

           $image_remote_path = $this->extractMetaFromUrl($base_url);
           if(isset($image_remote_path))
           {
                //$insertPath = $path.$currentMeta;
                echo "Download image from path: ".$image_remote_path;

                //after we have downloaded it and named it
                $extension = explode(".",$image_remote_path);
                if(count($extension)>0)
                {
                    $extension = $extension[count($extension)-1];
                }else{
                    $extension = "png";
                }
                $newFileName = time()."-".$blog->postid.".".$extension;
                //$path = "images/blog/".$newFileName;
                echo "Image for Post id: ".$blog->postid." not found";
        //         $contents = file_get_contents($image_remote_path);
        //         Storage::put("blog/".$newFileName, $contents);

        //         $blog->image_url = "images/blog/".$newFileName;
        //         $blog->save();

           }else{
               echo "Image for Post id: ".$blog->postid." not found";
           }
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
        $imageMetaLinks = [];
        $doc = hQuery::fromFile($url, false, $HTTP_CONTEXT); 
        // Find all meta:og image tags
        $metaTags = $doc->find('meta:property=og:image');
        foreach($metaTags as $key=>$meta)
        {
            //once found DO SOMETHING with it
            $metaContent = $meta->attr('content');
            if(strpos($metaContent,'http') > -1 && strpos($metaContent,'jpg') > -1 || strpos($metaContent,'png') > -1){
                //$proccesedLink['imageLink']= $metaContent;
                //$imageMetaLinks[] = $metaContent; 
                return $metaContent;
            }
        }
        return null;
    }
}