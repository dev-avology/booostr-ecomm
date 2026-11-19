(function() {
	"use strict";
	
	/*=====================================
	  Preloader
	======================================= */
	window.onload = function() {
		window.setTimeout(fadeout, 500);
	};

	function fadeout() {
		document.querySelector('.preloader').style.opacity = '0';
		document.querySelector('.preloader').style.display = 'none';
	}

	/*=====================================
	  Sticky
	======================================= */
	window.onscroll = function() {
		var header_navbar = document.querySelector(".navbar-area");
		var sticky = header_navbar.offsetTop;

		if (window.pageYOffset > sticky) {
			header_navbar.classList.add("sticky");
		} else {
			header_navbar.classList.remove("sticky");
		}

		// show or hide the back-top-top button
		var backToTo = document.querySelector(".scroll-top");
		if (
			document.body.scrollTop > 50 ||
			document.documentElement.scrollTop > 50
		) {
			backToTo.style.display = "flex";
		} else {
			backToTo.style.display = "none";
		}
	};

	/*=====================================
	  Wow Animation
	======================================= */
	new WOW().init();

	/*=====================================
	  Mobile Menu Button
	======================================= */
	let navbarToggler = document.querySelector(".mobile-menu-btn");
	navbarToggler.addEventListener("click", function() {
		navbarToggler.classList.toggle("active");
	});

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
		  if ($input.is(':disabled')) return false;

		  var currentVal = parseInt($input.val() || 0);
		  var maxVal = parseInt($input.attr('data-max') || $input.data('max'));
		  if (isNaN(maxVal) || maxVal <= 0) maxVal = 9999;

		  if (currentVal < maxVal) {
		  	$input.val(currentVal + 1);
		  } else {
		    if (typeof Sweet === 'function' && maxVal < 9999) {
		        Sweet('warning', 'Only ' + maxVal + ' pieces available in stock');
		    }
		  }
		});

		$(document).on("click",".minus",function() {
		  var $button = $(this);
		  var $input = $button.closest('.sp-quantity').find("input.quntity-input");
		  if ($input.is(':disabled')) return false;

		  var currentVal = parseInt($input.val() || 1);
		  var minVal = parseInt($input.attr('min') || 1);
		  if (currentVal > minVal) {
		  	$input.val(currentVal - 1);
		  }
		});

		$(document).on("change input", "input.quntity-input", function() {
		    var $input = $(this);
		    if ($input.is(':disabled')) return;
		    var val = parseInt($input.val());
		    var maxVal = parseInt($input.attr('data-max') || $input.data('max'));
		    if (isNaN(maxVal) || maxVal <= 0) maxVal = 9999;
		    var minVal = parseInt($input.attr('min') || 1);

		    if (isNaN(val) || val < minVal) {
		        $input.val(minVal);
		    } else if (val > maxVal) {
		        $input.val(maxVal);
		        if (typeof Sweet === 'function' && maxVal < 9999) {
		            Sweet('warning', 'Maximum available quantity is ' + maxVal);
		        }
		    }
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

