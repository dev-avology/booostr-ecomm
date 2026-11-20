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

        $seo=$home_data->seo ?? '';
        $pageTitle = store_page_title('Blogs');
        SEOMeta::setTitle($pageTitle);
        SEOMeta::setDescription($seo->description ?? '');
       

        OpenGraph::setDescription($seo->description ?? '');
        OpenGraph::setTitle($pageTitle);

        OpenGraph::addProperty('keywords', $seo->tags ?? '');

        TwitterCard::setTitle($pageTitle);
        TwitterCard::setSite($seo->twitter_title ?? '');

        JsonLd::setTitle($pageTitle);
        JsonLd::setDescription($seo->description ?? '');
        JsonLd::addImage($seo->meta_image ?? '');

        SEOTools::setTitle($pageTitle);
        SEOTools::setDescription($seo->description ?? '');
        SEOTools::opengraph()->setUrl(url('/'));
        SEOTools::setCanonical(url('/'));
        
        SEOTools::twitter()->setSite($seo->twitter_title ?? '');
        SEOTools::jsonLd()->addImage($seo->meta_image ?? '');
        SEOTools::opengraph()->addProperty('keywords', $seo->tags ?? '');

        $page_data=$home_data->meta ?? '';

         $posts=Term::query()->where('type','blog')->where('status',1)->with('preview','excerpt');
         if (!empty($request->src)) {
             $posts=$posts->where('title','LIKE','%'.$request->src.'%');
         }
         $posts=$posts->latest()->paginate(12);
         return  view(baseview('blog/index'),compact('posts','page_data'));
    }

    public function show($slug)
    {
        $info=Term::query()->where('type','blog')->where('status',1)->where('slug',$slug)->with('excerpt','description','preview')->first();
        abort_if(empty($info),404);
        $recent_posts=Term::query()->where('type','blog')->where('status',1)->with('preview')->latest()->take(3)->get();

        $pageTitle = store_page_title($info->title ?? 'Blog');
        JsonLdMulti::setTitle($pageTitle);
        JsonLdMulti::setDescription($info->excerpt->value ?? '');
        JsonLdMulti::addImage(asset($info->preview->value ?? ''));

        SEOMeta::setTitle($pageTitle);
        SEOMeta::setDescription($info->excerpt->value ?? '');
     
        SEOTools::setTitle($pageTitle);
        SEOTools::setDescription($info->excerpt->value ?? '');
     
        SEOTools::opengraph()->addProperty('image', asset('uploads/'.tenant('uid').'/logo.png'));
        SEOTools::twitter()->setTitle($pageTitle);
        SEOTools::twitter()->setSite($pageTitle);
        SEOTools::jsonLd()->addImage(asset($info->preview->value ?? ''));
        
        return  view(baseview('blog/show'),compact('info','recent_posts'));
    }
}
