@extends('admin::index')

@section('content')
    <div class="container-fluid">
        <h1 style="text-align: center;">POSGRADOS INGENIERÍA DE SISTEMAS</h1>
        <div class="row">
            @foreach($programas as $programa)
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">{{ $programa->Nombre_del_programa }}</h3>
                        </div>
                        <div class="card-body">
                            @if($programa->Logo)
                                <img src="{{ asset('uploads/' . $programa->Logo) }}" alt="Logo del programa" class="img-fluid">
                            @endif
                            <p>{{ $programa->Descripción }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
