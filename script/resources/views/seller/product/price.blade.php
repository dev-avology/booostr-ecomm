@push('css')
<link rel="stylesheet" href="{{ asset('admin/css/select2.min.css') }}">
@endpush

@section('head')
@include('layouts.backend.partials.headersection',['title'=>'Edit Product Price','prev'=> url('seller/product')])
@endsection

@extends('seller.product.productmain',['product_id'=>$id])

@section('product_content')
<div class="tab-pane fade show active" id="general_info" role="tabpanel" aria-labelledby="home-tab4">
   <form class="ajaxform_with_reload" action="{{ route('seller.product.update',$info->id) }}" method="post">
      @csrf
      @method("PUT")
      <div class="from-group row mb-2">
        <label for="" class="col-lg-12">{{ __('Name :') }} </label>
        <div class="col-lg-12">
            <input type="text" readonly name="name" required="" class="form-control" placeholder="Enter Product Name" value="{{ $info->title }}">
        </div>
      </div>
      <div class="from-group row mb-2">
         <label for="" class="col-lg-12">{{ __('Price Type') }} : </label>
         <div class="col-lg-12">
            <select name="product_type"  class="form-control product_type">
            <option value="0" @if($info->is_variation == 0) selected @endif>{{ __('Simple Product') }}</option>
            <option value="1" @if($info->is_variation == 1) selected @endif>{{ __('Variation Product') }}</option>
            </select>
         </div>
      </div>
      <input type="hidden" name="type" value="price">
      <div class="{{ $info->is_variation == 1 ? 'none' : '' }} single_product_area accordion-body">
         <div class="from-group row mb-2">
            <label for="" class="col-lg-12">{{ __('Product Price') }} : </label>
            <div class="col-lg-12">
               <input type="text" step="any" class="form-control product_price_input" name="price" value="@if($info->price){{ $info?->price->price ? '$'.number_format($info?->price->price, 2) : '' }}@endif" placeholder="$0.00">
            </div>
         </div>
         <div class="from-group row mb-2">
            <label for="" class="col-lg-12">{{ __('Quantity') }} : </label>
            <div class="col-lg-12">
               <input type="number" class="form-control stock-qty" name="qty" value="{{ $info->price->qty ?? '' }}" placeholder="0">
            </div>
         </div>
         <div class="from-group row mb-2">
            <label for="" class="col-lg-12">{{ __('SKU') }} : </label>
            <div class="col-lg-12">
               <input type="text" class="form-control" name="sku" value="{{ $info->price->sku ?? '' }}">
            </div>
         </div>
         @if($selected_product_type == "Physical Product")
         <div class="from-group row mb-2">
            <label for="" class="col-lg-12">{{ __('Weight') }} : </label>
            <div class="col-lg-12">
               <input type="number" step="any" class="form-control" name="weight" value="{{ $info->price->weight ?? '' }}" placeholder="0.00">
            </div>
         </div>
         @endif

         @php
         $stock_manage=$info->price->stock_manage ?? '';
         $stock_status=$info->price->stock_status ?? '';
         @endphp
         <div class="from-group row mb-2">
            <label for="" class="col-lg-12">{{ __('Manage Stock') }} : </label>
            <div class="col-lg-12">
               <select name="stock_manage" class="manage_stock form-control selectric">
               <option value="1" @if($stock_manage == 1) selected="" @endif>{{ __('Yes') }}</option>
               <option value="0" @if($stock_manage == 0) selected="" @endif>{{ __('No') }}</option>
               </select>
            </div>
         </div>
         <div class="from-group row mb-2">
            <label for="" class="col-lg-12">{{ __('Stock Status') }} : </label>
            <div class="col-lg-12">
               <select name="stock_status" class="form-control selectric">
               <option value="1" @if($stock_status == 1) selected="" @endif>{{ __('In Stock') }}</option>
               <option value="0" @if($stock_status == 0) selected="" @endif>{{ __('Out Of Stock') }}</option>
               </select>
            </div>
         </div>
      </div>

      <div class="from-group row mb-2">
         <label for="" class="col-lg-12">{{ __('Tax') }} : </label>
         @php 
           $tax=$info->price->tax ?? 1;
         @endphp
         <div class="col-lg-12">
            <select name="tax" class="form-control selectric">
               <option value="1" @if($tax == 1) selected="" @endif>{{ __('Enable') }}</option>
               <option value="0" @if($tax == 0) selected="" @endif>{{ __('Disable') }}</option>
            </select>
         </div>
      </div>
  

      <div class="variation_product_area {{ $info->is_variation == 0 ? 'none' : '' }}">
         <div id="accordion">

            @php 
            $usedarrtibuteOption = [];
            @endphp

            @foreach($info->productoptionwithcategories ?? [] as $key => $row)
            @php
            $selected_childs=[];
            $usedarrtibuteOption[$row->id] = $row->categorywithchild->name;

            foreach($row->priceswithvaritions->unique() ?? [] as $price_category){
                array_push($selected_childs, $price_category->id);
            }
            @endphp
             <div class="accordion renderchild{{ $key }}">
               <div class="accordion-header h-50" role="button" data-toggle="collapse" data-target="#panel-body-{{ $key }}">
                  <div class="float-left">
                     <h6>
                        <span id="option_name4">{{ $row->categorywithchild->name ?? '' }}</span>
                        @if($row->is_required == 1)<span class="text-danger">*</span> @endif
                    </h6>
                  </div>
                  <div class="float-right">
                     <a class="btn btn-danger btn-sm text-white option_delete" data-id="{{ $key }}" data-old_attr_id="{{ $row->id }}"><i class="fa fa-trash"></i></a>
                  </div>
               </div>
               <div class="accordion-body collapse show" id="panel-body-{{ $key }}" data-parent="#accordion">
                  <div class="row mb-2 " >
                     <div class="col-lg-6 from-group">
                        <label for="" >{{ __('Select Attribute :') }} </label>
                        <select required name="parentattribute[]"  class="form-control parentattribute selectric parentattribute{{ $key }}">
                           <option value="{{ $row->category_id }}"  class="parentAttr{{ $row->id }}" data-parentname="{{ $row->name }}" data-short="{{ $key }}" data-childattributes="{{ $row->categorywithchild->categories }}">{{ $row->categorywithchild->name ?? '' }}</option>
                        </select>
                     </div>
                     <div class="col-lg-6 from-group">
                        <label for="" >{{ __('Select Attribute Values :') }} </label>
                        <select required  class="form-control select2 childattribute childattribute{{$key}} multi-select" multiple="">
                            @foreach($row->categorywithchild->categories as $category)
                            <option
                            @if(in_array($category->id, $selected_childs))
                            selected
                            @endif
                            value="{{ $category->id }}"
                            data-parentid="{{ $row->id }}"
                            data-parent="{{ $row->categorywithchild->name ?? '' }}"
                            data-short="{{ $key }}"
                            data-attrname="{{ $category->name }}"
                            class='child_attr{{ $category->id }}
                            childattr{{ $key }}'
                            >
                            {{ $category->name }}
                           </option>
                            @endforeach
                        </select>
                     </div>
                     {{-- <div class="from-group col-lg-6  mb-2">
                        <label for="" >{{ __('Select Type :') }} </label>
                        <div >
                           <select name="optionattribute[{{$row->category_id}}][select_type]" class="form-control selectric    selecttype{{ $key }}">
                              <option value="1" @if($row->select_type == 1) selected @endif>{{ __('Multiple Select') }}</option>
                              <option value="0" @if($row->select_type == 0) selected @endif>{{ __('Single Select') }}</option>
                           </select>
                        </div>
                     </div>
                     <div class="from-group col-lg-6  mb-2">
                        <label for="" >{{ __('Is Required ? :') }} </label>
                        <div >
                           <select name="optionattribute[{{$row->category_id}}][is_required]" class="form-control selectric    is_required{{ $key }}">
                              <option value="1" @if($row->is_required == 1) selected @endif>{{ __('Yes') }}</option>
                              <option value="0" @if($row->is_required == 0) selected @endif>{{ __('No') }}</option>
                           </select>
                        </div>
                     </div> --}}
                  </div>
                  <hr>
                 
               </div>
            </div>
            @endforeach
            
            <div class="accordion renderchildVaritions">
               <div class="accordion-header h-50" >
                  <div class="float-left">
                     <h6>
                      Variation Products
                    </h6>
                  </div>
                   <div class="float-right">
                     <button class="btn btn-secondary float-right add_more_attribute" type="button"><i class="fas fa-plus"></i> {{ __('Add More variation') }}</button>
                  </div>
               </div>
               <div class="accordion-body collapse show" id="panel-body-Varitions" data-parent="#accordion">
                  <p id = "variation-delete-msg" style = "color:red;"></p>

                  <div id="children_attribute_render_area">
                     @php 
                     $used_combination = [];
                     $allVariationNames = []; 
                     @endphp
                     @foreach($info->prices ?? [] as $priceswithcategory)
                        @php
                           $class = '';
                            foreach($priceswithcategory->varitions as $varition){
                               $class .= ' attr-'.$varition->name; 
                            }
    
                         @endphp 

                      <div class="accordion{{$class}}" id="childcard{{$priceswithcategory->id}}">
                        <div class="accordion-header h-50" role="button" data-toggle="collapse" data-target="#panel-body-{{$priceswithcategory->id}}">
                           <div class="float-left">   
                                 <h4> 
                                       @php 
                                       $usku = ''; 
                                       $variationNames = []; 
                                       @endphp
                                       @foreach($priceswithcategory->varitions as $varition)
                                       
                                       {{ $usedarrtibuteOption[$varition->pivot->productoption_id] ?? '' }} / <span class="text-danger">  {{ $varition->name ?? '' }}</span>
                                       @php 
                                       $usku .= $varition->id; 
                                       $variationNames[] = $varition->name;
                                       $allVariationNames[] = $varition->name;
                                       @endphp
                                       <input type="hidden" name="childattribute[priceoption][{{$priceswithcategory->id}}][varition][{{$varition->pivot->productoption_id}}]" value="{{$varition->id}}">
                                       @endforeach
                                       @php $used_combination[] = trim($usku); @endphp
      
                                 </h4>
                           </div>
                           <div class="float-right">
                              <a class="btn btn-danger btn-sm text-white varition_option_delete" data-id="{{$priceswithcategory->id}}" data-old_id="{{$priceswithcategory->id}}"><i class="fa fa-trash"></i></a>
                           </div>      
                        </div>

      
                        <div class="accordion-body collapse show" id="panel-body-{{$priceswithcategory->id}}" data-parent="#children_attribute_render_area">
                           <div class=" row">
                              <div class="from-group col-lg-6">
                                 <label for="" >{{ __('Price :') }} </label>
                                 <div >
                                    <input type="number" step="any" class="form-control" name="childattribute[priceoption][{{$priceswithcategory->id}}][price]" value="{{ $priceswithcategory->price }}" />
                                 </div>
                              </div>
                              <div class="from-group col-lg-6  mb-2 ">
                                 <label for="">{{ __('Stock Quantity :') }} </label>
                                 <div >
                                    <input type="number" class="stock-qty form-control" @if($priceswithcategory->stock_manage == 0) disabled @endif  name="childattribute[priceoption][{{$priceswithcategory->id}}][qty]" value="{{ $priceswithcategory->qty }}"/>
                                 </div>
                              </div>
                              <div class="from-group col-lg-6 mb-2">
                                 <label for="" >{{ __('SKU :') }} </label>
                                 <div >
                                    <input type="text" class="form-control" name="childattribute[priceoption][{{$priceswithcategory->id}}][sku]" value="{{ $priceswithcategory->sku }}"/>
                                 </div>
                              </div>
                              <div class="from-group col-lg-6  mb-2">
                                 <label for="" >{{ __('Weight :') }} </label>
                                 <div >
                                    <input type="number" step="any" class="form-control" name="childattribute[priceoption][{{$priceswithcategory->id}}][weight]" value="{{ $priceswithcategory->weight }}"/>
                                 </div>
                              </div>
                              <div class="from-group col-lg-6  mb-2">
                                 <label for="" >{{ __('Manage Stock ?') }} </label>
                                 <div >
                                    <select class="form-control selectric manage_stock" name="childattribute[priceoption][{{$priceswithcategory->id}}][stock_manage]">
                                       <option value="1" @if($priceswithcategory->stock_manage == 1) selected @endif>{{ __('Yes') }}</option>
                                       <option value="0" @if($priceswithcategory->stock_manage == 0) selected @endif>{{ __('No') }}</option>
                                    </select>
                                 </div>
                              </div>
                              <div class="from-group col-lg-6  mb-2">
                                 <label for="" >{{ __('Stock Status:') }} </label>
                                 <div >
                                    <select class="form-control selectric" name="childattribute[priceoption][{{$priceswithcategory->id}}][stock_status]">
                                       <option value="1" @if($priceswithcategory->stock_status == 1) selected @endif>{{ __('In Stock') }}</option>
                                       <option value="0" @if($priceswithcategory->stock_status == 0) selected @endif>{{ __('Out Of Stock') }}</option>
                                    </select>
                                 </div>
                              </div>
      
                              <!--div class="from-group col-lg-6  mb-2">
                                 <label for="" >{{ __('Tax:') }} </label>
                                 <div >
                                    <select class="form-control selectric" name="childattribute[priceoption][{{$priceswithcategory->id}}][tax]">
                                       <option value="1" @if($priceswithcategory->tax == 1) selected @endif>{{ __('Enable') }}</option>
                                       <option value="0" @if($priceswithcategory->tax == 0) selected @endif>{{ __('Disable') }}</option>
                                    </select>
                                 </div>
                              </div-->
                           </div>
                        </div> 
                      </div>
                      @endforeach
      
                  </div>   


               </div>   

         </div>
         
      </div>    
      </div>

      <div class="from-group  mb-2">
         <button class="btn btn-primary basicbtn col-lg-2 float-left" type="submit"><i class="far fa-save"></i> {{ __('Update') }}</button>
    
         <button class="btn btn-primary col-lg-3 ml-5 create_variation_product" style="display:none" type="button"><i class="fas fa-plus"></i> {{ __('Create variation products') }}</button>

      </div>
   </form>
