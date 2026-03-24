<style>
    * {
        font-family: sans-serif;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    #color {
        border: 1px solid #000;
        padding: 4px;
        font-size: 10px;
    }

    .text-center {
        text-align: center;
    }

    .no-border {
        border: none;
    }

    .titulo-principal {
        font-weight: bold;
        font-size: 16px;
    }

    .negrita {
        font-weight: bold;
        text-align: center;
    }

    thead {
        display: table-header-group;
    }
</style>

<table>
    <tr>
        <td colspan="1" class="text-center"
            style="border-top:none; border-left:none; border-right:none; border-bottom:1px solid #000; height: 80px;">
            <img src="{{ public_path($encargados[0]->logo) }}" style="width: 80px; display:block;" alt="Logo"
                height="60px;">
        </td>
        <td colspan="9" class="text-center titulo-principal">Registro de asistencia</td>
    </tr>
    <tr id="color">
        <td id="color" colspan="2" rowspan="2" class="negrita">Nombre de la Institución</td>
        <td id="color" colspan="5" rowspan="2" class="negrita text-center">GAMANIEL BLANCO MURILLO</td>
        <td id="color" colspan="1" class="negrita">DRE</td>
        <td id="color" colspan="2" class="text-center" style="font-size: 9px;">PASCO</td>
    </tr>
    <tr id="color">
        <td id="color" colspan="1" class="negrita">UGEL</td>
        <td id="color" colspan="2" class="text-center" style="font-size: 9px;">DREP-PASCO</td>
    </tr>

    <tr id="color">
        <td id="color" colspan="1" rowspan="" class="negrita">Código Modular</td>
        <td id="color" colspan="2" rowspan="" class="negrita">Denominación</td>
        <td id="color" colspan="1" rowspan="" class="negrita">Gestión</td>
        <td id="color" colspan="1" rowspan="" class="negrita">D.S. / R.M. de Creación y R.D. de
            Revalidación</td>
        <td id="color" colspan="1" rowspan="" class="negrita">Dirección</td>
        <td id="color" colspan="4" rowspan="" class="text-center" style="font-size: 9px;"> AV. LOS
            PROCERES N° 872</td>
    </tr>
    <tr id="color">
        <td id="color" class="text-center">0575779</td>
        <td id="color" colspan="2" class="text-center">EESP</td>
        <td id="color" class="text-center">Público</td>
        <td id="color" class="text-center">R.M. N° 0205-1981</td>
        <td id="color" class="negrita">Provincia</td>
        <td id="color" class="text-center" style="font-size: 9px;">PASCO</td>
        <td id="color" class="negrita">Distrito</td>
        <td id="color" colspan="2" class="text-center" style="font-size: 9px;">YANACANCHA</td>
    </tr>
</table>
<br style="line-height:5px;">

<table>
    <tr id="color">
        <td style="width: 20%; font-size: 10px;" class="negrita">Programa de estudio / Turno</td>
        <td style="width: 45%; font-size: 9px; border: 1px solid #000;" class="text-center">
            {{ $query1[0]->nombre_malla_curricular }}</td>
        <td style="width: 15%; " id="color" class="negrita">Periodo Académico</td>
        <td style="width: 15%; " id="color" class="text-center">{{ $query1[0]->año }} -
            {{ $query1[0]->periodo }}</td>
    </tr>
    <tr id="color">
        <td style="width:20%" id="color" class="negrita">Resolución de Autorización</td>
        <td style="width:45%; font-size: 9px;" id="color" class="text-center">RD 0875-2001-ED</td>
        <td style="width:15%" id="color" class="text-center" class="negrita"> Ciclo - Sección</td>
        <td style="width:15%" id="color" class="text-center"> {{ $query1[0]->nombre_ciclo }} -
            {{ $query1[0]->nom_seccion }}</td>
    </tr>

    <tr id="color">
        <td style="width:20%" id="color" class="negrita">Director (a) General</td>
        <td style="width:45%; font-size: 9px;" id="color" class="text-center">{{ $encargados[0]->direc }}</td>
        <td style="width:15%" id="color" class="negrita">R.D. Encargatura</td>
        <td style="width:15%; font-size: 9px;" id="color" class="text-center">
            {{ $encargados[0]->reso_direc }}</td>
    </tr>
    <tr id="color">
        <td style="width:20%" id="color" class="negrita">Docente Formador</td>
        <td style="width:45%" id="color" class="text-center">{{ $query1[0]->nombre }}</td>
        <td style="width:15%" id="color" rowspan="2" class="negrita">Fecha:</td>
        {{-- {{ $fechaHora->format('d-m-Y') }} --}}
        <td style="width:15%" id="color" class="text-center" rowspan="2">
            {{ \Carbon\Carbon::now()->format('d/m/Y') }}
        </td>
    </tr>

    <tr id="color">
        <td style="width:15%" id="color" class="negrita">Curso / Módulo:</td>
        <td style="width:15%" id="color" class="text-center">{{ $query1[0]->nombre_curso }}</td>
    </tr>
</table>
<br style="line-height:5px;">

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
