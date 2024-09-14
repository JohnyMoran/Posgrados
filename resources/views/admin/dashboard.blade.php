@extends('admin::index')

@section('content')
    <div class="container-fluid">
        <div class="dashboard-title" 
             style="background-image: url('{{ asset('/uploads/Dashboard/facultad.jpg') }}'); 
                    background-size: cover; 
                    background-position: center; 
                    height: 200px; 
                    display: flex; 
                    align-items: center; 
                    justify-content: center; 
                    font-weight: bold; 
                    font-size: 5rem; 
                    color: white; 
                    text-shadow: 10px 10px 15px rgba(0, 0, 0, 10);"> <!-- Añadí sombra para mejor visibilidad del texto -->
            POSGRADOS INGENIERÍA DE SISTEMAS
        </div>
        <div class="row">
            @foreach($programas as $programa)
                <div class="col-md-4">
                    <div class="card h-100" style="background-color: #f8f9fa; border-color: #ccc; min-height: 350px;"> <!-- Fondo gris claro -->
                        <div class="card-header" style="background-color: #ffc107; color: #343a40;"> <!-- Amarillo para el header -->
                            <h3 class="card-title">{{ $programa->Nombre_del_programa }}</h3>
                        </div>
                        <div class="card-body d-flex flex-column">
                            @if($programa->Logo)
                                <img src="{{ asset('uploads/' . $programa->Logo) }}" alt="Logo del programa" class="img-fluid mb-3" style="max-height: 150px; object-fit: contain;">
                            @endif
                            <p class="flex-grow-1">{{ $programa->Descripción }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection

