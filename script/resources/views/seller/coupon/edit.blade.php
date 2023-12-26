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
    <h1>{{ __('Edit Coupon') }}</h1>
   </div>
   {{-- /section title --}}
   <div class="row">
      <div class="col-lg-12">
           <form class="ajaxform" method="post" action="{{ route('seller.coupon.update',$info->id) }}">
                @csrf
                @method('PUT')

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
                                                <option value="all" @if($info->coupon_for_name == 'all') selected @endif>{{ __('Total order amount') }}</option>
                                                <option  @if($info->coupon_for_name != 'all') selected @endif value="specific_product_or_cat">{{ __('Specific products/Categories') }}</option>
                                            </select>
                                        </div>
                                    </div>

                                

                                    <div class="form-group row mb-2" style="display:none;" id="specific-cat-pro">
                                        <label for="" class="col-lg-12">{{ __('Choose Specific products/Categories') }} </label>
                                        <div class="col-lg-12">
                                            <select class="form-control" data-live-search="true" name="choose_specific_product_or_category" data-selected = "{{$info->coupon_for_id}}" id="coupon_for"> 
                                                <option value="" selected disabled>{{ __('Choose Specific products') }}</option>
                                                <option value="product" @if($info->coupon_for_name == 'product') selected @endif>{{ __('Specific products') }}</option>
                                                <option value="category" @if($info->coupon_for_name == 'category') selected @endif>{{ __('Specific categories') }}</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group row mb-2 hide-all-coupon-value" style="display:none;">
                                        <label for="" id="specific-label" class="col-lg-12">{{ __('Choose Specific products') }} </label>
                                        <div class="col-lg-12">
                                            <select class="form-control selectpicker" multiple data-live-search="true" data-selected="{{$info->coupon_for_id}}" name="coupon_id[]" id="coupon_id">
                                               
                                            </select>
                                        </div>
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>



            <div class="row">
               <div class="col-lg-5">
                   <strong>{{ __('Coupon Details:') }}</strong>
                    <p>{{ __('Add your coupon details and necessary information from here') }}</p>
               </div>
               
               {{-- right side --}}
               <div class="col-lg-7">
                  <div class="card">
                    <div class="card-body">
                    
                        <div class="from-group row mb-2">
                                <label for="" class="col-lg-12">{{ __('Coupon Code Name/Title') }} </label>
                                <div class="col-lg-12">
                                    <input type="text" value="{{$info->coupon_code_name}}" required name="code_name" class="form-control" placeholder="Enter Name/Title">
                                </div>
                            </div>

                            <div class="from-group row mb-2">
                                <label for="" class="col-lg-12">{{ __('Coupon Code') }} </label>
                                <div class="col-lg-12">
                                    <input type="text" value="{{$info->code}}" required name="code" class="form-control" placeholder="Enter Coupon Code">
                                </div>
                            </div>


                            <div class="form-group row mb-2">
                                <label for="" class="col-lg-12">{{ __('Type:') }}</label>
                                <div class="col-lg-12">
                                    <select class="form-control" name="discount_type" id="discount_type" required>
                                        <option value="" selected disabled>{{ __('Choose dollar off or percent off') }}</option>
                                        @php $isPercentage = $info->is_percentage; @endphp
                                        <option value = "0" {{ $isPercentage === 0 ? 'selected' : '' }}>{{ __('Dollar off') }}</option>
                                        <option value = "1" {{ $isPercentage === 1 ? 'selected' : '' }}>{{ __('Percent off') }}</option>
                                    </select>
                                </div>
                            </div>


                            <div class="from-group row mb-2" style="display:none;" id="discount-amount-hide">
                                <label for="" class="col-lg-12">Discount Amount</label>
                                <div class="col-lg-12 input-with-icon">


                                    <input type="text"  value="{{$info->value}}" required="" 
                                   step="any" name="price" class="form-control" id="maskprice" data-inputmask="'mask': '9{0,2}.9{0,3}'" data-mask placeholder="Enter percent off or doller off">
                                </div>
                            </div>
                            

                            <div class="from-group row mb-2" id="min_amount_area">
                                <label for="" class="col-lg-12">{{ __('Condition to qualify:') }} </label>
                                <div class="col-lg-12">
                                    <select class="form-control" name="min_amount_option" id="min_amount_option">
                                        <option value="0" {{ $info->min_amount_option == 0 ? 'selected' : '' }}> Qualify for all </option>
                                        <option value="1" {{ $info->min_amount_option == 1 ? 'selected' : '' }} >{{ __('Minimum order subtotal amount (in Dollar)') }}</option>
                                        <option value="2" {{ $info->min_amount_option == 2 ? 'selected' : '' }}>{{ __('Minimum number of items in cart (all products/categories)') }}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="from-group row mb-2" id="min_amount_val" @if($info->min_amount_option == 0)style="display:none;" @endif>
                                <label for="" class="col-lg-12">{{ __('Enter minimum amount:') }} </label>
                                <div class="col-lg-12 input-with-icon">
                                    <input type="number" value="{{$info->min_amount}}" step="any" name="min_amount" class="form-control" placeholder="Enter Min Amount">
                                    <span class="applied-text" style="font-size:10px;">Applies only to selected products. OR Applies only to selected categories. (depends on if they selected products or categories)</span>
                                </div>
                            </div>


                            <div class="from-group row mb-2">
                                <label for="" class="col-lg-12">{{ __('Limit Number of times discount can be used:') }} </label>
                                <div class="col-lg-12">
                                    <input class="tgl tgl-light" @if($info->max_use > 0) checked @endif id="max_use_checkbox" type="checkbox"/>
                                </div>
                            </div>

                            <div class="from-group row mb-2" id="max_use_value" @if($info->max_use == 0) style="display:none;" @endif >
                                <label for="" class="col-lg-12">{{ __('Add the number of times it can be used:') }} </label>
                                <div class="col-lg-12">
                                    <input type="number" name="max_use" value="{{$info->max_use}}" class="form-control" placeholder="Add the number of times it can be used">
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
                                    <input type="datetime-local" value="{{$info->start_from}}" name="start_from" class="form-control">
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
                                        <input type="datetime-local" value=""{{$info->will_expire}} name="will_expire" class="form-control" >
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
        placeholder: "5.00", // adjust the placeholder as needed
        greedy: true
      }).mask('#maskprice');

      $("#cb1-6").change(function () {
                $(".coupon-hidden-date").toggle($(this).is(":checked"));
                var isChecked = $(this).is(":checked");
                if(isChecked){
                    // $(".coupon-hidden-date input[type='date']").prop('required', isChecked);
                    $('#date_checkbox').val(1);
                }
     });

        $(document).on('change', '.input-with-icon input[type=number]', function () {
            // This function will be executed when the input value changes.
            var inputValue = $(this).val();

            inputValue = inputValue.match(/[0-9.]+/g);

            if (inputValue === null || inputValue === '') {
                return;
            }
            $(this).val(parseFloat(inputValue).toFixed(2));
        });

        $(document).on('change', '#maskprice', function () {
            // This function will be executed when the input value changes.
            var inputValue = $(this).val();
            inputValue = inputValue.match(/[0-9.]+/g);

            if (inputValue === null || inputValue === '') {
                return;
            }
            $(this).val(parseFloat(inputValue).toFixed(2));
        });

        var value = '';
        var coupon_for = $('#coupon_for').val();

        if(coupon_for == 'all'){
            $('.hide-all-coupon-value').css('display','none');
        }

        var initialCouponFor = $('#coupon_for').val();

        console.log(initialCouponFor,'for');
        couponCode(initialCouponFor);

        var discountVal;
        var minamount;
        var checkboxValue;
        userDiscount($("#discount_type").val());
        minAmount($("#min_amount_option").val());
        discountCheckbox($("#cb1-6"));
        $('#cb1-6').prop('checked', true);   
        $('.coupon-hidden-date').css('display','block'); 

        $('#coupon_first').change(function(){
            var coupon_first = $(this).val();
            if(coupon_first == 'specific_product_or_cat'){
                    $('#specific-cat-pro').css('display','block');
                    var value = '';
                    var coupon_for = $('#coupon_for').val();
                    if(coupon_for == 'all'){
                        $('.hide-all-coupon-value').css('display','block');
                        console.log('ok');
                    }
            
                    couponCode(coupon_for);
            }else{
                    $('#specific-cat-pro').css('display','none');
                    $('.hide-all-coupon-value').css('display','none');
            }
        })


        $("#max_use_checkbox").change(function () {
            $("#max_use_value").toggle($(this).is(":checked"));
            // Set input value to 0 when checkbox is unchecked
            if (!$(this).is(":checked")) {
                $("input[name='max_use']").val(0);
            }
      
        });


        var coupon_first = $('#coupon_first').val();
        if(coupon_first == 'specific_product_or_cat'){
            $('#specific-cat-pro').css('display','block');
            $('.hide-all-coupon-value').css('display','block');
        }else{
            $('#specific-cat-pro').css('display','none');
            $('.hide-all-coupon-value').css('display','none');
        }
        


            $("#cb1-6").change(function () {
                discountCheckbox($("#cb1-6"))
            });

            $("#min_amount_option").change(function () {
                var min_amount_check = $(this).val();

                console.log(min_amount_check,'ok');
                minAmount(min_amount_check);
            });

            $("#discount_type").change(function () {
                var discount_type = $(this).val();

                userDiscount(discount_type);
            });


            function userDiscount(discountVal){
                var discount_type = discountVal;
                
                $('.input-with-icon i').remove();
                // var discount_type = $(this).val();
                if(discount_type==0){
                    console.log(discount_type,'0');
                    $('#discount-amount-hide').css('display','block');
                    $('#discount-amount-hide label').text('Enter discount amount in doller($)');

                    var iconElement = $('<i class="fas fa-dollar-sign"></i>');
                    $('.input-with-icon input[name="price"]').before(iconElement);

                }else if(discount_type==1){
                    $('#discount-amount-hide').css('display','block');
                    $('#discount-amount-hide label').text('Enter discount amount in percent(%)');
                    var iconElement = $('<i class="fas fa-percent"></i>');
                    $('.input-with-icon input[name="price"]').before(iconElement);
                }else{
                    $('#discount-amount-hide').css('display','none');
                    $('#discount-amount-hide label').text('');
                }
            }

            function minAmount(minamount){
                var min_amount_check = minamount;
                // if (min_amount_check == 1) {
                //     $('#min_amount_val').css('display', 'block');
                // } else if (min_amount_check == 2) {
                //     $('#min_amount_val').css('display', 'block');
                // }else{
                //     $('#min_amount_val').css('display', 'none');
                //     $("input[name='min_amount']").val(0);
                // }

                $('.input-with-icon i').remove();
                // var min_amount_check = $(this).val();
                if (min_amount_check == 1) {
                    $('#min_amount_val').css('display', 'block');
                    var iconElement = $('<i class="fas fa-dollar-sign"></i>');
                    $('.input-with-icon input[name="min_amount"]').before(iconElement);

                } else if (min_amount_check == 2) {
                    $('#min_amount_val').css('display', 'block');
                }else{
                    $('#min_amount_val').css('display', 'none');
                }
            }

            function discountCheckbox(checkboxValue){
                $(".coupon-hidden-date").toggle(checkboxValue.is(":checked"));
                var isChecked = checkboxValue.is(":checked");
                if(isChecked){
                    // $(".coupon-hidden-date input[type='date']").prop('required', isChecked);
                    $('#date_checkbox').val(1);
                }else{
                    $('#date_checkbox').val(0);
                }
            }


            function couponCode(value){
                $('.selectpicker').selectpicker();
                var selectedValues = value ? value : '';


                if (selectedValues && selectedValues.includes('all')) {
                    $('.hide-all-coupon-value').css('display', 'none');
                }

                var selected_id = $('#coupon_id').data('selected');

                var selected_id = selected_id;

                console.log(selectedValues,'ok');

                if (selectedValues || (selectedValues.includes('product') || selectedValues.includes('category'))) {
                    console.log(selectedValues,'new');
                    $.ajax({
                        type: "GET",
                        url: '{{ url("get-coupon-type") }}' + '/' + selectedValues,
                        success: function(data) {
                            console.log(data,'data');
                            $('.hide-all-coupon-value').css('display', 'block');

                            var specificLabel = '';

                            $.each(data['term'], function(key, value) {
                                console.log('ashish');
                                if (data['type'] == 'product') {
                                    specificLabel = 'Choose specific products';
                                    $('#specific-label').text(specificLabel);
                                    var isSelected = selected_id.includes(value.id.toString());
                                    $('#coupon_id').append('<option value="' + value.id + '" ' + (isSelected ? 'selected' : '') + '>' + value.title + '</option>');
                                } else if (data['type'] == 'category') {
                                    specificLabel = 'Choose specific categories';
                                    $('#specific-label').text(specificLabel);
                                    var isSelected = selected_id.includes(value.id.toString());
                                    $('#coupon_id').append('<option value="' + value.id + '" ' + (isSelected ? 'selected' : '') + '>' + value.name + '</option>');
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
            }


            $('#coupon_for').change(function() {
                couponCode($(this).val());
            });
      });
</script>
@endpush

