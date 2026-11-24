<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Term;
use Artesaos\SEOTools\Facades\SEOMeta;
use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\TwitterCard;
use Artesaos\SEOTools\Facades\JsonLd;
use Artesaos\SEOTools\Facades\JsonLdMulti;
use Artesaos\SEOTools\Facades\SEOTools;
class BlogController extends Controller
{
    public function index(Request $request)
    {
        $home_data=optionfromcache('blog_page');
        store_set_seo('Blogs');

        $page_data=$home_data->meta ?? '';

         $posts=Term::query()->where('type','blog')->where('status',1)->with('preview','excerpt');
         if (!empty($request->src)) {
             $posts=$posts->where('title','LIKE','%'.$request->src.'%');
         }
         $posts=$posts->latest()->paginate(12);
         return  view(baseview('blog/index'),['posts' => $posts, 'page_data' => $page_data]);
    }

    public function show($slug)
    {
        $info=Term::query()->where('type','blog')->where('status',1)->where('slug',$slug)->with('excerpt','description','preview')->first();
        abort_if(empty($info),404);
        $recent_posts=Term::query()->where('type','blog')->where('status',1)->with('preview')->latest()->take(3)->get();

        $previewImg = !empty($info->preview->value) ? asset($info->preview->value) : null;
        store_set_seo($info->title ?? 'Blog', $info->excerpt->value ?? null, $previewImg, 'article');
        
        return  view(baseview('blog/show'),['info' => $info, 'recent_posts' => $recent_posts]);
    }
}
