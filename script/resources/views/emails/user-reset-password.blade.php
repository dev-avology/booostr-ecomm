<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ __('Password Reset Request') }}</title>
    <style>
        body {
            margin: 0;
            padding: 15px 0;
            background-color: #ffffff;
            font-family: Arial, Helvetica, sans-serif;
            color: #222222;
            font-size: 14px;
            line-height: 1.5;
        }
        .email-content {
            width: 100%;
            margin: 0;
            color: #222222;
        }
        .email-content p {
            margin: 0 0 16px;
        }
        .email-content a {
            color: #1a73e8;
            text-decoration: underline;
        }
    </style>
</head>
<body>
<div class="email-content">
    {!! $data['message'] ?? '' !!}
</div>
</body>
</html>

