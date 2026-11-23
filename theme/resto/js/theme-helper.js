"use strict";

window.booostrFormatPrice = function(amount) {
    if (typeof amount === 'string' && amount.trim().startsWith('$')) {
        return amount.trim();
    }
    var clean = (typeof amount === 'string') ? amount.replace(/[^0-9.-]+/g, '') : amount;
    var num = parseFloat(clean);
    if (isNaN(num)) num = 0.00;
    return '$' + num.toFixed(2);
};

var currency_name = $('#currency_name').val();
var cart_link = $('#cart_link').val();
var clickaudio = $('#click_sound').val();
var cart_sound = $('#cart_sound').val();
var base_url = $('#base_url').val();
var preloader= $('#preloader').val();
var cart_increment = $('#cart_increment').val();
var cart_decrement = $('#cart_decrement').val();

var initial_cart = {};
try {
    var rawCart = $('#cart_content').val();
    if (rawCart && rawCart !== '') {
        initial_cart = JSON.parse(rawCart);
    }
} catch (e) {
    initial_cart = {};
}
window.booostr_cart_content = initial_cart;
const cart_content = window.booostr_cart_content;

function get_product_cart_qty(productId) {
    var total = 0;
    if (!window.booostr_cart_content) return 0;
    $.each(window.booostr_cart_content, function(k, item) {
        if (item && item.id == productId) {
            var q = parseInt(item.qty);
            if (!isNaN(q) && q > 0) {
                total += q;
            }
        }
    });
    return total;
}

function update_all_cards_stock_ui() {
    $('.single-popular-product').each(function() {
        var $card = $(this);
        var productId = $card.attr('data-product-id');
        if (!productId) return;

        var stockStatus = parseInt($card.attr('data-stock-status'));
        if (isNaN(stockStatus)) stockStatus = 1;
        var stockManage = parseInt($card.attr('data-stock-manage'));
        if (isNaN(stockManage)) stockManage = 0;
        var totalStock = parseInt($card.attr('data-total-stock')) || 0;
        var isVariation = parseInt($card.attr('data-is-variation')) || 0;
        var productSlug = $card.attr('data-product-slug') || '';
        var currentBaseUrl = $('#base_url').val() || '';

        // Inherent out of stock in database
        if (stockStatus === 0 || (stockManage === 1 && totalStock <= 0)) {
            $card.addClass('product-out-of-stock');
            if ($card.find('.out-of-stock-badge').length === 0) {
                $card.find('.image').addClass('out-of-stock-img-wrap').prepend('<span class="out-of-stock-badge">Out of Stock</span>');
            }
            $card.find('.quantity-main').addClass('disabled-quantity');
            $card.find('.quntity-input').prop('disabled', true).val(1);
            $card.find('.sp-minus, .sp-plus').prop('disabled', true);
            $card.find('.cart-button').html('<a class="btn-out-of-stock" href="javascript:void(0)"><i class="icofont-close-circled"></i>Out of stock</a>');
            return;
        }

        // Managed stock tracking
        if (stockManage === 1) {
            var inCart = get_product_cart_qty(productId);
            var remaining = Math.max(0, totalStock - inCart);

            if (remaining <= 0) {
                // All items already added to cart
                $card.find('.quantity-main').addClass('disabled-quantity');
                $card.find('.quntity-input').prop('disabled', true).attr('data-max', 0).val(0);
                $card.find('.sp-minus, .sp-plus').prop('disabled', true);
                $card.find('.cart-button').html('<a class="btn-out-of-stock btn-max-in-cart" href="javascript:void(0)" title="All available stock is in your cart"><i class="icofont-check-circled"></i>Max in cart</a>');
            } else {
                // Stock still available to add
                $card.removeClass('product-out-of-stock');
                $card.find('.quantity-main').removeClass('disabled-quantity');
                var $input = $card.find('.quntity-input');
                $input.prop('disabled', false).attr('data-max', remaining);
                var curVal = parseInt($input.val()) || 1;
                if (curVal > remaining) {
                    $input.val(remaining);
                } else if (curVal < 1) {
                    $input.val(1);
                }
                $card.find('.sp-minus, .sp-plus').prop('disabled', false);

                if (isVariation === 1) {
                    $card.find('.cart-button').html('<a href="' + currentBaseUrl + '/product/' + productSlug + '"><i class="icofont-basket"></i>Add to cart</a>');
                } else {
                    $card.find('.cart-button').html('<a class="add_to_cart cart' + productId + '" href="javascript:void(0)" data-isvariation="0" data-id="' + productId + '" data-stockstatus="' + stockStatus + '" data-stockmanage="' + stockManage + '" data-qty="' + totalStock + '"><i class="icofont-basket"></i>Add to cart</a>');
                }
            }
        } else {
            // Unmanaged stock: ensure controls are enabled
            $card.removeClass('product-out-of-stock');
            $card.find('.quantity-main').removeClass('disabled-quantity');
            $card.find('.quntity-input').prop('disabled', false).attr('data-max', 9999);
            $card.find('.sp-minus, .sp-plus').prop('disabled', false);
            if (isVariation === 1) {
                $card.find('.cart-button').html('<a href="' + currentBaseUrl + '/product/' + productSlug + '"><i class="icofont-basket"></i>Add to cart</a>');
            } else {
                $card.find('.cart-button').html('<a class="add_to_cart cart' + productId + '" href="javascript:void(0)" data-isvariation="0" data-id="' + productId + '" data-stockstatus="' + stockStatus + '" data-stockmanage="' + stockManage + '" data-qty="' + totalStock + '"><i class="icofont-basket"></i>Add to cart</a>');
            }
        }
    });
}

