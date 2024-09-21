@extends('layouts.auth')

@section('title', 'Restablecer Contraseña')

@section('content')
    <h2 class="text-center mb-4" style="color: #f1f3f6; font-size: 3rem">Restablecer Contraseña</h2>

    <form action="{{ route('password.update') }}" method="post">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <div class="mb-3">
            @if($errors->has('email'))
                <div class="alert alert-danger">{{$errors->first('email')}}</div>
            @endif
            <label for="email" class="form-label">Correo</label>
            <div class="input-group mb-3">
                <span class="input-group-text"><i class="icon-envelope"></i></span>
                <input type="email" class="form-control" name="email" id="email" value="{{ $email ?? old('email') }}" required>
            </div>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Nueva Contraseña</label>
            <div class="input-group mb-3">
                <span class="input-group-text"><i class="icon-eye"></i></span>
                <input type="password" class="form-control" name="password" id="password" required>
            </div>
        </div>

        <div class="mb-3">
            <label for="password-confirm" class="form-label">Confirmar Contraseña</label>
            <div class="input-group mb-3">
                <span class="input-group-text"><i class="icon-eye"></i></span>
                <input type="password" class="form-control" name="password_confirmation" id="password-confirm" required>
            </div>
        </div>

        <div class="clearfix">
            <button type="submit" class="btn float-end btn-secondary">Restablecer Contraseña</button>
        </div>
    </form>
@endsection


