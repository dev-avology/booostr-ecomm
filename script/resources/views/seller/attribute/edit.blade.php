@extends('layouts.backend.app')

@section('content')
<section class="section">
    {{-- section title --}}
    <div class="section-header">
        <a href="{{ url('seller/attribute') }}" class="btn btn-primary mr-2">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1>{{ __('Edit Attribute') }}</h1>
    </div>
    {{-- /section title --}}

    <x-storenotification></x-storenotification>


    <div class="row">
        <div class="col-lg-12">
         <form class="ajaxform" method="post" action="{{ route('seller.attribute.update',$info->id) }}">
            @csrf
            @method('PUT')
            <div class="row">
                {{-- left side --}}
                <div class="col-lg-5">
                    <strong>{{ __('Attribute') }}</strong>
                    <p>{{ __('Add your attribute name and necessary information from here.') }}</p>
                </div>
                {{-- /left side --}}

                {{-- right side --}}
                <div class="col-lg-7">
                    <div class="card">
                        <div class="card-body">
                            <div class="from-group row mb-2">
                                <label for="" class="col-lg-12">{{ __('Name :') }} </label>
                                <div class="col-lg-12">
                                    <input type="text" name="parent_name" class="form-control" required="" placeholder="Parent Attribute" value="{{ $info->name }}">
                                </div>
                            </div>
                            <div class="from-group row mb-2">
                                <label for="" class="col-lg-12">{{ __('Type :') }} </label>
                                <div class="col-lg-12">
                                    <select class="form-control selectric" id="select_type" name="select_type">
                                      
                                        {{-- <option value="checkbox">{{ __('Checkbox (Multiple Select)') }}</option>
                                        <option value="checkbox_custom">{{ __('Checkbox (Custom Multiple Select)') }}</option> --}}
                                        <option value="radio">{{ __('Radio Button') }}</option>
                                        {{-- <option value="radio_custom">{{ __('Radio Button (Custom Single Select)') }}</option> --}}
                                        <option value="color_single">{{ __('Color Selector') }}</option>
                                        {{-- <option value="color_multi">{{ __('Color Selector (Multiple Select)') }}</option> --}}
                                        <option value="select">{{ __('Select Dropdown') }}</option>
                                    </select>
                                </div>
                            </div>
                            {{-- <div class="from-group row mb-2">
                                <label for="" class="col-lg-12">{{ __('Is Filterable ?') }} </label>
                                <div class="col-lg-12">
                                 <select class="form-control selectric" name="featured">
                                     <option value="1" @if($info->featured == 1) selected @endif>{{ __('Yes') }}</option>
                                     <option value="0" @if($info->featured == 0) selected @endif>{{ __('No') }}</option>
                                 </select>
                             </div>
                         </div> --}}
                         <input type="hidden" name="featured" value="0">

                     </div>
                     <div class="card-footer">
                        <button class="btn btn-primary basicbtn">{{ __('Save') }}</button>
                    </div>
                </div>
            </div>
            {{-- /right side --}}
        </div>
        <div class="row">
            {{-- left side --}}
            <div class="col-lg-5">
                <strong>{{ __('Attribute Values') }}</strong>
                <p>{{ __('Add your attribute value and necessary information from here') }}</p>
            </div>
            {{-- /left side --}}

            {{-- right side --}}

           @php
           $info->categories = $info->categories->sortBy('position');
           @endphp
            <div class="col-lg-7">
                <div class="card">
                    <div class="card-body child_row">
                    <input type="hidden" name="total_attributes" value="{{  $info->categories->count() }}" />


            <div id="dynamic-list" class="sortable-list">
                    @foreach($info->categories as $key => $row)
                    <div class="from-group row mb-2 attribute-value childs child{{ $key }}" data-id="{{ $key+1 }}">
                    <div class="col-lg-10">
                            <div class="col-lg-1 grid_icon drag-handle">
                                <img src="{{ asset('admin/img/Screenshot_1.png') }}" alt=""/>
                            </div>    
                            <label for="" class="d-block">{{ __('Name:') }}</label>
                            <input type="text" required name="oldchild[{{$row->id}}][{{ $key+1 }}]" class="form-control" placeholder="Enter Child Attribute Name" data-ids="{{ $key+1 }}" value="{{ $row->name }}">
                        </div>
                        <div class="col-lg-2 remove_attr_button" style="margin-top:31px;">
                            <button type="button" data-id="{{ $key }}" class="btn btn-danger trash"><i class="fa fa-trash"></i></button>
                        </div>
                    </div>
                    @endforeach
            </div>
            
                    </div>
                    <div class="card-footer">
                        <div class="from-group row mb-2 attribute-value">
                            <div class="col-lg-12">
                                <button type="button" class="btn btn-primary add_more_attr"><i class="fa fa-plus"></i> {{ __('Add Child Attribute') }}</button>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            {{-- /right side --}}
        </div>
    </form>