/*-------------------------------------
		render_product_preloaded
	---------------------------------------*/
function render_product_preloaded(preload_count = 6, target) {
    var html = `<div class="col-lg-3 col-md-6 col-6 content-preloader">
                              <div class="single-popular-product  content-placeholder" data-height="200px" data-width="100%"  >
                              </div>
                           </div>`;
    for (var i = 1; i <= preload_count; i++) {
        $(target).append(html);
    }
}

/*-------------------------------------
		render_primary_product
	---------------------------------------*/
function render_primary_product(products, target,additional_class='') {
    var base_url = $('#base_url').val();
    additional_class != '' ? $('.'+additional_class).remove() : '';
    $(target).find('.no-products-found').remove();

    if (!products || products.length === 0) {
        var empty_html = `<div class="col-12 text-center py-4 no-products-found ${additional_class}">
            <div class="no-products-found-card">
                <div class="no-products-found-icon">
                    <i class="icofont-box"></i>
                </div>
                <h4 class="no-products-found-title">No Products Found</h4>
                <p class="no-products-found-desc">There are currently no products available in this section. Please check back later!</p>
            </div>
        </div>`;
        $(target).append(empty_html);
        return;
    }

    var listview= $('.listviewproducts').length != 0 ? true : false;

    $.each(products, function(key, product) {

        var preview = product.preview != null ? product.preview.value : base_url + '/uploads/default.png';

       
       
        var rawPrice = product.firstprice ? product.firstprice.price : 0;
        var numPrice = parseFloat(rawPrice);
        var price = !isNaN(numPrice) ? ('$' + numPrice.toFixed(2)) : (typeof amount_format === 'function' ? amount_format(rawPrice, 'icon') : ('$' + rawPrice));


        var is_out_of_stock = false;
        var is_max_in_cart = false;
        var total_stock = 0;
        var remaining_stock = 9999;
        var stock_manage = 0;
        var stock_status = 1;

        if (product.firstprice) {
            stock_status = parseInt(product.firstprice.stock_status);
            if (isNaN(stock_status)) stock_status = 1;
            stock_manage = parseInt(product.firstprice.stock_manage);
            if (isNaN(stock_manage)) stock_manage = 0;
            total_stock = parseInt(product.firstprice.qty) || 0;

            if (stock_status == 0) {
                is_out_of_stock = true;
            } else if (stock_manage == 1 && total_stock <= 0) {
                is_out_of_stock = true;
            } else if (stock_manage == 1) {
                var in_cart_qty = get_product_cart_qty(product.id);
                remaining_stock = Math.max(0, total_stock - in_cart_qty);
                if (remaining_stock <= 0) {
                    is_max_in_cart = true;
                }
            }
        }

        var cart_html = '';
        if (is_out_of_stock) {
            cart_html = `<a class="btn-out-of-stock" href="javascript:void(0)"><i class="icofont-close-circled"></i>Out of stock</a>`;
        } else if (is_max_in_cart) {
            cart_html = `<a class="btn-out-of-stock btn-max-in-cart" href="javascript:void(0)" title="All available stock is in your cart"><i class="icofont-check-circled"></i>Max in cart</a>`;
        } else if (product.is_variation == 1) {
            cart_html = `<a href="${base_url+'/product/'+product.slug}"><i class="icofont-basket"></i>Add to cart</a>`;
        } else {
            cart_html = `<a class="add_to_cart cart${product.id}" href="javascript:void(0)" data-isvariation="${product.is_variation}" data-id="${product.id}" data-stockstatus="${stock_status}" data-stockmanage="${stock_manage}" data-qty="${total_stock}"><i class="icofont-basket"></i>Add to cart</a>`;
        }

        var is_disabled_ctrls = is_out_of_stock || is_max_in_cart;
        var max_data_attr = stock_manage == 1 ? remaining_stock : 9999;
        var default_input_val = is_max_in_cart ? 0 : 1;
        var min_input_val = is_max_in_cart ? 0 : 1;

        var ratings='';

         for (var i = 1; i <= 5; i++) {
            var review_full=`<li><i class="icofont-star star"></i></li>`;
            var review_half=`<li><i class="icofont-star"></i></li>`;

            i > product.rating ? ratings += review_half: ratings += review_full;
        }

        var rating_html='';

        var raw_excerpt = product.excerpt != null ? product.excerpt.value : '';
        var clean_excerpt = raw_excerpt ? raw_excerpt.replace(/<\/?[^>]+(>|$)/g, "").trim() : '';

        var in_wishlist = window.booostr_wishlist_ids && window.booostr_wishlist_ids.indexOf(parseInt(product.id)) !== -1;
        var wishlist_btn_class = in_wishlist ? 'btn wishlist add_to_wishlist in-wishlist activewishlist' : 'btn wishlist add_to_wishlist';
        var wishlist_btn_title = in_wishlist ? 'Remove from Wishlist' : 'Add to Wishlist';

        var html=`<div class="col-lg-3 col-md-6 col-6 ${additional_class} d-flex align-items-stretch">
                                        <div class="single-popular-product w-100 ${is_out_of_stock ? 'product-out-of-stock' : ''}" data-product-id="${product.id}" data-stock-status="${stock_status}" data-stock-manage="${stock_manage}" data-total-stock="${total_stock}" data-is-variation="${product.is_variation}" data-product-slug="${product.slug}">
                                            <div class="image ${is_out_of_stock ? 'out-of-stock-img-wrap' : ''}">
                                                ${is_out_of_stock ? '<span class="out-of-stock-badge">Out of Stock</span>' : ''}
                                                <img class="lazy" src="${preloader}" data-src="${preview}" alt="">
                                                <div class="product-overlay">
                                                    ${is_out_of_stock ? '' : `<a href="${base_url+'/product/'+product.slug}" class="btn add-to-cart" title="add to Cart"><i class="icofont-shopping-cart"></i></a>`}
                                                    <a href="javascript:void(0)" data-id="${product.id}" class="${wishlist_btn_class}" title="${wishlist_btn_title}"><i class="icofont-heart"></i></a>
                                                    <a href="${base_url+'/product/'+product.slug}"class="btn view"><i class="icofont-link-alt"></i></a>
                                                </div>
                                            </div>
                                            <div class="single-item-text">
                                                <h3 class="single-item-title"><a href="${base_url+'/product/'+product.slug}">${str_limit(product.title,35)}</a></h3>
                                                <h4 class="single-item-price">${price}</h4>
                                                <p class="single-item-excerpt">${str_limit(clean_excerpt,50)}</p>
                                            </div>
                                            <div class="single-item-bottom">
                                                <div class="quantity-main boxed-quantity ${is_disabled_ctrls ? 'disabled-quantity' : ''}">
                                                    
                                                    <div class="sp-quantity">
                                                      <button class="inline arrow sp-minus fff" ${is_disabled_ctrls ? 'disabled' : ''}><a class="minus" data-multi="-1">-</a></button>
                                                      <div class="inline sp-input">
                                                        <input type="number" ${is_disabled_ctrls ? 'disabled' : ''}  value="${default_input_val}" min="${min_input_val}" data-max="${max_data_attr}" class="quntity-input cart_qty${is_disabled_ctrls ? '' : product.id}" >
                                                      </div>
                                                      <button class="inline arrow sp-plus fff" ${is_disabled_ctrls ? 'disabled' : ''}><a class="plus" data-multi="1">+</a></button>
                                                    </div>
                                                </div>
                                                <div class="cart-button">
                                                    ${cart_html}
                                                </div>
                                            </div>
                                        </div>
                                    </div>`;

            var listViewHtml=`<div class="col-12 ${additional_class}">
                                            <div class="single-popular-product list-style ${is_out_of_stock ? 'product-out-of-stock' : ''}" data-product-id="${product.id}" data-stock-status="${stock_status}" data-stock-manage="${stock_manage}" data-total-stock="${total_stock}" data-is-variation="${product.is_variation}" data-product-slug="${product.slug}">
                                                        <div class="image ${is_out_of_stock ? 'out-of-stock-img-wrap' : ''}">
                                                            ${is_out_of_stock ? '<span class="out-of-stock-badge">Out of Stock</span>' : ''}
                                                            <img class="lazy" src="${preloader}" data-src="${preview}" alt="">
                                                            <div class="product-overlay">
                                                            ${is_out_of_stock ? '' : `<a href="${base_url+'/product/'+product.slug}" class="btn add-to-cart" title="add to Cart"><i class="icofont-shopping-cart"></i></a>`}
                                                            <a href="javascript:void(0)" data-id="${product.id}" class="${wishlist_btn_class}" title="${wishlist_btn_title}"><i class="icofont-heart"></i></a>
                                                            <a href="${base_url+'/product/'+product.slug}"class="btn view"><i class="icofont-link-alt"></i></a>
                                                            </div>
                                                        </div>
                                                        <div class="single-item-text">
                                                            <h3 class="single-item-title"><a href="${base_url+'/product/'+product.slug}">${str_limit(product.title,40)}</a></h3>
                                                            <h4 class="single-item-price">${price}</h4>
                                                            <p class="single-item-excerpt">${str_limit(clean_excerpt,100)}</p>
                                                        </div>
                                                        <div class="single-item-bottom">
                                                            <div class="quantity-main boxed-quantity ${is_disabled_ctrls ? 'disabled-quantity' : ''}">
                                                            <div class="sp-quantity">
                                                            <button class="inline arrow sp-minus fff" ${is_disabled_ctrls ? 'disabled' : ''}><a class="minus" data-multi="-1">-</a></button>
                                                            <div class="inline sp-input">
                                                            <input type="number" ${is_disabled_ctrls ? 'disabled' : ''}  value="${default_input_val}" min="${min_input_val}" data-max="${max_data_attr}" class="quntity-input cart_qty${is_disabled_ctrls ? '' : product.id}" >
                                                            </div>
                                                            <button class="inline arrow sp-plus fff" ${is_disabled_ctrls ? 'disabled' : ''}><a class="plus" data-multi="1">+</a></button>
                                                            </div>
                                                            </div>
                                                            <div class="cart-button">
                                                                ${cart_html}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>`;

            listview == true ? $('.listviewproducts').append(listViewHtml) : '';                                             

             $(target).append(html);

    });

    run_lazy();
    update_all_cards_stock_ui();
}

