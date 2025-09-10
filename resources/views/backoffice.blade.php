<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ config('app.name', 'Laravel') }}</title>
    @vite('resources/js/app.js')
    @inertiaHead
</head>
<body class="font-sans antialiased">
    @inertia
</body>
</html>
