@extends('layouts.backend.app')

@push('css')
<!-- CSS Libraries -->
<link rel="stylesheet" href="{{ asset('admin/plugins/dropzone/dropzone.css') }}">
<link rel="stylesheet" href="{{ asset('admin/css/select2.min.css') }}">
@endpush

@section('title','Dashboard')

@section('head')
@include('layouts.backend.partials.headersection',['title'=>'Create Product','prev'=> url('seller/product')])
@endsection

@section('content')
<x-storenotification></x-storenotification>

<section class="section">
   <div class="row">
      <div class="col-lg-12">
         <form class="ajaxform_with_reset" method="post" action="{{ route('seller.product.store') }}">
            @csrf
            {{-- Featured Image --}}
            <div class="row">
               {{-- left side --}}
            <div class="col-lg-4">
                  <strong>{{ __('Featured Image') }}</strong>
                  <p>{{ __('Upload your product image here') }}</p>
               </div>
               {{-- /left side --}}
               {{-- right side --}}
               <div class="col-lg-8">
                  <div class="card card-primary">
                     <div class="card-body">
                        {{mediasection()}}
                     </div>
                  </div>
               </div>
               {{-- /right side --}}
            </div>


            <div class="row">
               {{-- left side --}}
               <div class="col-lg-4">
                  <strong>{{ __('Price type & Product type') }}</strong>
                  <p>{{ __('Select price type and product type here') }}</p>
               </div>
               {{-- /left side --}}
               {{-- right side --}}
               <div class="col-lg-8">
                  <div class="card card-primary">
                     <div class="card-body">
                        <div class="from-group row mb-2">
                           <label for="" class="col-lg-12">{{ __('Select Product Type') }} : </label>
                           <div class="col-lg-12">
                               <select name="categories[]" class="selectric form-control drop_product_type">
                                  <option disabled="" value="" selected="">{{ __('Select Type') }}</option>
                                   @foreach($product_type as $row)
                                   <option value="{{ $row->id }}">{{ $row->name }}</option>
                                   @endforeach
                               </select>
                           </div>
                        </div>

                        <div class="from-group row mb-2">
                           <label for="" class="col-lg-12">{{ __('Price Type') }} : </label>
                           <div class="col-lg-12">
                              <select name="product_type"  class="form-control product_type ">
                                 <option value="0">{{ __('Simple Product') }}</option>
                                 <option value="1">{{ __('Variation Product') }}</option>
                              </select>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>



            <div class="row">
               {{-- left side --}}
               <div class="col-lg-4">
                  <strong>{{ __('Information') }}</strong>
                  <p>{{ __('Add your product details and necessary information from here') }}</p>
               </div>
               {{-- /left side --}}
               {{-- right side --}}
               <div class="col-lg-8">
                  <div class="card card-primary">
                     <div class="card-body">
                        <div class="from-group row mb-2">
                           <label for="" class="col-lg-12">{{ __('Name :') }} </label>
                           <div class="col-lg-12">
                              <input  type="text" name="name" class="form-control" placeholder="Enter Product Name">
                           </div>
                        </div>
                     <div class="from-group row mb-2">
                           <label for="" class="col-lg-12">{{ __('Short Description :') }} </label>
                           <div class="col-lg-12">
                              <textarea  name="short_description" maxlength="500" class="form-control h-150"></textarea>
                           </div>
                        </div>
                        <div class="from-group row mb-2">
                           <label for="" class="col-lg-12">{{ __('Select Featured Type') }} : </label>
                           <div class="col-lg-12">
                              <select name="categories[]"   class="form-control selectric">
                              @foreach($features as $row)
                              <option value="{{ $row->id }}">{{ $row->name }}</option>
                              @endforeach
                              </select>
                           </div>
                        </div>
                        <div class="from-group row mb-2">
                           <label for="" class="col-lg-12">{{ __('List On') }} : </label>
                           <div class="col-lg-4">
                              <input type="radio" name="list_type" value="0" checked/> All
                           </div>
                           <div class="col-lg-4">
                              <input type="radio" name="list_type" value="1"/> Web Only
                           </div>
                           <div class="col-lg-4">
                              <input type="radio" name="list_type" value="2"/> POS Only
                           </div>
                        </div>
                        <div class="from-group row mb-2">
                           <label for="" class="col-lg-12">{{ __('Status') }} : </label>
                           <div class="col-lg-12">
                              <select name="status"  class="form-control selectric">
                                 <option value="1">{{ __('Publish') }}</option>
                                 <option value="0">{{ __('Draft') }}</option>
                              </select>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
            {{-- Gallery --}}
            <div class="row">
               {{-- left side --}}
            <div class="col-lg-4">
                  <strong>{{ __('Categories & Brands') }}</strong>
                  <p>{{ __('Select product brand and categories from here') }}</p>
               </div>
               {{-- /left side --}}
               {{-- right side --}}
               <div class="col-lg-8">
                  <div class="card card-primary">
                     <div class="card-body">
                        <div class="from-group row mb-2">
                           <label for="" class="col-lg-12">{{ __('Select Product Category') }} : </label>
                           <div class="col-lg-12">
                              <select name="categories[]" multiple="" class="select2 form-control">

                              {{NastedCategoryList('category')}}
                              </select>
                           </div>
                        </div>
                        
                        {{-- <div class="from-group row mb-2">
                           <label for="" class="col-lg-12">{{ __('Select Product Brand') }} : </label>
                           <div class="col-lg-12">
                              <select name="categories[]"  multiple="" class="selectric form-control select3" id="mySelect3">
                              <option disabled="" value="" selected="">{{ __('Select Brand') }}</option>
                              {{NastedCategoryList('brand')}}
                              </select>
                           </div>
                        </div> --}}
                        {{-- <div class="from-group row mb-2">
                           <label for="" class="col-lg-12">{{ __('Select Product Tags') }} : </label>
                           <div class="col-lg-12">
                              <select name="categories[]" multiple=""  class="form-control select2">
                              {{NastedCategoryList('tag')}}
                              </select>
                           </div>
                        </div> --}}

                        <div class="from-group row mb-2">
                           <label for="" class="col-lg-12">{{ __('Select Product Brand') }} : </label>
                           <div class="col-lg-12">
                              <select name="categories[]" multiple="" class="form-control select2" id="mySelect3">
                                 {{NastedCategoryList('brand')}}
                              </select>
                           </div>
                        </div>

                        <div class="from-group row mb-2">
                           <label for="" class="col-lg-12">{{ __('Select Product Tags') }} : </label>
                           <div class="col-lg-12">
                              <select name="categories[]" multiple=""  class="form-control select2" id="mySelect2">
                              {{NastedCategoryList('tag')}}
                              </select>
                           </div>
                        </div>



                     </div>
                  </div>
               </div>
               {{-- /right side --}}
            </div>
            {{-- <input type="hidden" value="0" name="product_type" /> --}}
             <div class="row">
               {{-- left side --}}
               <div class="col-lg-4">
                  <strong>{{ __('Tax') }}</strong>
                  <p>{{ __('Select tax form here') }}</p>
               </div>
               {{-- /left side --}}
               {{-- right side --}}
               <div class="col-lg-8">
                  <div class="card card-primary">
                     <div class="card-body">
                        
                        <div class="from-group row mb-2">
                           <label for="" class="col-lg-12">{{ __('Tax') }} : </label>
                           <div class="col-lg-12">
                              <select name="tax" class="form-control selectric">
                                 <option value="1" >{{ __('Enable') }}</option>
                                 <option value="0" >{{ __('Disable') }}</option>
                              </select>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
            <div class="row single_product_price_area">
               {{-- left side --}}
               <div class="col-lg-4">
                  <strong>{{ __('Simple Product Information') }}</strong>
                  <p>{{ __('Add your simple product description and necessary information from here') }}</p>
               </div>
               {{-- /left side --}}
               {{-- right side --}}
               <div class="col-lg-8">
                  <div class="card card-primary">
                     <div class="accordion-body card-body">
                        <div class="from-group row mb-2">
                           <label for="" class="col-lg-12">{{ __('Product Price') }} : </label>
                           <div class="col-lg-12">
                              <input type="number" step="any" class="form-control" name="price" placeholder="0.00">
                           </div>
                        </div>
                        <div class="from-group row mb-2">
                           <label for="" class="col-lg-12">{{ __('Quantity') }} : </label>
                           <div class="col-lg-12">
                              <input type="number" class="form-control stock-qty" name="qty" placeholder="0">
                           </div>
                        </div>
                        <div class="from-group row mb-2">
                           <label for="" class="col-lg-12">{{ __('SKU') }} : </label>
                           <div class="col-lg-12">
                              <input type="text" class="form-control" name="sku">
                           </div>
                        </div>
                        <div class="from-group row mb-2 product_weight_sec">
                           <label for="" class="col-lg-12">{{ __('Weight') }} : </label>
                           <div class="col-lg-12">
                              <input type="number" step="any" class="form-control" name="weight" placeholder="0.00">
                           </div>
                        </div>
                        <div class="from-group row mb-2">
                           <label for="" class="col-lg-12">{{ __('Manage Stock') }} : </label>
                           <div class="col-lg-12">
                              <select name="stock_manage" class="form-control selectric manage_stock">
                                 <option value="1">{{ __('Yes') }}</option>
                                 <option value="0">{{ __('No') }}</option>
                              </select>
                           </div>
                        </div>
                        <div class="from-group row mb-2">
                           <label for="" class="col-lg-12">{{ __('Stock Status') }} : </label>
                           <div class="col-lg-12">
                              <select name="stock_status" class="form-control selectric">
                                 <option value="1" >{{ __('In Stock') }}</option>
                                 <option value="0" >{{ __('Out Of Stock') }}</option>
                              </select>
                           </div>
                        </div>
                        
                     </div>
                     <div class="card-footer">
                           <button class="btn btn-primary mt-2 basicbtn" type="submit">{{ __('Create Product') }}</button>
                        </div>
                  </div>
               </div>
            </div>
            <div class="row variation_select_area " style="display:none">
               {{-- left side --}}
               <div class="col-lg-4">
                  <strong>{{ __('Product Variation Information') }}</strong>
                  <p>{{ __('Add your product variation and necessary information from here') }}</p>
               </div>
               {{-- /left side --}}
               {{-- right side --}}
               <div class="col-lg-8">
                  <div class="card card-primary">
                     <div class="attribute_render_area"></div>
                     <div id="children_attribute_render_area"></div>
                     
                     <button class="btn btn-primary col-sm-12 add_more_attribute" type="button"><i class="fa fa-plus"></i> {{ __('Add More Attribute') }}</button>
                   
                     <button class="btn btn-primary mt-2 col-sm-12 create_variation_product" style="display:none" type="button"><i class="fas fa-plus"></i> {{ __('Create variation products') }}</button>

                     <button class="btn btn-primary mt-2 basicbtn" type="submit">{{ __('Create Product') }}</button>
               </div>
            </div>
         </form>
      </div>
   </div>
