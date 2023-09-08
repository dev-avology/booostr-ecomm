<div class="loading"></div>
<!-- media model area start -->
<input type="hidden" id="base_url" value="{{ url('/') }}">
<div class="modal fade bd-example-modal-xl media-single"  tabindex="-1" aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
<div class="modal-dialog modal-xl">
   <div class="modal-content">
      <div class="modal-header">
         <!-- <ul class="nav nav-tabs" id="myTab3" role="tablist">
            <li class="nav-item">
               <a class="nav-link active" id="home-tab3" data-toggle="tab" href="#upload_area" role="tab" aria-controls="home" aria-selected="true">Upload File</a>
            </li>
            <li class="nav-item">
               <a class="nav-link" id="profile-tab3" data-toggle="tab" href="#media_list" role="tab" aria-controls="profile" aria-selected="false">Media List</a>
            </li>
         </ul> -->
         <div class="nav nav-tabs" id="myTab3" role="tablist">
            <button class="nav-link active" id="home-tab3" data-bs-toggle="tab" data-bs-target="#upload_area" type="button" role="tab" aria-controls="home" aria-selected="true">Upload File</button>
            <button class="nav-link" id="profile-tab3" data-bs-toggle="tab" data-bs-target="#media_list" type="button" role="tab" aria-controls="profile" aria-selected="false">Media List</button>
         </div>
         <button type="button" class="close" data-dismiss="modal" aria-label="Close">
         <span aria-hidden="true">&times;</span>
         </button>
      </div>
      <div class="modal-body">
         <div class="tab-content" id="myTabContent2">
            <div class="tab-pane fade show active" id="upload_area" role="tabpanel" aria-labelledby="home-tab3">
               <form method="post" action="{{ route('seller.media.store') }}" class="dropzone dropzones">
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

            <nav>
               <div class="nav nav-tabs" id="nav-tab" role="tablist">
                  <button class="nav-link active" id="nav-home-tab" data-bs-toggle="tab" data-bs-target="#nav-home" type="button" role="tab" aria-controls="nav-home" aria-selected="true">Home</button>
                  <button class="nav-link" id="nav-profile-tab" data-bs-toggle="tab" data-bs-target="#nav-profile" type="button" role="tab" aria-controls="nav-profile" aria-selected="false">Profile</button>
                  <button class="nav-link" id="nav-contact-tab" data-bs-toggle="tab" data-bs-target="#nav-contact" type="button" role="tab" aria-controls="nav-contact" aria-selected="false">Contact</button>
               </div>
               </nav>
               <div class="tab-content" id="nav-tabContent">
                  <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">Home...</div>
                  <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">profile...</div>
                  <div class="tab-pane fade" id="nav-contact" role="tabpanel" aria-labelledby="nav-contact-tab">contact...</div>
               </div>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-danger media-single-dismiss" data-dismiss="modal">{{ __('Close') }}</button>
            <button type="button" class="btn btn-primary none radio_use" data-dismiss="modal">{{ __('Use') }}</button>
         </div>
      </div>
   </div>
</div>



