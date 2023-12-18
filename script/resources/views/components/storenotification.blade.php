
<div class="store-notification">
    @php 
    $checkListArr = userChecklist();
    
    $addressChecked = ($checkListArr['address'] == 1) ? 'yes' : 'no';
    
    $taxChecked = ($checkListArr['tax'] == 1) ? 'yes' : 'no';
    
    $bannerLogoChecked = ($checkListArr['banner_logo'] == 1) ? 'yes' : 'no';
    
    $shippingMethodChecked = ($checkListArr['shipping_method'] == 1) ? 'yes' : 'no';
    
    
    $categoryChecked = ($checkListArr['category'] == 1) ? 'yes' : 'no';
    
    $simpleProductChecked = ($checkListArr['simple_product'] == 1) ? 'yes' : 'no';
    
    $variationProductChecked = ($checkListArr['variation_product'] == 1) ? 'yes' : 'no';
    $currentRoute = request()->route()->getName();
    // dump($currentRoute)
    @endphp


    
    @if((storeLaunch() != 1) || $checkListArr['address'] != 1 || ($checkListArr['tax'] != 1) || ($checkListArr['banner_logo'] != 1) || ($checkListArr['shipping_method'] != 1))
    
    <div class="row">
      <div class="col-lg-12 col-md-12 col-sm-12">
        <div class="card card-statistic-2 checklist-main">
            <p class="checklist-alert-msg">Your store is almost ready to launch! Complete required tasks to start selling online. <button type="button" class="btn btn-primary" id="checkList"> 
                {{-- data-toggle="modal" data-target="#exampleModal" --}}
                See Task List
              </button></p>
        </div>
       </div>
    </div>
    
    
    <div class="modal" id="exampleModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
          <div class="modal-content checklist-content-model">
            <div class="modal-header">
              <p class="modal-title">Complete required tasks to start selling online</p>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
            
                <div class="card_modal">
                   {{-- <p class="collapsible">Review and update</p> --}}
    
                    <div class="content">   
                        <div class="accordion checklist-collapse" id="accordionExample">
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <div class="checklist_left_side_style">
                                        
                                        <h6 class="mb-4 inner_heading_modal_body text-center">Required Task</h6>
                                        <ul>

                                        @if($addressChecked=='yes')

                                          <li>Set your store physical address<span><i class="fa fa-check" aria-hidden="true"></i>&nbsp;&nbsp;Completed</span></li> 

                                        @else

                                        <li>Set your store physical address<a href="{{ url('seller/site-settings/general/#address-section') }}" class="set_banner_btn">Set Address</a></li> 
                                        
                                        @endif


                                        @if($taxChecked=='yes')

                                          <li>Set your store sales tax rate<span><i class="fa fa-check" aria-hidden="true"></i>&nbsp;&nbsp;Completed</span></li> 

                                        @else

                                        <li>Set your store sales tax rate<a href="{{ url('seller/site-settings/general/#tax-section') }}" class="set_banner_btn">Set Sales Tax</a></li> 
                                        
                                        @endif


                                        @if($shippingMethodChecked=='yes')

                                        <li>Set your store shipping rates<span><i class="fa fa-check" aria-hidden="true"></i>&nbsp;&nbsp;Completed</span></li> 

                                        @else

                                        <li>Set your store shipping rates<a href="{{ url('seller/site-settings/general/#store-shipping-section') }}" class="set_banner_btn">Set Ship Rates</a></li> 
                                        
                                        @endif


                                        @if($bannerLogoChecked=='yes')

                                        <li>Set your initial store main banner<span><i class="fa fa-check" aria-hidden="true"></i>&nbsp;&nbsp;Completed</span></li> 

                                        @else

                                        <li>Set your initial store main banner<a href="{{ url('seller/site-settings/general/#store-banner-section') }}" class="set_banner_btn">Set Banner</a></li> 
                                        
                                        @endif

                                        </ul>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="checklist_right_side_style">
                                        <h6 class="mb-4 inner_heading_right_side text-center">Suggested Task</h6>
    
                                        <ul>

                                          @if($categoryChecked=='yes')

                                          <li>Set up your store product categories<span><i class="fa fa-check" aria-hidden="true"></i>&nbsp;&nbsp;Completed</span></li> 
                                             
                                          @else

                                          <li>Set up your store product categories<a href="{{ url('seller/category/create') }}" class="set_banner_btn">Set Categories</a></li>  

                                          @endif



                                          @if($variationProductChecked=='yes')

                                          <li>Add any variant product attributes<span><i class="fa fa-check" aria-hidden="true"></i>&nbsp;&nbsp;Completed</span></li> 
                                             
                                          @else
                                          
                                          <li>Add any variant product attributes<a href="{{ url('seller/attribute/create') }}" class="set_banner_btn">Set Attributes</a></li>  

                                          @endif



                                          @if($simpleProductChecked=='yes')

                                          <li>Add any variant product attributes<span><i class="fa fa-check" aria-hidden="true"></i>&nbsp;&nbsp;Completed</span></li> 
                                             
                                          @else
                                          
                                          <li>Add your first product(s)<a href="{{ url('seller/product/create') }}" class="set_banner_btn">Add Product(s)</a></li>  

                                          @endif

                                        </ul>
                                    </div>
                                </div>
                            </div>
                             
                            <div class="bottom_btn_modal_parent">
                                <div class="row">
                                    <div class="col">
                                        <div class="bottom_modal_btn text-center mt-4">
                                          @if($addressChecked=='yes' && $taxChecked=='yes' && $shippingMethodChecked=='yes' && $bannerLogoChecked=='yes')
                                            <a style="cursor:pointer;" class="store_launch_click ">Make Your Store Live - Launch</a>
                                          @else
                                          <a class="checklist-disable-btn">Complete Required tasks To Launch</a>
                                          @endif  
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
          console.log('$currentRoute');

          @if(($addressChecked=='no' || $taxChecked=='no' || $shippingMethodChecked=='no' || $bannerLogoChecked=='no' ) &&  $currentRoute == 'seller.dashboard')
            $("#exampleModal").modal("show");
            $(".modal-backdrop").remove(); 
           @endif

           

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


          $('.store_launch_click').on('click', function () {
                $.ajax({
                    url: "/seller/checklist-store-launch", 
                    type: "POST", 
                    data: {
                    'check_list_val': 1,
                    '_token': "{{csrf_token()}}"
                    },
                    dataType: "json", 
                    success: function(data) {
                    if (data) {
                      console.log(data);
                        // $('#exampleModal').hide();
                        location.reload();

                    }
                    },
                    error: function(xhr, status, error) {
                    console.log(error);
                    }
               });
          });


        });
    </script>
    <script>
        $("#checkList").on("click",function(){
          $("#exampleModal").modal("show");
          $(".modal-backdrop").remove(); 
        })
    </script>
    @endpush
    
    