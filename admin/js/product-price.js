(function ($) {
   "use strict";
   var short=$('#max_short').val();
   const parentAttributes= JSON.parse($('#parentattributes').val());

   if (parentAttributes.length == $('.parentattribute').length) {
      $('.add_more_attribute').hide();
   }
  //add more attributes
   $('.add_more_attribute').on('click',function(e){
     
      if (parentAttributes.length <= $('.parentattribute').length) {
         return true;
      }

      short++;

      var selected_options=[];
      $(".parentattribute option:selected").each(function()
      {
         if ($(this).val() != '') {
             selected_options.push(parseInt($(this).val()));
         }
      
      });
      
      var options='';
      $.each(parentAttributes, function (index, row) {
         var childs=[];

         $.each(row.categories,(i, child)=>{
            var childarray={
               "id":child.id,
               "name":child.name
            };
            childs.push(childarray);
         });
         childs=JSON.stringify(childs);
         
        if (jQuery.inArray(row.id, selected_options) == -1) {
         options +=`<option value="${row.id}"  class="parentAttr${row.id}" data-parentname="${row.name}" data-short="${short}" data-childattributes='${childs}'>${row.name}</option>`;
        }
      });
      if (options == '') {
         return true;
      }   
   var html=`
   <div class="accordion renderchild${short}">
               <div class="accordion-header h-50" role="button" data-toggle="collapse" data-target="#panel-body-${short}">
                  <div class="float-left">
                     <h6>
                        <span>Add Variation</span> 
            
                    </h6>
                  </div>
                  <div class="float-right">
                     <a class="btn btn-danger btn-sm text-white option_delete" data-id="${short}"><i class="fa fa-trash"></i></a>
                  </div>
               </div>
               <div class="accordion-body collapse show" id="panel-body-${short}" data-parent="#accordion">
                  <div class="row mb-2 " >
                     <div class="col-lg-6 from-group">
                        <label for="" >Select Attribute </label>
                        <select data-type="new" required name="parentattribute[]"  class="form-control parentattribute select2 parentattribute${short}">
                         <option value="" disabled  selected>Select Attribute</option>
                            ${options}
                        </select>
                     </div>
                     <div class="col-lg-6 from-group">
                        <label for="" >Select Attribute Values : </label>
                        <select required data-type="new" class="form-control select2 childattribute childattribute${short} multi-select" multiple="">

                        </select>
                     </div>
                     <!--<div class="from-group col-lg-6  mb-2">
                        <label for="" >Select Type : </label>
                        <div >
                           <select  class="form-control selectric selecttype${short}">
                              <option value="1" >Multiple Select</option>
                              <option value="0" >Single Select</option>
                           </select>
                        </div>
                     </div>
                     <div class="from-group col-lg-6  mb-2">
                        <label for="" >Is Required ? : </label>
                        <div >
                           <select  class="form-control selectric is_required${short}">
                              <option value="1" >Yes</option>
                              <option value="0" >No</option>
                           </select>
                        </div>
                     </div> -->
                  </div>
                  <hr>
                  <div id="children_attribute_render_area${short}">
                    
                  </div>
               </div>
            </div>
   `;


      $('.renderchildVaritions').before(html);

      $('.select2').select2();
      $(".selectric").selectric({
          disableOnMobile: false,
          nativeOnMobile: false
        });
   });

   //on change event will execute when change parent attribute
   $(document).on('change','.parentattribute',function(argument) {
      var variations=$('option:selected', this).data('childattributes');
     
      var short= $('option:selected', this).data('short');
      var parent_name=$('option:selected', this).data('parentname');
      var parent_id= parseInt($(this).val()); 
      $('.renderchild'+short).removeClass('parent_area'+parent_id);
      
       if (document.getElementsByClassName("parent_area"+parent_id).length > 0) {
         
         $(this).val('');
         return true;
       }
       
      $('.childattr'+short).remove();

      $('.renderchild'+short).addClass('parent_area'+parent_id);

      $.each(variations, function (key, item) 
      {
         var html=`<option value="${item.id}" data-parentid="${parent_id}" data-parent="${parent_name}" data-short="${short}" data-attrname="${item.name}" class='child_attr${item.id} childattr${short}'>${item.name}</option>`;
         $('.childattribute'+short).append(html);
      });

     $('.multi-select').select2()

      
      if(document.getElementById("children_attribute_render_area"+short) == null)
      {
         var html=`<div id="children_attribute_render_area${short}"></div>`;
         $('.children_attribute_render_area').append(html);
      }
      $('.create_variation_product').show();
   });

   

//this event will execute when children attribute change
 $(document).on('change','.childattribute',function (argument) {
   
  if($(this).data('type') == 'new'){
   $('#children_attribute_render_area').html('');  
  }

    $('.create_variation_product').show();
    return false;
 })

   $(document).on('click','.create_variation_product',function (argument) {
      
      var used_combination = $('#used_combination').val();
      var usedCombination = used_combination.replace(/&quot;/g, '"');
        usedCombination = JSON.parse(usedCombination);
   
       var useedOptions = {};

      var selectedOptions = [];
      console.log($(".parentattribute option:selected").length);
            $(".parentattribute option:selected").each(function(index, row)
            {
               var varValue = [];

              if($(".childattribute"+ $(row).data('short') +" option:selected").length == 0){
               alert('choosed valid attribute and it\'s option');
               $(".childattribute"+ $(row).data('short')).closest('.row').css({"border": "1px solid red"});

               return true;
              }else{
               $(".childattribute"+ $(row).data('short')).closest('.row').css({"border": "0px"});
              }

               $(".childattribute"+ $(row).data('short') +" option:selected").each(function(index, row1)
               {
                  varValue.push($(row1).val());
                  useedOptions[$(row1).val()] = {'short':$(row).data('short'), 'type':$(row1).parent().data('type'),'parent':$(row1).data('parent'),'parent_id':$(row).val(),'label':$(row1).data('attrname')};
               });

            // selectedOptions[$(row).val()] = varValue;
               selectedOptions.push(varValue);

            });

         var result = crossJoin(selectedOptions);

        
         var html = '';

         $(result).each(function(variationIndex, variation) {
            
            var id = '';
            var type = ''; 

            if(Array.isArray(variation)){
               $(variation).each(function(optIndex, opt) {
                  id += opt;
                  if(`${useedOptions[opt].type}` == 'new'){
                     type =  `${useedOptions[opt].type}`;
                  }

               });
            }else{
               id += variation;
               type =  `${useedOptions[variation].type}`;
            }


           if ($.inArray(id, usedCombination) !== -1 && type !== 'new') {
               return true; 
           }
           id = ''

          html +=`<div class="accordion" id="childcard${variationIndex}new">
          <div class="accordion-header h-50" role="button" data-toggle="collapse" data-target="#panel-body-${variationIndex}new">
             <div class="float-left"> <h4>`;
          var hiddenInput = '';
           type = ''; 
          var parentid = '';
          var short= '';
          
            if(Array.isArray(variation)){
               $(variation).each(function(optIndex, opt) {
                  html +=` ${useedOptions[opt].parent} / <span class="text-danger">  ${useedOptions[opt].label}</span>`;
                  type =  `${useedOptions[opt].type}`;

                  short =  `${useedOptions[opt].short}`;
                 
                  parentid = `${useedOptions[opt].parent_id}`;

                  // if (type == 'new') {


                  // $('.selecttype'+short).attr('name',"optionattribute["+parentid+"][select_type]");
                  // $('.is_required'+short).attr('name',"optionattribute["+parentid+"][is_required]");
                  // }
                  // }else{
                  //    hiddenInput += `<input type="hidden" name="childattribute[new_priceoption][${variationIndex}][varition][${useedOptions[opt].parent_id}]" value="${opt}">`;

                  // }

                  hiddenInput += `<input type="hidden" name="childattribute[childrens][${variationIndex}][varition][${useedOptions[opt].parent_id}]" value="${opt}">`;

                  id += opt;
               });  
            }else{
               html +=` ${useedOptions[variation].parent} / <span class="text-danger">  ${useedOptions[variation].label}</span>`;
               id += variation;
               type =  `${useedOptions[variation].type}`;

               short =  `${useedOptions[variation].short}`;
                 
               parentid = `${useedOptions[variation].parent_id}`;

               // if (type == 'new') {
               // //hiddenInput += `<input type="hidden" name="childattribute[childrens][${variationIndex}][varition][${useedOptions[variation].parent_id}]" value="${variation}">`;

               // $('.selecttype'+short).attr('name',"optionattribute["+parentid+"][select_type]");
               // $('.is_required'+short).attr('name',"optionattribute["+parentid+"][is_required]");
               // }

               // }else{
               //    hiddenInput += `<input type="hidden" name="childattribute[new_priceoption][${variationIndex}][varition][${useedOptions[variation].parent_id}]" value="${variation}">`;

               // }
               hiddenInput += `<input type="hidden" name="childattribute[childrens][${variationIndex}][varition][${useedOptions[variation].parent_id}]" value="${variation}">`;

            }   


           // if (type == 'new') {
               var price_name=`childattribute[childrens][${variationIndex}][price]`;
               var qtyname=`childattribute[childrens][${variationIndex}][qty]`;
               var skuname=`childattribute[childrens][${variationIndex}][sku]`;
               var stock_manage_name=`childattribute[childrens][${variationIndex}][stock_manage]`;
               var weight_name=`childattribute[childrens][${variationIndex}][weight]`;
               var stock_status=`childattribute[childrens][${variationIndex}][stock_status]`;
            // }
            // else{
            //     var price_name=`childattribute[new_priceoption][${variationIndex}][price]`;
            //     var qtyname=`childattribute[new_priceoption][${variationIndex}][qty]`;
            //     var skuname=`childattribute[new_priceoption][${variationIndex}][sku]`;
            //     var stock_manage_name=`childattribute[new_priceoption][${variationIndex}][stock_manage]`;
            //     var weight_name=`childattribute[new_priceoption][${variationIndex}][weight]`;
            //     var stock_status=`childattribute[new_priceoption][${variationIndex}][stock_status]`;
            // }



            html +=`</h4>
            </div>
            <div class="float-right">
               <a class="btn btn-danger btn-sm text-white varition_option_delete" data-id="${variationIndex}new"><i class="fa fa-trash"></i></a>
            </div>      
         </div> <div class="accordion-body collapse show" id="panel-body-${variationIndex}new" data-parent="#children_attribute_render_area">
         <div class="row">
                     
                      <div class="from-group col-lg-6">
                        <label for="" >Price : </label>
                        <div >
                           <input type="number" required step="any" class="form-control" name="${price_name}" value="0"/>
                        </div>
                     </div>
                      
                      <div class="from-group col-lg-6  mb-2">
                        <label for="">Stock Quantity : </label>
                        <div >
                           <input type="number" class="form-control" name="${qtyname}" value="100"/>
                        </div>
                     </div>
                     <div class="from-group col-lg-6 mb-2">
                        <label for="" >SKU : </label>
                        <div >
                           <input type="text" class="form-control" name="${skuname}" value="SKU${id}"/>
                        </div>
                     </div>
                      <div class="from-group col-lg-6  mb-2">
                        <label for="" >Weight : </label>
                        <div >
                           <input type="number" step="any" class="form-control" name="${weight_name}" value="0"/>
                        </div>
                     </div>
                     <div class="from-group col-lg-6  mb-2">
                        <label for="" >Manage Stock ? </label>
                        <div >
                           
                           <select class="form-control selectric" name="${stock_manage_name}">
                              <option value="1" >Yes</option>
                              <option value="0" selected>No</option>
                           </select>
                        </div>
                     </div>
                     <div class="from-group col-lg-6  mb-2">
                        <label for="" >Stock Status: </label>
                        <div >
                          
                           <select class="form-control selectric" name="${stock_status}">
                              <option value="1" selected>In Stock</option>
                              <option value="0">Out Of Stock</option>
                           </select>
                        </div>
                     </div>
                     ${hiddenInput}
                  </div>
               </div></div>`;



        });
        
        console.log(html);
        
        $('#children_attribute_render_area').append(html);  

        $('.create_variation_product').hide();


   });

   
   $(document).on('click','.option_delete',function(){
       var id=$(this).data('id');
       $('.renderchild'+id).remove();
      $('#children_attribute_render_area').html('');  

      if($('.parentattribute').length > 0){
         $('.create_variation_product').show();
         $('.add_more_attribute').show();
      }

   });

   $(document).on('click','.varition_option_delete',function(){
      var id=$(this).data('id');
      $('#childcard'+id).remove();
  });

   $('.product_type').on('change',function(){
      var type=$(this).val();
      if (type == 1) {
         $('.single_product_area').hide();
         $('.variation_product_area').show();
      }
      else{
         $('.variation_product_area').hide();
         $('.single_product_area').show();
      }
   });

   function crossJoin(arrays) {
      // Check if there are at least two arrays
      if (arrays.length < 2) {
        console.error('At least two arrays are required for cross join.');
        return arrays[0];
      }
    
      // Initialize result with the first array
      let result = arrays[0].map(item => [item]);
    
      // Iterate over the remaining arrays
      for (let i = 1; i < arrays.length; i++) {
        const currentArray = arrays[i];
        const tempResult = [];
    
        // Iterate over the existing result and combine with the current array
        for (let j = 0; j < result.length; j++) {
          for (let k = 0; k < currentArray.length; k++) {
            // Ensure each element in result is an array before using concat
            tempResult.push(result[j].concat(currentArray[k]));
          }
        }
    
        // Update the result with the temporary result
        result = tempResult;
      }
    
      return result;
    }
  
 
})(jQuery);