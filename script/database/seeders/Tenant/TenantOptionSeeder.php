<?php

namespace Database\Seeders\Tenant;

use Illuminate\Database\Seeder;
use App\Models\Option;
class TenantOptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {


        $club_info = tenant_club_info();
        $address = explode(',',$club_info['address']);

        $store_name = $club_info['club_name'];
        $store_email= $club_info['club_email'];
        // $store_address = trim($address[0]);
        // $store_house = trim($address[1]);
        // $store_state = trim($address[count($address)-2]);
        // $store_country = trim($address[count($address)-1]);
        $store_address = '';
        $store_house = '';
        $store_state = '';
        $store_city = '';
        $store_country = '';
        $phone_number = $club_info['phone_number'];
        $timezone = $club_info['timezone'];

        if($timezone == ''){
            $timezone = 'UTC';
        }

        $options= array(
        array(
            "id" => 1,
            "key" => "seo",
            "value" => '{"title":"foodfire","description":"test","canonical":"","tags":"test","twitterTitle":"@foodfire"}',
            "autoload" => 0
        ),
        array(
            "id" => 2,
            "key" => "google_map",
            "value" => '{"api":"","range":"","shipping":""}',
            "autoload" => 0
        ),
        array(
            "id" => 3,
            "key" => "languages",
            "value" => '[{"name":"English","code":"en"}]',
            "autoload" => 1
        ),
        array(
            "id" => 4,
            "key" => "store_sender_email",
            "value" => $store_email,
            "autoload" => 1
        ),
        array(
            "id" => 5,
            "key" => "invoice_data",
            "value" => '{"store_legal_name":"'.$store_name.'","store_legal_phone":"'.$phone_number.'","store_legal_address":"'.$store_address.'","store_legal_house":"'.$store_house.'","store_legal_city":"'.$store_city.'","country":"'.$store_country.'","state":"'.$store_state.'","post_code":"","store_legal_email":"'.$store_email.'"}',
            "autoload" => 1
        ),
        array(
            "id" => 6,
            "key" => "timezone",
            "value" => $timezone,
            "autoload" => 1
        ),
        array(
            "id" => 7,
            "key" => "default_language",
            "value" => "en",
            "autoload" => 1
        ),
        array(
            "id" => 8,
            "key" => "weight_type",
            "value" => "LB",
            "autoload" => 1
        ),
        array(
            "id" => 9,
            "key" => "currency_data",
            "value" => '{"currency_name":"USD","currency_position":"left","currency_icon":"$"}',
            "autoload" => 1
        ),
        array(
            "id" => 10,
            "key" => "average_times",
            "value" => '{"delivery_time":"10 min","pickup_time":"20 min"}',
            "autoload" => 0
        ),
        array(
            "id" => 11,
            "key" => "order_method",
            "value" => "mail",
            "autoload" => 0
        ),
        array(
            "id" => 12,
            "key" => "whatsapp_no",
            "value" => "8801830617500",
            "autoload" => 1
        ),
        array(
            "id" => 15,
            "key" => "order_settings",
            "value" => '{"order_method":"mail","shipping_amount_type":"shipping","google_api":"","google_api_range":"2000","delivery_fee":"10"}',
            "autoload" => 0
        ),
        array(
            "id" => 17,
            "key" => "home_page",
            "value" => '{"meta":{"featured_products_title":"Top Product","featured_products_description":"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent orci lacus, tempus quis libero nec, vehicula sagittis erat.","featured_products_status":"yes","products_area_short_title":"For you","products_area_title":"Latest Foods","products_area_description":"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent orci lacus, tempus quis libero nec, vehicula sagittiserat.","discount_product_title":"Deals Of The Day","discount_product_description":"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent orci lacus, tempus quis libero nec, vehicula sagittis erat.","discount_product_status":"yes","testimonial_title":"Happy Clients Say","testimonial_description":null,"testimonial_status":"yes","menu_area_title":"Our Recommendations","menu_area_description":"There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form.","menu_area_status":"yes","blog_area_short_title":"Read articles","blog_area_title":"Read articles","blog_area_description":"There are many variations of passages of Lorem Ipsum available,but the majority have suffered alteration in some form.","blog_area_status":"yes"},"seo":{"site_title":"Foodfaire up","twitter_title":"@foodfaire","tags":"food, fair","description":"testdescriptions","meta_image":"uploads/dummy/21/12/61af7a4466fcf0712211638890052.jpg"}}',
            "autoload" => 0
        ),
        array(
            "id" => 19,
            "key" => "product_page",
            "value" => '{"meta":{"product_page_short_title":"Wanna more?","product_page_title":"Check Related products","product_page_banner":"uploads/dummy/21/12/61cde8e0371023012211640884448.jpg"},"seo":{"site_title":"Products","twitter_title":"@foodfaire","tags":"tags1","description":"test","meta_image":"uploads/dummy/21/12/61cdee406c8533012211640885824.png"}}',
            "autoload" => 0
        ),
        array(
            "id" => 20,
            "key" => "cart_page",
            "value" => '{"meta":{"cart_page_title":"Cart","cart_page_description":"Lorem Ipsum is simply dummy text of the printing and typesetting industry.","cart_page_banner":"uploads/dummy/21/12/61cde8e0371023012211640884448.jpg"},"seo":{"site_title":"Cart","twitter_title":"@foodfaire","tags":"food, fair","description":"test","meta_image":null}}',
            "autoload" => 0
        ),
        array(
            "id" => 21,
            "key" => "checkout_page",
            "value" => '{"meta":{"cart_page_title":"Checkout","cart_page_description":"Lorem Ipsum is simply dummy text of the printing and typesetting industry.","checkout_page_banner":"uploads/dummy/21/12/61cde8e0371023012211640884448.jpg","checkout_form_title":"Make Your Checkout Here","checkout_form_description":"Please register in order to checkout more quickly"},"seo":{"site_title":"Checkout","twitter_title":"@foodfaire","tags":"food, fair","description":"test","meta_image":"uploads/dummy/21/12/61cded4b446463012211640885579.png"}}',
            "autoload" => 0
        ),
        array(
            "id" => 22,
            "key" => "menu_page",
            "value" => '{"meta":{"menu_page_title":"Our Menu","menu_page_description":"Lorem Ipsum is simply dummy text of the printing and typesetting industry.","menu_page_banner":"uploads/dummy/21/12/61cde8e0371023012211640884448.jpg","menu_product_section_title":"Latest Receipe","menu_product_section_description":"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent orci lacus, tempus quis libero nec, vehicula sagittis erat.","menu_page_product_ads_banner":"uploads/dummy/21/12/61cded4b446463012211640885579.png","menu_page_product_ads_link":"/products"},"seo":{"site_title":"Our menu","twitter_title":"@foodfaire","tags":"food, fair","description":"test","meta_image":"uploads/dummy/21/12/61af7a4477da90712211638890052.jpg"}}',
            "autoload" => 0
        ),
        array(
            "id" => 23,
            "key" => "products_page",
            "value" => '{"meta":{"products_page_title":"Products","products_page_description":"Lorem Ipsum is simply dummy text of the printing and typesetting industry.","products_page_banner":"uploads/dummy/21/12/61cde8e0371023012211640884448.jpg","products_page_product_ads_banner":"uploads/dummy/21/12/61cdee406c8533012211640885824.png","products_page_product_ads_link":"/products"},"seo":{"site_title":"Our products","twitter_title":"@foodfaire","tags":"food, fair","description":"tes","meta_image":null}}',
            "autoload" => 0
        ),
        array(
            "id" => 44,
            "key" => "login_page",
            "value" => '{"seo":{"site_title":"Login","twitter_title":"@foodfaire","tags":"food, fair","description":"test","meta_image":null}}',
            "autoload" => 0
        ),
        array(
            "id" => 45,
            "key" => "register_page",
            "value" => '{"seo":{"site_title":"Register","twitter_title":"@foodfaire","tags":"food, fair","description":"fff","meta_image":null}}',
            "autoload" => 0
        ),
        array(
            "id" => 46,
            "key" => "wishlist_page",
            "value" => '{"meta":{"wishlist_page_title":"Wishlist","wishlist_page_description":"test","wishlist_page_banner":"uploads/dummy/21/12/61cde8e0371023012211640884448.jpg"},"seo":{"site_title":"Wishlist","twitter_title":"@foodfaire","tags":"food, fair","description":"tes","meta_image":null}}',
            "autoload" => 0
        ),
        array(
            "id" => 47,
            "key" => "blog_page",
            "value" => '{"seo":{"site_title":"Foodfaire Blogs","twitter_title":"@foodfaire","tags":"food, fair","description":"test","meta_image":null},"meta":{"blog_page_title":"Blogs","blog_page_description":"test","blog_page_banner":"uploads/dummy/21/12/61cde8e0371023012211640884448.jpg"}}',
            "autoload" => 0
        ),
        array(
            "id"=>48,
            "key"=>'site_settings',
            "value"=>'{"meta":{"footer_column1":"<h3>Our Mobile App<\/h3><ul class=\"app-btn\"><li class=\"single-ap-btn\"> <a href=\"#\"> <i class=\"icofont-brand-apple\"><\/i> <span class=\"small-title\">Download on the<\/span> <span class=\"big-title\">App Store<\/span> <\/a> <\/li><li class=\"single-ap-btn\"> <a href=\"#\"> <i class=\"icofont-ui-play\"><\/i> <span class=\"small-title\">Download on the<\/span> <span class=\"big-title\">Google Play<\/span> <\/a> <\/li><\/ul>","footer_column2":"<h3>Get In Touch With Us<\/h3> <p class=\"phone\">Phone: +1 (900) 33 169 7720<\/p><ul> <li><span>Monday-Friday: <\/span> 9.00 am - 8.00 pm<\/li><li><span>Saturday: <\/span> 10.00 am - 6.00 pm<\/li><\/ul> <p class=\"mail\"><a href=\"#\"><span>contact@yourmail.com<\/span><\/a> <\/p>","footer_column3":"<h3>Information<\/h3> <ul> <li><a href=\"#\">About Us<\/a><\/li><li><a href=\"#\">Contact Us<\/a><\/li><li><a href=\"#\">Downloads<\/a><\/li><li><a href=\"#\">Sitemap<\/a><\/li><li><a href=\"#\">FAQs Page<\/a><\/li><\/ul>","footer_column4":"<h3>Shop Departments<\/h3> <ul> <li><a href=\"#\">Computers & Accessories<\/a><\/li><li><a href=\"#\">Smartphones & Tablets<\/a><\/li><li><a href=\"#\">TV, Video & Audio<\/a><\/li><li><a href=\"#\">Cameras, Photo & Video<\/a><\/li><li><a href=\"#\">Headphones<\/a><\/li><\/ul>","bottom_left_column":"<span>We Accept:<\/span><img src=\"uploads\/1\/22\/01\/61d3411c8fc8e0301221641234716.png\" alt=\"#\"\/>","bottom_center_column":"<p>\u00a9 Copyright 2021 -2022. <a href=\"#\" >AMCoders<\/a> All right reserved<\/p>","bottom_right_column":"<ul class=\"socila\"> <li><span>Follow Us On:<\/span><\/li><li><a href=\"#\"><i class=\"icofont-facebook\"><\/i><\/a><\/li><li><a href=\"#\"><i class=\"icofont-twitter\"><\/i><\/a><\/li><li><a href=\"#\" ><i class=\"icofont-instagram\"><\/i><\/a><\/li><li><a href=\"#\"><i class=\"icofont-linkedin\"><\/i><\/a><\/li><\/ul>","preloader":"yes","scroll_to_top":"yes","cart_sidebar":"yes","bottom_bar":"yes"}}',
            "autoload" => 1
        ),
        // array(
        //     "id"=>49,
        //     "key"=>'free_shipping',
        //     "value"=>'1',
        //     "autoload" => 0
        // ),
        // array(
        //     "id"=>50,
        //     "key"=>'min_cart_total',
        //     "value"=>'100.00',
        //     "autoload" => 0
        // ),
        // array(
        //     "id"=>51,
        //     "key"=>'shipping_method',
        //     "value"=>'',
        //     "autoload" => 0
        // )
      );

      
      Option::insert($options);

    }
}