/*-------------------------------------
		render_primary_product
	---------------------------------------*/
function render_reviews(data,target) {
    $.each(data, function(key, row) {
        var html=`<div class="col-lg-4 col-md-6 col-12">
                  <div class="single-testimonial">
                     <div class="text">
                        <p>"${escapeHtml(row.comment)}"</p>
                     </div>
                     <div class="testi-author">
                        <div class="image">
                           <div class="quote-icon"><i class="icofont-quote-left"></i></div>
                           <img src="https://ui-avatars.com/api/?background=random&rounded=true&name=${escapeHtml(row.user.name)}" alt="#">
                        </div>
                        <h4 class="name"> ${escapeHtml(row.user.name)} </h4>
                     </div>
                  </div>
               </div>`;
        $(target).append(html);       
    });

    testimonial_slider()
}


/*-------------------------------------
		render_discountable_product
	---------------------------------------*/
function render_discountable_product(products, target) {
     var base_url = $('#base_url').val();


    $.each(products, function(key, product) {

        var fPrice = parseFloat(product.firstprice ? product.firstprice.price : 0) || 0;
        var price = '';
        if (product.is_variation == 1 ) {
            var lPrice = parseFloat(product.lastprice ? product.lastprice.price : 0) || 0;
            price = lPrice > 0 ? ('$' + fPrice.toFixed(2) + ' - $' + lPrice.toFixed(2)) : ('$' + fPrice.toFixed(2));
        }
        else{
            var oldPrice = product.firstprice && product.firstprice.old_price != null ? parseFloat(product.firstprice.old_price) : null;
            price = (oldPrice !== null && !isNaN(oldPrice) && oldPrice > 0)
                ? ('$' + fPrice.toFixed(2) + ` <span>$${oldPrice.toFixed(2)}</span>`)
                : ('$' + fPrice.toFixed(2));
        }

        var preview = product.preview != null ? product.preview.value : base_url + '/uploads/default.png';
        var ratings='';

         for (var i = 1; i <= 5; i++) {
            var review_full=`<li><i class="icofont-star star"></i></li>`;
            var review_half=`<li><i class="icofont-star"></i></li>`;

            i > product.rating ? ratings += review_half: ratings += review_full;
        }
       

   var html=` <div class="col-lg-6 col-md-6 col-6">
                        <!-- Start Single Deal -->
                        <div class="single-deal">
                           <div class="row align-items-center">
                              <div class="col-lg-5 col-md-5 col-12">
                                 <div class="image">
                                   <a href="${base_url+'/product/'+product.slug}"> <img class="lazy" src="${preloader}" data-src="${preview}" alt=""/></a>
                                 </div>
                              </div>
                              <div class="col-lg-7 col-md-7 col-12">
                                 <div class="content">
                                    <h3><a href="${base_url+'/product/'+product.slug}">${str_limit(product.title,20)}</a></h3>
                                    <p>
                                       ${str_limit(product.excerpt != null ? product.excerpt.value : '',30)}
                                    </p>
                                    <div class="price">
                                       <h5>${price}</h5>
                                    </div>

                                    <div class="rating-main">
                                       <ul class="rating">
                                          ${ratings != '' ? ratings : ''}
                                       </ul>
                                       <a href="${base_url+'/product/'+product.slug}" class="total-review">${product.rating != null ? '('+product.rating+') Review' : ''}</a>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                        <!-- End Single Deal -->
                     </div>`;

                      $(target).append(html);
                 });
     run_lazy();
}

