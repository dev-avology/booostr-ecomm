@extends('layouts.backend.app')

@push('css')
<!-- CSS Libraries -->
  <link rel="stylesheet" href="{{ asset('admin/plugins/dropzone/dropzone.css') }}">
  <link rel="stylesheet" href="{{ asset('admin/css/select2.min.css') }}">
@endpush

@section('title','Dashboard')

@section('content')
<section class="section">
   {{-- section title --}}
   <div class="section-header">
      <a href="{{ url('seller/shipping') }}" class="btn btn-primary mr-2">
      <i class="fas fa-arrow-left"></i>
      </a>
      <h1>{{ __('Create Shipping Rate') }}</h1>
   </div>
   {{-- /section title --}}
   <div class="row">
      <div class="col-lg-12">
         <form class="ajaxform_with_reset" method="post" action="{{ route('seller.shipping.store') }}">
            @csrf
            <div class="row">
               {{-- left side --}}
               <div class="col-lg-5">
                  <h6>{{ __('Image') }}</h6>
                  <strong>{{ __('Upload your shipping image here') }}</strong>
               </div>
               {{-- /left side --}}
               {{-- right side --}}
               <div class="col-lg-7">
                  <div class="card">
                     <div class="card-body">
                        {{mediasection()}}
                     </div>
                  </div>
               </div>
               {{-- /right side --}}
            </div>
            <div class="row">
               {{-- left side --}}
               <div class="col-lg-5">
                  <strong>{{ __('Description') }}</strong>
                  <p>{{ __('Add your shipping details and necessary information from here') }}</p>
               </div>
               {{-- /left side --}}
               {{-- right side --}}
               <div class="col-lg-7">
                  <div class="card">
                     <div class="card-body">
                        <div class="from-group row mb-2">
                           <label for="" class="col-lg-12">{{ __('Name :') }} </label>
                           <div class="col-lg-12">
                              <input type="text" name="name" class="form-control" placeholder="Enter Shipping Name">
                           </div>
                        </div>
               
                        <div class="from-group row mb-2">
                           <label for="" class="col-lg-12">{{ __('Type :') }} </label>
                           <div class="col-lg-12">
                                   <select name="shipping_type" id="shipping_type" class="select2 form-control">
                                    <option value="" > Choose Shipping Type</option>
                                   <option value="weight_based">Weight</option>
                                   <option value="per_item">Per Item</option>
                                   <option value="flat_rate">Flat Rate</option>
                                   <option value="free_shipping">Free Shipping</option>
                                   </select>
                           </div>
                        </div>

                        <div class="from-group row mb-2 type_price weight_based" style="display:none;"  >
                           <label for="" class="col-lg-12">{{ __('Price per LB :') }} </label>
                           <div class="col-lg-12">
                             <input type="number" required="" value="0" step="any" name="type_price['perlb']" class="form-control" placeholder="Enter Price per lb">
                           </div>
                        </div>

                        <div class="from-group row mb-2 type_price per_item" style="display:none;">
                           <label for="" class="col-lg-12">{{ __('Price per item :') }} </label>
                           <div class="col-lg-12">
                             <input type="number" required="" value="0" step="any" name="type_price['per_item']" class="form-control" placeholder="Enter Price per Item">
                           </div>
                        </div>

                        <div class="from-group row mb-2 type_price flat_rate" style="display:none;">
                           <label for="" class="col-lg-10">{{ __('flatrate for Cart Total :') }} </label> 
                          
                           <div class="col-lg-12">

                           <div class="row">
                             <div class="col-md-2">
                                  <input type="number" required="" value="0" step="any" name="type_price['flatrate_range'][0][from]" class="form-control" placeholder="Min Cart Total">
                              </div>
                              <div class="col-md-1">
                                    <label for="">{{ __('-') }} </label>
                              </div>
                              <div class="col-md-2">
                                 <input type="number" required="" value="25" step="any" name="type_price['flatrate_range'][0][to]" class="form-control" placeholder="Min Cart Total">
                              </div>
                              <div class="col-lg-6">
                                  <input type="number" required="" value="0" step="any" name="type_price['flatrate_range'][0][price]" class="form-control" placeholder="Enter Price">
                              </div>
                              <div class="col-md-1">
                                    <a href="javascript:void(0)" class="flatraterow" ><i class="fas fa-plus"></i></a>
                              </div>
                           </div>


                           </div>
                        </div>

                        <div class="from-group row mb-2 type_price free_shipping" style="display:none;"  >
                           <label for="" class="col-lg-12">{{ __('Min Cart Total :') }} </label>
                           <div class="col-lg-12">
                             <input type="number" required="" value="0" step="any" name="type_price['free_shipping'][cart_min]" class="form-control" placeholder="Enter Min Cart total">
                           </div>
                        </div>

                        <div class="from-group row mb-2 baseprice">
                           <label for="" class="col-lg-12">{{ __('Base Price :') }} </label>
                           <div class="col-lg-12">
                              <input type="number" required="" value="0" step="any" name="price" class="form-control" placeholder="Enter Price">
                           </div>
                        </div>
                        
                        {{-- <div class="from-group row mb-2">
                           <label for="" class="col-lg-12">{{ __('Select Locations :') }} </label>
                           <div class="col-lg-12">
                              <select name="locations[]" multiple="" class="select2 form-control">
                                 @foreach($posts as $row)
                                 <option value="{{ $row->id }}">{{ $row->name }}</option>
                                 @endforeach  
                              </select>
                           </div>
                        </div> --}}
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
<script src="{{ asset('admin/js/select2.min.js') }}"></script>

   <script>
      var rowtotal = 1;
     $(document).ready(function(){
        $('#shipping_type').select2().change(function(){
         $('.type_price').hide();
         $('.'+$(this).val()).show();
        });

      $('.flatraterow').on('click',function(){
         $(this).closest('.row').after('<div class="row mt-2">' +
            '<div class="col-md-2"><input type="number" required="" value="0" step="any" name="type_price[\'flatrate_range\']['+rowtotal+'][from]" class="form-control" placeholder="Min Cart Total"></div>' +
            '<div class="col-md-1"><label for="">-</label></div>' +
            '<div class="col-md-2"><input type="number" required="" value="25" step="any" name="type_price[\'flatrate_range\']['+rowtotal+'][to]" class="form-control" placeholder="Min Cart Total"></div>' +
            '<div class="col-lg-6"><input type="number" required="" value="0" step="any" name="type_price[\'flatrate_range\']['+rowtotal+'][price]" class="form-control" placeholder="Enter Price"></div>' +
            '' +
         '</div>');
         rowtotal++;


      });

     });

  

   </script>
@endpush

