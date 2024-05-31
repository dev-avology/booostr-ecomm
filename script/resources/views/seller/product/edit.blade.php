@push('css')
<link rel="stylesheet" href="{{ asset('admin/css/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('admin/assets/css/summernote/summernote-bs4.css') }}">
@endpush

@section('head')

@include('layouts.backend.partials.headersection',['title'=>'Edit Product - '.$info->title,'prev'=> url('seller/product')])
<x-storenotification></x-storenotification>

@endsection


@extends('seller.product.productmain',['product_id'=>$id])

@section('product_content')

<form class="ajaxform" action="{{ route('seller.product.update',$info->id) }}" method="post">
    @csrf
    @method("PUT")
    <div class="tab-pane fade show active" id="general_info" role="tabpanel" aria-labelledby="home-tab4">
        <div class="from-group row mb-2">
            <label for="" class="col-lg-12">{{ __('Name :') }} </label>
            <div class="col-lg-12">
                <input type="text" name="name" required="" class="form-control" placeholder="Enter Product Name" value="{{ $info->title }}">
            </div>
        </div>
        <div class="from-group row mb-2">
            <label for="" class="col-lg-12">{{ __('Slug :') }} </label>
            <div class="col-lg-12">
                <input type="text" name="slug" required="" class="form-control" placeholder="Enter Product Slug" value="{{ $info->slug }}">
            </div>
        </div>
        <div class="from-group row mb-2">
            <label for="" class="col-lg-12">{{ __('Description :') }} </label>
            <div class="col-lg-12">
                <textarea name="short_description" class="form-control h-150">{{ $info->excerpt->value ?? '' }}</textarea>
            </div>
        </div>
        {{-- <div class="from-group row mb-2">
            <label for="" class="col-lg-12">{{ __('Long Description :') }} </label>
            <div class="col-lg-12">
                <textarea name="long_description" class="form-control summernote">{{ $info->description->value ?? '' }}</textarea>
            </div>
        </div> --}}
        <div class="from-group row mb-2">
            <label for="" class="col-lg-12">{{ __('Select Product Category') }} : </label>
            <div class="col-lg-12">
                <select name="categories[]" multiple="" class="select2 form-control">
                    {{NastedCategoryList('category',$selected_categories)}}
                </select>
            </div>
        </div>
        <div class="from-group row mb-2">
            <label for="" class="col-lg-12">{{ __('Select Product Type') }} : </label>
            <div class="col-lg-12">
                <select name="categories[]" class="selectric form-control">
                    @if(isset($product_type) && !empty($product_type))
                        @foreach($product_type as $row)
                         <option value="{{ $row->id }}" @if(in_array($row->id,$selected_categories)) selected @endif>{{ $row->name }}</option>
                        @endforeach
                        @endif;
                </select>

             
            </div>
        </div>

        <div class="from-group row mb-2">
          <label for="" class="col-lg-12">{{ __('Select a form to connect (optional)') }} : </label>
          <div class="col-lg-12">
            <?php
              $formConnectorVal = $info->formType->value ?? '';
            ?>
              <select name="form_type" class="selectric form-control" id="form_type">
              <option value="0" @if($formConnectorVal == '' ) selected @endif >{{ __('No form to connect to this product') }}</option>
                @if($formApiData)
                  @foreach ($formApiData as $item)
                    <option data-fields = "{{$item->fields}}" value="{{$item->id}}" @if($item->id == $formConnectorVal) selected @endif>{{$item->title}}</option>         
                  @endforeach
                @endif
              </select>
              <input type="hidden" name="form_fields" id="form_fields" value="{{$info->formFields->value ?? ''}}">
          </div>
        </div>

        <div class="from-group row mb-2">
            <label for="" class="col-lg-12">{{ __('Select Product Brand') }} : </label>
            <div class="col-lg-12">
                <select name="categories[]"  multiple="" class="form-control select2">

                    {{NastedCategoryList('brand',$selected_categories)}}
                </select>
            </div>
        </div>



        <div class="from-group row mb-2">
            <label for="" class="col-lg-12">{{ __('Select Product Tags') }} : </label>
            <div class="col-lg-12">
                <select name="categories[]" multiple="" class="form-control select2"  id="mySelect2">

                    {{NastedCategoryList('tag',$selected_categories)}}
                </select>
                <input type="hidden" name="type" value="general">
            </div>
        </div>

        

        <div class="from-group row mb-2">
            <label for="" class="col-lg-12">{{ __('Select Featured Type') }} : </label>
            <div class="col-lg-12">
                <select name="categories[]" class="form-control selectric">
                    <option value="" selected=""></option>
                    @foreach($features as $row)
                    <option value="{{ $row->id }}" @if(in_array($row->id,$selected_categories)) selected @endif>{{ $row->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="from-group row mb-2">
            <label for="" class="col-lg-12">{{ __('List On') }} : </label>
            <div class="col-lg-4">
                <input type="radio" name="list_type" value="0" @if($info->list_type == 0) checked @endif /> All
            </div>
            <div class="col-lg-4">
                <input type="radio" name="list_type" value="1" @if($info->list_type == 1) checked @endif /> Web Only
            </div>
            <div class="col-lg-4">
                <input type="radio" name="list_type" value="2" @if($info->list_type == 2) checked @endif /> POS Only
            </div>
        </div>
        <div class="from-group row mb-2">
            <label for="" class="col-lg-12">{{ __('Status') }} : </label>
            <div class="col-lg-12">
                <select name="status" class="form-control selectric">
                    <option value="1" @if($info->status == 1) selected @endif>{{ __('Publish') }}</option>
                    <option value="0" @if($info->status == 0) selected @endif>{{ __('Draft') }}</option>
                </select>
            </div>
        </div>
        <div class="from-group  mb-2">
            <button class="btn btn-primary basicbtn col-lg-2" type="submit"><i class="far fa-save"></i> {{ __('Update') }}</button>
        </div>
    </div>
</form>
@endsection

@push('script')
<script src="{{ asset('admin/js/select2.min.js') }}"></script>
<script src="{{ asset('admin/assets/js/summernote-bs4.js') }}"></script>
<script src="{{ asset('admin/assets/js/summernote.js') }}"></script>


<script src="{{ asset('admin/plugins/dropzone/dropzone.min.js') }}"></script>
<script src="{{ asset('admin/plugins/dropzone/components-multiple-upload.js') }}"></script>

<script>
    $(document).ready(function() {
      $('#form_type').on('change', function() {
          var selectedOption = $(this).find('option:selected');
          console.log(selectedOption);
          $('#form_fields').val(selectedOption.data('fields'));
      });

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
          
          var newOptionText = selectedOption.text;
          var type = "create_dynamic_option";
          
          $.ajax({
            url: "/seller/add-jquery-tag", 
            type: "POST", 
            data: {
              'tag_name': newOptionText,
              'type': type,
              '_token': "{{csrf_token()}}"
            },
            dataType: "json", 
            success: function(data) {
             //  console.log(data);
               if (data) {
                  
                   var newOptionId = data;
                   $('#mySelect2').find('option[value="' + newOptionText + '"]').remove();
                
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
         
          var newOptionText = selectedOption.text;
          var type = "create_dynamic_option";
         
          $.ajax({
            url: "/seller/add-jquery-brand", 
            type: "POST", 
            data: {
              'brand_name': newOptionText,
              'type': type,
              '_token': "{{csrf_token()}}"
            },
            dataType: "json", 
            success: function(data) {
             //  console.log(data);
               if (data) {
                  
                var newOptionId = data;
                $('#mySelect3').find('option[value="' + newOptionText + '"]').remove();
                
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
