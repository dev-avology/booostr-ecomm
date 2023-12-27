@extends('layouts.backend.app')

@push('css')

<!-- CSS Libraries -->
<link rel="stylesheet" href="{{ asset('admin/plugins/dropzone/dropzone.css') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@x.x.x/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@x.x.x/dist/css/bootstrap-select.min.css">
@endpush

@section('title','Dashboard')

@section('content')
<section class="section">
    {{-- section title --}}
    <div class="section-header">
        <a href="{{ url('seller/coupon') }}" class="btn btn-primary mr-2">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1>{{ __('Create Coupon') }}</h1>
    </div>
    {{-- /section title --}}
    <div class="row">
        <div class="col-lg-12">
            <form class="ajaxform_with_reset" method="post" action="{{ route('seller.coupon.store') }}">
                @csrf
                {{-- <div class="row">
                    <div class="col-lg-5">
                    <strong>{{ __('Image') }}</strong>
                    <p>{{ __('Upload Coupon image here') }}</p>
                    </div>
                    
                    <div class="col-lg-7">
                    <div class="card">
                        <div class="card-body">
                            {{mediasection()}}
                        </div>
                    </div>
                    </div>
                </div> --}}
                <div class="row">
                    {{-- left side --}}
                    <div class="col-lg-5">
                    <strong>{{ __('Coupon Basics Information') }}</strong>
                        <p>{{ __('Add your coupon Basics information from here') }}</p>
                    </div>
                    {{-- /left side --}}
                    {{-- right side --}}
                    <div class="col-lg-7">
                    <div class="card">
                        <div class="card-body">

                                    <div class="from-group row mb-2">
                                        <label for="" class="col-lg-12">{{ __('Coupon code is for discount off') }} </label>
                                        <div class="col-lg-12">
                                            <select class="form-control" name="coupon_first" id="coupon_first" required>
                                                <option value="all" selected>{{ __('Total order amount') }}</option>
                                                <option value="specific_product_or_cat">{{ __('Specific products/Categories') }}</option>
                                            </select>
                                        </div>
                                    </div>

                                    {{-- <div class="from-group row mb-2" style="display:none;" id="specific-cat-pro">
                                        <label for="" class="col-lg-12">{{ __('Choose Specific products/Categories') }} </label>
                                        <div class="col-lg-12">
                                            <select class="form-control" name="choose_specific_product_or_category" id="coupon_for"> 
                                                <option value="" selected disabled>{{ __('Choose Specific products') }}</option>
                                                <option value="product">{{ __('Specific products') }}</option>
                                                <option value="category">{{ __('Specific categories') }}</option>
                                            </select>
                                        </div>
                                    </div>


                                    <div class="from-group row mb-2 hide-all-coupon-value" style="display:none;">
                                        <label for="" class="col-lg-12">{{ __('Choose Specific products') }} </label>
                                        <div class="col-lg-12">
                                            <select class="form-control" name="coupon_id" id="coupon_id">
                                                <option selected disabled>Select</option>
                                            </select>
                                        </div>
                                    </div> --}}

                                    <div class="form-group row mb-2" style="display:none;" id="specific-cat-pro">
                                        <label for="" class="col-lg-12">{{ __('Choose Specific products/Categories') }} </label>
                                        <div class="col-lg-12">
                                            <select class="form-control" data-live-search="true" name="choose_specific_product_or_category" id="coupon_for"> 
                                                <option value="" selected disabled>{{ __('Choose Specific products') }}</option>
                                                <option value="product">{{ __('Specific products') }}</option>
                                                <option value="category">{{ __('Specific categories') }}</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group row mb-2 hide-all-coupon-value" style="display:none;">
                                        <label for="" id="specific-label" class="col-lg-12">{{ __('Choose Specific products') }} </label>
                                        <div class="col-lg-12">
                                            <select class="form-control selectpicker" multiple data-live-search="true" name="coupon_id[]" id="coupon_id">
                                                <!-- Remove the 'Select' option as it is not needed for multi-select -->
                                            </select>
                                        </div>
                                    </div>
                                    
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    {{-- left side --}}
                    <div class="col-lg-5">
                    <strong>{{ __('Coupon Details:') }}</strong>
                        <p>{{ __('Add your coupon details and necessary information from here') }}</p>
                    </div>
                    {{-- /left side --}}
                    {{-- right side --}}
                    <div class="col-lg-7">
                    <div class="card">
                        <div class="card-body">
                            
                                    <div class="from-group row mb-2">
                                        <label for="" class="col-lg-12">{{ __('Coupon Code Name/Title') }} </label>
                                        <div class="col-lg-12">
                                            <input type="text" required name="code_name" class="form-control" placeholder="Enter Name/Title">
                                        </div>
                                    </div>

                                    <div class="from-group row mb-2">
                                        <label for="" class="col-lg-12">{{ __('Coupon Code') }} </label>
                                        <div class="col-lg-12">
                                            <input type="text" required name="code" class="form-control" placeholder="Enter Coupon Code">
                                        </div>
                                    </div>


                                    <div class="from-group row mb-2">
                                        <label for="" class="col-lg-12">{{ __('Type:') }} </label>
                                        <div class="col-lg-12">
                                            <select class="form-control" name="discount_type" id="discount_type" required>
                                                <option value="" selected disabled>{{ __('Choose dollar off or percent off') }}</option>
                                                <option value="0">{{ __('Dollar off') }}</option>
                                                <option value="1">{{ __('Percent off') }}</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="from-group row mb-2" style="display:none;" id="discount-amount-hide">
                                        <label for="" class="col-lg-12">Discount Amount</label>
                                        <div class="col-lg-12 input-with-icon">
                                         
                                           <input type="text"  required="" value="0" 
                                           step="any" name="price" class="form-control" id="maskprice"  placeholder="Enter percent off or doller off">
                                        </div>
                                    </div>
                                    

                                    <div class="from-group row mb-2" id="min_amount_area">
                                        <label for="" class="col-lg-12">{{ __('Condition to qualify:') }} </label>
                                        <div class="col-lg-12">
                                            <select class="form-control" name="min_amount_option" id="min_amount_option">

                                                <option value="0"> Qualify for all </option>

                                                <option value="1">{{ __('Minimum order subtotal amount (in Dollar)') }}</option>
                                                <option value="2">{{ __('Minimum number of items in cart (all products/categories)') }}</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="from-group row mb-2" id="min_amount_val" style="display:none;">
                                        <label for="" class="col-lg-12">{{ __('Enter minimum amount:') }} </label>
                                        <div class="col-lg-12 input-with-icon">
                                            <input type="number" step="any" name="min_amount" class="form-control" placeholder="" id ="min_amount_mask">
                                            <span class="applied-text" style="font-size:10px;">Applies only to selected products. OR Applies only to selected categories. (depends on if they selected products or categories)</span>
                                        </div>
                                       
                                    </div>


                                    <div class="from-group row mb-2">
                                        <label for="" class="col-lg-12">{{ __('Limit Number of times discount can be used:') }} </label>
                                        <div class="col-lg-12">
                                            <input class="tgl tgl-light" id="max_use_checkbox" type="checkbox"/>
                                        </div>
                                    </div>




                                    <div class="from-group row mb-2" id="max_use_value" style="display:none;">
                                        <label for="" class="col-lg-12">{{ __('Add the number of times it can be used:') }} </label>
                                        <div class="col-lg-12">
                                            <input type="number" name="max_use" value="0" class="form-control" placeholder="Add the number of times it can be used">
                                        </div>
                                    </div>
                                    
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    {{-- left side --}}
                    <div class="col-lg-5">
                    <strong>{{ __('Coupon Scheduling:') }}</strong>
                        <p>{{ __('Add your coupon Scheduling from here') }}</p>
                    </div>
                    {{-- /left side --}}
                    {{-- right side --}}
                    <div class="col-lg-7">
                    <div class="card">
                        <div class="card-body">
                            
                                <div class="from-group row mb-2" >
                                    <label for="" class="col-lg-12">{{ __('Start From:') }} </label>
                                    <div class="col-lg-12">
                                        <input type="datetime-local" name="start_from" class="form-control" >
                                    </div>
                                </div>


                                    <div class="from-group row mb-2">
                                        <label for="" class="col-lg-12">{{ __('Expiration date:') }} </label>
                                        <div class="col-lg-12">
                                            <div class="coupon-checkbox-wrapper-6">
                                                <input class="tgl tgl-light" id="cb1-6" type="checkbox"/>
                                                
                                                <label class="tgl-btn" for="cb1-6">
                                            </div>
                                        </div>
                                        <input type="hidden" name="date_checkbox" id="date_checkbox"/>
                                    </div>



                                    <div class="from-group row mb-2 coupon-hidden-date" style="display:none;">
                                        <label for="" class="col-lg-12">{{ __('Will Expire:') }} </label>
                                        <div class="col-lg-12">
                                            <input type="datetime-local" name="will_expire" class="form-control" >
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-12">
                                            <button class="btn btn-primary basicbtn" type="submit">{{ __('Save') }}</button>
                                        </div>
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>

               
            </form>
        </div>
    </div>
