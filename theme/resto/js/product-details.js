"use strict";

var totalprice=0;
var totaloldprice=0;

function matchVariationCombination() {
	if (!window.booostr_variation_prices || window.booostr_variation_prices.length === 0) {
		return false;
	}

	var selectedOptionIds = [];
	var totalGroups = $('.variation-group').length;

	$('.variation-group').each(function() {
		var $select = $(this).find('select.pricesvariations');
		if ($select.length > 0) {
			var val = $select.val();
			if (val) selectedOptionIds.push(parseInt(val));
		} else {
			var $checked = $(this).find('input[type="radio"].pricesvariations:checked');
			if ($checked.length > 0) {
				var val = $checked.val();
				if (val) selectedOptionIds.push(parseInt(val));
			}
		}
	});

	if (selectedOptionIds.length < totalGroups) {
		$('#selected_variation_id').val('');
		$('.add_to_cart_form_btn').text('Select Options').attr('disabled', 'disabled').css({
			'background': '#94a3b8',
			'border-color': '#94a3b8',
			'cursor': 'not-allowed'
		});
		$('.stock_status').hide();
		$('.sku_display').hide();
		return true;
	}

	var matchedPrice = null;
	for (var i = 0; i < window.booostr_variation_prices.length; i++) {
		var p = window.booostr_variation_prices[i];
		if (!p.varitions || p.varitions.length !== selectedOptionIds.length) continue;
		var allMatch = p.varitions.every(function(v) {
			var catId = (v.pivot && v.pivot.category_id) ? parseInt(v.pivot.category_id) : parseInt(v.id);
			return selectedOptionIds.indexOf(catId) !== -1;
		});
		if (allMatch) {
			matchedPrice = p;
			break;
		}
	}

	if (matchedPrice) {
		$('#selected_variation_id').val(matchedPrice.id);
		totalprice = parseFloat(matchedPrice.price) || 0;
		totaloldprice = parseFloat(matchedPrice.old_price) || 0;

		var formattedPrice = '$' + totalprice.toFixed(2);
		var oldPriceHtml = (totaloldprice > 0) ? '<s style="font-size: 16px; color: #94a3b8; font-weight: 400; margin-left: 8px;">$' + totaloldprice.toFixed(2) + '</s>' : '';
		$('.price_area').html('<span class="discount">' + formattedPrice + '</span>' + oldPriceHtml);

		if (matchedPrice.sku && matchedPrice.sku !== '' && matchedPrice.sku !== '0' && matchedPrice.sku !== 0) {
			$('.sku_value').text(matchedPrice.sku);
			$('.sku_display').show();
		} else {
			$('.sku_display').hide();
		}

		var isOut = (matchedPrice.stock_status == 0) || (matchedPrice.stock_manage == 1 && parseInt(matchedPrice.qty) <= 0);
		var stockstatus_html = !isOut ? 
			'<span class="badge" style="background-color: var(--primary, #0A8CDE) !important; color: #ffffff !important; font-size: 12px; font-weight: 600; padding: 4px 10px; border-radius: 4px;">In Stock</span>' : 
			'<span class="badge" style="background-color: #94a3b8 !important; color: #ffffff !important; font-size: 12px; font-weight: 600; padding: 4px 10px; border-radius: 4px;">Out of Stock</span>';
		$('.stock_status_display').html(stockstatus_html);
		$('.stock_status').show();

		if (matchedPrice.stock_manage == 1 && !isOut) {
			$('.input_qty').attr('data-max', matchedPrice.qty).attr('max', matchedPrice.qty);
		}

		if (isOut) {
			$('.add_to_cart_form_btn').text('Out of Stock').attr('disabled', 'disabled').css({
				'background': '#94a3b8',
				'border-color': '#94a3b8',
				'cursor': 'not-allowed'
			});
			$('.product-quantity .sp-quantity').addClass('disabled-quantity');
			$('.product-quantity input.input_qty').attr('disabled', 'disabled');
		} else {
			$('.add_to_cart_form_btn').text('Add to cart').removeAttr('disabled').css({
				'background': '',
				'border-color': '',
				'cursor': ''
			});
			$('.product-quantity .sp-quantity').removeClass('disabled-quantity');
			$('.product-quantity input.input_qty').removeAttr('disabled');
		}
	} else {
		$('#selected_variation_id').val('');
		$('.add_to_cart_form_btn').text('Unavailable').attr('disabled', 'disabled').css({
			'background': '#94a3b8',
			'border-color': '#94a3b8',
			'cursor': 'not-allowed'
		});
	}

	return true;
}

