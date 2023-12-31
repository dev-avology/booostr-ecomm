@extends('layouts.backend.app')

@section('title','Dashboard')

@section('head')
@include('layouts.backend.partials.headersection',['title'=>'Tags','button_name'=> 'Create','button_link'=> url('seller/tag/create')])
@endsection

@section('content')
<x-storenotification></x-storenotification>

<section class="section">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="form-group">
                        <label for="Search">{{ __('Search') }}</label>
                         <form method="get">
                            <div class="row">
                                <input name="src" type="text" value="{{ $request->src ?? '' }}" class="form-control col-lg-4 ml-2" placeholder="search...">
                            </div>
                         </form>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover text-center table-borderless">
                            <thead>
                                <tr>
                                    <th>{{ __('Name') }}</th>
                                    <!-- <th>{{ __('Url') }}</th> -->
                                    <th>{{ __('Created At') }}</th>
                                    <th>{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($posts as $row)
                                <tr>
                                  <td>{{ $row->name }}</td>
                                  <!-- <td>{{ url('/tag',$row->slug) }}</td> -->
                                  <td>{{ date('d-m-Y', strtotime($row->created_at)) }}</td>
                                  <td class="">
                                    <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        Action
                                    </button>
                                    <div class="dropdown-menu tags-dropdown" x-placement="bottom-start">
                                       
                                        <a class="dropdown-item has-icon text-warning" href="{{ route('seller.tag.edit', $row->id) }}"><i class="fa fa-edit"></i>{{ __('Edit') }}</a>
                                        <a class="dropdown-item has-icon delete-confirm text-danger" href="javascript:void(0)" data-id="{{$row->id}}"><i class="fa fa-trash"></i>{{ __('Delete') }}</a>
                                        <!-- Delete Form -->
                                         <form class="d-none" id="delete_form_{{ $row->id }}" action="{{ route('seller.tag.destroy', $row->id) }}" method="POST">
                                       @csrf
                                       @method('delete')
                                    </form>
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
</section>
@endsection

