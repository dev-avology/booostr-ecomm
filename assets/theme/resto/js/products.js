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
    var $this = $(this);
    var clickedId = $this.data('id');
    var isBrand = $this.closest('.product_brands').length > 0;

    if (!isBrand) {
        $('.product_menu .menu_item').removeClass('active');
        $this.addClass('active');

        if (clickedId === 'all' || !clickedId) {
            categories = [];
            $('.product_brands .menu_item').removeClass('active');
            $('.product_brands .all_brand_item').addClass('active');
        } else {
            categories = [clickedId];
            $('.product_brands .menu_item').removeClass('active');
            $('.product_brands .all_brand_item').addClass('active');
        }
    } else {
        $('.product_brands .menu_item').removeClass('active');
        $this.addClass('active');

        if (clickedId === 'all' || !clickedId) {
            categories = [];
            $('.product_menu .menu_item').removeClass('active');
            $('.product_menu .all_cat_item').addClass('active');
        } else {
            categories = [clickedId];
            $('.product_menu .menu_item').removeClass('active');
            $('.product_menu .all_cat_item').addClass('active');
        }
    }

	if ($this.data('count') == 0 && clickedId !== 'all') {
		$('.product-item').remove();
		$('.zero_product').show();
		render_pagination('.pagination-list',[],'icofont-arrow-left','icofont-arrow-right');
		return false;
	}
	$('.zero_product').hide();

	getproducts(base_url + '/get-product');
});

// Sidebar Accordion Collapse/Expand
$(document).on('click', '.sidebar-accordion-header', function(e) {
    e.preventDefault();
    var $header = $(this);
    var $widget = $header.closest('.single-widget');
    var $content = $widget.find('.sidebar-widget-content');
    var $icon = $header.find('.sidebar-accordion-icon');

    $content.stop(true, true).slideToggle(200, function() {
        if ($content.is(':visible')) {
            $header.removeClass('collapsed');
            $icon.css('transform', 'rotate(0deg)');
        } else {
            $header.addClass('collapsed');
            $icon.css('transform', 'rotate(-90deg)');
        }
    });
});

