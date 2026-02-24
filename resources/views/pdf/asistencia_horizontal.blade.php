<table style="width: 160%; border-collapse: collapse; font-size: 10px;">
    <tr>
        <td class="text-center">
            <img src="{{ public_path($encargados[0]->logo) }}" style="width: 80px; display:block;" alt="Logo"
                height="50px;">
        </td>
        <td class="text-center titulo-principal">
            <h2 style="margin: 0; font-size: 25px; color: #471f00; text-decoration: underline;">Reporte de Asistencia
            </h2><br>
        </td>
        {{-- <td class="text-center">
            <img src="{{ public_path($queryDa[0]->foto_postulante) }}" style="width: 60px; display:block;"
                alt="." height="60px">
        </td> --}}
    </tr>
</table><br>

<div style="margin-bottom: 10px;">
    <p style="margin: 2px 0; font-size: 11px;">
        <strong style="color: #883302;">Carrera:</strong> {{ $nombre_de_carrera }} &nbsp;&nbsp;&nbsp;&nbsp;
        <strong style="color: #883302;">Curso:</strong> {{ $nombre_curso }} &nbsp;&nbsp;&nbsp;&nbsp;
        <strong style="color: #883302;">Ciclo:</strong> {{ $nombre_ciclo }} &nbsp;&nbsp;&nbsp;&nbsp;
        <strong style="color: #883302;">Año:</strong> {{ $año }} &nbsp;&nbsp;&nbsp;&nbsp;
        <strong style="color: #883302;">Periodo:</strong> {{ $periodo }} &nbsp;&nbsp;&nbsp;&nbsp;
        <strong style="color: #883302;">Tipo:</strong> {{ $tipodocente_curso == 1 ? 'REGULAR' : 'SUBSANACION' }}

    </p>
    <p style="margin: 2px 0; font-size: 10px;">
        <strong style="color: #883302;">Fechas:</strong> {{ $fech_ini }} al
        {{ $fech_fin }}
    </p>
</div>
@foreach ($bloquesDeFechas as $fechasChunk)
    <table style="width: 100%; border-collapse: collapse; font-size: 10px;">
        <thead>
            <tr style="background-color: #883302; color: white;">
                <th style="border: 1px solid #ccc;">#</th>
                <th style="border: 1px solid #ccc;">Apellidos y Nombres</th>
                @foreach ($fechasChunk as $fecha)
                    <th style="border: 1px solid #ccc;">{{ \Carbon\Carbon::parse($fecha)->format('d/m') }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($listAlumnos as $index => $alumno)
                <tr style="background-color: {{ $index % 2 == 0 ? '#f9f9f8' : '#ffffff' }};">
                    <td style="border: 1px solid #ccc; text-align: center;">{{ $index + 1 }}</td>
                    <td style="border: 1px solid #ccc;">&nbsp;&nbsp;
                        {{ $alumno->apellidos_pater_postulante }}
                        {{ $alumno->apellidos_mater_postulante }},
                        {{ $alumno->nombres_postulante }}
                    </td>
                    @foreach ($fechasChunk as $fecha)
                        @php
                            $key = $alumno->idincripcion_curso . '-' . $fecha;
                            $estado = $asistencias[$key]->estado ?? '-';

                            $color = match ($estado) {
                                'P' => '#28a745', // verde
                                'T' => '#ffc107', // amarillo
                                'F' => '#dc3545', // rojo
                                'J' => '#007bff', // azul
                                default => '#6c757d', // gris para vacío o desconocido
                            };
                        @endphp
                        <td style="border: 1px solid #ccc; text-align: center; color: {{ $color }};">
                            <strong>{{ $estado }}</strong>
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>

    @if (!$loop->last)
        <div style="page-break-after: always;"></div>
    @endif
@endforeach
