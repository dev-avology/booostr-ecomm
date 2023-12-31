"use strict";

// $('.cart_subtotal').text(amount_format(subtotal));
// $('.cart_tax').text(amount_format(tax));
// $('.cart_total').text(amount_format(total));

var cartState = {Rsubtotal:subtotal,Rtax:tax,Rshipping:price,Rdiscount:discount};

calculateTotal(cartState);

var loader = $('#page-loader');

//$('.cart_credit_card_fee').text(amount_format(credit_card_fee));
//$('.cart_booster_platform_fee').text(amount_format(booster_platform_fee));
//$('.cart_grand_total').text(amount_format(total+credit_card_fee+booster_platform_fee+price));
//$('.cart_grand_total').text(amount_format(total+price));

 /*-------------------------
        Order Method Change
    --------------------------*/
$('.order_method').on('change',function(){

	if ($(this).val() == 'pickup') {
		$('.shipping_method_area').hide();
		$('.delivery_address_area').hide();
		$('.post_code_area').hide();
		$('.shipping_fee').hide();
		$('.map_area').hide();
		//$('.cart_total').text(amount_format(subtotal));
		cartState.Rtax = 0.00;
		cartState.Rshipping = 0.00;
		calculateTotal(cartState);

	}
	else{
		$('.shipping_method_area').show();
		$('.delivery_address_area').show();
		$('.post_code_area').show();
		$('.shipping_fee').show();
		$('.map_area').show();
		//$('.cart_total').text(parseFloat(new_total.toFixed(2)));
		calculateTotal(cartState);
	}
});

/*-------------------------
        Payment Getway
    --------------------------*/
$('.getway').on('click',function(){
	$('.currency_area').hide();
	$('.rate_area').hide();
	$('.charge_area').hide();
	$('.instruction_area').hide();
	


	var logo=$(this).data('logo');
	var instruction=$(this).data('instruction');
	var currency=$(this).data('currency');
	var rate=$(this).data('rate');
	var charge=$(this).data('charge');
	
	

	$('.getway_logo').attr('src',logo);
	$('.currency').text(currency);
	$('.rate').text(rate);
	$('.charge').text(charge);
	$('.instruction').text(instruction);

	$('.payement_inst').show();

	currency != '' ? $('.currency_area').show() : $('.currency_area').hide();
	rate != '' ? $('.rate_area').show() : $('.rate_area').hide();
	charge != '' ? $('.charge_area').show() : $('.charge_area').hide();
	instruction != '' ? $('.instruction_area').show() : $('.instruction_area').hide();

});

/*-------------------------
       Location Change
    --------------------------*/
$('#locations').on('change',function(){

	$('.shipping_method').remove();
	var shippings=$(this).find('option:selected').data('shipping')
	
	
	$.each(shippings,function(key,value){

		var html=`<label class="checkbox-inline shipping_method" for="shipping${value.id}">
					<input name="shipping_method" class="shipping_item" value="${value.id}" data-price="${value.slug}"  id="shipping${value.id}" type="radio" > ${value.name}
					</label>`;

		$('.shipping_render_area').append(html);
	});

	$('.shipping_method_area').show();

});

/*-------------------------
       shipping_item
    --------------------------*/
$(document).on('change','.shipping_item',function(){
	calculateShipping();
});


function calculateShipping(){

	var selectedShipping = $('input[name=shipping_method]:checked');
	
	 price= selectedShipping.data('price');
	 
	var shippingD = selectedShipping.data('shippinginfo');
	var mt = shippingD.method_type;
	var cartweight = parseInt($('#totalWeight').val());

    var subtotal = $('#subtotal').val();
    var current_subtotal = cartState.Rsubtotal - cartState.Rdiscount;
	
	var cartItems = $('#totalItem').val();
	if(mt == 'free_shipping'){
	price = 0;

	}else if(mt == 'per_item'){

      var per_item_charge = parseFloat(shippingD.pricing);
       price = parseFloat(price) + cartItems * per_item_charge;

	}else if(mt == 'weight_based'){
		var per_lb_charge = parseFloat(shippingD.pricing);
		price = parseFloat(price) + cartweight * per_lb_charge;

	}else if(mt == 'flat_rate'){

		var pricing = shippingD.pricing;
       

		if (Array.isArray(pricing)) {
			pricing.forEach(item => {
				var from = parseFloat(item.from)??0;
				var to = parseFloat(item.to)>0? parseFloat(item.to):Number.MAX_VALUE;

				if (current_subtotal > from && current_subtotal <= to) {
				price = parseFloat(item.price);
			  }
			});

		  } else if (typeof pricing === 'object' && pricing !== null) {
			console.log('Object');
			Object.values(pricing).forEach(item => {
				var from = parseFloat(item.from)??0;
				var to = parseFloat(item.to)>0? parseFloat(item.to):Number.MAX_VALUE;
				if (current_subtotal > from && current_subtotal <= to) {
				  price = parseFloat(item.price);
			    }
			});
		  }
	     //	price = parseInt(pricing[0]?.price??0);
	}

	cartState.Rshipping = price;
	calculateTotal(cartState);

	// $('.shipping_fee').text(amount_format(price));

	// new_total=total+price;

	// $('.cart_total').text(amount_format(new_total));

	// console.log(new_total,'new shipping');


}

