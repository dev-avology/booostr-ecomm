@extends('layouts.backend.app')

@section('title','Page List')

@section('head')
{{-- @include('layouts.backend.partials.headersection',['title'=>'Pages','button_name'=> 'Add New','button_link'=> route('seller.page.create')]) --}}
@include('layouts.backend.partials.headersection',['title'=>'Pages','button_name'=> 'Add New','button_link'=> route('seller.page.create')])
@endsection

@section('content')
<x-storenotification></x-storenotification>

<div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped" id="table-2">
                  <thead>
                    <tr>                     
                      <th>{{ __('Title') }}</th>
                      <th>{{ __('Url') }}</th>
                      <th>{{ __('Status') }}</th>
                      <th>{{ __('Created At') }}</th>
                      <th>{{ __('Action') }}</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($posts as $row)
                    <tr>
                      <td>{{ $row->title }}</td>
                      <td>{{ url('/page',$row->slug) }}</td>
                      @if($row->status == 1)
                      <td class="text-success">Active</td>
                      @endif
                      @if($row->status == 0)
                      <td class="text-danger">Inactive</td>
                      @endif
                      <td>{{ date('d-m-Y', strtotime($row->created_at)) }}</td>
                      <td>
                        <div class="dropdown d-inline">
                          <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                          Action
                          </button>
                          <div class="dropdown-menu">
                            <a class="dropdown-item has-icon" href="{{ route('seller.page.edit', $row->id) }}"><i class="fa fa-edit"></i>{{ __('edit') }}</a>
                            @if(!in_array($row->slug,['terms-and-conditions','privacy-policy','return-policy']))
                            <a class="dropdown-item has-icon delete-confirm" href="javascript:void(0)" data-id={{ $row->id }}><i class="fa fa-trash"></i>{{ __('Delete') }}</a>
                            <!-- Delete Form -->
                            <form class="d-none" id="delete_form_{{ $row->id }}" action="{{ route('seller.page.destroy', $row->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            </form>
                            @endif
                          </div>
                        </div>
                      </td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
              {{ $posts->links('vendor.pagination.bootstrap-4') }}
            </div>
        </div>
      </div>
    </div>
</div>
@endsection
