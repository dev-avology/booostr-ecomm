
<div class="store-notification">
@php 
$checkListArr = userChecklist();

$addressChecked = ($checkListArr['address'] == 1) ? 'checked' : '';
$addressOutput = ($checkListArr['address'] == 1) ? '<del>Store address></del><b>(REQUIRED)</b>' : 'Store address<b>(REQUIRED)</b>';

$taxChecked = ($checkListArr['tax'] == 1) ? 'checked' : '';
$taxOutput = ($checkListArr['tax'] == 1) ? '<del>Set sales tax rate for state</del><b>(REQUIRED)</b>' : 'Set sales tax rate for state<b>(REQUIRED)</b>';

// $bannerUrlChecked = ($checkListArr['banner_url'] == 1) ? 'checked' : '';
// $bannerUrlOutput = ($checkListArr['banner_url'] == 1) ? '<del>Banner url</del>' : 'Banner url';

$bannerLogoChecked = ($checkListArr['banner_logo'] == 1) ? 'checked' : '';
$bannerLogoOutput = ($checkListArr['banner_logo'] == 1) ? '<del>Set Store Banner</del><b>(REQUIRED)</b>' : 'Set Store Banner<b>(REQUIRED)</b';

$shippingMethodChecked = ($checkListArr['shipping_method'] == 1) ? 'checked' : '';
$shippingMethodOutput = ($checkListArr['shipping_method'] == 1) ? '<del>Set Shipping Rates</del><b>(REQUIRED)</b>' : 'Set Shipping Rates<b>(REQUIRED)</b';

// $freeShippingChecked = ($checkListArr['free_shipping'] == 1) ? 'checked' : '';
// $freeShippingOutput = ($checkListArr['free_shipping'] == 1) ? '<del>Free shipping
// </del>' : 'Free shipping';

$categoryChecked = ($checkListArr['category'] == 1) ? 'checked' : '';
$categoryOutput = ($checkListArr['category'] == 1) ? '<del>Set Up Store Product Categories</del>' : 'Set Up Store Product Categories';

$simpleProductChecked = ($checkListArr['simple_product'] == 1) ? 'checked' : '';
$simpleProductOutput = ($checkListArr['simple_product'] == 1) ? '<del>Add First Product</del>' : 'Add First Product';

$variationProductChecked = ($checkListArr['variation_product'] == 1) ? 'checked' : '';
$variationProductOutput = ($checkListArr['variation_product'] == 1) ? '<del>Add any Variant product Attributes
</del>' : 'Add any Variant product Attributes';

@endphp

@if(($checkListArr['address'] != 1) || ($checkListArr['tax'] != 1) || ($checkListArr['banner_logo'] != 1) || ($checkListArr['shipping_method'] != 1))



<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12">
    <div class="card card-statistic-2 checklist-main">
        <p class="checklist-alert-msg">Your store is almost ready to launch! Complete required tasks to start selling online. <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">
            See Task List
          </button></p>
    </div>
   </div>
</div>


