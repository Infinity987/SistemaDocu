<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Acta de evaluación</title>
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
</head>

<body>
    <br>
    <table>
        <tr>
            <td colspan="1" class="text-center"
                style="border-top:none; border-left:none; border-right:none; border-bottom:1px solid #000; height: 80px;">
                <img src="{{ public_path($encargados[0]->logo) }}" style="width: 80px; display:block;" alt="Logo"
                    height="60px;">
            </td>
            <td colspan="9" class="text-center titulo-principal">ACTA DE EVALUACION DEL CURSO Y/O MODULO</td>
        </tr>
        <tr id="color">
            <td id="color" colspan="2" rowspan="2" class="negrita">Nombre de la Institución</td>
            <td id="color" colspan="5" rowspan="2" class="text-center">GAMANIEL BLANCO MURILLO</td>
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
            <td style="width:45%; font-size: 9px;" id="color" class="text-center">TRINIDAD EGUSQUIZA, HUGO</td>
            <td style="width:15%" id="color" class="negrita">R.D. Encargatura</td>
            <td style="width:15%; font-size: 9px;" id="color" class="text-center">{{ $encargados[0]->reso_direc }}</td>
        </tr>
        <tr id="color">
            <td style="width:20%" id="color" class="negrita">Docente Formador</td>
            <td style="width:45%" id="color" class="text-center">{{ $query1[0]->nombre }}</td>
            <td style="width:15%" id="color" rowspan="2" class="negrita">Fecha:</td>
            <td style="width:15%" id="color" rowspan="2"></td>
        </tr>

        <tr id="color">
            <td style="width:15%" id="color" class="negrita">Curso / Módulo:</td>
            <td style="width:15%" id="color" class="text-center">{{ $query1[0]->nombre_curso }}</td>
        </tr>
    </table>
    <br style="line-height:5px;">

    <table style="width:100%; border-collapse: collapse;">
        <thead>
            <tr id="color">
                <th id="color" style="width: 8%;">N° Orden</th>
                <th id="color" style="width: 10%;">N° Matrícula</th>
                <th id="color" style="width: 48%;">APELLIDOS Y NOMBRES (Por Orden Alfabético)</th>
                <th id="color" style="width: 7%;">Calificativo</th>
                <th id="color" style="width: 7%;">Crédito</th>
                <th id="color" style="width: 7%;">Puntaje</th>
                <th id="color" style="width: 13%;">Observaciones</th>
            </tr>
        </thead>
        <tbody>
            @php
                $cont = 0;
            @endphp
            @foreach ($listAlumnos as $listAlumno)
            @php
                $cont++;
            @endphp
                <tr id="color">
                    <td style="width: 8%;" id="color" class="text-center">{{ $cont }}</td>
                    <td style="width: 10%;" id="color" class="text-center">{{ $listAlumno->id_alumno }}</td>
                    <td style="width: 48%; font-size:9px;" id="color">{{ $listAlumno->ape_nom }}</td>
                    <td style="width: 7%;" id="color" class="text-center">
                        {{ $listAlumno->idestado_matricula == 1 ? $listAlumno->total : ($listAlumno->idestado_matricula == 2 ? 'L' : '--') }}
                    </td>
                    <td style="width: 7%;" id="color" class="text-center">
                        {{ $listAlumno->idestado_matricula == 1 ? $listAlumno->credito : ($listAlumno->idestado_matricula == 2 ? 'L' : '--') }}
                    </td>
                    <td style="width: 7%;" id="color" class="text-center">
                        {{ $listAlumno->idestado_matricula == 1 ? $listAlumno->total * $listAlumno->credito : ($listAlumno->idestado_matricula == 2 ? 'L' : '--') }}
                    <td style="width: 13%;" id="color" style="font-size: 7px;">
                        {{ $listAlumno->recomendacion_nota3 }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div style="page-break-inside: avoid; margin-top: 20px;">
        <table style="width:30%;">
            <tr>
                <td style="border: 1px solid #000; font-size: 10px; width: 25%; font-weight: bold;">Matriculados en el
                    curso o Modulo</td>
                <td style="border: 1px solid #000; font-size: 10px; width: 5%" class="text-center">
                    {{ COUNT($listAlumnos) }}</td>
            </tr>
            <tr>
                <td style="border: 1px solid #000; font-size: 10px; width: 25%; font-weight: bold;">Aprobados</td>
                <td style="border: 1px solid #000; font-size: 10px; width: 5%" class="text-center">
                    {{ COUNT(collect($listAlumnos)->where('total', '>', '10')) }}</td>
            </tr>
            <tr>
                <td style="border: 1px solid #000; font-size: 10px; width: 25%; font-weight: bold;">Desaprobados</td>
                <td style="border: 1px solid #000; font-size: 10px; width: 5%" class="text-center">
                    {{ COUNT(collect($listAlumnos)->whereNotNull('total')->where('total', '!==', '')->where('total', '<', '11')) }}</td>
            </tr>
            <tr>
                <td style="border: 1px solid #000; font-size: 10px; width: 25%; font-weight: bold;">Con Licencia</td>
                <td style="border: 1px solid #000; font-size: 10px; width: 5%" class="text-center">
                    {{ COUNT(collect($listAlumnos)->where('idestado_matricula', '=', '2')) }}</td>
            </tr>
            <tr>
                <td style="border: 1px solid #000; font-size: 10px; width: 25%; font-weight: bold;">TOTAL</td>
                <td style="border: 1px solid #000; font-size: 10px; width: 5%" class="text-center">
                    {{ COUNT($listAlumnos) }}</td>
            </tr>
        </table>
        <br><br><br><br><br>

        <table style="width:100%;">
            <tr>
                <td style="text-align:center; line-height:12px;">
                    ____________________
                    <br><span style="font-size: 10px; font-weight: bold;">{{ $encargados[0]->secre }}</span>
                    <br><span style="font-size: 9px;">Secretario Academico</span>
                    <br><span style="font-size: 9px; font-weight: bold;">Firma, Post Firma y Sello</span>
                </td>
                <td style="text-align:center; line-height:12px;">
                    ____________________
                    <br><span style="font-size: 10px; font-weight: bold;">{{ $query1[0]->nombre }}</span>
                    <br><span style="font-size: 9px;">Formador del curso y/o Modulo</span>
                    <br><span style="font-size: 9px; font-weight: bold;">Firma, Post Firma y Sello</span>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="text-align:center; padding-top:40px; line-height:12px;">
                    ____________________
                    <br><span style="font-size: 10px; font-weight: bold;">{{ $encargados[0]->direc }}</span>
                    <br><span style="font-size: 9px;">DIRECTOR(A) GENERAL </span>
                    <br><span style="font-size: 9px; font-weight: bold;">Firma, Post Firma y Sello</span>
                </td>
            </tr>
        </table>
    </div>
</body>

</html>
