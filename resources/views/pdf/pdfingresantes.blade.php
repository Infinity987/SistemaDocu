<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tabla de Resultados</title>
    <style>
        body {
    font-family: 'Times New Roman', Times, serif; /* Tipografía formal */
    background-color: #ffffff; /* Fondo limpio */
    color: #333; /* Texto legible */
    text-align: center;
    margin: 0;
    padding: 20px;
    font-size: 11px; /* Tamaño más compacto */
}

h1 {
    color: #714129; /* Color institucional */
    font-size: 1.4em; /* Más sobrio */
    margin-bottom: 20px;
    text-transform: uppercase;
}

h2 {
    font-size: 1.1em;
    margin: 5px 0;
    color: #444;
}

.resultados {
    margin: 10px auto;
    border-collapse: collapse;
    width: 90%;
    max-width: 900px;
    border: 1px solid #000;
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
}

.resultados th, .resultados td {
    padding: 6px; /* Más compacto */
    font-size: 0.9em; /* Ligero ajuste */
    text-align: center;
    border: 1px solid #444;
    line-height: 1.4;
}

.resultados thead th {
    background-color: #714129;
    color: white;
    font-size: 0.85em;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.resultados tbody tr:nth-child(odd) {
    background-color: #f4f4f9;
}

.resultados tbody tr:nth-child(even) {
    background-color: #e9ecef;
}

.resultados tbody tr:hover {
    background-color: #dae0e5;
    transition: background-color 0.2s;
}

/* Fila de carrera destacada */
.resultados .fila-carrera {
    background-color: #fdd1a3;
    color: black;
    font-weight: bold;
    text-align: left;
    padding-left: 10px;
}

/* Condición especial */
.resultados .condicion-ingreso {
    font-weight: bold;
    background-color: #d1e7dd;
    color: #0f5132;
}
    </style>
</head>
<body>
    <h1>Resultados Ingresantes</h1>

    @foreach ($procesonombre as $item)  
    <h2>{{$item->nombreproceso}}</h2>
    <h2>{{$item->nombre_modalidad}}</h2>
    @endforeach

    <table class="resultados">
        <thead>
            <tr>
                <th>CARRERA ESPECIALIDAD</th>
                <th>DNI</th>
                <th>APELLIDO PATERNO</th>
                <th>APELLIDO MATERNO</th>
                <th>NOMBRES</th>
                <th>NOTA TOTAL</th>
                <th>CONDICION</th>
            </tr>
        </thead>
        <tbody>
            @php $carreraAnterior = null; @endphp
            @foreach ($ddatospostulantesingresantes as $item)
                @if ($carreraAnterior !== $item->nombre_de_carrera)
                    @if ($carreraAnterior !== null)
                        <!-- Línea separadora destacada -->
                       
                    @endif
                    <!-- Fila para mostrar el nombre de la carrera solo una vez -->
                    <tr>
                        <td colspan="7" style="background-color: #fdd1a3; color: black; font-weight: bold; text-align: left; padding-left: 10px;">
                            Carrera: {{ $item->nombre_de_carrera }}
                        </td>
                    </tr>
                @endif
    
                <!-- Datos del postulante -->
                <tr>
                    <td>{{ $item->nombre_de_carrera }}</td>
                    <td>{{ $item->idpostulante }}</td>
                    <td>{{ $item->apellidos_pater_postulante }}</td>
                    <td>{{ $item->apellidos_mater_postulante }}</td>
                    <td>{{ $item->nombres_postulante }}</td>
                    <td>{{ $item->nota_total }}</td>
                    <td
    @if ($item->estado_ingreso === 'Alcanzó vacante')
        style="background-color: #d1e7dd; color: #0f5132; font-weight: bold;"
    @endif
>
    {{ $item->estado_ingreso }}
    @if ($item->estado_ingreso === 'Desaprobó')
        (No pasa a la segunda evaluación)
    @endif
</td>

                </tr>
    
                @php $carreraAnterior = $item->nombre_de_carrera; @endphp
            @endforeach
        </tbody>
    </table>
    
</body>
</html>