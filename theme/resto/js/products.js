"use strict";

runpreloader();

var callbackdata = {
    "categories":{
        "limit": -1,
        "withcount":true
    },
    "brands":{
        "limit": -1,
        "withcount":true
    },
    "randomfeatures":{
        "limit": 1,
        "random":true
    }
}

var callback_url = $('#callback_url').val();

var categories=[];

$('#cat').val() != '' ? categories.push($('#cat').val()) : '';

console.log(categories);

$(document).on('click','.menu_item',function(){
	$('.menu_item').removeClass('active');
	$(this).addClass('active')
	if ($(this).data('count') == 0 && $(this).is(':checked') == true) {
		$('.product-item').remove();
		$('.zero_product').show();
		render_pagination('.pagination-list',[],'icofont-arrow-left','icofont-arrow-right');
		return false;
	}
	$('.zero_product').hide();
	categories=[];
	categories.push($(this).data('id'));

	getproducts(base_url + '/get-product');


});

$.ajax({
    type: 'get',
    url: callback_url,
    data: {
        body: callbackdata
    },
    dataType: 'json',
    beforeSend: function() {
    	   for (var i = 0; i <= 10; i++) {
        		var html=`<div class="col-lg-4 col-md-6 col-12 content-preloader">
        		<div class="top-addon-single  content-placeholder"  data-height="100px"  data-width="100%">
        		</div>
        		</div>`;
        		$('.product_area').append(html);		
        	}
            runpreloader();
    },
    success: function(response) {
       $('.menu_preload').remove();

       var cat=$('#cat').val();

       if (response.categories && response.categories.length > 0) {
           $.each(response.categories, function(key, category) {
               if(cat == category.id){
                   var selected="active";
               }
               else{
                   var selected='';
               }
               var count = parseInt(category.termcategories_count) || 0;
               var html=`<li><a data-count="${count}" data-id="${category.id}" class="menu_item ${selected}" href="javascript:void(0)">${category.name}<span>${count}</span></a></li>`;
               $('.product_menu').append(html);
           });
       } else {
           $('.product_menu').append('<li class="no-item-msg" style="padding: 14px 20px; background: #f8fafc; color: #64748b; font-size: 14px; font-weight: 500; border-radius: 4px; border: 1px dashed #cbd5e1; text-align: center;">No categories available</li>');
       }

       $('.brand_area').removeClass('none').show();
       $('.product_brands').empty();

       if (response.brands && response.brands.length > 0) {
           $.each(response.brands, function(key, category) {
               if(cat == category.id){
                   var selected="active";
               }
               else{
                   var selected='';
               }
               var count = parseInt(category.termcategories_count) || 0;
               var html=`<li><a data-count="${count}" data-id="${category.id}" class="menu_item ${selected}" href="javascript:void(0)">${category.name}<span>${count}</span></a></li>`;
               $('.product_brands').append(html);
           });
       } else {
           $('.product_brands').append('<li class="no-item-msg" style="padding: 14px 20px; background: #f8fafc; color: #64748b; font-size: 14px; font-weight: 500; border-radius: 4px; border: 1px dashed #cbd5e1; text-align: center;">No brands available</li>');
       }

       var featuredids=[];
       $.each(response.randomfeatures, function(key, category) {
	       	
       	    featuredids.push(category.id);
       		
        	$('.featured_title').html(category.name);
        });

       
       getrandomproducts(featuredids);
        



    },
    error: function(xhr, status, error) {


    }
});

$(document).on('click','.page-link',function(){
	var link=$(this).data('url');
	if (link == '') {
		return false;
	}
	getproducts(link);
});

getproducts(base_url + '/get-product');

function getrandomproducts(ids) {
	if (ids.length == 0) {
		return false;
	}
	
	var callbackdata = {
        "limit":4,
        "categories": ids,
        "random":true,
        "with": ["preview"]
    }

    $.ajax({
        type: 'get',
        url: base_url + '/get-product',
        data: callbackdata,
        dataType: 'json',
        beforeSend: function() {
        	
        },
        success: function(response) {
           response.length > 0 ? $('.featured_area').show() : '';

           $.each(response,function(key,product){

           	var ratings='';

           	for (var i = 1; i <= 5; i++) {
           		var review_full=`<li><i class="icofont-star star"></i></li>`;
           		var review_half=`<li><i class="icofont-star"></i></li>`;

           		i > product.rating ? ratings += review_half: ratings += review_full;
           	}

           	var preview = product.preview != null ? product.preview.value : base_url + '/uploads/default.png';
           	var price= product.firstprice.price;
        
            price = amount_format(price,'icon');

            if (product.is_variation == 1) {
              var cart_html=`<a class="btn btn-alt"  href="${base_url+'/product/'+product.slug}"><i class="icofont-basket"></i>Add to cart</a>`;
        } else {
            if (product.firstprice.stock_status == 0) {
               var cart_html=`<a class="btn btn-alt"  href="${base_url+'/product/'+product.slug}"><i class="icofont-basket"></i>Add to cart</a>`;
            } else {
                var cart_html=`<a class="add_to_cart cart${product.id} btn btn-alt" href="javascript:void(0)" data-isvariation="${product.is_variation}"  data-id="${product.id}" data-stockstatus="${product.firstprice.stock_status}" data-stockmanage="${product.firstprice.stock_manage}" data-qty="${product.firstprice.qty}"><i class="icofont-basket"></i>Add to cart</a>`;
            }
          
        }

           	var html=`<li class="single-product">
										<div class="image">
											 <img class="lazy" src="${preloader}" data-src="${preview}" alt="">
										</div>
										<div class="content">
											<h3 class="title"><a href="${base_url+'/product/'+product.slug}">${product.title}</a></h3>
											<div class="price-rating">
												<p>${price}</p>
												<div class="rating-main">
													<ul class="rating">
														${ratings}
													</ul>
												</div>
											</div>
											<div class="button">
												${cart_html}
											</div>
										</div>
									</li>`;
			$('.featured_products').append(html);						

           });
           
           run_lazy();

        },

    });
}

function getproducts(url) {
    var callbackdata = {
        "with_paginate": true,
        "limit":12,
        "categories": categories,
        "with": ["preview", "excerpt"]
    }

    $.ajax({
        type: 'get',
        url: url,
        data: callbackdata,
        dataType: 'json',
        beforeSend: function() {
        	$('.product-item').remove();
        	render_product_preloaded(6, '.latest_products');

            runpreloader();
        },
        success: function(response) {
           $('.content-preloader').remove();
           $('.from_products').html(response.from);
           $('.to_products').html(response.to);
           $('.total_products').html(response.total);
          
           render_primary_product(response.data, '.latest_products','product-item');
           
           if(response.links.length > 3) {
           		render_pagination('.pagination-list',response.links,'icofont-arrow-left','icofont-arrow-right');
           }
           else{
           		$('.page-item').remove();
           }


           

        },

    });
}