$(document).on('change', '.pricesvariations', function(){
	if (window.booostr_variation_prices && window.booostr_variation_prices.length > 0) {
		matchVariationCombination();
		return;
	}

	var stockstatus=$(this).data('stockstatus');
	var stockmanage=$(this).data('stockmanage');
	var sku=$(this).data('sku');
	var qty=$(this).data('qty');
	var oldprice=$(this).data('oldprice');
	var price=$(this).data('price');
	totalprice=0;
	totaloldprice=0;

	if ($(this).is(':checked')){
		var isOut = (stockstatus == 0) || (stockmanage == 1 && parseInt(qty) <= 0);
		var stockstatus_html = !isOut ? 
			'<span class="badge" style="background-color: var(--primary, #0A8CDE) !important; color: #ffffff !important; font-size: 12px; font-weight: 600; padding: 4px 10px; border-radius: 4px;">In Stock</span>' : 
			'<span class="badge" style="background-color: #94a3b8 !important; color: #ffffff !important; font-size: 12px; font-weight: 600; padding: 4px 10px; border-radius: 4px;">Out of Stock</span>';
		$('.stock_status_display').html(stockstatus_html);
		$('.stock_status').show();

		// Update SKU display
		if (sku && sku !== '') {
			$('.sku_value').text(sku);
			$('.sku_display').show();
		} else {
			$('.sku_display').hide();
		}

		if(stockmanage == 1 && !isOut){
			$('.qty_display').show();
		    $('.maxqty').html(qty);
		} else {
			$('.qty_display').hide();
		}

		if (isOut) {
			$('.add_to_cart_form_btn').text('Out of Stock').attr('disabled', 'disabled').css({
				'background': '#94a3b8',
				'border-color': '#94a3b8',
				'cursor': 'not-allowed'
			});
			$('.product-quantity .sp-quantity').addClass('disabled-quantity');
			$('.product-quantity input.input_qty').attr('disabled', 'disabled');
		} else {
			$('.add_to_cart_form_btn').text('Add to cart').removeAttr('disabled').css({
				'background': '',
				'border-color': '',
				'cursor': ''
			});
			$('.product-quantity .sp-quantity').removeClass('disabled-quantity');
			$('.product-quantity input.input_qty').removeAttr('disabled');
		}
		
	}
	else{

		$('.stock_status').hide();
		$('.qty_display').hide();
	
	}
	
	variations();
});

// Initial variations check on page load
if (window.booostr_variation_prices && window.booostr_variation_prices.length > 0) {
	if (typeof $.fn.niceSelect === 'function') {
		$('select.pricesvariations').niceSelect('update');
	}
	matchVariationCombination();
} else {
	variations();
}

