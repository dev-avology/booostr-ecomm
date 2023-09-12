(function() {
	"use strict";
	
	

	

	/*=====================================
	  Wow Animation
	======================================= */
	new WOW().init();

	/*=====================================
	  Mobile Menu Button
	======================================= */

	$('.shopping-list').perfectScrollbar();          
	
	$('.order-popup-inner').perfectScrollbar();          
	
	$('.cart-sidebar,.close-button').on("click", function() {
		$('.shopping-item').toggleClass('active');
	});

	
		

		$('.accounts-top-btn a').on( "click", function(){
			$('.accounts-signin-top-form').toggleClass('active');
		});		
	
			
		
		$('select').niceSelect();
		
		
		$(document).on("click",".plus",function() {
		  var $button = $(this);
		  var $input = $button.closest('.sp-quantity').find("input.quntity-input");
		  if ($input.val() < $input.data('max')) {
		  	$input.val((i, v) => Math.max(0, +v + 1 * $button.data('multi')));
		  }
		 
		  
		});

		$(document).on("click",".minus",function() {
		  var $button = $(this);
		  var $input = $button.closest('.sp-quantity').find("input.quntity-input");
		 
		  	$input.val((i, v) => Math.max(0, +v + 1 * $button.data('multi')));
		  
		 
		  
		});
		

		$('.pricesvariations').on('change', function () {
			var id=$(this).val();
			if ($(this).is(':checked')){
				$('.variation'+id).addClass('active');
			}
			else{
				$('.variation'+id).removeClass('active');
			}
			
		});

		$('.color_single').on('change', function () {
			var id=$(this).val();
			var idName=$(this).attr('id');

			if ($(this).is(':checked')){
				$('.'+idName).html('<i class="icofont-verification-check"></i>');
			}
			else{
				$('.'+idName).html('');
			}
			
		});
		


		
	

})();

