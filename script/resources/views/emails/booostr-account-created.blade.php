<!DOCTYPE html>
<html>
<head>
  <title>{{ $data['club_name'] ?? 'Booostr' }} Website Account Created</title>
  <meta charset="UTF-8">
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
</head>

<body style="margin:0; padding:20px; background:#f3f3f3; font-family:'Roboto', sans-serif;">

  <div style="max-width:700px; margin:30px auto; background:#ffffff; border-radius:12px; overflow:hidden;">
    
    <!-- Header with Club Logo & Login Link -->
    <div style="padding:25px 30px 15px;">
      <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="width:100%; border-collapse:collapse;">
        <tr>
          <td valign="middle" align="left" style="vertical-align:middle; text-align:left;">
            <table role="presentation" border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
              <tr>
                @if(!empty($data['logo']))
                <td valign="middle" style="vertical-align:middle; padding-right:16px;">
                  <img src="{{ $data['logo'] }}" alt="{{ $data['club_name'] ?? 'Booostr' }}" height="75" style="display:block; height:75px; min-height:75px; width:auto; max-width:180px; object-fit:contain; border:0; outline:none; text-decoration:none;">
                </td>
                @endif
                <td valign="middle" style="vertical-align:middle;">
                  <span style="font-size:22px; font-weight:700; color:#111827; line-height:1.2; display:inline-block; vertical-align:middle;">
                    {{ $data['club_name'] ?? 'Booostr' }}
                  </span>
                </td>
              </tr>
            </table>
          </td>
          <td valign="middle" align="right" style="vertical-align:middle; text-align:right; white-space:nowrap; padding-left:15px;">
            <a href="{{ $data['login_url'] }}" style="font-size:16px; color:#8f8f8f; text-decoration:none; font-weight:600; display:inline-block; line-height:1;" target="_blank">Login</a>
          </td>
        </tr>
      </table>
    </div>

    <!-- Green Welcome Banner -->
    <div style="margin:20px 30px; background:#dffff2; padding:22px; border-radius:6px; text-align:center;">
      <h2 style="margin:0; font-size:24px; color:#222; font-weight:500; font-family:'Roboto', sans-serif;">
        {{ $data['club_name'] ?? 'Booostr' }} Website Account Created
      </h2>
    </div>

    <!-- Body Text -->
    <div style="padding:20px 30px 35px; color:#000; font-size:16px; font-weight:400; line-height:1.8; font-family:'Roboto', sans-serif;">

      @php
        $fullName = trim($data['name'] ?? '');
        $firstName = !empty($data['first_name']) ? $data['first_name'] : (!empty($fullName) ? explode(' ', $fullName)[0] : 'there');
        $clubTitle = $data['club_name'] ?? 'Booostr';
      @endphp

      <p><strong>Hello {{ $firstName }},</strong></p>

      <p style="margin-top:25px;">
        Your website account with {{ $clubTitle }} is created. By creating your account you can more easily track your activity with {{ $clubTitle }}.
      </p>

      <p style="margin:25px 0;">
        You can login on our website at any time here:
        <a href="{{ $data['login_url'] }}" style="color:#0a8ddb; text-decoration:underline;">LOGIN</a>
      </p>

      @if(!empty($data['support_email']))
      <p>
        If you have any questions or issues with your account you can email us here:
        <br>
        <a href="mailto:{{ $data['support_email'] }}" style="color:#0a8ddb; text-decoration:underline;">
          {{ $data['support_email'] }}
        </a>
      </p>
      @endif

      <p>Welcome Aboard!</p>

      <p>
        Thank you!<br>
        <strong>The {{ $clubTitle }} Team</strong>
      </p>

    </div>

    <!-- Co-Branding Footer -->
    <div style="text-align:center; padding:20px; font-size:12px; color:#555; line-height:1.6; font-family:'Roboto', sans-serif; border-top:1px solid #f0f0f0;">
        {{ $data['club_name'] ?? 'Booostr' }}’s website is powered by 
        <a href="https://booostr.co/" style="color:#0a8ddb; text-decoration:underline;" target="_blank">Booostr</a><br>
    
        By creating an account on 
        <a href="{{ $data['site_url'] }}" style="color:#0a8ddb; text-decoration:underline;" target="_blank">{{ $data['site_url_text'] }}</a>, 
        you have also created a user account on 
        <a href="https://booostr.co/" style="color:#0a8ddb; text-decoration:underline;" target="_blank">booostr.co</a>
    </div>

  </div>

</body>
</html>

