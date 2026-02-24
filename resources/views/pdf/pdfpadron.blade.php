<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ficha de Inscripción</title>
    <style>
        @page {
            margin: 0.5cm;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #ffffff;
        }

        .header {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            padding: 10px 0;
            border-bottom: 2px solid #000;
            position: relative;
        }

        .logo {
            position: absolute;
            left: 1cm;
            top: -1px;
            height: 40px;
        }

        .content {
            padding: 0 1cm;
        }

        .divider {
            border: 0;
            height: 2px;
            background: #000;
            margin: 20px 0;
        }

        .page-break {
            page-break-before: always;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        td {
            border: 1px solid #000;
            padding: 5px;
            vertical-align: top;
        }

        .img-container {
            width: 150px;
            height: 150px;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #000;
            border-radius: 5px;
            box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.5);
        }

        .img-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        h3, h4 {
            margin: 10px 0;
        }

        /* Reducir tamaño de texto en las tablas de alumnos */
table td {
    font-size: 13px;   /* más pequeño que el default */
    padding: 4px;      /* menos espacio interno */
}

/* Opcional: títulos de las columnas (APELLIDO PATERNO, etc.) un poco más destacados */
table td:first-child {
    font-weight: bold;
    font-size: 13px;
    background-color: #f9f6f2; /* crema suave para diferenciar */
}
    </style>
</head>
<body>
    @php
        $aulaAnterior = null;
        $contadorPorPagina = 0;
    @endphp

    @foreach ($datosparapadron as $item)
        {{-- Salto de página si cambia el aula --}}
        @if ($aulaAnterior !== null && $aulaAnterior !== $item->idaula)
            <div class="page-break"></div>
            @php $contadorPorPagina = 0; @endphp
        @endif

        {{-- Salto de página cada 4 alumnos --}}
        @if ($contadorPorPagina > 0 && $contadorPorPagina % 4 === 0)
            <div class="page-break"></div>
            @php $contadorPorPagina = 0; @endphp
        @endif

        {{-- Encabezado por página si es el primer alumno de la página --}}
        @if ($contadorPorPagina === 0)
            <div class="header">
                <img src="{{ public_path('logos/logo.png') }}" class="logo" alt="Logo">
                @foreach ($proceso as $item1)
                    PADRÓN DE POSTULANTES - {{ $item1->nombremodalidad }}
                @endforeach
            </div>
            <div class="content">
                <h4>AULA: {{ $item->idaula }}</h4>
            </div>
        @endif

        {{-- Ficha del postulante --}}
        <div class="content">
            <table>
                <tr>
                    <td rowspan="5" width="150px">
                       @php
    $imagen = 'fotos_postulantes/default-user.jpg';
    if (!empty($item->foto_postulante)) {
        $rutaImagen = public_path($item->foto_postulante);
        if (file_exists($rutaImagen)) {
            $imagen = $item->foto_postulante;
        }
    }
@endphp                       
                        <div class="img-container">
                            <img src="{{ public_path($imagen) }}" alt="Foto">
                        </div>
                    </td>
                    <td width="200px">APELLIDO PATERNO:</td>
                    <td>{{ $item->apellidos_pater_postulante }}</td>
                </tr>
                <tr>
                    <td>APELLIDO MATERNO:</td>
                    <td>{{ $item->apellidos_mater_postulante }}</td>
                </tr>
                <tr>
                    <td>NOMBRES:</td>
                    <td>{{ $item->nombres_postulante }}</td>
                </tr>
                <tr>
                    <td>CARRERA:</td>
                    <td>{{ $item->nombre_de_carrera }}</td>
                </tr>
                <tr>
                    <td>DNI:</td>
                    <td>{{ $item->idpostulante }}</td>
                </tr>
            </table>
            <div class="divider"></div>
        </div>

        @php
            $aulaAnterior = $item->idaula;
            $contadorPorPagina++;
        @endphp
    @endforeach
</body>
</html>