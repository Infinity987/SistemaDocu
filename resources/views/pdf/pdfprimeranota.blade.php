<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tabla de Resultados</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif; /* Tipografía clásica y formal */
            background-color: #ffffff; /* Fondo blanco para mayor claridad */
            color: #333; /* Texto en gris oscuro */
            text-align: center;
            margin: 0;
            padding: 20px;
        }
    
        .resultados {
            margin: 10px auto;
            border-collapse: collapse; /* Bordes compartidos */
            width: 90%; /* Ancho más amplio para formalidad */
            max-width: 900px; /* Ajuste para pantallas más grandes */
            border: 1px solid #000; /* Borde exterior negro */
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15); /* Sombra ligera */
        }
    
        .resultados th, .resultados td {
             padding: 6px; /* antes 10px */
    font-size: 0.95em; /* antes 0.95em */

            text-align: center;
            border: 1px solid #444; /* Bordes más definidos */
        }
    
        .resultados thead th {
            background-color: #714129; /* Azul oscuro formal */
            color: white; /* Texto blanco */
            font-size: 0.85em; /* antes 1.2em */

            text-transform: uppercase; /* Letras en mayúsculas para encabezados */
            letter-spacing: 0.05em; /* Espaciado elegante */
        }
    
        .resultados tbody tr:nth-child(odd) {
            background-color: #f4f4f9; /* Gris claro */
        }
    
        .resultados tbody tr:nth-child(even) {
            background-color: #e9ecef; /* Un gris intermedio */
        }
    
        .resultados tbody tr:hover {
            background-color: #dae0e5; /* Gris un poco más oscuro en el hover */
            transition: background-color 0.2s; /* Suavidad en el cambio de color */
        }
    
        .resultados td {
            font-size: 0.95em; /* Texto más pequeño para un look profesional */
            line-height: 1.5; /* Espaciado entre líneas */
        }
    
        h1 {
            color: #714129; /* Azul oscuro, combina con la tabla */
            margin-bottom: 30px; /* Espaciado debajo del título */
            text-transform: uppercase; /* Título en mayúsculas */
            font-size: 1.8em; /* Tamaño destacado */
        }

        body {
    font-size: 11px; /* antes no tenía tamaño definido */
}
h1 {
    font-size: 1.4em; /* antes 1.8em */
    margin-bottom: 20px;
}

h2 {
    font-size: 1.1em; /* puedes agregar esta regla si no está definida */
    margin: 5px 0;
}
    </style>
    
</head>
<body>
    <h1>CUADRO DE RESULTADOS DE LA PRIMERA FASE: PRUEBA DE CONOCIMIENTOS</h1>
    @foreach ($nombreproceso as $item)  
    <h2>{{$item->nombreproceso}}</h2>
    <h2>{{$item->nombre_modalidad}}</h2>
    @endforeach
    <table  class="resultados">
        <thead>
            <tr>
                <th>CARRERA ESPECIALIDAD</th>
                <th>DNI</th>
                <th>APELLIDO PATERNO</th>
                <th>APELLIDO MATERNO</th>
                <th>NOMBRES</th>
                <th>PROMEDIO PRIMERA FASE</th>
                <th>CONDICION</th>
            </tr>
        </thead>
        <tbody>
            @php $carreraAnterior = null; @endphp
            @foreach ($datospostulantesprimera as $item)
                @if ($carreraAnterior !== $item->nombre_de_carrera)
                    @if ($carreraAnterior !== null)
                        <!-- Línea separadora más destacada -->
                       
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
                    <td >{{ $item->nombre_de_carrera }}</td>
                    <td>{{ $item->idpostulante }}</td>
                    <td>{{ $item->apellidos_pater_postulante }}</td>
                    <td>{{ $item->apellidos_mater_postulante }}</td>
                    <td>{{ $item->nombres_postulante }}</td>
                    @if ($item->asistencia == "No se presentó")
                    <td>NSP</td>
                    @else
                    <td>{{ $item->nota1 / 2.5 }}</td> 
                    @endif
                    
                    @if ($item->asistencia == "No se presentó")
                        <TD style="background-color: #eba3a3; color: #000000; font-weight: bold;" >NSP</TD>
                    @else
                    <td
                        @if ($item->estado_apro_desa=== 'Aprobó')
                        style="background-color: #d1e7dd; color: #0f5132; font-weight: bold;"
                        @elseif($item->estado_apro_desa=== 'Desaprobó')
                        style="background-color: #eba3a3; color: #000000; font-weight: bold;"

                        @endif 
                    >

                        {{ $item->estado_apro_desa }}(Apto para entrevista)
                        @if ($item->estado_apro_desa === 'Desaprobó')
                            (No pasa a la segunda evaluación)
                        @endif
                    </td> 
                    @endif
                                       
                    
                </tr>
        
                @php $carreraAnterior = $item->nombre_de_carrera; @endphp
            @endforeach
        </tbody>
        
    </table>
</body>
</html>