function variations() {
	if ($('.pricesvariationshide').length == 1) {

		$('.pricesvariationshide').each(function() {

		
			var oldprice=$(this).data('oldprice');
		    var price=$(this).data('price');
		    var qty=$(this).data('qty');
		    totalprice= totalprice+price;
		    totaloldprice= totaloldprice+oldprice;

		    if ($(this).data('stockstatus') == 1 && $(this).data('stockmanage') == 1) {
		    	$('.input_qty').attr('data-max',qty);
		    	$('.input_qty').attr('max',qty);
		    }
		    $('.add_to_cart_btn').removeAttr('disabled');
		       
			
        });

	    $('.pricesvariationshide').each(function() {
	    	if ($(this).is(':checked') && $(this).data('stockstatus') == 0) {
	    		$('.add_to_cart_btn').attr('disabled','');
	    	}
	    });


		
		
	}
	else{
		$('.pricesvariations').each(function() {

		if ($(this).is(':checked') && $(this).data('stockstatus') == 1) {
			var oldprice=$(this).data('oldprice');
		    var price=$(this).data('price');
		    var qty=$(this).data('qty');
		    totalprice= totalprice+price;
		    totaloldprice= totaloldprice+oldprice;

		    if ($(this).data('stockstatus') == 1) {
		    	$('.input_qty').attr('data-max',qty);
		    }
		    $('.add_to_cart_btn').removeAttr('disabled');
		       
			}
	    });

	    $('.pricesvariations').each(function() {
	    	if ($(this).is(':checked') && $(this).data('stockstatus') == 0) {
	    		$('.add_to_cart_btn').attr('disabled','');
	    	}
	    });


		if (totalprice == 0) {
			$('.price_area').html('');
			return false;
		}
	}

	

	
    var formattedTotalPrice = '$' + (parseFloat(totalprice) || 0).toFixed(2);
    var newtotaloldprice = totaloldprice != 0 ? ('<s style="font-size: 16px; color: #94a3b8; font-weight: 400; margin-left: 8px;">$' + (parseFloat(totaloldprice) || 0).toFixed(2) + '</s>') : '';
    var price_html='<span class="discount">' + formattedTotalPrice + '</span>' + newtotaloldprice;
	$('.price_area').html(price_html);


}


 
  $(document).on('submit','.product_option_form', function(e) {
        e.preventDefault();

        if ($('.pricesvariationshide').length == 1) {
	        var $qty = $('.input-number');
	        var $button = $(this);
	        var $input = $button.closest('.quantity').find("input.input-number");
	        var currentVal = parseInt($input.val(), 10);
	        if (!isNaN(currentVal)) {
	        	
	        	if ($('.input_qty').data('max') < currentVal) {
	        		Sweet('error',$('.input_qty').data('max')+' Pieces Available Only');
	        		return false;
	        	}
	        	
	        }

        }

        var required=false;
        
        if (window.booostr_variation_prices && window.booostr_variation_prices.length > 0) {
            if (!$('#selected_variation_id').val()) {
                Sweet('error', 'Please select all required options');
                return false;
            }
            var varId = parseInt($('#selected_variation_id').val());
            var matchedVar = window.booostr_variation_prices.find(function(p) { return parseInt(p.id) === varId; });
            if (matchedVar) {
                var isOutStock = (matchedVar.stock_status == 0) || (matchedVar.stock_manage == 1 && parseInt(matchedVar.qty) <= 0);
                if (isOutStock) {
                    Sweet('error', 'Selected variation is out of stock');
                    return false;
                }
            }
            required = true;
        } else {
            if($('.required_var').length > 0){
            	$('.required_var').each(function () {
            		var optionid=$(this).data('id');
            		required = false;

            		$('.variations'+optionid).each(function () {
            			if (this.checked == true) {
            				required = true;
            			}
            		});	

            	});
            }
            else{
            	required = true;
            }

            if(required == false || totalprice == 0){
            	Sweet('error','Select a required variation');
    	    	return false;
            }
        }



        

        var form_data = $(this).serialize();
      
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
              $('.subtotal').html(response.cart_subtotal);
              $('.cart_subtotal').html(response.cart_subtotal);
              
              $('.tax').html(response.cart_tax);
              $('.cart_subtotal').html(response.cart_total);
              $('.total_amount').val(response.cart_total);
              $('.cart_count').html(response.cart_count)
              
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

// Smooth scroll to full description
$(document).on('click', '.scroll-to-desc', function(e) {
    e.preventDefault();
    var target = $('#product-full-description');
    if (target.length) {
        $('html, body').animate({
            scrollTop: target.offset().top - 90
        }, 500);
    }
});

