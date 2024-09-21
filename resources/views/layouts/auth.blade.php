<!DOCTYPE html> 
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{config('app.name')}} | @yield('title')</title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

    <!-- Estilos -->
    <link rel="stylesheet" href="{{ Admin::asset('open-admin/css/styles.css') }}">
    <script src="{{ Admin::asset('bootstrap5/bootstrap.bundle.min.js') }}"></script>

    <style>
        body {
            background-color: #32393c;
        }
        .container {
            height: 900px; 
            max-width: 600px;
            margin-top: 20px;
        }
        .dashboard-title {
            height: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 5rem;
            color: rgb(215, 195, 11);
            text-shadow: 10px 10px 15px rgba(0, 0, 0, 0.5);
        }
        .bg-white {
            background-color: #ffffff;
            padding: 2rem;
            border-radius: 0.5rem;
            box-shadow: 0 0 50px rgba(0, 0, 0, 0.1);
        }
    </style>

    @yield('extra-css')
</head>
<body>
    <div class="d-flex justify-content-center align-items-center h-100">
        <div class="container">
            <div class="dashboard-title">
                SIPOS
            </div>

            <!-- Imagen de login -->
            <div class="text-center mb-4">
                <img src="{{ asset('uploads/login/Login.png') }}" alt="Header Image" class="img-fluid">
            </div>

            <!-- Contenido de la página -->
            <div class="bg-white p-4 shadow-sm rounded-3">
                @yield('content')
            </div>
        </div>
    </div>

    @yield('extra-js')
</body>
</html>