</div>
</div>
</section>
<input type="hidden" id="typ" value="{{$info->slug}}">
@endsection

@push('script')
<script>
"use strict";

var total={{ $info->categories->count() }};
</script>

<script src="{{ asset('admin/js/attribute-edit.js') }}"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
<script>

(function($){
    $(document).ready(function() {

        let total = {{$info->categories->count()}};

        $(".sortable-list").sortable({
            handle: '.drag-handle',
            update: function(event, ui) {
                // You can handle the update event here to save new positions, if needed
            }
        });
        $(".sortable-list").disableSelection();




        // $('#dynamic-list').sortable({
        //     handle: '.drag-handle', 
        // }).disableSelection();

        $('.add_more_attr').on('click', function(e) {
        e.preventDefault();
        total++;
        var newChildId = total;
        var child = `
        <div class="from-group row mb-2 attribute-value childs child${newChildId}" data-id="${newChildId}">
            <div class="col-lg-10">
                        <div class="col-lg-1 grid_icon drag-handle">
                            <img src="{{ asset('admin/img/Screenshot_1.png') }}" alt=""/>
                        </div>  
                <label for="" class="d-block">Name:</label>
                <input type="text" required name="newchild[${newChildId}]" class="form-control" data-ids="${newChildId}" placeholder="Enter Child Attribute Name">
            </div>
            <div class="col-lg-2 remove_attr_button" style="margin-top:31px;">
                <button type="button" data-id="${newChildId}" class="btn btn-danger trash"><i class="fa fa-trash"></i></button>
            </div>
        </div>`;
    
        $('#dynamic-list').append(child);
    });
    $(document).on('click', '.trash', function() {
        var id = $(this).data('id');
        $('.child' + id).remove();
    });
        
    });
})(jQuery);
</script>

@endpush



<style>

div#dynamic-list    .col-lg-10 {
    position: relative;
}

div#dynamic-list .col-lg-1.grid_icon {
    position: absolute;
    top: 54%;
    left: 30px;
    transform: translateX(-50%);
    opacity: 0.6;
}
div#dynamic-list .col-lg-10 input.form-control {
    padding-left: 36px !important;
}

.col-lg-1.grid_icon img {
    width: 12px;
    height: 24px;
}

.from-group.ui-sortable-helper input {
    border: 2px solid rgb(0, 192, 255);
}
button.btn.btn-danger.trash {
    height: 37px;
}

.drag-handle {
    width: 20px;
    height: 20px;
    color: white;
    text-align: center;
    line-height: 20px;
    border-radius: 50%;
    position: absolute;
    top: 10px;
    right: 10px;
    cursor: grab;
}
.sortable-item.ui-sortable-dragging .drag-handle {
    cursor: grabbing;
}
.ui-sortable-handle, .sort-handler {
    cursor: grab!important;

}
</style>