/*-------------------------------------
		Add To Cart Modal
	---------------------------------------*/
$(document).on('click', '.add_to_cart', function() {
    var $btn = $(this);
    var id = $btn.data('id');
    var stockstatus = parseInt($btn.data('stockstatus'));
    if (isNaN(stockstatus)) stockstatus = 1;
    var stockmanage = parseInt($btn.data('stockmanage'));
    if (isNaN(stockmanage)) stockmanage = 0;
    var stockqty = parseInt($btn.data('qty')) || 0;

    if (stockstatus === 0) {
        Sweet('error', 'This item is currently out of stock.');
        return false;
    }

    var $qtyInput = $('.cart_qty' + id);
    var requested_qty = parseInt($qtyInput.val()) || 1;

    if (requested_qty < 1) {
        $qtyInput.val(1);
        requested_qty = 1;
    }

    if (stockmanage === 1) {
        var already_in_cart = get_product_cart_qty(id);
        var remaining = stockqty - already_in_cart;

        if (remaining <= 0) {
            Sweet('error', 'Maximum available stock limit (' + stockqty + ') is already in your cart.');
            update_all_cards_stock_ui();
            return false;
        }

        if (requested_qty > remaining) {
            Sweet('error', 'Only ' + remaining + ' piece(s) available in stock. Cannot add ' + requested_qty + '.');
            $qtyInput.val(remaining);
            return false;
        }
    }

    var btn_html = $btn.html();
    $btn.attr('disabled', 'disabled');
    var spinner = `<div class="spinner-border spinner-border-sm" role="status">
  <span class="visually-hidden"></span>
</div> Please Wait...`;
    $btn.html('');
    $btn.html(spinner);

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        type: 'POST',
        url: cart_link,
        data: {
            id: id,
            qty: requested_qty
        },
        dataType: 'json',
        success: function(response) {
            $('.cart' + id).html(btn_html);
            $('.cart' + id).removeAttr('disabled');

            $('.cart_subtotal').html(response.cart_subtotal);
            $('.cart_count').html(response.cart_count);
            $('.cart_total').html(response.cart_total);
            $('.cart_tax').html(response.cart_tax);

            if (response.cart_content) {
                window.booostr_cart_content = response.cart_content;
                render_cart(response.cart_content);
            }
            update_all_cards_stock_ui();
            audio();
            Sweet('success', 'Cart Added');
        },
        error: function(xhr, status, error) {
            $('.cart' + id).html(btn_html);
            $('.cart' + id).removeAttr('disabled');

            if (xhr.responseJSON && xhr.responseJSON.errors) {
                $.each(xhr.responseJSON.errors, function(key, item) {
                    Sweet('error', item);
                });
            } else {
                Sweet('error', 'Unable to add item to cart. Please try again.');
            }
            update_all_cards_stock_ui();
        }
    });
});