<div class="modal" id="exampleModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
      <div class="modal-content checklist-content-model">
        <div class="modal-header">
          <h5 class="modal-title">Complete required tasks to start selling online</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
        
            <div class="card">
               {{-- <p class="collapsible">Review and update</p> --}}

                <div class="content">   
                    <div class="accordion checklist-collapse" id="accordionExample">

                        <div class="card">
                            <div class="card-header" id="headingNine">
                                <h2 class="mb-0">
                                <input type="checkbox" {{ $addressChecked }} disabled>
                                <button class="btn btn-link collapsed collapsed-btn" type="button" data-toggle="collapse" data-target="#collapseNine" aria-expanded="false" aria-controls="collapseNine">
                                    {!! $addressOutput !!}
                                </button>
                                </h2>
                            </div>
                            <div id="collapseNine" class="collapse" aria-labelledby="headingNine" data-parent="#accordionExample">
                                <div class="card-body">
                                   <p>You must complete the store address before launching store.&nbsp;&nbsp;<span><a href="{{ url('seller/site-settings/general/#address-section') }}">Edit</a></span></p>
                                </div>
                            </div>
                        </div>


                        <div class="card">
                            <div class="card-header" id="headingTen">
                                <h2 class="mb-0">
                                <input type="checkbox" {{ $taxChecked }} disabled>
                                <button class="btn btn-link collapsed collapsed-btn" type="button" data-toggle="collapse" data-target="#collapseTen" aria-expanded="false" aria-controls="collapseTen">
                                    {!! $taxOutput !!}
                                </button>
                                </h2>
                            </div>
                            <div id="collapseTen" class="collapse" aria-labelledby="headingTen" data-parent="#accordionExample">
                                <div class="card-body">
                                    <p>You must complete the sales tax before launching store.&nbsp;&nbsp;<span><a href="{{ url('seller/site-settings/general/#tax-section') }}">Edit</a></span></p>
                                </div>
                            </div>
                        </div>


                        <div class="card">
                            <div class="card-header" id="headingEleven">
                                <h2 class="mb-0">
                                <input type="checkbox" {{ $shippingMethodChecked }} disabled>
                                <button class="btn btn-link collapsed collapsed-btn" type="button" data-toggle="collapse" data-target="#collapseEleven" aria-expanded="false" aria-controls="collapseEleven">
                                    {!! $shippingMethodOutput !!}
                                </button>
                                </h2>
                            </div>
                            <div id="collapseEleven" class="collapse" aria-labelledby="headingEleven" data-parent="#accordionExample">
                                <div class="card-body">
                                  <div class="free-shipping-body">
                                    <h6>Free shipping:-</h6><span>Unlock savings and convenience with 'Is Free Shipping' – enjoy a hassle-free shopping experience without the added cost of delivery fees. Our commitment to free shipping enhances your online shopping, ensuring both affordability and satisfaction in every purchase.</span>
                                    <h6 style="margin-top: 12px;">Shipping method:-</h6><span>There are three types of shipping methods ,Weight based, Per item, Flat rate.&nbsp;&nbsp;<span><a href="{{ url('seller/site-settings/general/#store-shipping-section') }}">Edit</a></span></span>
                                  </div>
                                </div>
                            </div>
                        </div>



                        <div class="card">
                            <div class="card-header" id="headingTwelve">
                                <h2 class="mb-0">
                                <input type="checkbox" {{ $bannerLogoChecked }} disabled>
                                <button class="btn btn-link collapsed collapsed-btn" type="button" data-toggle="collapse" data-target="#collapseTwelve" aria-expanded="false" aria-controls="collapseTwelve">
                                    {!! $bannerLogoOutput !!}
                                </button>
                                </h2>
                            </div>
                            <div id="collapseTwelve" class="collapse" aria-labelledby="headingTwelve" data-parent="#accordionExample">
                                <div class="card-body">
                                    <p>You must complete the banner before launching store.&nbsp;&nbsp;<span><a href="{{ url('seller/site-settings/general/#store-banner-section') }}">Edit</a></span></p>
                                </div>
                            </div>
                        </div>

                    
                        <div class="card">
                        <div class="card-header" id="headingTwo">
                            <h2 class="mb-0">
                            <input type="checkbox" disabled>
                            <button class="btn btn-link collapsed collapsed-btn" type="button" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                Set store live on profile (launch!)
                            </button>
                            </h2>
                        </div>
                        <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionExample">
                            <div class="card-body">
                                <p>Store live profile</p>
                            </div>
                        </div>
                        </div>


                        <div class="card">
                        <div class="card-header" id="headingThree">
                            <h2 class="mb-0">
                            <input type="checkbox" checked disabled>
                            <button class="btn btn-link collapsed collapsed-btn" type="button" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                <del>Review and update</del>
                            </button>
                            </h2>
                        </div>
                        <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordionExample">
                            @php
                                $page_arr = settingLinks();
                            @endphp
                            <div class="card-body">
                                <div>
                                    <h6>Terms & Conditions Page:-</h6>
                                    <span>Please review our terms and conditions; it's essential for your store. <a href="{{ url('seller/page', $page_arr['term_condition_id']) }}/edit">Edit</a></span>


                                    <h6 style="margin-top: 12px;">Privacy Policy Page:-</h6><span>Please check your privacy policy.This is necessary for you store.&nbsp;&nbsp;<span><a href="{{ url('seller/page', $page_arr['privacy_policy_id']) }}/edit">Edit</a></span>
                                    
                                    <h6 style="margin-top: 12px;">Return Policy Page:-</h6><span>Please check your return policy.This is necessary for you store.&nbsp;&nbsp;<span><a href="{{ url('seller/page', $page_arr['return_policy_id']) }}/edit">Edit</a></span>
                                </div>
                            </div>
                        </div>
                        </div>

                       
                        <div class="card">
                            <div class="card-header" id="headingSix">
                                <h2 class="mb-0">
                                <input type="checkbox" {{ $categoryChecked }} disabled>
                                <button class="btn btn-link collapsed collapsed-btn" type="button" data-toggle="collapse" data-target="#collapseSix" aria-expanded="false" aria-controls="collapseSix">
                                    {!! $categoryOutput !!}
                                </button>
                                </h2>
                            </div>
                            <div id="collapseSix" class="collapse" aria-labelledby="headingSix" data-parent="#accordionExample">
                                <div class="card-body">
                                    <p>If you have to check your category section.Please click edit button.&nbsp;&nbsp;<span><a href="{{ url('seller/category') }}">Click here</a></span></p>
                                </div>
                            </div>
                        </div>

                                    


                        <div class="card">
                            <div class="card-header" id="headingEight">
                                <h2 class="mb-0">
                                <input type="checkbox" {{ $variationProductChecked }} disabled>
                                <button class="btn btn-link collapsed collapsed-btn" type="button" data-toggle="collapse" data-target="#collapseEight" aria-expanded="false" aria-controls="collapseEight">
                                    {!! $variationProductOutput !!}
                                </button>
                                </h2>
                            </div>
                            <div id="collapseEight" class="collapse" aria-labelledby="headingEight" data-parent="#accordionExample">
                                <div class="card-body">
                                    <p>You can add variation attributes here.&nbsp;&nbsp;<span><a href="{{ url('seller/attribute/create') }}">Click here</a></span></p>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header" id="headingSeven">
                                <h2 class="mb-0">
                                <input type="checkbox" {{ $simpleProductChecked }} disabled>
                                <button class="btn btn-link collapsed collapsed-btn" type="button" data-toggle="collapse" data-target="#collapseSeven" aria-expanded="false" aria-controls="collapseSeven">
                                    {!! $simpleProductOutput !!}
                                </button>
                                </h2>
                            </div>
                            <div id="collapseSeven" class="collapse" aria-labelledby="headingSeven" data-parent="#accordionExample">
                                <div class="card-body">
                                    <p>You can add simple product here.&nbsp;&nbsp;<span><a href="{{ url('seller/product/create') }}">Click here</a></span></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>   
        </div>
      </div>
    </div>
  </div>


@else
 
    @if(checkListOkVal() != 1)

    <div class="row" id="ok-checklist">
        <div class="col-lg-12 col-md-12 col-sm-12">
        <div class="card card-statistic-2 ok-card-checklist">
            <!-- Close button -->  
            <p>Your store is now set up! Way to go. Keep adding products and categories as needed.&nbsp;&nbsp;&nbsp;<span class="checklist-close" style="cursor:pointer;">&#x2716;
            </span></p>
        </div>
        </div>
    </div>
    @endif

@endif

</div>
@push('script')

<script>
    $(document).ready(function () {
      $('.checklist-close').on('click', function () {
            $.ajax({
                url: "/seller/add-ok-checkist-val", 
                type: "POST", 
                data: {
                'check_list_val': 1,
                '_token': "{{csrf_token()}}"
                },
                dataType: "json", 
                success: function(data) {
                if (data) {
                    $('#ok-checklist').hide();
                }
                },
                error: function(xhr, status, error) {
                console.log(error);
                }
           });
      });
    });
</script>

@endpush

