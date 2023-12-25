@extends('layouts.backend.app')
@push('css')
<!-- CSS Libraries -->
  <link rel="stylesheet" href="{{ asset('admin/plugins/dropzone/dropzone.css') }}">
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

                                    <div class="from-group row mb-2" style="display:none;" id="specific-cat-pro">
                                        <label for="" class="col-lg-12">{{ __('Choose Specific products/Categories') }} </label>
                                        <div class="col-lg-12">
                                            <select class="form-control" name="choose_specific_product_or_category" id="coupon_for"> 
                                                <option value="" selected disabled>{{ __('Choose Specific products') }}</option>
                                                <option value="product" @if($info->coupon_for_name == 'product') selected @endif>{{ __('Specific products') }}</option>
                                                <option @if($info->coupon_for_name == 'category') selected @endif value="category">{{ __('Specific categories') }}</option>
                                            </select>
                                        </div>
                                    </div>


                                    <div class="from-group row mb-2 hide-all-coupon-value" style="display:none;">
                                        <label for="" class="col-lg-12 choose-specific">Choose Specific products</label>
                                        <div class="col-lg-12">
                                            <select class="form-control" name="coupon_id" id="coupon_id" data-selected="{{$info->coupon_for_id??0}}">
                                                <option disabled>Select</option>
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
                                <div class="col-lg-12">
                                    <input type="number" required="" value="{{$info->value}}" step="any" name="price" class="form-control" placeholder="Enter percent off or doller off">
                                </div>
                            </div>
                            

                            <div class="from-group row mb-2" id="min_amount_area">
                                <label for="" class="col-lg-12">{{ __('Minimum amount to qualify:') }} </label>
                                <div class="col-lg-12">
                                    <select class="form-control" name="min_amount_option" id="min_amount_option">
                                        <option value="1" {{ $info->min_amount_option === 1 ? 'selected' : '' }}>{{ __('Yes') }}</option>
                                        <option value="0" {{ $info->min_amount_option === 0 ? 'selected' : '' }}>{{ __('No') }}</option>
                                    </select>

                                </div>
                            </div>

                            <div class="from-group row mb-2" id="min_amount_val" style="display:none;">
                                <label for="" class="col-lg-12">{{ __('Enter minimum amount:') }} </label>
                                <div class="col-lg-12">
                                    <input type="number" required="" value="{{$info->min_amount}}" step="any" name="min_amount" class="form-control" placeholder="Enter Min Amount">
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
                                    <input type="date" value="{{$info->start_from}}" name="start_from" class="form-control">
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
                                        <input type="date" value="{{$info->will_expire}}" name="will_expire" class="form-control end_date" >
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
<script>
    $(document).ready(function() {

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
                minAmount(min_amount_check);
            });

            $("#discount_type").change(function () {
                var discount_type = $(this).val();

                userDiscount(discount_type);
            });


            function userDiscount(discountVal){
                var discount_type = discountVal;
                if(discount_type==0){
                    console.log(discount_type,'0');
                    $('#discount-amount-hide').css('display','block');
                    $('#discount-amount-hide label').text('Enter discount amount in doller($)');

                }else if(discount_type==1){
                    console.log(discount_type,'1');
                    $('#discount-amount-hide').css('display','block');
                    $('#discount-amount-hide label').text('Enter discount amount in percent(%)');
                }else{
                    console.log(discount_type,'2');
                    $('#discount-amount-hide').css('display','none');
                    $('#discount-amount-hide label').text('');
                }
            }

            function minAmount(minamount){
                var min_amount_check = minamount;
                if (min_amount_check == 0) {
                    $('#min_amount_val').css('display', 'none');
                } else if (min_amount_check == 1) {
                    $('#min_amount_val').css('display', 'block');
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


            var value = '';
            var coupon_for = $('#coupon_for').val();
            if(coupon_for == 'all'){
                $('.hide-all-coupon-value').css('display','none');
                console.log('ok');
            }

            couponCode(coupon_for);

            function couponCode(value){
               
                if(value == 'all'){
                  $('.hide-all-coupon-value').css('display','none');
                }  

                if(value == 'product' || value == 'category'){
                    var selected_id = $('#coupon_id').data('selected');

                    $.ajax({
                    type: "GET",
                    url: '{{ url("get-coupon-type") }}' + '/' + value,
                    success: function(data) {
                        $('#coupon_id').empty();
                        $('.hide-all-coupon-value').css('display','block');

                        var defaultOptionText = '';

                        // var coupon_arr = {{$info->coupon_for_name}};
                        // console.log

                       

                        $.each(data['term'], function(key, value) {

                            var couponIdentity = '{{$info->coupon_for_name}}';
                           
                            if (data['type'] == 'product') {
                                defaultOptionText = 'Select product';
                                lableText = 'Choose Specific Products';
                                $('.choose-specific').text(lableText);
 
                                var selected = (value.id == selected_id);
                                $('#coupon_id').append('<option value="' + value.id + '" ' + (selected ? 'selected' : '') + '>' + value.title + '</option>');

                               // $('#coupon_id').append('<option value="' + value.id + '">' + value.title + '</option>');
                            } else if (data['type'] == 'category') {
                                defaultOptionText = 'Select category';
                                lableText = 'Choose Specific Categories';
                                $('.choose-specific').text(lableText);

                                var selected = (value.id == selected_id);

                                console.log(value,'oo');

                                console.log(value.name,'ok');

                                $('#coupon_id').append('<option value="' + value.id + '" ' + (selected ? 'selected' : '') + '>' + value.name + '</option>');

                                //  $('#coupon_id').append('<option value="' + value.id + '">' + value.name + '</option>');
                            }

                        });

                        $('#coupon_id').prepend('<option disabled>' + defaultOptionText + '</option>');
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