/*----------------------
		remove_cart
	------------------------*/
$(document).on('click', '.remove_cart', function() {
    audio(cart_sound);
    var rowid = $(this).data('id');
    $('.cart_item' + rowid).remove();

    if (window.booostr_cart_content && window.booostr_cart_content[rowid]) {
        delete window.booostr_cart_content[rowid];
        update_all_cards_stock_ui();
    }

    $.ajax({
        type: 'get',
        url: base_url + '/remove-cart/' + rowid,
        dataType: 'json',
        success: function(response) {
            $('.cart_count').html(response.cart_count);
            $('.cart_subtotal').html(window.booostrFormatPrice(response.cart_subtotal));
            if ($('.cart_page').length > 0) {
                $('.cart_total').html(window.booostrFormatPrice(response.cart_subtotal));
            } else {
                $('.cart_total').html(window.booostrFormatPrice(response.cart_total));
            }
            $('.cart_tax').html(response.cart_tax);

            if (response.cart_content) {
                window.booostr_cart_content = response.cart_content;
                render_cart(response.cart_content);
            }
            update_all_cards_stock_ui();
        },
    });
});

/*----------------------
		cart_increment
	------------------------*/
$(document).on('click', '.cart_increment', function() {
    var rowid = $(this).data('id');
    var stock = $(this).data('stock');
    var productid = $(this).data('productid');
    var totalqty = parseInt($('.current_cart_qty' + rowid).val()) || 0;

    if ($(this).data('isvariation') == 1) {
        return true;
    }

    var stockNum = parseInt(stock);
    if (!isNaN(stockNum) && stockNum > 0) {
        if (totalqty >= stockNum) {
            Sweet('error', 'Maximum available stock limit (' + stockNum + ') reached.');
            return false;
        }
    }

    var newqty = totalqty + 1;
    $('.current_cart_qty' + rowid).val(newqty);

    if (window.booostr_cart_content && window.booostr_cart_content[rowid]) {
        window.booostr_cart_content[rowid].qty = newqty;
        update_all_cards_stock_ui();
    }

    audio(cart_sound);
    cartqty(rowid, newqty);
});


/*----------------------
		cart_decrement
	------------------------*/
