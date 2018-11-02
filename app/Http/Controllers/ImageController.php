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

        // VAR DECLARATIONS 
        // Extract links and images, define vars and arrays
        $links  = array();
        $images = array();
        $titles = array();
        //To use  global var from outside function, write global infront of the var you intend to use as a global (not when initally defining it).
        global $HTTP_CONTEXT;
        //This array holds (a)->href, the string inside the href, i.e. an array of the blog links.
        $proccesedLinks = [];

        $doc = hQuery::fromFile('https://blog.shortletsmalta.com/page1/', false, $HTTP_CONTEXT);
   
        //LOOP THROUGH ALL PAGES - 6
        for($pageNum=1; $pageNum<=6; $pageNum++ ){
            $URL = 'https://blog.shortletsmalta.com/page';
            $page = (string)$pageNum;
            $completeURL = "{$URL}{$page}";
        }
        
        foreach($completeURL as $currentURL){
            $doc = hQuery::fromFile($currentURL, false, $HTTP_CONTEXT);
        }
    
    
       


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


                $this->saveImage($key,$proccesedLink['imageLink'], $proccesedLink['title']);

                break;
            }
        }

        return null;
    }
    //this function downloads the images to a folder on computer
    function saveImage($key,$imageLink, string $title){
        $imageContent = file_get_contents($imageLink);
        $imagePath = '/Users/leanne/Sites/php-script/image-script/imageScriptSLM/storage/image_local_links';
        $imageExtension = strpos($imageLink,'png') ? 'png' : 'jpg';
        $imageName = "{$key}.{$imageExtension}";

        $imageInstance = Image::firstOrCreate(['name' => $key]);
        $imageInstance->update(['extension' => $imageExtension, 'title' => $title]);
        file_put_contents($imagePath.'/'.$imageName, $imageContent);
        
    }

    // public function assignBlogIds()
    // {
    //     echo "Hi im in here";
    //     $blogArray[] = DB::table('blog')->get();
    //     $picsArray[] = DB::table('shortlets_images')->get();
    //     foreach($blogArray as $blog)
    //     {
    //         if($blogArray['title'] == $picsArray['title'])
    //         {
    //             DB::table('shortlets_images')->insert(
    //                 [$blogArray['blog_id'] => $picsArray['blog_id']]
    //             );
    //         }
    //     }
    //     // $imageTitles = DB::table('shortlets_images.title')->get();
    //     // $titlesOfBlogs = DB::table('blog')->where('blog.title' == $imageTitles);
    // }


    public function assignBlogIds()
    {
        $blogArray[] = DB::table('blog')->get();
        $picsArray[] = DB::table('shortlets_images')->get();
        foreach($blogArray as $blog)
        {
            foreach($picsArray as $pics)
            {
                if($blog['title'] == $picsArray['title'])
                {
                    DB::table('shortlets_images')->insert(
                        [$blogArray['blog_id'] => $picsArray['blog_id']]
                    );
                }
            }
            
        }
    }
}