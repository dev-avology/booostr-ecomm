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
        <h1>{{ __('Edit Shipping Rate') }}</h1>
   </div>
   {{-- /section title --}}
   <div class="row">
      <div class="col-lg-12">
         <form class="ajaxform" method="post" action="{{ route('seller.shipping.update',$info->id) }}">
                @csrf
                @method('PUT')
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
                        {{mediasection(['value'=>$info->preview->content ?? '','preview'=> $info->preview->content ?? 'admin/img/img/placeholder.png'])}}
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
                                    <input type="text" value="{{ $info->name }}" name="name" class="form-control" placeholder="Enter Shipping Name">
                                </div>
                            </div>


                           <div class="from-group row mb-2">
                           <label for="" class="col-lg-12">{{ __('Type :') }} </label>
                           @php
                              $shipping_types = array('weight_based'=>'Weight','per_item'=>'Per Item','flat_rate'=>'Flat Rate','free_shipping'=>'Free Shipping');
                              $shipping_info = json_decode($info->shippingMethod->content,true);
                              $method = $shipping_info['method_type'];
                              $shipping_price = $shipping_info['pricing'];
                              $countp = 1;
                           @endphp
                           <div class="col-lg-12">
                                   <select name="shipping_type" id="shipping_type" class="select2 form-control">
                                    <option value="" > Choose Shipping Type</option>
                                    @foreach( $shipping_types as $stype=>$label)
                                     <option @if($method == $stype) selected @endif value="{{$stype}}">{{$label}}</option>
                                    @endforeach
                                   </select>
                           </div>
                        </div>

                        @foreach( $shipping_types as $stype=>$label)
                           @php
                           $p = 0;
                           $display = 'display:none;';
                           if($method == $stype){
                           $p = $shipping_price;
                           $display = 'display:block;';
                           }
                           @endphp

                        @if($stype == 'weight_based')
                        <div class="from-group row mb-2 type_price weight_based" style="{{$display}}"  >
                           <label for="" class="col-lg-12">{{ __('Price per LB :') }} </label>
                           <div class="col-lg-12">
                             <input type="number" required="" value="{{$p}}" step="any" name="type_price['perlb']" class="form-control" placeholder="Enter Price per lb">
                           </div>
                        </div>
                       @endif

                       @if($stype == 'per_item')
                        <div class="from-group row mb-2 type_price per_item" style="{{$display}}">
                           <label for="" class="col-lg-12">{{ __('Price per item :') }} </label>
                           <div class="col-lg-12">
                             <input type="number" required="" value="{{$p}}" step="any" name="type_price['per_item']" class="form-control" placeholder="Enter Price per Item">
                           </div>
                        </div>
                       @endif

                       @if($stype == 'flat_rate')
                        <div class="from-group row mb-2 type_price flat_rate" style="{{$display}}">
                           <label for="" class="col-lg-10">{{ __('flatrate for Cart Total :') }} </label> 
                          
                           <div class="col-lg-12">
                                       @php  

                                       $countp = 0;
                                       if(!is_array($p)){
                                          $p = array();
                                          $p[] = array('from'=>0,'to'=>25,'price'=>10);
                                       }
                                       
                                       @endphp

                                       

                                       @foreach($p as $k=>$v)

                                         
                                       <div class="row">
                                          <div class="col-md-2">
                                                <input type="number" required="" value="{{$v['from']}}" step="any" name="type_price['flatrate_range'][{{$countp}}][from]" class="form-control" placeholder="Min Cart Total">
                                             </div>
                                             <div class="col-md-1">
                                                   <label for="">{{ __('-') }} </label>
                                             </div>
                                             <div class="col-md-2">
                                                <input type="number" required="" value="{{$v['to']}}" step="any" name="type_price['flatrate_range'][{{$countp}}][to]" class="form-control" placeholder="Min Cart Total">
                                             </div>
                                             <div class="col-lg-6">
                                                <input type="number" required="" value="{{$v['price']}}" step="any" name="type_price['flatrate_range'][{{$countp}}][price]" class="form-control" placeholder="Enter Price">
                                             </div>
                                             <div class="col-md-1">
                                                   <a href="javascript:void(0)" class="flatraterow" ><i class="fas fa-plus"></i></a>
                                             </div>
                                          </div>
                                          @php
                                          $countp++ 
                                          @endphp
                                          @endforeach
                          
                                 </div>
                              </div>    
                         @endif
                         @if($stype == 'free_shipping')
                           <div class="from-group row mb-2 type_price free_shipping" style="{{$display}}"  >
                              <label for="" class="col-lg-12">{{ __('Min Cart Total :') }} </label>
                              <div class="col-lg-12">
                              <input type="number" required="" value="{{ is_array($p)? $p['cart_min'] :0 }}" step="any" name="type_price['free_shipping'][cart_min]" class="form-control" placeholder="Enter Min Cart total">
                              </div>
                           </div>
                         @endif
                        
                      @endforeach




                            <div class="from-group row mb-2">
                                <label for="" class="col-lg-12">{{ __('Price :') }} </label>
                                <div class="col-lg-12">
                                    <input type="number" value="{{ $info->slug }}" required="" value="0" step="any" name="price" class="form-control" placeholder="Enter Price">
                                </div>
                            </div>
                             {{-- <div class="from-group row mb-2">
                                <label for="" class="col-lg-12">{{__('Select Locations :')}} </label>
                                <div class="col-lg-12">
                                   <select name="locations[]" multiple="" class="select2 form-control">
                                    @foreach($posts as $row)
                                       <option value="{{ $row->id }}" @if(in_array($row->id,$location_array)) selected @endif>{{ $row->name }}</option>
                                     @endforeach  
                                   </select>
                                </div>
                            </div> --}}
                            <div class="from-group row mb-2">
                                <label for="" class="col-lg-12">{{ __('Status') }} : </label>
                                <div class="col-lg-12">
                                    <select name="status"  class="form-control">
                                       <option value="1" @if($info->status == 1) selected @endif>{{ __('Enable') }}</option>
                                       <option value="0" @if($info->status != 1) selected @endif>{{ __('Disable') }}</option>
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
<script src="{{ asset('admin/js/select2.min.js') }}"></script>


<script>
      var rowtotal = {{$countp}};
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


