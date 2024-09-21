@extends('layouts.auth')

@section('title', 'Restablecer Contraseña')

@section('content')
    <h2 class="text-center mb-4" style="color: #1f1f1e; font-size: 2rem">Restablecer Contraseña</h2>

    <!-- Mostrar mensajes de confirmación o error -->
    @if (session('status'))
        <div class="alert alert-success" role="alert">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger" role="alert">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('password.email') }}" method="post">
        @csrf
        <div class="mb-3">
            @if($errors->has('username'))
                <div class="alert alert-danger">{{$errors->first('username')}}</div>
            @endif
            <label for="username" class="form-label">Correo</label>
            <div class="input-group mb-3">
                <span class="input-group-text"><i class="icon-user"></i></span>
                <input type="text" class="form-control" name="username" id="username" value="{{ old('username') }}" required>
            </div>
        </div>

        <div class="clearfix">
            <button type="submit" class="btn float-end btn-secondary">Enviar enlace de restablecimiento</button>
        </div>
    </form>
@endsection



