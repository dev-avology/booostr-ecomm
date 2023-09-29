@extends('layouts.backend.app')

@section('title','Dashboard')

@section('content')
<section class="section">
{{-- section title --}}
<div class="section-header">
 <a href="{{ route('seller.site-settings.index') }}" class="btn btn-primary mr-2">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1>{{ __('Store details') }}</h1>
</div>
{{-- /section title --}}
<div class="row">
   <div class="col-lg-12">
      <form class="ajaxform" method="post" action="{{ route('seller.site-settings.update','general') }}">
                @csrf
                @method('PUT')
         <div class="row">
            {{-- left side --}}
            <div class="col-lg-4">
                <h6>{{ __('Store contact information') }}</h6>
                <strong>{{ __('Your customers will use this information to contact you.') }}</strong>
            </div>
            {{-- /left side --}}
            {{-- right side --}}
            <div class="col-lg-8">
               <div class="card">
                  <div class="card-body">
                    <div class="from-group row mb-2">
                        <label for="" class="col-lg-12">{{ __('Store name :') }}  </label>
                        <div class="col-lg-12">
                            <input type="text" disabled value="{{ $store_name }}"  required="" name="store_name" class="form-control" max="30">
                        </div>
                    </div>
                    <div class="from-group row mb-2">
                       <label for="" class="col-lg-12">{{ __('Sender email') }} : </label>
                        <div class="col-lg-12">
                            <input type="email" disabled value="{{ $store_sender_email ?? '' }}" required  name="store_sender_email" class="form-control"  max="50">
                            <small>{{ __('Your customers will see this address if you email them.') }}</small>
                        </div>
                    </div>
                    <div class="from-group row mb-2">
                        <label for="" class="col-lg-12">{{ __('Store Latitude:') }}  </label>
                        <div class="col-lg-12">
                            <input type="number" disabled value="{{ $lat_lang[0] }}"  step="any"  required="" name="latitude" class="form-control"  placeholder="31.9686">
                        </div>
                    </div>
                    <div class="from-group row mb-2">
                        <label for="" class="col-lg-12">{{ __('Store Longitude:') }}  </label>
                        <div class="col-lg-12">
                            <input type="number" disabled value="{{ $lat_lang[1] }}"  step="any"  required="" name="longitude" class="form-control"  placeholder="99.9018">
                        </div>
                    </div>
                  </div>
               </div>
            </div>
            {{-- /right side --}}
         </div>
         <div class="row">
            {{-- left side --}}
            {{-- <div class="col-lg-4">
                <h6>{{ __('Store Images') }}</h6>
                <strong>{{ __('Your customers will see the images.') }}</strong>
            </div> --}}
            {{-- /left side --}}
            {{-- right side --}}
            {{-- <div class="col-lg-8">
               <div class="card">
                  <div class="card-body">
                    <div class="from-group row mb-2">
                        <label for="" class="col-lg-12">{{ __('Store Logo:') }} (Height: 100px)</label>
                        <div class="col-lg-12">
                            <input type="file"  name="logo" class="form-control" accept=".png" >
                        </div>
                    </div>
                    <div class="from-group row mb-2">
                        <label for="" class="col-lg-12">{{ __('Store Favicon:') }} (48x48)</label>
                        <div class="col-lg-12">
                            <input type="file"  name="favicon" class="form-control" accept=".ico" >
                        </div>
                    </div>
                    <div class="from-group row mb-2">
                        <label for="" class="col-lg-12">{{ __('Store Notification icon:') }} (50x50)</label>
                        <div class="col-lg-12">
                            <input type="file"  name="notification_icon" class="form-control" accept=".png" >
                        </div>
                    </div>
                    <div class="from-group row mb-2">
                        <label for="" class="col-lg-12">{{ __('Store Banner:') }}</label>
                        <div class="col-lg-12">
                            <input type="file"  name="banner" class="form-control" accept=".png" >
                        </div>
                    </div>
                  </div>
               </div>
            </div> --}}
            {{-- /right side --}}
         </div>
         <div class="row">
            {{-- left side --}}
            <div class="col-lg-4">
                <h6>{{ __('Store address') }}</h6>
                <strong>{{ __('This address will appear on your invoices.') }}</strong>
            </div>
            {{-- /left side --}}
            {{-- right side --}}
            <div class="col-lg-8">
               <div class="card">
                  <div class="card-body">
                    <div class="from-group row mb-2">
                        <label for="" class="col-lg-12">{{ __('Legal name of company :') }}  </label>
                        <div class="col-lg-12">
                            <input type="text" disabled value="{{ $store_name }}" name="store_legal_name" class="form-control" max="50">
                        </div>
                    </div>
                    <div class="from-group row mb-2">
                       <label for="" class="col-lg-12">{{ __('Phone') }} : </label>
                        <div class="col-lg-12">
                            <input type="number" disabled value="{{ str_replace('-','',$phone_number) ?? '' }}" name="store_legal_phone" class="form-control" required>
                        </div>
                    </div>
                     <div class="from-group row mb-2">
                       <label for="" class="col-lg-12">{{ __('Email') }} : </label>
                        <div class="col-lg-12">
                            <input type="email" disabled value="{{ $store_sender_email ?? '' }}" name="store_legal_email" class="form-control" required>
                        </div>
                    </div>
                    
                    <div class="from-group row mb-2">
                       <label for="" class="col-lg-12">{{ __('Address') }} : </label>
                        <div class="col-lg-12">
                            <input type="text" disabled value="{{ isset($address[0]) ? $address[0] : '' }}"  name="store_legal_address" class="form-control" required>
                        </div>
                    </div>
                    <div class="from-group row mb-2">
                       <label for="" class="col-lg-12">{{ __('Apartment, suite, etc.') }} : </label>
                        <div class="col-lg-12">
                            <input type="text" disabled value="{{ isset($address[1]) ? $address[1] : '' }}" name="store_legal_house" class="form-control" required>
                        </div>
                    </div>
                    <div class="from-group row mb-2">
                       <label for="" class="col-lg-12">{{ __('City') }} : </label>
                        <div class="col-lg-12">
                            <input type="text" disabled value="{{ trim($address[count($address)-2]) ?? '' }}" name="store_legal_city" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="from-group col-lg-6  mb-2">
                         <label for="" >{{ __('Country/region') }} : </label>
                          <input type="text" disabled value="{{ trim($address[count($address)-1]) ?? '' }}" name="country" class="form-control">
                        </div>
                        <div class="from-group col-lg-6  mb-2">
                         <label for="" >{{ __('Postal code') }} : </label>
                          <input type="text" disabled value="" name="post_code" class="form-control">
                        </div>
                  </div>
                  </div>
               </div>
            </div>
            {{-- /right side --}}
         </div>
          <div class="row">
            {{-- left side --}}
            <div class="col-lg-4">
                <h6>{{ __('Standards and formats') }}</h6>
                <strong>{{ __('Standards and formats are used to calculate product prices, shipping weights, and order times.') }}</strong>
            </div>
            {{-- /left side --}}
            {{-- right side --}}
            <div class="col-lg-8">
               <div class="card">
                  <div class="card-body">
                    {{-- <!--div class="from-group row mb-2">
                        <label for="" class="col-lg-12">{{ __('Time zone') }} : </label>
                        <div class="col-lg-12">
                            <select disabled class="form-control selectric" name="timezone" id="timezone" >
                               <option value='UTC'>UTC</option>
                            </select>
                        </div>
                    </div>
                    <div class="from-group row mb-2">
                        <label for="" class="col-lg-12">{{ __('Select Default language') }} : </label>
                        <div class="col-lg-12">
                            <select disabled class="form-control selectric" name="default_language" id="default_language">
                              @foreach($languages ?? [] as $row)
                              <option value="{{ $row->code }}">{{ $row->name }}</option>
                              @endforeach
                            </select>
                        </div>
                    </div--> --}}
                    

                    <div class="from-group row mb-2">
                        <label for="" class="col-lg-12">{{ __('Default weight unit') }} : </label>
                        <div class="col-lg-12">
                            @php 
                              $weights = ['OZ, LBS, TONS'];
                              $weight_type = $weight_type->value??'LBS';
                            @endphp
                           {{-- <select disabled  class="form-control selectric" name="weight_type" id="weight_type">
                            @foreach($weights ?? [] as $row)
                            <option value="{{ $row }}" {{( $weight_type == $row) ? 'selected' : ''}}>{{ $row }}</option>
                            @endforeach
                          </select> --}}

                          <input type="text" disabled name="weight_type" value="OZ, LBS, TONS" class="form-control">

                        </div>
                    </div>

                    <div class="from-group row mb-2">
                        <label for="" class="col-lg-12">{{ __('Default Measurment  unit') }} : </label>
                        <div class="col-lg-12">
                            @php 
                              $measurments = ['IN, FT, YDS'];
                              $measurment_type = $measurment_type->value??'IN';
                            @endphp
                           {{-- <select disabled  class="form-control selectric" name="measurment_type" id="measurment_type">
                            @foreach($measurments ?? [] as $row)
                            <option value="{{ $row }}" {{( $measurment_type == $row) ? 'selected' : ''}}>{{ $row }}</option>
                            @endforeach
                          </select> --}}

                          <input type="text" disabled name="measurment_type" value="IN, FT, YDS" class="form-control">

                        </div>
                    </div>

                  </div>
               </div>
            </div>
            {{-- /right side --}}
         </div>
           <div class="row">
            {{-- left side --}}
            <div class="col-lg-4">
                <h6>{{ __('Store currency') }}</h6>
                <strong>{{ __('This is the currency your products are sold in') }}</strong>
            </div>
            {{-- /left side --}}
            {{-- right side --}}
            <div class="col-lg-8">
               <div class="card">
                  <div class="card-body">
                    <div class="from-group row mb-2">
                        <label for="" class="col-lg-12">{{ __('Store currency') }} : </label>
                        <div class="col-lg-12">
                            <input type="text" disabled name="currency_name" value="{{ $currency_info->currency_name ?? '' }}" class="form-control" required="">
                        </div>
                    </div>
                    <div class="from-group row mb-2">
                        <label for="" class="col-lg-12">{{ __('Currency icon') }} : </label>
                        <div class="col-lg-12">
                            <input type="text" disabled name="currency_icon" value="{{ $currency_info->currency_icon ?? '' }}" class="form-control" required="">
                        </div>
                    </div>
                    @php
                    $currency_position=$currency_info->currency_position ?? '';
                    @endphp
                    <div class="from-group row mb-2">
                        <label for="" class="col-lg-12">{{ __('Currency Position') }} : </label>
                        <div class="col-lg-12">
                           <select class="form-control selectric" disabled name="currency_position">
                               <option value="left" @if($currency_position == 'left') selected="" @endif>{{ __('Left') }}</option>
                               <option value="right" @if($currency_position == 'right') selected="" @endif>{{ __('right') }}</option>
                           </select>
                        </div>
                    </div>
                    
                  </div>
               </div>
            </div>
            {{-- /right side --}}
         </div>


         <div class="row">
            {{-- left side --}}
            <div class="col-lg-4">
                <h6>{{ __('Store Sale Tax Setting') }}</h6>
                <strong>{{ __('This is tax setting will be applied to All in state order') }}</strong>
            </div>
            {{-- /left side --}}
            {{-- right side --}}
            <div class="col-lg-8">
               <div class="card">
                  <div class="card-body">
                  
                    <div class="from-group row mb-2">
                        <label for="" class="col-lg-12">{{ __('Sales Tax Percentage Amount') }} : </label>
                        <div class="col-lg-12">
                            <input type="text" value="{{ $tax ?? 0.00 }}"
                            name="tax" class="form-control"  id="tax" data-inputmask="'mask': '9{0,2}.9{0,3}[%]'" data-mask>
                        </div>
                    </div>

                  </div>
               </div>
            </div>
            {{-- /right side --}}
         </div>


         <div class="row">
            {{-- left side --}}
            <div class="col-lg-4">
                <h6>{{ __('Store Shipping Setting') }}</h6>
            </div>
            {{-- /left side --}}
            {{-- right side --}}
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">

                        <div class="from-group row mb-2">
                            <label for="" class="col-lg-12">{{ __('Is Free Shipping') }} : </label>
                            <div class="col-lg-12">
                                <select name="free_shipping" class="form-control">
                                    <option value="1" @if ($free_shipping == 1) selected @endif>
                                        {{ __('Enable') }}</option>
                                    <option value="0" @if ($free_shipping != 1) selected @endif>
                                        {{ __('Disable') }}</option>
                                </select>
                            </div>
                        </div>

                        <div class="from-group row mb-2">
                            <label for=""
                                class="col-lg-12">{{ __('Min Cart Total for free shipping') }} : </label>
                            <div class="col-lg-12">
                                <div class="input-with-icon">
                                <i class="fas fa-dollar-sign"></i>
                                <input type="number" step="any" value="{{ $min_cart_total ?? 100 }}"
                                    name="min_cart_total" class="form-control" placeholder="0.00">
                                </div>  
                                <small>{{ __('Your Minimum Cart total in store currency.') }}</small>
                            </div>
                        </div>



                        <div class="from-group row mb-2">
                            <label for="" class="col-lg-12">{{ __('Regular Shipping Method:') }}
                            </label>
                            @php
                                $shipping_types = ['weight_based' => 'Weight Based', 'per_item' => 'Per Item', 'flat_rate' => 'Flat Rate'];
                                
                                $shipping_info = json_decode($shipping_method->value, true);
                                $method = $shipping_info['method_type'];
                                $shipping_label = $shipping_info['label'];
                                $shipping_price = $shipping_info['pricing'];
                                $shipping_base_price = $shipping_info['base_pricing'];
                                
                                $countp = 1;
                            @endphp

                            <div class="col-lg-12">
                                <select name="shipping_method" id="shipping_type" class="select2 form-control">
                                    <option value=""> Choose Shipping Type</option>
                                    @foreach ($shipping_types as $stype => $label)
                                        <option @if ($method == $stype) selected @endif
                                            value="{{ $stype }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="from-group row mb-2">
                            <label for="" class="col-lg-12">{{ __('Shipping Method Label:') }} </label>

                            <div class="col-lg-12">
                                <input type="text" value="{{ $shipping_label ?? '' }}" required
                                    name="shipping_method_label" class="form-control" >
                                <small>{{ __('Your Shipping Method Label.') }}</small>
                            </div>
                        </div>


                        @foreach ($shipping_types as $stype => $label)
                            @php
                                $p = 0;
                                $display = 'display:none;';
                                if ($method == $stype) {
                                    $p = $shipping_price;
                                    $display = 'display:block;';
                                }
                            @endphp

                            @if ($stype == 'weight_based')
                                <div class="from-group row mb-2 type_price weight_based"
                                    style="{{ $display }}">
                                    <label for="" class="col-lg-12">{{ __('Price per LB :') }} </label>
                                    <div class="col-lg-12">
                                        <div class="input-with-icon">
                                            <i class="fas fa-dollar-sign"></i>
                                        <input type="number" required="" value="{{ $p }}"
                                            step="any" name="type_price['perlb']" class="form-control"
                                            placeholder="0.00">
                                        </div>    
                                        <small>{{ __('Your Shipping per LB in store currency.') }}</small>
                                    </div>
                                </div>
                                <div class="from-group row mb-2 type_price weight_based"
                                    style="{{ $display }}">
                                    <label for="" class="col-lg-12">{{ __('Shipping Base Price:') }}
                                    </label>

                                    <div class="col-lg-12">
                                        <div class="input-with-icon">
                                            <i class="fas fa-dollar-sign"></i>
                                        <input type="number" step="any"
                                            value="{{ $shipping_base_price ?? 0 }}" required
                                            name="base_price['perlb']" class="form-control" placeholder="0.00">
                                        </div>   
                                        <small>{{ __('Your Shipping Base Price.') }}</small>
                                    </div>
                                </div>
                            @endif

                            @if ($stype == 'per_item')
                                <div class="from-group row mb-2 type_price per_item"
                                    style="{{ $display }}">
                                    <label for="" class="col-lg-12">{{ __('Price per item :') }}
                                    </label>
                                    <div class="col-lg-12">
                                        <div class="input-with-icon">
                                            <i class="fas fa-dollar-sign"></i>
                                         <input type="number" required="" value="{{ $p }}"
                                            step="any" name="type_price['per_item']" class="form-control"
                                            placeholder="0.00">
                                        </div>
                                        <small>{{ __('Your Shipping per item Price in store currency.') }}</small>
                                    </div>
                                </div>

                                <div class="from-group row mb-2 type_price per_item"
                                    style="{{ $display }}">
                                    <label for="" class="col-lg-12">{{ __('Shipping Base Price:') }}
                                    </label>

                                    <div class="col-lg-12">
                                        <div class="input-with-icon">
                                            <i class="fas fa-dollar-sign"></i>
                                        <input type="number" step="any"
                                            value="{{ $shipping_base_price ?? 0 }}" required
                                            name="base_price['per_item']" class="form-control" placeholder="0.00">
                                        </div>
                                        <small>{{ __('Your Shipping Base Price in store currency.') }}</small>
                                    </div>
                                </div>
                            @endif

                            @if ($stype == 'flat_rate')
                                <div class="from-group row mb-2 type_price flat_rate"
                                    style="{{ $display }}">
                                    <input type="hidden" value="0" name="base_price['flat_rate']">
                                    <label for=""
                                        class="col-lg-10">{{ __('flat Rate Shipping Price for Cart Totals :') }} </label>

                                    <div class="col-lg-12" id="flat_rate">
                                        @php
                                            
                                            $countp = 0;
                                            if (!is_array($p)) {
                                                $p = [];
                                                $p[] = ['from' => 0, 'to' => 25, 'price' => 10];
                                            }
                                            
                                        @endphp



                                        @foreach ($p as $k => $v)
                                            <div class="row mt-2" id="f-{{$k}}" >
                                                <div class="col-lg-3">
                                                      
                                                         <small>{{ __('Cart Subtotal Range (low)') }}</small>
                                                      
                                                        <div class="input-with-icon">
                                                            <i class="fas fa-dollar-sign"></i>
                                                        <input type="number" required=""
                                                        value="{{ $v['from'] }}" step="any"
                                                        name="type_price['flatrate_range'][{{ $countp }}][from]"
                                                        class="form-control" placeholder="0.00">
                                                        </div>
                                                </div>
                                                <div class="col-lg-1">
                                                    <label for="">{{ __('-') }} </label>
                                                </div>
                                                <div class="col-lg-3">

                                                   
                                                         <small>{{ __('Cart Subtotal Range (high)') }}</small>
                            
                                                        <div class="input-with-icon">
                                                            <i class="fas fa-dollar-sign"></i>
                                                    <input type="number" required=""
                                                        value="{{ $v['to'] }}" step="any"
                                                        name="type_price['flatrate_range'][{{ $countp }}][to]"
                                                        class="form-control" placeholder="0.00">
                                                        </div>
                                                </div>
                                                <div class="col-lg-3">

                                                  
                                                        <small>{{ __('Shipping Cost to Customer') }}</small>
                                                   
                                                    <div class="input-with-icon">
                                                        <i class="fas fa-dollar-sign"></i>
                                                    <input type="number" required=""
                                                        value="{{ $v['price'] }}" step="any"
                                                        name="type_price['flatrate_range'][{{ $countp }}][price]"
                                                        class="form-control" placeholder="0.00">
                                                    </div>
                                                </div>
                                                <div class="col-lg-2">
                                                   
                                                    <a href="javascript:void(0)" data-rowid="f-{{$k}}" class="flatrate-remove-row"><i
                                                        class="fas fa-minus pt-4"></i></a>      
                                                </div>
                                            </div>
                                            @php
                                                $countp++;
                                            @endphp
                                        @endforeach

                                    </div>
                                    <div class="row">
                                        <div class="col-md-12 pt-4" style="text-align: center;"><a href="javascript:void(0)" class="flatraterow"><i
                                            class="fas fa-3x fa-plus" style="font-size:2em;"></i></a></div>
                                    </div>
                                </div>
                            @endif
                        @endforeach


                        <div class="from-group row mt-2">
                       
                            <div class="col-lg-4">
                               <button type="submit" class="basicbtn btn btn-primary">{{ __('Save changes') }}</button>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
            {{-- /right side --}}
        </div>



          <div class="row" >
            {{-- left side --}}
            {{-- <div class="col-lg-4">
                <h6>{{ __('Order Settings') }}</h6>
                <strong>{{ __('Configure your order methods and other settings') }}</strong>
            </div> --}}
            {{-- /left side --}}
            {{-- right side --}}
            {{-- <div class="col-lg-8">
               <div class="card">
                  <div class="card-body">
                    <div class="from-group row mb-2">
                        <label for="" class="col-lg-12">{{ __('Select Order Method') }} : </label>
                        <div class="col-lg-12">
                           <select  class="form-control selectric" name="order_method">
                            @if(tenant('push_notification') == 'on')
                               <option value="fmc" @if($order_method == 'fmc') selected="" @endif>{{ __('Real Time Push Notification') }}</option>
                            @endif   
                               <option value="mail" @if($order_method == 'mail') selected="" @endif>{{ __('Mail Notification') }}</option>
                               <option value="whatsapp" @if($order_method == 'whatsapp') selected="" @endif>{{ __('Whatsapp Notification') }}</option>
                           </select>
                        </div>
                    </div>
                   
                    <div class="from-group row mb-2">
                        @php
                        $shipping_amount_type= $order_settings->shipping_amount_type  ?? '';
                        @endphp
                        <label for="" class="col-lg-12">{{ __('Shipping Amount Type') }} : </label>
                        <div class="col-lg-12">
                            <select  class="form-control selectric" id="shipping_amount_type" name="shipping_amount_type">
                               <option value="shipping" @if($shipping_amount_type == 'shipping') selected @endif>{{ __('Based On  Shipping Charge') }}</option>
                               <option value="distance" @if($shipping_amount_type == 'distance') selected @endif>{{ __('Based On Distance') }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="from-group row mb-2 google_api">
                        <label for="" class="col-lg-12">{{ __('Enter Google Place API') }} : </label>
                        <div class="col-lg-12">
                            <input type="text" value="{{ $order_settings->google_api ?? '' }}" name="google_api" class="form-control google_api">
                        </div>
                    </div>
                    
                    <div class="from-group row mb-2 google_api_range">
                        <label for="" class="col-lg-12">{{ __('Delivery Fee (Per Kilo Meter)') }} : </label>
                        <div class="col-lg-12">
                            <input type="number" step="any" value="{{ $order_settings->delivery_fee ?? '' }}" name="delivery_fee" class="form-control ">
                        </div>
                    </div>

                    <div class="from-group row mb-2 google_api_range">
                        <label for="" class="col-lg-12">{{ __('Max Delivery Range (Meter)') }} : </label>
                        <div class="col-lg-12">
                            <input type="number" step="any" value="{{ $order_settings->google_api_range ?? '' }}" name="google_api_range" class="form-control ">
                        </div>
                    </div>
                    @php
                    $pickup_order= $order_settings->pickup_order ?? 'off';
                    $pre_order= $order_settings->pre_order ?? 'off';
                    $source_code= $order_settings->source_code ?? 'on';
                    
                    @endphp
                     <div class="from-group row mb-2">
                        <label for="" class="col-lg-12">{{ __('Enable Pre Order') }} : </label>
                        <div class="col-lg-12">

                            <select class="form-control" name="pickup_order">
                                <option value="on" @if($pickup_order == 'on') selected="" @endif>{{  __('Enable') }}</option>
                                <option value="off" @if($pickup_order == 'off') selected="" @endif>{{  __('Disable') }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="from-group row mb-2">
                        <label for="" class="col-lg-12">{{ __('Pickup Order Status') }} : </label>
                        <div class="col-lg-12">

                            <select class="form-control" name="pre_order">
                                <option value="on" @if($pre_order == 'on') selected="" @endif>{{  __('Enable') }}</option>
                                <option value="off" @if($pre_order == 'off') selected="" @endif>{{  __('Disable') }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="from-group row mb-2">
                        <label for="" class="col-lg-12">{{ __('Disable Source Code For Checkout Page') }} : </label>
                        <div class="col-lg-12">

                            <select class="form-control" name="source_code">
                                <option value="on" @if($source_code == 'on') selected="" @endif>{{  __('Enable') }}</option>
                                <option value="off" @if($source_code == 'off') selected="" @endif>{{  __('Disable') }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="from-group row mb-2">
                        <label for="" class="col-lg-12">{{ __('Whatsapp number for receiving order') }} : </label>
                        <div class="col-lg-12">
                            <input type="number" name="whatsapp_no" class="form-control" value="{{ $whatsapp_no->value ?? '' }}">
                        </div>
                    </div>

                    <div class="from-group row mb-2">
                        <label for="" class="col-lg-12">{{ __('Average delivery time') }} : </label>
                        <div class="col-lg-12">
                            <input type="text" name="delivery_time" value="{{ $average_times->delivery_time ?? '' }}" class="form-control">
                        </div>
                    </div>
                    <div class="from-group row mb-2">
                        <label for="" class="col-lg-12">{{ __('Pickup time') }} : </label>
                        <div class="col-lg-12">
                            <input type="text" name="pickup_time" value="{{ $average_times->pickup_time ?? '' }}" class="form-control">
                        </div>
                    </div>
                   

                    <div class="from-group row mt-2">
                       
                        <div class="col-lg-4">
                           <button type="submit" class="basicbtn btn btn-primary">{{ __('Save changes') }}</button>
                        </div>
                    </div>

                  </div>
               </div>
            </div> --}}
            {{-- /right side --}}
         </div>
           <div class="row" >
            {{-- left side --}}
            {{-- <div class="col-lg-4">
                <h6>{{ __('Whatsapp Settings') }}</h6>
                <strong>{{ __('Whatsapp Modules For Site ') }}</strong>
            </div> --}}
            {{-- /left side --}}
            {{-- right side --}}
            {{-- <div class="col-lg-8">
               <div class="card">
                  <div class="card-body">
                   
                    
                   
                    <div class="from-group row mb-2">
                        <label for="" class="col-lg-12">{{ __('Whatsapp Number for contact') }} : </label>
                        <div class="col-lg-12">
                            <input type="text" name="whatsapp_no" value="{{ $whatsapp_settings->whatsapp_no ?? '' }}" class="form-control">
                        </div>
                    </div>
                    <div class="from-group row mb-2">
                        <label for="" class="col-lg-12">{{ __('Pretext For Product Page') }} : </label>
                        <div class="col-lg-12">
                          <textarea class="form-control h-150" required="" name="shop_page_pretext" placeholder="I want to purchase this">{{ $whatsapp_settings->shop_page_pretext ?? '' }}</textarea>

                          <span><span class="text-primary">{{ __('The Api Text Will Append Like This')  }} : <br> </span>{{ __('i want to purchase this product') }} http:://url.com/product/product-name</span>
                        </div>
                    </div>

                    <div class="from-group row mb-2">
                        <label for="" class="col-lg-12">{{ __('Pretext For Other Page') }} : </label>
                        <div class="col-lg-12">
                          <textarea class="form-control h-150" required="" name="other_page_pretext" placeholder="I i have a query">{{ $whatsapp_settings->other_page_pretext ?? '' }}</textarea>
                        </div>
                    </div>
                    @php
                    $whatsapp_status=$whatsapp_settings->whatsapp_status ?? ''
                    @endphp
                    <div class="from-group row mb-2">
                        <label for="" class="col-lg-12">{{ __('Status') }} : </label>
                        <div class="col-lg-12">
                          <select class="form-control selectric" name="whatsapp_status">
                            <option value="on" @if($whatsapp_status == 'on') selected @endif >{{ __('Enable') }}</option>
                            <option value="off" @if($whatsapp_status == 'off') selected @endif>{{ __('Disable') }}</option>
                          </select>
                        </div>
                    </div>

                    <div class="from-group row mt-2">
                       
                        <div class="col-lg-4">
                           <button type="submit" class="basicbtn btn btn-primary">{{ __('Save changes') }}</button>
                        </div>
                    </div>

                  </div>
               </div>
            </div> --}}
            {{-- /right side --}}
         </div>
      </form>
   </div>
</div>
</section>

@endsection
@push('script')
<script>

$(document).ready(function () {
    Inputmask("9{0,2}.9{0,3}", {
                placeholder: "5.000",
                greedy: true
            }).mask('#tax');
        });
</script>
<script>
  "use strict";
    $('#timezone').val('{{ $timezone->value ?? '' }}')
    $('#default_language').val('{{ $default_language->value ?? '' }}');
   

    $(document).on('change','.input-with-icon input[type=number]',function() {
       // This function will be executed when the input value changes.
       var inputValue = $(this).val();
       
       inputValue = inputValue.match(/[0-9.]+/g);

       if(inputValue === null || inputValue === ''){
        return;
       }
       $(this).val(parseFloat(inputValue).toFixed(2));
    });


    $(document).on('change','#tax',function() {
       // This function will be executed when the input value changes.
       var inputValue = $(this).val();
       inputValue = inputValue.match(/[0-9.]+/g);

       if(inputValue === null || inputValue === ''){
        return;
       }
       $(this).val(parseFloat(inputValue).toFixed(3));
    });




    $('#shipping_amount_type').on('change',function(){
        var type=$(this).val();
        if (type == 'distance') {
            $('.google_api').show();
            $('.google_api_range').show();
        }
        else{
            $('.google_api').hide();
            $('.google_api_range').hide();
        }
    });

     var type=$('#shipping_amount_type').val();
   

        if (type == 'distance') {
            $('.google_api').show();
            $('.google_api_range').show();
        }
        else{
            $('.google_api').hide();
            $('.google_api_range').hide();
        }
    


        var rowtotal = {{ $countp }};
        //$(document).ready(function() {
            $('#shipping_type').change(function() {
                $('.type_price').hide();
                $('.' + $(this).val()).show();
            });
            $(document).on('click','.flatrate-remove-row', function() {
                $('#'+$(this).data('rowid')).remove();
                return false;
            });
            $('.flatraterow').on('click', function() {

                $('#flat_rate').append('<div class="row mt-2" id="f-'+rowtotal+'">' +
                    '<div class="col-lg-3"> <small> Cart Subtotal Range (low)</small><div class="input-with-icon"><i class="fas fa-dollar-sign"></i><input type="number"  value="" step="any" name="type_price[\'flatrate_range\'][' +
                    rowtotal + '][from]" class="form-control" placeholder="0.00"></div></div>' +
                    '<div class="col-lg-1"><label for="">-</label></div>' +
                    '<div class="col-lg-3"><small> Cart Subtotal Range (high)</small><div class="input-with-icon"><i class="fas fa-dollar-sign"></i><input type="number"  value="" step="any" name="type_price[\'flatrate_range\'][' +
                    rowtotal + '][to]" class="form-control" placeholder="0.00"></div></div>' +
                    '<div class="col-lg-3"><small>Shipping Cost to Customer</small> <div class="input-with-icon"><i class="fas fa-dollar-sign"></i><input type="number"  value="" step="any" name="type_price[\'flatrate_range\'][' +
                    rowtotal + '][price]" class="form-control" placeholder="0.00"></div></div>' +
                    ' <div class="col-lg-2"><a href="javascript:void(0)" data-rowid="f-'+rowtotal+'" class="flatrate-remove-row"><i class="fas fa-minus pt-4"></i></a></div> ' +
                    '</div>');

                rowtotal++;
            });

        //});

</script>
@endpush


