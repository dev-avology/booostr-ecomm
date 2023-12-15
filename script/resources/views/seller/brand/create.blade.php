@extends('layouts.backend.app')

@push('css')
<!-- CSS Libraries -->
  <link rel="stylesheet" href="{{ asset('admin/plugins/dropzone/dropzone.css') }}">
@endpush

@section('head')
@include('layouts.backend.partials.headersection',['title'=>'Create Brand','prev'=> url('seller/brand')])
@endsection

@section('title','Dashboard')

@section('content')

<x-storenotification></x-storenotification>

<section class="section">
   <div class="row">
      <div class="col-lg-12">
         <form class="ajaxform_with_reset" method="post" action="{{ route('seller.category.store') }}">
            @csrf
            <div class="row">
               {{-- left side --}}
               <div class="col-lg-5">
                  <strong>{{ __('Image') }}</strong>
                  <p>{{ __('Upload brand image here') }}</p>
               </div>
               {{-- /left side --}}
               {{-- right side --}}
               <div class="col-lg-7">
                  <div class="card">
                     <div class="card-body">
                        {{mediasection()}}
                     </div>
                  </div>
               </div>
               {{-- /right side --}}
            </div>
            <div class="row">
               {{-- left side --}}
                  <div class="col-lg-5">
                     <strong>{{ __('Description') }}</strong>
                     <p>{{ __('Add your brand details and necessary information from here') }}</p>
                  </div>
                  {{-- /left side --}}
                  {{-- right side --}}
                  <div class="col-lg-7">
                     <div class="card">
                        <div class="card-body">
                           <div class="from-group row mb-2">
                                 <label for="" class="col-lg-12">{{ __('Name :') }} </label>
                                 <div class="col-lg-12">
                                       <input type="text" name="name" class="form-control" placeholder="Enter Brand Name">
                                 </div>
                              </div>
                              <div class="from-group row mb-2">
                                 <label for="" class="col-lg-12">{{ __('Description :') }} </label>
                                 <div class="col-lg-12">
                                       <textarea  name="description" class="form-control h-150"></textarea>
                                 </div>
                              </div>
                              <div class="from-group row mb-2">
                           <label for="" class="col-lg-12">{{ __('Is Featured ?') }} : </label>
                           <div class="col-lg-12">
                              <select name="featured"  class="form-control">
                                 <option value="1">{{ __('Yes') }}</option>
                                 <option value="0">{{ __('No') }}</option>
                              </select>
                           </div>
                        </div>
                        <input type="hidden" name="type" value="brand">
                        <div class="row">
                           <div class="col-lg-12">
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
@endpush