// Sidebar "View More / View Less"
$(document).on('click', '.btn-sidebar-view-more', function(e) {
    e.preventDefault();
    var $btn = $(this);
    var targetSelector = $btn.data('target');
    var extraCount = $btn.data('extra');
    var $extraItems = $(targetSelector).find('.sidebar-extra-item');
    var isExpanded = $btn.hasClass('is-expanded');

    if (isExpanded) {
        $extraItems.addClass('d-none');
        $btn.removeClass('is-expanded');
        $btn.find('.toggle-text').text('+ View More (' + extraCount + ')');
    } else {
        $extraItems.removeClass('d-none');
        $btn.addClass('is-expanded');
        $btn.find('.toggle-text').text('- View Less');
    }
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
           var catHasActiveExtra = false;
           var totalCatCount = 0;
           $.each(response.categories, function(key, category) {
               totalCatCount += (parseInt(category.termcategories_count) || 0);
           });

           var isAllCatActive = (!cat || cat === '' || cat === '0' || cat === 'all') ? 'active' : '';
           var allCatHtml = `<li><a data-count="${totalCatCount}" data-id="all" class="menu_item all_cat_item ${isAllCatActive}" href="javascript:void(0)">All<span class="all_cat_count">${totalCatCount}</span></a></li>`;
           $('.product_menu').append(allCatHtml);

           $.each(response.categories, function(key, category) {
               if(cat == category.id){
                   var selected="active";
                   if (key >= 5) catHasActiveExtra = true;
               }
               else{
                   var selected='';
               }
               var isExtra = (key >= 5) ? 'sidebar-extra-item d-none' : '';
               var count = parseInt(category.termcategories_count) || 0;
               var html=`<li class="${isExtra}"><a data-count="${count}" data-id="${category.id}" class="menu_item ${selected}" href="javascript:void(0)">${category.name}<span>${count}</span></a></li>`;
               $('.product_menu').append(html);
           });

           if (response.categories.length > 5) {
               var extraCount = response.categories.length - 5;
               var moreHtml = `<div class="sidebar-more-wrapper">
                   <button type="button" class="btn-sidebar-view-more" data-target=".product_menu" data-extra="${extraCount}">
                       <span class="toggle-text">+ View More (${extraCount})</span>
                   </button>
               </div>`;
               var $catBox = $('.category_area .sidebar-widget-content').length ? $('.category_area .sidebar-widget-content') : $('.category_area');
               $catBox.find('.sidebar-more-wrapper').remove();
               $catBox.append(moreHtml);
               if (catHasActiveExtra) {
                   $catBox.find('.btn-sidebar-view-more').trigger('click');
               }
           }
       } else {
           $('.product_menu').append('<li class="no-item-msg" style="padding: 14px 20px; background: #f8fafc; color: #64748b; font-size: 14px; font-weight: 500; border-radius: 4px; border: 1px dashed #cbd5e1; text-align: center;">No categories available</li>');
       }

       $('.brand_area').removeClass('none').show();
       $('.product_brands').empty();

       if (response.brands && response.brands.length > 0) {
           var brandHasActiveExtra = false;
           var totalBrandCount = 0;
           $.each(response.brands, function(key, category) {
               totalBrandCount += (parseInt(category.termcategories_count) || 0);
           });

           var isAllBrandActive = (!cat || cat === '' || cat === '0' || cat === 'all') ? 'active' : '';
           var allBrandHtml = `<li><a data-count="${totalBrandCount}" data-id="all" class="menu_item all_brand_item ${isAllBrandActive}" href="javascript:void(0)">All<span class="all_brand_count">${totalBrandCount}</span></a></li>`;
           $('.product_brands').append(allBrandHtml);

           $.each(response.brands, function(key, category) {
               if(cat == category.id){
                   var selected="active";
                   if (key >= 5) brandHasActiveExtra = true;
               }
               else{
                   var selected='';
               }
               var isExtra = (key >= 5) ? 'sidebar-extra-item d-none' : '';
               var count = parseInt(category.termcategories_count) || 0;
               var html=`<li class="${isExtra}"><a data-count="${count}" data-id="${category.id}" class="menu_item ${selected}" href="javascript:void(0)">${category.name}<span>${count}</span></a></li>`;
               $('.product_brands').append(html);
           });

           if (response.brands.length > 5) {
               var extraCount = response.brands.length - 5;
               var moreHtml = `<div class="sidebar-more-wrapper">
                   <button type="button" class="btn-sidebar-view-more" data-target=".product_brands" data-extra="${extraCount}">
                       <span class="toggle-text">+ View More (${extraCount})</span>
                   </button>
               </div>`;
               var $brandBox = $('.brand_area .sidebar-widget-content').length ? $('.brand_area .sidebar-widget-content') : $('.brand_area');
               $brandBox.find('.sidebar-more-wrapper').remove();
               $brandBox.append(moreHtml);
               if (brandHasActiveExtra) {
                   $brandBox.find('.btn-sidebar-view-more').trigger('click');
               }
           }
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
        
            var numPrice = parseFloat(product.firstprice ? product.firstprice.price : 0);
            price = !isNaN(numPrice) ? ('$' + numPrice.toFixed(2)) : amount_format(price,'icon');

            if (product.is_variation == 1) {
              var cart_html=`<a class="btn btn-alt btn-choose-options" href="${base_url+'/product/'+product.slug}"><i class="icofont-options"></i>Choose Options</a>`;
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
           var total = response.total ? parseInt(response.total) : (response.data ? response.data.length : 0);
           var from = response.from ? response.from : (total > 0 ? 1 : 0);
           var to = response.to ? response.to : total;
           
           $('.from_products').html(from);
           $('.to_products').html(to);
           $('.total_products').html(total);
           if (categories.length === 0 && total > 0) {
               $('.all_cat_count').html(total);
           }
          
           if (!response.data || response.data.length === 0) {
               $('.zero_product').show();
               $('.pagination-list').empty();
           } else {
               $('.zero_product').hide();
               render_primary_product(response.data, '.latest_products','product-item');
               
               if(response.links && response.links.length > 3) {
               		render_pagination('.pagination-list',response.links,'icofont-arrow-left','icofont-arrow-right');
               }
               else{
               		$('.page-item').remove();
               }
           }
        },

    });
}