</section>
{{ mediasingle() }} 
@endsection

@push('script')
 <!-- JS Libraies -->
<script src="{{ asset('admin/plugins/dropzone/dropzone.min.js') }}"></script>
<!-- Page Specific JS File -->
<script src="{{ asset('admin/plugins/dropzone/components-multiple-upload.js') }}"></script>
<script src="{{ asset('admin/js/media.js') }}"></script>
<script src="{{ asset('admin/js/seller.js') }}"></script>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@x.x.x/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@x.x.x/dist/js/bootstrap-select.min.js"></script>

<script>
      $(document).ready(function() {
        Inputmask("9{0,2}.9{0,3}", {
        placeholder: "5.00", 
        greedy: true
       }).mask('#maskprice');


      $(document).on('change', '#maskprice', function () {
        var discountType = $('#discount_type').val();
        if(discountType==0){
            var inputValue = $(this).val();
            inputValue = inputValue.match(/[0-9.]+/g);
            if (inputValue === null || inputValue === '') {
                return;
            }
            $(this).val(parseFloat(inputValue).toFixed(2));
        }
    });

    $(document).on('change', '#min_amount_mask', function () {
        var minAmount = $('#min_amount_option').val();
        if(minAmount==1){
            var inputValue = $(this).val();
            inputValue = inputValue.match(/[0-9.]+/g);
            if (inputValue === null || inputValue === '') {
                return;
            }
            $(this).val(parseFloat(inputValue).toFixed(2));
        }
    });

          $('#coupon_first').change(function(){
            var coupon_first = $(this).val();
            if(coupon_first == 'specific_product_or_cat'){
                 $('#specific-cat-pro').css('display','block');
            }else{
                 $('#specific-cat-pro').css('display','none');
                 $('.hide-all-coupon-value').css('display','none');
            }
          })

            $("#cb1-6").change(function () {
                $(".coupon-hidden-date").toggle($(this).is(":checked"));
                var isChecked = $(this).is(":checked");

                if(isChecked){
                    $('#date_checkbox').val(1);
                }
            });

            $("#max_use_checkbox").change(function () {
                $("#max_use_value").toggle($(this).is(":checked"));
            });

            $("#min_amount_option").change(function () {
                
                $('.input-with-icon i').remove();
                var inputValue = $('#min_amount_mask').val();
                var min_amount_check = $(this).val();

                if (min_amount_check == 1) {
                    $('#min_amount_val').css('display', 'block');
                    var iconElement = $('<i class="fas fa-dollar-sign"></i>');
                    $('.input-with-icon input[name="min_amount"]').before(iconElement);

                    inputValue = inputValue.match(/[0-9.]+/g);
                    if (inputValue === null || inputValue === '') {
                        return;
                    }
                    $('#min_amount_mask').val(parseFloat(inputValue).toFixed(2)); 

                } else if (min_amount_check == 2) {
                    $('#min_amount_val').css('display', 'block');
                    $('#min_amount_val').css('display', 'block');
                    $('#min_amount_mask').val(inputValue.replace(/\.00$/, ''))
                }else{
                    $('#min_amount_val').css('display', 'none');
                }
            });

            $("#discount_type").change(function () {
                $('.input-with-icon i').remove();
                var inputValue = $('#maskprice').val();
                var discount_type = $(this).val();

                if(discount_type==0){

                    $('#discount-amount-hide').css('display','block');
                    $('#discount-amount-hide label').text('Enter discount amount in doller($)');

                    inputValue = inputValue.match(/[0-9.]+/g);
                    if (inputValue === null || inputValue === '') {
                        return;
                    }

                    $('#maskprice').val(parseFloat(inputValue).toFixed(2)); 

                    var iconElement = $('<i class="fas fa-dollar-sign"></i>');
                    $('.input-with-icon input[name="price"]').before(iconElement);

                }else if(discount_type==1){

                    $('#discount-amount-hide').css('display','block');
                    $('#discount-amount-hide label').text('Enter discount amount in percent(%)');
                    var iconElement = $('<i class="fas fa-percent"></i>');
                    $('.input-with-icon input[name="price"]').before(iconElement);
                    $('#maskprice').val(inputValue.replace(/\.00$/, ''))
                }else{
                    $('#discount-amount-hide').css('display','none');
                    $('#discount-amount-hide label').text('');
                }
            });

            // var coupon_for = $('#coupon_for').val();
            // if(coupon_for == 'all'){
            //   $('.hide-all-coupon-value').css('display','none');
            // }

            // $('#coupon_for').change(function() {
            //     var selectedValue = $(this).val();

            //     if(selectedValue == 'all'){
            //       $('.hide-all-coupon-value').css('display','none');
            //     }  

            //     if(selectedValue == 'product' || selectedValue == 'category'){

            //         $.ajax({
            //         type: "GET",
            //         url: '{{ url("get-coupon-type") }}' + '/' + selectedValue,
            //         success: function(data) {
            //             $('#coupon_id').empty();
            //             $('.hide-all-coupon-value').css('display','block');

            //             var defaultOptionText = '';

            //             $.each(data['term'], function(key, value) {
            //                 if (data['type'] == 'product') {
            //                     defaultOptionText = 'Select product';
            //                     $('#coupon_id').append('<option value="' + value.id + '">' + value.title + '</option>');
            //                 } else if (data['type'] == 'category') {
            //                     defaultOptionText = 'Select category';
            //                     $('#coupon_id').append('<option value="' + value.id + '">' + value.name + '</option>');
            //                 }
            //             });

            //             $('#coupon_id').prepend('<option selected disabled>' + defaultOptionText + '</option>');
            //         },
            //         error: function(error) {
            //             console.log(error);
            //         }
            //     });

            //     }
            // });

            // Initialize Bootstrap Select
            $('.selectpicker').selectpicker();

            $('#coupon_for').change(function() {
                var selectedValues = $(this).val();

                if (selectedValues && selectedValues.includes('all')) {
                    $('.hide-all-coupon-value').css('display', 'none');
                }

                if (selectedValues && (selectedValues.includes('product') || selectedValues.includes('category'))) {
                    $.ajax({
                        type: "GET",
                        url: '{{ url("get-coupon-type") }}' + '/' + selectedValues,
                        success: function(data) {
                            $('#coupon_id').empty();
                            $('.hide-all-coupon-value').css('display', 'block');

                            // var defaultOptionText = '';
                            var specificLabel = '';

                            $.each(data['term'], function(key, value) {
                                if (data['type'] == 'product') {
                                    // defaultOptionText = 'Select product';
                                    specificLabel = 'Choose specific products';
                                    $('#specific-label').text(specificLabel);
                                    $('#coupon_id').append('<option value="' + value.id + '">' + value.title + '</option>');
                                } else if (data['type'] == 'category') {
                                    // defaultOptionText = 'Select category';
                                    specificLabel = 'Choose specific categories';
                                    $('#specific-label').text(specificLabel);
                                    $('#coupon_id').append('<option value="' + value.id + '">' + value.name + '</option>');
                                }
                            });

                            // Refresh Bootstrap Select after updating options
                            $('#coupon_id').selectpicker('refresh');
                        },
                        error: function(error) {
                            console.log(error);
                        }
                    });
                }
            });



            
        });
</script>
@endpush