$(document).on('click', '.cart_decrement', function() {
    var rowid = $(this).data('id');
    var stock = $(this).data('stock');
    var productid = $(this).data('productid');
    var totalqty = parseInt($('.current_cart_qty' + rowid).val()) || 0;

    if ($(this).data('isvariation') == 1) {
        return true;
    }

    var newqty = totalqty - 1;
    $('.current_cart_qty' + rowid).val(newqty);

    if (newqty <= 0) {
        $('.cart_item' + rowid).remove();
        if (window.booostr_cart_content && window.booostr_cart_content[rowid]) {
            delete window.booostr_cart_content[rowid];
            update_all_cards_stock_ui();
        }
    } else {
        if (window.booostr_cart_content && window.booostr_cart_content[rowid]) {
            window.booostr_cart_content[rowid].qty = newqty;
            update_all_cards_stock_ui();
        }
    }

    audio(cart_sound);
    cartqty(rowid, newqty);
});

/*----------------------
		render_cart
	------------------------*/
function render_cart(items) {
    if (items) {
        window.booostr_cart_content = items;
    }
    $('.cart_item').remove();
    var cartpage=$('.cart_page').length != 0 ? true : false;
    var itemCount = items ? (Array.isArray(items) ? items.length : Object.keys(items).length) : 0;
    if (cartpage) {
        if (itemCount === 0) {
            $('.cart-filled-view').hide();
            $('.cart-empty-view').show();
        } else {
            $('.cart-empty-view').hide();
            $('.cart-filled-view').show();
        }
    }
    $.each(items, function(key, item) {
         var cart_options='';
              $.each(item.options.options, function (option_key, option) 
              {
                var child_options='';
                $.each(option, function (child_option_key, child_option_value) 
                  {
                   child_options +=`${child_option_value.name},`;
                  })

                cart_options +=`<br><small>${option_key}: (${child_options})</small>`;
              }); 

        var item_line_price = (typeof window.booostrFormatPrice === 'function')
            ? window.booostrFormatPrice(item.price * parseInt(item.qty || 1))
            : ('$' + (parseFloat(item.price || 0) * parseInt(item.qty || 1)).toFixed(2));

        var cart_item = `<li class="cart_item cart_item${item.rowId}">
                     <div class="cart-single-main">
                        <div class="cart-item-img-wrap">
                           <a href="${base_url+'/product/'+item.options.slug}" class="cart-img">
                              <img src="${item.options.preview != null ? item.options.preview : ''}" alt="${item.name}">
                           </a>
                        </div>
                        <div class="cart-single-item">
                           <div class="cart-item-header">
                              <h4 class="cart-item-title"><a href="${base_url+'/product/'+item.options.slug}">${str_limit(item.name, 45)}</a></h4>
                              <a data-id="${item.rowId}" href="javascript:void(0)" class="remove remove_cart" title="Remove this item"><i class="icofont-trash"></i></a>
                           </div>
                           ${cart_options ? `<div class="cart-item-meta">${cart_options}</div>` : ''}
                           <div class="cart-item-footer">
                              <div class="sp-quantity">
                                <button type="button" class="inline arrow sp-minus fff cart_decrement" data-id="${item.rowId}" data-stock="${item.options.stock}" data-isvariation="${item.options.options.length != 0 ? 1 : 0}" data-productid="${item.id}"><a class="ddd" href="${item.options.options.length != 0 ? base_url+'/product/'+item.options.slug : 'javascript:void(0)'}" data-multi="-1">-</a></button>
                                <div class="inline sp-input">
                                 <input type="text" readonly class="quntity-input current_cart_qty${item.rowId} exist_cart${item.id}" value="${item.qty}">
                                </div>
                                <button type="button" class="inline arrow sp-plus fff cart_increment last" data-id="${item.rowId}" data-stock="${item.options.stock}" data-isvariation="${item.options.options.length != 0 ? 1 : 0}" data-productid="${item.id}"><a class="ddd" href="${item.options.options.length != 0 ? base_url+'/product/'+item.options.slug : 'javascript:void(0)'}" data-multi="1">+</a></button>
                              </div>
                              <span class="cart-item-price">${item_line_price}</span>
                           </div>
                        </div>
                     </div>
                  </li>`;

        if (cartpage == true) {
            var unit_price = window.booostrFormatPrice(item.price);
            var line_total = window.booostrFormatPrice(item.price * parseInt(item.qty || 1));

            var cart_page_item=`<tr class="cart_item cart_item${item.rowId}">
            <td class="image" data-title="No"><img src="${item.options.preview != null ? item.options.preview : ''}" alt="#"></td>
            <td class="product-desc" data-title="Description">
            <p class="product-name"><a href="${base_url+'/product/'+item.options.slug}">${str_limit(item.name,100)}</a></p>
            <p class="product-des">${cart_options}</p>
            </td>
            <td class="price" data-title="Price"><span>${unit_price}</span></td>
            <td class="qty" data-title="Qty">
            <div class="sp-quantity">
                <button class="inline arrow sp-minus fff cart_decrement" data-id="${item.rowId}" data-stock="${item.options.stock }" data-isvariation="${item.options.options.length != 0 ? 1 : 0}" data-productid="${item.id}""><a class="ddd" href="${item.options.options.length != 0 ? base_url+'/product/'+item.options.slug : 'javascript:void(0)'}" data-multi="-1">-</a></button>
                <div class="inline sp-input">
                 <input type="text" class="quntity-input current_cart_qty${item.rowId} exist_cart${item.id}" value="${item.qty}">
                </div>
                <button class="inline arrow sp-plus fff cart_increment last" data-id="${item.rowId}" data-stock="${item.options.stock }" data-isvariation="${item.options.options.length != 0 ? 1 : 0}" data-productid="${item.id}"><a class="ddd" href="${item.options.options.length != 0 ? base_url+'/product/'+item.options.slug : 'javascript:void(0)'}" data-multi="1">+</a></button>
            </div>

            </td>
            <td class="total-amount" data-title="Total"><span>${line_total}</span></td>
            <td class="action" data-title="Remove"><a data-id="${item.rowId}" class="remove remove_cart" href="javascript:void(0)"><i class="icofont-trash remove-icon"></i></a></td>
            </tr>`;

            $('.cart_page').append(cart_page_item);
        }

        $('.shopping-list').append(cart_item);


    });

    update_all_cards_stock_ui();
}

