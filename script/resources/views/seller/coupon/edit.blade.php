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
                  <strong>{{ __('Image') }}</strong>
                  <p>{{ __('Upload your category image here') }}</p>
               </div>
               {{-- /left side --}}
               {{-- right side --}}
               <div class="col-lg-7">
                  <div class="card">
                     <div class="card-body">
                        {{mediasection(['value'=>$info->avatar ?? '','preview'=> $info->avatar ?? 'admin/img/img/placeholder.png'])}}
                     </div>
                  </div>
               </div>
               {{-- /right side --}}
            </div>
            <div class="row">
               {{-- left side --}}
               <div class="col-lg-5">
                   <strong>{{ __('Description') }}</strong>
                    <p>{{ __('Edit your coupon details and necessary information from here') }}</p>
               </div>
               {{-- /left side --}}
               {{-- right side --}}
               <div class="col-lg-7">
                  <div class="card">
                     <div class="card-body">
                       <div class="from-group row mb-2">
                                <label for="" class="col-lg-12">{{ __('Coupon Code:') }} </label>
                                <div class="col-lg-12">
                                    <input type="text" required name="coupon_code" class="form-control" placeholder="{{ __('Enter Coupon Code') }}" value="{{ $info->code }}">
                                </div>
                            </div>

                            <div class="from-group row mb-2">
                                <label for="" class="col-lg-12">{{ __('Price:') }} </label>
                                <div class="col-lg-12">
                                    <input type="number" required=""  step="any" name="price" class="form-control" placeholder="{{ __('Enter Percentage Rate Or Flat Rate') }}" value="{{ $info->value }}">
                                </div>
                            </div>
                            <div class="from-group row mb-2">
                                <label for="" class="col-lg-12">{{ __('Discount Type:') }} </label>
                                <div class="col-lg-12">
                                    <select class="form-control" name="discount_type" >
                                        <option value="1" @if($info->is_percentage == 1) selected @endif>{{ __('Percentage') }}</option>
                                        <option value="0" @if($info->is_percentage == 0) selected @endif>{{ __('Flat Rate Discount') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="from-group row mb-2">
                                <label for="" class="col-lg-12">{{ __('Is Conditional ?') }} </label>
                                <div class="col-lg-12">
                                    <select class="form-control" name="is_conditional" id="is_conditional">
                                        <option @if($info->is_conditional == 1) selected @endif value="1">{{ __('Yes') }}</option>
                                        <option @if($info->is_conditional == 0) selected @endif value="0">{{ __('No') }}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="from-group row mb-2 @if($info->is_conditional != 1) none @endif" id="min_amount_area">
                                <label for="" class="col-lg-12">{{ __('Min Amount:') }} </label>
                                <div class="col-lg-12">
                                    <input type="number" required=""  step="any" name="min_amount" class="form-control" placeholder="{{ __('Enter Min Amount') }}" value="{{ $info->min_amount }}">
                                </div>
                            </div>
                            <div class="from-group row mb-2">
                                <label for="" class="col-lg-12">{{ __('Start From:') }} </label>
                                <div class="col-lg-12">
                                    <input type="date" required="" value="{{ $info->start_from }}"  name="start_from" class="form-control" >
                                </div>
                            </div>
                            <div class="from-group row mb-2">
                                <label for="" class="col-lg-12">{{ __('Will Expire:') }} </label>
                                <div class="col-lg-12">
                                    <input type="date" required="" value="{{ $info->will_expire }}" name="will_expire" class="form-control" >
                                </div>
                            </div>
                            <div class="from-group row mb-2">
                                <label for="" class="col-lg-12">{{ __('Is Featured ?') }} </label>
                                <div class="col-lg-12">
                                     <select class="form-control" name="is_featured">
                                        <option value="1" @if($info->is_featured == 1) selected @endif>{{ __('Yes') }}</option>
                                        <option value="0" @if($info->is_featured != 1) selected @endif>{{ __('No') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="from-group row mb-2">
                                <label for="" class="col-lg-12">{{ __('Status') }} </label>
                                <div class="col-lg-12">
                                     <select class="form-control" name="status">
                                        <option value="1" @if($info->status == 1) selected @endif>{{ __('Enable') }}</option>
                                        <option value="0" @if($info->status != 1) selected @endif>{{ __('Disable') }}</option>
                                    </select>
                                </div>
                            </div>


                            <div class="from-group row mb-2">
                                <label for="" class="col-lg-12">{{ __('Coupon For') }} </label>
                                <div class="col-lg-12">
                                    <select class="form-control" name="coupon_for" id="coupon_for">
                                        <option value="all" @if($info->coupon_for_name == 'all') selected @endif>{{ __('All Products and Categories') }}</option>
                                        <option value="product" @if($info->coupon_for_name == 'product') selected @endif>{{ __('Have Product') }}</option>
                                        <option value="category" @if($info->coupon_for_name == 'category') selected @endif>{{ __('Have Category') }}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="from-group row mb-2 hide-all-coupon-value">
                                <label for="" class="col-lg-12">{{ __('Select coupon for') }} </label>
                                <div class="col-lg-12">
                                    <select class="form-control" name="coupon_id" id="coupon_id">
                                        <option selected disabled>Select</option>
                                    </select>
                                </div>
                            </div>


                            <div class="row">
                            <div class="col-lg-12">
                                <input type="hidden" name="type" value="{{ $info->type }}">
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

                    $.ajax({
                    type: "GET",
                    url: '{{ url("get-coupon-type") }}' + '/' + value,
                    success: function(data) {
                        $('#coupon_id').empty();
                        $('.hide-all-coupon-value').css('display','block');

                        var defaultOptionText = '';

                        $.each(data['term'], function(key, value) {
                            console.log(data['term']);
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
            }

            $('#coupon_for').change(function() {
                couponCode($(this).val());
            });
      });
</script>
@endpush

