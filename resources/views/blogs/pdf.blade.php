<!DOCTYPE html>
<html>
<head>
    <title>{{ $blog->title }}</title>
    <style>
        body {
            font-family: DejaVu Sans;
        }
        h1 {
            text-align: center;
        }
        p {
            margin-top: 20px;
        }
    </style>
</head>
<body>

    <h1>{{ $blog->title }}</h1>

    <p>{!! nl2br(e($blog->content)) !!}</p>

</body>
</html>
