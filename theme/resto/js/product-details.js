"use strict";

var totalprice=0;
var totaloldprice=0;

$('.pricesvariations').on('change',function(){
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

variations();

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

