<div class="loading"></div>
<!-- media model area start -->
<input type="hidden" id="base_url" value="{{ url('/') }}">
<div class="modal fade bd-example-modal-xl media-single"  tabindex="-1" aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
<div class="modal-dialog modal-xl">
   <div class="modal-content">
      <div class="modal-header">
         <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="location.reload();">
         <span aria-hidden="true">&times;</span>
         </button>
      </div>
      <div class="modal-body">
         <ul class="nav nav-tabs" id="myTab3" role="tablist" style="border-bottom: 1px solid #dee2e6;">
            <li class="nav-item">
               <a class="nav-link text-center active" id="home-tab3" data-toggle="tab" href="#upload_area" role="tab" aria-controls="home" aria-selected="true">Upload File</a>
            </li>
            <li class="nav-item">
               <a class="nav-link text-center" id="profile-tab3" data-toggle="tab" href="#media_list" role="tab" aria-controls="profile" aria-selected="false">Media List</a>
            </li>
         </ul>
         <div class="tab-content" id="myTabContent2">
            <div class="tab-pane fade show active" id="upload_area" role="tabpanel" aria-labelledby="home-tab3">
               <form method="post" action="{{ route('seller.media.store') }}" id="dropzoneinsidemodel" class="dropzone dropzones">
                  @csrf
                  <div class="fallback">
                     <input name="media" type="file"  multiple />
                  </div>
               </form>
            </div>

            <input type="hidden" class="media_url" value="{{ route('seller.media.index') }}">
            <div class="tab-pane fade" id="media_list" role="tabpanel" aria-labelledby="profile-tab3">
               <div class="row">
                  <div class="col-sm-10">

                  <div class="row gutters-sm radio-media-list media-list model-media-list">

                  </div>
                   <div >
                     <button class="btn btn-primary text-center last_link none" type="button">{{ __('Load More....') }}</button>
                  </div>

                  </div>
                  <div class="col-sm-2">
                     <div class="model-rightbar media-info-bar">
                        <img class="img-fluid media-thumbnail" id="previewimg" src="{{ asset('admin/img/img/placeholder.png') }}" alt="">
                        <div class="modal-media-info">

                           <strong>Full Url:</strong>
                           <div>
                              <input type="text" id="medialink" value="" class="form-control">
                           </div>

                           <strong>Uploaded At:</strong>
                           <div><small id="upload"></small></div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-danger media-single-dismiss" data-dismiss="modal" onclick="location.reload();">{{ __('Close') }}</button>
            <button type="button" class="btn btn-primary none radio_use" data-dismiss="modal">{{ __('Use') }}</button>
         </div>
      </div>
   </div>
</div>



