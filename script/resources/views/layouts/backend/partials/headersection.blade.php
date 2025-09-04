<div class="section-header">
	@isset($prev)
	<div class="section-header-back">
		<a href="{{ url($prev) }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
	</div>
	@endisset
  <h1>{{ $title }}</h1>
  @isset($button_name)
  <div class="section-header-button">
  	<a href="{{ url($button_link) }}" class="btn btn-primary">{{ $button_name }}</a>
  </div>
  @endisset

  @isset($risk_level) 

      @if($risk_level == 'high')
      <div class="section-header-risk-level">
        <span class="risk-level-text1"><img src="{{ asset('uploads/security-2.png') }}" alt="High"></span>
        <span class="risk-level-text1">High Risk of fraud detected </span>
      </div>
    @endif

  @endisset

  <div class="section-header-breadcrumb">
  	  @foreach(request()->segments() as $segment)
      <div class="breadcrumb-item">{{ $segment }}</div>
      @endforeach
  </div>
</div>