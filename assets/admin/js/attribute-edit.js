"use strict";

var typ=$('#typ').val();
$('#select_type').val(typ);

  $('.add_more_attr').off('click').on('click', function(e) {
            e.preventDefault();
            total++;
            var newChildId = total;
            var child = `
            <div class="from-group row mb-2 attribute-value childs child${newChildId}" data-id="${newChildId}">
                <div class="col-lg-10">
                    <label for="" class="d-block">Name:</label>
                    <input type="text" required name="newchild[]" class="form-control" placeholder="Enter Child Attribute Name">
                </div>
                <div class="col-lg-2">
                    <label for="" class="text-danger">Remove</label>
                    <button type="button" data-id="${newChildId}" class="btn btn-danger trash"><i class="fa fa-trash"></i></button>
                </div>
            </div>`;
         
           $('#dynamic-list').append(child);
         
        });
        $(document).on('click', '.trash', function() {
            var id = $(this).data('id');
            $('.child' + id).remove();
        });