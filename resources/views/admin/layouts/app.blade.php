<!DOCTYPE html>
<html lang="en">

<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>KPI Dashboard</title>

    {{-- Bootstrap CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">

    {{-- Bootstrap Icons --}}
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>

        body{
            margin:0;
            background:#f5f6fa;
            font-family:'Segoe UI', sans-serif;
        }

        .wrapper{
            display:flex;
        }

        .main-content{
            flex:1;
            padding:24px;
        }

    </style>

</head>

<body>

<div class="wrapper">

    {{-- SIDEBAR --}}
    @include('admin.partials.sidebar')

    {{-- CONTENT --}}
    <div class="main-content">

        @yield('content')

    </div>

</div>

{{-- Bootstrap JS --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>