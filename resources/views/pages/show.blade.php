<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $template->name ?? 'Page' }}</title>
    <style>
        {!! $template->getCssAttribute() !!}
    </style>
</head>
<body>
{!! $html !!}
</body>
</html>
