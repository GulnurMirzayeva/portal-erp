<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dashboard - Portal ERP</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>

    <h1>Portal ERP Dashboard</h1>

    <p>
        Welcome, {{ auth()->user()->name }}
    </p>

    <form method="POST" action="{{ route('logout') }}">
        @csrf

        <button type="submit">
            Logout
        </button>
    </form>

</body>
</html>