  (function ($) {
   "use strict";
   var short=1;
   const parentAttributes= JSON.parse($('#parentattributes').val());

   console.log(parentAttributes);
  
   $('.product_type').on('change',function(){
      var product_type=$(this).val();
     // alert(product_type)
      if (product_type == 1) {
         $('.single_product_price_area').hide();
         $('.variation_select_area').show();
      }
      else{
         $('.single_product_price_area').show();
         $('.variation_select_area').hide();
      }
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
       
    
     var usedCombination = [];
    
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
                            <input type="number" class="form-control stock-qty" name="${qtyname}" value="0"/>
                         </div>
                      </div>
                      <div class="from-group col-lg-6 mb-2">
                         <label for="" >SKU : </label>
                         <div >
                            <input type="text" class="form-control" name="${skuname}" value=""/>
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
                            
                            <select class="form-control selectric manage_stock" name="${stock_manage_name}">
                               <option value="1">Yes</option>
                               <option value="0">No</option>
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
 
    





   //this event will execute when children attribute change
   // $(document).on('change','.childattribute',function (argument) {
   //    var short=$('option:selected', this).data('short');
   //    var parent_name=$('option:selected', this).data('parent');
   //    var parentid=$('option:selected', this).data('parentid');
   //    var selected_child_ids=$(this).val();

   //    // $(this).attr('name',"childattribute["+parentid+"][]");
   //    $('.selecttype'+short).attr('name',"childattribute[options]["+parentid+"][select_type]");
   //    $('.is_required'+short).attr('name',"childattribute[options]["+parentid+"][is_required]");

   //    //select name from selected options
   //    var namearray = $('option:selected', this).toArray().map(item => item.text).join();
   //    var names=namearray.split(',');

   //    var selected = $(this).find('option:selected');
   //    var unselected = $(this).find('option:not(:selected)');
      

   //    $.each(unselected, function(index, value){
   //        $('#childcard'+$(this).val()).remove();
   //     });

   //    var unselectedparentAttr = $('.parentattribute'+short).find('option:not(:selected)');
     
   //    $.each(unselectedparentAttr, function(index, value){
         
   //        $(this).attr('disabled','');
   //     });

   //    $.each(selected_child_ids, function (index, id) {
   //       if(document.getElementById("childcard"+id) == null)
   //       {
   //       var html=`<div class="card " id="childcard${id}">
   //                <div class="card-header">
   //                   <h4>${parent_name} / <span class="text-danger">  ${names[index]}</span></h4>
   //                </div>
   //                <div class="card-body row">
   //                    <div class="from-group col-lg-6">
   //                      <label for="" >Price : </label>
   //                      <div >
   //                         <input type="number" step="any" class="form-control" name="childattribute[priceoption][${parentid}][${id}][price]"/>
   //                      </div>
   //                   </div>
   //                    <div class="from-group col-lg-6  mb-2">
   //                      <label for="">Stock Quantity : </label>
   //                      <div >
   //                         <input type="number" class="form-control" name="childattribute[priceoption][${parentid}][${id}][qty]"/>
   //                      </div>
   //                   </div>
   //                   <div class="from-group col-lg-6 mb-2">
   //                      <label for="" >SKU : </label>
   //                      <div >
   //                         <input type="text" class="form-control" name="childattribute[priceoption][${parentid}][${id}][sku]"/>
   //                      </div>
   //                   </div>
   //                    <div class="from-group col-lg-6  mb-2">
   //                      <label for="" >Weight : </label>
   //                      <div >
   //                         <input type="number" step="any" class="form-control" name="childattribute[priceoption][${parentid}][${id}][weight]"/>
   //                      </div>
   //                   </div>
   //                   <div class="from-group col-lg-6  mb-2">
   //                      <label for="" >Manage Stock ? </label>
   //                      <div>
   //                         <select class="form-control select2" name="childattribute[priceoption][${parentid}][${id}][stock_manage]">
   //                            <option value="1">Yes</option>
   //                            <option value="0" selected>No</option>
   //                         </select>
   //                      </div>
   //                   </div>
   //                   <div class="from-group col-lg-6  mb-2">
   //                      <label for="" >Stock Status: </label>
   //                      <div>
   //                         <select class="form-control select2" name="childattribute[priceoption][${parentid}][${id}][stock_status]">
   //                            <option value="1">In Stock</option>
   //                            <option value="0">Out Of Stock</option>
   //                         </select>
   //                      </div>
   //                   </div>
   //                </div>
   //             </div>`;
   //      $('#children_attribute_render_area'+short).append(html);  
   //      }
   //    });
   //    $('.select2').select2()
   // });






$(document).on('click','.varition_option_delete',function(){
      var id=$(this).data('id');
      $('#childcard'+id).remove();
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




   //add more attributes
   $('.add_more_attribute').on('click',function(e){
      
     $('#children_attribute_render_area').html('');  


       if(parentAttributes.length == 0){
         alert('Please ensure that product variation attributes, such as size and color, are added. If these attributes have not been created yet, kindly create them first before adding them to the product.');
         return ;
       }


     if($('.parentattribute').length > 0){
      console.log($('.parentattribute').length);
        $('.create_variation_product').show();
        $('.add_more_attribute').show();
     }
      
      // console.log(parentAttributes.length,'ok');
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

      console.log(selected_options);
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
      var html=`<div class="renderchild${short} "><div class="card-header"><h4>Add Variation</h4><div class="card-header-action">
                      <a class="btn btn-icon btn-danger delete_attribute" data-short="${short}" href="javascript:void(0)"><i class="fas fa-times"></i></a>
                    </div></div><div class="card-body">
                     <div class="row mb-2 " >
                        <div class="col-lg-6 from-group">
                             <label for="" >Select Attribute : </label>
                           <select required name="parentattribute[]"  class="form-control parentattribute select2 parentattribute${short}">
                           <option value="" disabled  selected>Select Attribute</option>
                           ${options}
                              
                           </select>
                        </div>
                        <div class="col-lg-6 from-group">
                             <label for="" >Select Attribute Values : </label>
                           <select required   class="form-control select2 childattribute childattribute${short} multi-select" multiple="">
                              
                           </select>
                        </div>
                     </div>
                  </div>
               <div id="children_attribute_render_area${short}">
               </div></div>`;

      $('.attribute_render_area').append(html);

      $('.select2').select2();
   });

   //remove attribute area
  $(document).on('click','.delete_attribute',function (argument) {
   var id=$(this).data('short');
   $('.renderchild'+id).remove();

   $('#children_attribute_render_area').html('');  

   if($('.parentattribute').length > 0){
      $('.create_variation_product').show();
      $('.add_more_attribute').show();
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