/*----------------------
		cartqty
	------------------------*/
function cartqty(cartId, qty) {
    var url = cart_increment;
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        type: 'POST',
        url: url,
        data: {
            id: cartId,
            qty: qty
        },
        dataType: 'json',
        success: function(response) {

            $('.cart_subtotal').html(window.booostrFormatPrice(response.cart_subtotal));
           
            $('.cart_count').html(response.cart_count);
            if ($('.cart_page').length > 0) {
                $('.cart_total').html(window.booostrFormatPrice(response.cart_subtotal));
            } else {
                $('.cart_total').html(window.booostrFormatPrice(response.cart_total));
            }
            $('.cart_tax').html(response.cart_tax);
            
            if (response.cart_content) {
                window.booostr_cart_content = response.cart_content;
                render_cart(response.cart_content);
            }
            update_all_cards_stock_ui();
        },
        error: function(xhr, status, error) {

            if (xhr.responseJSON && xhr.responseJSON.errors) {
                $.each(xhr.responseJSON.errors, function(key, item) {
                    Sweet('error', item);
                });
            }
            update_all_cards_stock_ui();
        }
    });

}

/*-------------------------
		add_to_cart_modal
	--------------------------*/
 $(document).on('click','.add_to_cart_modal',function(){

    var product_id=$(this).data('id');
    render_card_modal(product_id);

  });

  /*-------------------------
		add_to_wishlist (Smart Toggle: Add / Remove)
	--------------------------*/
 $(document).on('click','.add_to_wishlist',function(e){
    e.preventDefault();
    var $btn = $(this);
    var product_id = parseInt($btn.data('id'));
    var url = base_url+'/add-to-wishlist';
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        type: 'POST',
        url: url,
        data: {
            id: product_id,
        },
        dataType: 'json',
        success: function(response) {
            var count = typeof response === 'object' ? response.count : response;
            var action = typeof response === 'object' ? response.action : 'added';
            var message = (typeof response === 'object' && response.message) ? response.message : (action === 'added' ? 'Added to Wishlist' : 'Removed from Wishlist');

            $('.wishlist_count').html(count);

            if (typeof response === 'object' && response.wishlist_ids) {
                window.booostr_wishlist_ids = response.wishlist_ids;
            } else {
                window.booostr_wishlist_ids = window.booostr_wishlist_ids || [];
                var idx = window.booostr_wishlist_ids.indexOf(product_id);
                if (action === 'added' && idx === -1) {
                    window.booostr_wishlist_ids.push(product_id);
                } else if (action === 'removed' && idx !== -1) {
                    window.booostr_wishlist_ids.splice(idx, 1);
                }
            }

            var $allWishlistBtns = $('.add_to_wishlist[data-id="' + product_id + '"]');
            if (action === 'added') {
                $allWishlistBtns.addClass('in-wishlist activewishlist').attr('title', 'Remove from Wishlist');
                $allWishlistBtns.find('.wishlist-btn-text').text('In Wishlist');
                Sweet('success', message);
            } else {
                $allWishlistBtns.removeClass('in-wishlist activewishlist').attr('title', 'Add to Wishlist');
                $allWishlistBtns.find('.wishlist-btn-text').text('Add to Wishlist');
                Sweet('info', message);
            }
        },
        error: function(xhr, status, error) {
            if (xhr.responseJSON && xhr.responseJSON.errors) {
                $.each(xhr.responseJSON.errors, function(key, item) {
                    Sweet('error', item);
                });
            } else {
                Sweet('error', 'Unable to update wishlist');
            }
        }
    });

 });

   /*-------------------------
		render_card_modal
	--------------------------*/
  function render_card_modal(product_id) {
     var url=$('#pos_product_varidation').val();

    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });
    $.ajax({
      type: 'GET',
      url: url+'/'+product_id,
      dataType: 'json',
      contentType: false,
      cache: false,
      processData:false,
      beforeSend: function() {
            $('.option_form_area').html('');
             preload();  

       },
      
      success: function(response){ 
        var html='';

        $.each(response.productoptionwithcategories, function(key, item){
          var child_html='';

          $.each(item.priceswithcategories,function(k,child){
              child_html +=`
              <label class="selectgroup-item">
              <input data-qty="${child.qty}" data-stockmanage="${child.stock_manage}" data-stockstatus="${child.stock_status}" ${child.stock_status == 0 && child.qty == 0  ? 'disabled' : ''} type="${item.select_type == 1 ? 'checkbox' : 'radio'}" name="option[${item.id}][]" value="${child.id}" class="selectgroup-input product_option ${item.is_required == 1 ? 'req' : ''}">
              <span class="selectgroup-button">${child.stock_status == 0 && child.qty == 0 ? '<del>' : ''} ${child.category.name} ${child.stock_status == 0 && child.qty == 0  ? '</del>' : ''}</span>
            </label>
            `;
          });

          html +=`
            <div class="form-group">
          <label class="form-label">${item.categorywithchild.name} ${item.is_required == 1 ? '<span class="text-danger">*</span>' : ''} </label>
          <div class="selectgroup w-100">
            ${child_html}
          
          </div>
        </div>
          `;
        });
        html +=`<div class="form-group">
   <label class="form-label">Quantity</label>
   <input type="number" name="qty" class="form-control input_qty" value="0" required min="1">
   <p class="text-danger none required_option">Please Select A Option From Required Field</p>
   <input type="hidden" name="id" value="${product_id}" />
</div>
`; 

        $('.option_form_area').html(html);
      },
      error: function(xhr, status, error) 
      {
       
        $.each(xhr.responseJSON.errors, function (key, item) 
        {
          Sweet('error',item)
          
        });
      
      }
    })
  }

    /*-------------------------
		    product_option
	    --------------------------*/
    $(document).on('click','.product_option',function(){

        var qty=$(this).data('qty');
        var stockmanage=$(this).data('stockmanage');
        var stockstatus=$(this).data('stockstatus');

        if (stockmanage == 1) {
        $('.input_qty').attr('max',qty);
        }
        else{
        $('.input_qty').removeAttr('max');
        }
    });
    
    /*-------------------------
        option_form
    --------------------------*/
    $(document).on('submit','.option_form', function(e) {
        e.preventDefault();
         var form_data = $(this).serialize();
        var required = false;
        if ($('.req').length > 0) {
            $('.req:checked').each(function() {
                if (this.checked == true) {
                    required = true;
                } else {
                    required = false;
                }

            });
            if (required == false) {
                $('.required_option').show();
            } else {
                $('.required_option').hide();
            }
        } else {
            required = true;
        }
        if (required == true) {
           $.ajaxSetup({
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
           });

          $.ajax({
            type: 'POST',
            url: cart_link,
            data: form_data,
            dataType: 'json',
            beforeSend: function(){
              $('.add_to_cart_form_btn').text('Please Wait.....');
              $('.add_to_cart_form_btn').attr('disabled','');
            },
            success: function(response){ 
              $('.add_to_cart_form_btn').text('Add To Cart');
              $('.add_to_cart_form_btn').removeAttr('disabled');

              render_cart(response.cart_content);
              $('.subtotal').text(response.cart_subtotal);
             
              $('.cart_count').html(response.cart_count)
              audio();
              Sweet('success','Cart Added');
             
              $('#product_variation_modal').modal('toggle');
             

              
            },
            error: function(xhr, status, error) 
            {
              $('.add_to_cart_form_btn').text('Add To Cart');
              $('.add_to_cart_form_btn').removeAttr('disabled');
        
              $.each(xhr.responseJSON.errors, function (key, item) 
              {
                Sweet('error',item)
              });
              
            }
        });


        }
    });   
  render_cart(cart_content);
  
  $('.trans_lang').on('change',function(){
      $('.change_lang_form').submit();
  });

var entityMap = {
      '&': '&amp;',
      '<': '&lt;',
      '>': '&gt;',
      '"': '&quot;',
      "'": '&#39;',
      '/': '&#x2F;',
      '`': '&#x60;',
      '=': '&#x3D;'
};

/*-------------------------
        escapeHtml
    --------------------------*/
function escapeHtml(string) {
      return String(string).replace(/[&<>"'`=\/]/g, function (s) {
        return entityMap[s];
    });
 }