$(document).on('change','#billing-name,#billing-email,#billing-phone,#location_input,#location_city,#location_state,#billing-country,#post_code',function(){

	var shipping_address = $('#shipping_address');
	$(this).closest('.form-group').removeClass('error');

	if(shipping_address.is(':checked')){
		let shippingf = $(this).data('shippingf');
		$('#'+shippingf).val($(this).val()).closest('.form-group').removeClass('error');
		if(shippingf == 'location_state1'){
			$('#'+shippingf).trigger('change');
		}
	}

});


/*-------------------------
       Create New Account
    --------------------------*/
$('#shipping_address').on('change',function(){
	if ($(this).is(':checked')){

        $('#shipping-name').val($('#billing-name').val())
        $('#shipping-email').val($('#billing-email').val())
        $('#shipping-phone').val($('#billing-phone').val())
        $('#location_input1').val($('#location_input').val())
        $('#location_city1').val($('#location_city').val())
        $('#location_state1').val($('#location_state').val()).trigger('change')
        $('#shipping-country').val($('#billing-country').val())
        $('#post_code1').val($('#post_code').val())
		//shipping_state_change();

		$('.shipping_address_area').hide();		
	}
	else{
		$('.shipping_address_area').show();
	}
})

/*-------------------------
       Order Form Submit
    --------------------------*/
// $('.orderform').on('submit', function(e) {
// 	$('.submitbtn').attr("disabled", "disabled");
// 	$('.submitbtn').text("Please wait...");
// });


/*-------------------------
       Pre Order Change
    --------------------------*/
$('#pre_order').on('change',function(){
if ($(this).is(':checked')){
		$('.pre_order_area').show();
	}
	else{
		$('.pre_order_area').hide();
	}
});

/*-------------------------
       Getway Btn Click
    --------------------------*/
$(document).on('click','.getway_btn',function(){

var id=$(this).data('id');
$('#getway'+id).prop("checked", true);

});

/*-------------------------
       Time Change
    --------------------------*/
$(document).on('change','#time',function(){
	var inputEle = document.getElementById('time');
	var timeSplit = inputEle.value.split(':'),
	hours,
	minutes,
	meridian;
	hours = timeSplit[0];
	minutes = timeSplit[1];
	if (hours > 12) {
	meridian = 'PM';
	hours -= 12;
} else if (hours < 12) {
	meridian = 'AM';
	if (hours == 0) {
	hours = 12;
	}
} else {
	meridian = 'PM';
}


$('.time').val(hours + ':' + minutes + ' ' + meridian)
});

$(document).on('change','#location_state1',function(){
	shipping_state_change();
})

$(document).ready(function(){
	$(".shipping_method_area .shipping_method").find(".shipping_item").eq(0).trigger('click');
});
function shipping_state_change()
{
	var shipping_state = $('#location_state1').val();
	
    if(shipping_state != ''){
		loader.css('display','flex');
		$.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
		});

		$.ajax({
			type: 'POST',
			url: apply_tax_url,
			data: {shipping_state: $('#location_state1').val(),shipping_price:price},
			dataType: 'json',
			success: function(response){ 			
				$('#tax').val(response.cart_tax);
                 
				tax = response.cart_tax;
                cartState.Rtax= parseFloat(response.cart_tax);
                calculateTotal(cartState);
				//$('.cart_subtotal').text(amount_format(response.cart_subtotal));
				// $('.cart_tax').text(amount_format(response.cart_tax));
				// $('.cart_total').text(amount_format(response.cart_total));

				loader.css('display','none');

			}
		});
 
 
}
console.log('Tax updated');
}


$(document).on('click','#applyCouponBtn',function(){
	var couponValue = $("#couponInput").val();
	$('#show_coupon_error').hide();

	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	$.ajax({
		url: '/apply-coupon-code',
		type: 'POST',
		data: {
			coupon_code: couponValue
		},
		dataType: 'JSON',
		success: function (response) {
			if(response){
				console.log(response);
				let data = null;

				 if(response.status == 422){
					$('#show_coupon_error').html(response.msg);
					$('#show_coupon_error').show();
					 data= response.data;
				  }else{
                      data= response.result;
				  }
				  console.log(data);

					cartState.Rtax = parseFloat(data.tax);
					cartState.Rdiscount = parseFloat(data.discount);
                   
					if(data.items_on_discount !== 'all'){
                     let items_on_discount = data.items_on_discount;
					 let valuesArray = Object.values(items_on_discount);
					 valuesArray.forEach(function(item){
						if(item.discount){
						 let netotal = (parseFloat(item.price) - parseFloat(item.discount)) * parseFloat(item.qty);
							$('#'+item.rowId+' span.price').html('<span class="old-price">'+amount_format((parseFloat(item.price)*parseFloat(item.qty)))+'</span><span>'+amount_format(netotal)+'</span>');
    					}else{
							$('#'+item.rowId+' span.price').html('<span>'+amount_format((parseFloat(item.price)*parseFloat(item.qty)))+'</span>');
						}

					 })
					}

					calculateShipping();
					calculateTotal(cartState);

			
				return false;
			}
		},
		error: function (err) {
		   console.log(err);
		},
	});

});



function calculateTotal(state = cartState){

	var newtotal = state.Rsubtotal+state.Rshipping+state.Rtax-state.Rdiscount;
	cartState.Rtotal = newtotal;

	$('.cart_subtotal').text(amount_format(state.Rsubtotal));
	$('.cart_discount').text(amount_format(state.Rdiscount));
	$('.shipping_fee').text(amount_format(state.Rshipping));
	$('.cart_tax').text(amount_format(state.Rtax));
	$('.cart_total').text(amount_format(newtotal));
	
}
