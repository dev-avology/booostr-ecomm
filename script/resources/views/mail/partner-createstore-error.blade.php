@component('mail::message')
# Store setup needs another try

Hello,

We received your store creation request for **{{ $data['store_name'] ?? 'your store' }}** (club ID: {{ $data['club_id'] ?? '' }}), but the setup stopped before it could finish.

**Where it stopped:** {{ $data['failed_step'] ?? 'store setup' }}

**What happened:** {{ $data['message'] ?? '' }}

**What to do next:** {{ $data['next_step'] ?? 'Please retry the same createstore API request with the same store details.' }}

@if (!empty($data['technical_error']))
**Technical detail:** {{ $data['technical_error'] }}
@endif

Please submit the same API request again. If the store database or tenant record was already created, we will resume from the next unfinished step and complete the setup.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
