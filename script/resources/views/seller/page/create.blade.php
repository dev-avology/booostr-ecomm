@extends('layouts.backend.app')

@section('title','Create New Page')

@section('head')
@include('layouts.backend.partials.headersection',['title'=>'Create New Page','prev'=> route('seller.page.index')])
@endsection

@section('style')
<link rel="stylesheet" href="{{ asset('admin/assets/css/summernote/summernote-bs4.css') }}">
@endsection

@section('content')
<x-storenotification></x-storenotification>

<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4>{{ __('Add New Page') }}</h4>
      </div>
      
      <form method="POST" action="{{ route('seller.page.store') }}" enctype="multipart/form-data" class="ajaxform_with_reset">
        @csrf
        <div class="card-body">
          <div class="form-group">
            <label>{{ __('Page Title') }}</label>
            <input type="text" class="form-control" placeholder="Page Title" required name="title">
          </div>
          <div class="form-group">
              <label>{{ __('Page excerpt') }}</label>
              <textarea name="page_excerpt" cols="30" rows="10" class="form-control"></textarea>
          </div>
          <div class="form-group">
            <label>{{ __('Page Content') }}</label>
            <textarea name="page_content" class="summernote form-control"></textarea>
          </div>
          <div class="form-group">
            <div class="custom-file mb-3">
              <label>{{ __('Status') }}</label>
              <select name="status" class="form-control">
                <option value="1">{{ __('Active') }}</option>
                <option value="0">{{ __('Inactive') }}</option>
              </select>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-12">
              <button type="submit" class="btn btn-primary btn-lg float-right w-100 basicbtn">{{ __('Submit') }}</button>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@push('script')
  <script src="{{ asset('admin/assets/js/summernote-bs4.js') }}"></script>
  <script src="{{ asset('admin/assets/js/summernote.js') }}"></script>
@endpush