</section>
{{ mediasingle() }}
<input type="hidden" id="parentattributes" value="{{ $attributes }}" />
@endsection

@push('script')
 <!-- JS Libraies -->
<script src="{{ asset('admin/plugins/dropzone/dropzone.min.js') }}"></script>
<!-- Page Specific JS File -->
<script src="{{ asset('admin/plugins/dropzone/components-multiple-upload.js') }}"></script>
<script src="{{ asset('admin/js/media.js') }}"></script>
<script src="{{ asset('admin/js/select2.min.js') }}"></script>
<script src="{{ asset('admin/js/product-create.js') }}"></script>
<script>

$(document).on('change', '.manage_stock', function() {
    if ($(this).val() == 0) {
        $(this).closest('.accordion-body').find('.stock-qty').prop('disabled', true);
    } else {
        $(this).closest('.accordion-body').find('.stock-qty').prop('disabled', false);
    }
  });
  
$(".drop_product_type").change(function() {
    // This function will be executed when the input value changes.
    var inputValue = $('.drop_product_type option:selected').text();
    if(inputValue === 'Digital Product'){
        $('.product_weight_sec').hide();
        return;
    }
    $('.product_weight_sec').show();
});


$(document).ready(function() {
  $('#mySelect2').select2({
    tags: true,
    createTag: function(params) {
      return {
        id: params.term,
        text: params.term,
        newOption: true // Indicates it's a new option
      };
    }
  });

  $('#mySelect3').select2({
    tags: true,
    createTag: function(params) {
      return {
        id: params.term,
        text: params.term,
        newOption: true // Indicates it's a new option
      };
    }
  });


  // get newly added option
  $('#mySelect2').on('select2:select', function(e) {
    var selectedOption = e.params.data;
    if (selectedOption.newOption) {
      // This is a new option, save it to your system
      var newOptionText = selectedOption.text;
      var type = "create_dynamic_option";
      // ajax request for updating the new option
      $.ajax({
        url: "/seller/add-jquery-tag", // Updated route definition
        type: "POST", // HTTP request method (GET, POST, PUT, DELETE, etc.)
        data: {
          'tag_name': newOptionText,
          'type': type,
          '_token': "{{csrf_token()}}"
        },
        dataType: "json", // Expected data type of the response
        success: function(data) {
         //  console.log(data);
           if (data) {
               // Update the value of the new option with the received ID
               var newOptionId = data;
               $('#mySelect2').find('option[value="' + newOptionText + '"]').remove();
             //  $('#mySelect2').find('option[value="' + newOptionId + '"]').attr('selected');
               var newOption = new Option(newOptionText, newOptionId, true, true);
               $('#mySelect2').append(newOption);
           }
        },
        error: function(xhr, status, error) {
          $('#mySelect2').find('option[value="' + newOptionText + '"]').remove();
        }
      });
    }
  });


// get newly added option
$('#mySelect3').on('select2:select', function(e) {
    var selectedOption = e.params.data;
    if (selectedOption.newOption) {
      // This is a new option, save it to your system
      var newOptionText = selectedOption.text;
      var type = "create_dynamic_option";
      // ajax request for updating the new option
      $.ajax({
        url: "/seller/add-jquery-brand", // Updated route definition
        type: "POST", // HTTP request method (GET, POST, PUT, DELETE, etc.)
        data: {
          'brand_name': newOptionText,
          'type': type,
          '_token': "{{csrf_token()}}"
        },
        dataType: "json", // Expected data type of the response
        success: function(data) {
         //  console.log(data);
           if (data) {
            console.log(data);
               // Update the value of the new option with the received ID
                var newOptionId = data;
                $('#mySelect3').find('option[value="' + newOptionText + '"]').remove();
                //$('#mySelect3').find('option[value="' + newOptionId + '"]').attr('selected');
               var newOption = new Option(newOptionText, newOptionId, true, true);
               $('#mySelect3').append(newOption);

           }
        },
        error: function(xhr, status, error) {
          $('#mySelect3').find('option[value="' + newOptionText + '"]').remove();
        }
      });
    }
  });


});

</script>
@endpush