</div>
<input type="hidden" id="max_short" value="{{ count($info->productoptionwithcategories) }}">
<input type="hidden" id="parentattributes" value="{{ $attributes }}" />
<input type="hidden" id="used_combination" value='{!! json_encode($used_combination,true) !!}' />
@endsection

@push('script')
<script src="{{ asset('admin/js/select2.min.js') }}"></script>
<script src="{{ asset('admin/js/product-price.js?v=1') }}"></script>

<script>
  $(document).on('change', '.manage_stock', function() {
    if ($(this).val() == 0) {
        $(this).closest('.accordion-body').find('.stock-qty').prop('disabled', true);
    } else {
        $(this).closest('.accordion-body').find('.stock-qty').prop('disabled', false);
    }
  });

    $(".product_price_input").change(function() {
       // This function will be executed when the input value changes.
       var inputValue = $(this).val();
       inputValue = inputValue.match(/[0-9.]+/g);
       if(inputValue === null || inputValue === ''){
          return;
       }
       $(this).val("$" + parseFloat(inputValue).toFixed(2));
    });

    $('.varition_option_delete').click(function() {
      var id = $(this).data('id');
      var oldId = $(this).data('old_id');

      $.ajax({
        url: '/seller/product/remove-price/'+oldId, 
        method: 'GET',
        success: function(response) {
          console.log('GET request successful:', response);
          $('#variation-delete-msg').text('Variation product deleted successfully');
          removeAttrValue();
        },
        error: function(jqXHR, textStatus, errorThrown) {
          console.error('GET request failed:', textStatus, errorThrown);
        }
      });
   });

   $('.option_delete').click(function() {
      var id = $(this).data('old_attr_id');

      $.ajax({
        url: '/seller/product/remove-variation-attribute/'+id, 
        method: 'GET',
        success: function(response) {
          console.log('GET request successful:', response);
          $('#variation-delete-msg').text('Variation attribute deleted successfully');
        },
        error: function(jqXHR, textStatus, errorThrown) {
          console.error('GET request failed:', textStatus, errorThrown);
        }
      });
   });

   function removeAttrValue() {
    $(".parentattribute option:selected").each(function(index, row) {
      $(".childattribute" + $(row).data('short') + " option:selected").each(function(index, row1) {
         var attrVal = $(row1).data('attrname'); 
         var new_attr = '#children_attribute_render_area .attr-'+attrVal;
          if($(new_attr).length == 0 ){
             $(this).prop("selected", false)
             $(".childattribute" + $(row).data('short')).trigger('change.select2');
         }

      });
    });
   }


</script>

@endpush
