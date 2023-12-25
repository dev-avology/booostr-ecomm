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

                                    <div class="from-group row mb-2" style="display:none;" id="specific-cat-pro">
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
                                        <div class="col-lg-12">
                                            <input type="number" required="" value="0" step="any" name="price" class="form-control" placeholder="Enter percent off or doller off">
                                        </div>
                                    </div>
                                    
                                    {{-- <div class="from-group row mb-2">
                                        <label for="" class="col-lg-12">{{ __('Is Conditional ?') }} </label>
                                        <div class="col-lg-12">
                                            <select class="form-control" name="is_conditional" id="is_conditional">
                                                <option value="1">{{ __('Yes') }}</option>
                                                <option value="0" selected="">{{ __('No') }}</option>
                                            </select>
                                        </div>
                                    </div> --}}

                                    <div class="from-group row mb-2" id="min_amount_area">
                                        <label for="" class="col-lg-12">{{ __('Minimum amount to qualify:') }} </label>
                                        <div class="col-lg-12">
                                            <select class="form-control" name="min_amount_option" id="min_amount_option">
                                                <option value="1">{{ __('Yes') }}</option>
                                                <option value="0" selected="">{{ __('No') }}</option>
                                            </select>

                                        </div>
                                    </div>

                                    <div class="from-group row mb-2" id="min_amount_val" style="display:none;">
                                        <label for="" class="col-lg-12">{{ __('Enter minimum amount:') }} </label>
                                        <div class="col-lg-12">
                                            <input type="number" required="" value="0" step="any" name="min_amount" class="form-control" placeholder="Enter Min Amount">
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
                                        <input type="date" name="start_from" class="form-control" >
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
                                            <input type="date" name="will_expire" class="form-control" >
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

<script>
      $(document).ready(function() {

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
                    // $(".coupon-hidden-date input[type='date']").prop('required', isChecked);
                    $('#date_checkbox').val(1);
                }
            });

            $("#min_amount_option").change(function () {
                var min_amount_check = $(this).val();
                if (min_amount_check == 0) {
                    $('#min_amount_val').css('display', 'none');
                } else if (min_amount_check == 1) {
                    $('#min_amount_val').css('display', 'block');
                }
            });

            $("#discount_type").change(function () {
                var discount_type = $(this).val();
                if(discount_type==0){
                    console.log(discount_type,'0');
                    $('#discount-amount-hide').css('display','block');
                    $('#discount-amount-hide label').text('Enter discount amount in doller($)');

                }else if(discount_type==1){
                    console.log(discount_type,'1');
                    $('#discount-amount-hide').css('display','block');
                    $('#discount-amount-hide label').text('Enter discount amount in percent(%)');
                }else{
                    $('#discount-amount-hide').css('display','none');
                    $('#discount-amount-hide label').text('');
                }
            });

            // var coupon_for = $('#coupon_for').val();
            // if(coupon_for == 'all'){
            //   $('.hide-all-coupon-value').css('display','none');
            // }

            $('#coupon_for').change(function() {
                var selectedValue = $(this).val();

                if(selectedValue == 'all'){
                  $('.hide-all-coupon-value').css('display','none');
                }  

                if(selectedValue == 'product' || selectedValue == 'category'){

                    $.ajax({
                    type: "GET",
                    url: '{{ url("get-coupon-type") }}' + '/' + selectedValue,
                    success: function(data) {
                        $('#coupon_id').empty();
                        $('.hide-all-coupon-value').css('display','block');

                        var defaultOptionText = '';

                        $.each(data['term'], function(key, value) {
                            if (data['type'] == 'product') {
                                defaultOptionText = 'Select product';
                                $('#coupon_id').append('<option value="' + value.id + '">' + value.title + '</option>');
                            } else if (data['type'] == 'category') {
                                defaultOptionText = 'Select category';
                                $('#coupon_id').append('<option value="' + value.id + '">' + value.name + '</option>');
                            }
                        });

                        $('#coupon_id').prepend('<option selected disabled>' + defaultOptionText + '</option>');
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

