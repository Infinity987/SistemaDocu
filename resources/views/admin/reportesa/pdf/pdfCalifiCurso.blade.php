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
    {{-- @dump($query) --}}
    <table>
        <tr>
            <td class="text-center">
                <img src="{{ public_path($encargados[0]->logo) }}" style="width: 80px; display:block;" alt="Logo"
                    height="60px;">
            </td>
            <td class="text-center titulo-principal">REPORTE DE CALIFICACIONES DE CURSOS Y MÓDULOS</td>
            <td class="text-center">
                <img src="{{ ($queryDa[0]->foto_postulante) ? asset($queryDa[0]->foto_postulante) : '' }}" style="width: 60px; display:block;"
                    alt="." height="60px">
            </td>
        </tr>
    </table>
    <br>
    {{-- @dump($queryDa) --}}

    <table>
        @foreach ($queryDa as $queryD)
            <tr id="color">
                <td style="width: 20%; font-size: 7px;" id="color" class="negrita">Nombre de la Institución</td>
                <td style="width: 55%; font-size: 7px;" class="negrita" class="text-center">GAMANIEL BLANCO MURILLO</td>
                <td style="width: 15%; font-size: 7px;" id="color" class="negrita">Periodo Académico</td>
                <td style="width: 10%; font-size: 7px;" class="negrita" class="text-center">{{ $queryD->aperi }}</td>
            </tr>
            <tr id="color">
                <td style="width: 20%; font-size: 7px;" id="color" class="negrita">Programa de Estudios </td>
                <td style="width: 55%; font-size: 7px;" class="negrita" class="text-center">
                    {{ $queryD->nombre_malla_curricular }}</td>
                <td style="width: 15%; font-size: 7px;" id="color" class="negrita">Ciclo - Sección </td>
                <td style="width: 10%; font-size: 7px;" class="negrita" class="text-center">{{ $queryD->cs }}</td>
            </tr>
            <tr id="color">
                <td style="width: 20%; font-size: 7px;" id="color" class="negrita">Apellidos y Nombres del
                    Estudiante</td>
                <td style="width: 55%; font-size: 7px;" class="negrita" class="text-center">{{ $queryD->ape_nom }}</td>
                <td style="width: 15%; font-size: 7px;" id="color" class="negrita">Turno</td>
                <td style="width: 10%; font-size: 7px;" class="negrita" class="text-center">
                    {{ $queryD->id_turno == 1 ? 'MAÑANA' : 'TARDE' }}</td>
            </tr>
            <tr id="color">
                <td style="width: 20%; font-size: 7px;" id="color" class="negrita">Número de Matrícula</td>
                <td style="width: 55%; font-size: 7px;" class="negrita" class="text-center">{{ $queryD->id_alumno }}
                </td>
                <td style="width: 15%; font-size: 7px;" id="color" class="negrita">Modalidad de Estudios</td>
                <td style="width: 10%; font-size: 7px;" class="negrita" class="text-center">PRESENCIAL</td>
            </tr>
        @endforeach
    </table>
    <br style="line-height:5px;">

    <table>

    </table>

    <table>
        <thead>
            <tr id="color">
                <th id="color" style="width: 3%; font-size: 7px;" class="negrita" class="text-center">N°</th>
                <th id="color" style="width: 27%; font-size: 7px;" class="negrita" class="text-center">CURSOS
                </th>
                <th id="color" style="width: 7%; font-size: 7px;" class="negrita" class="text-center">
                    COMPETENCIA</th>
                <th id="color" style="width: 11%; font-size: 7px;" class="negrita" class="text-center">NIVEL DE
                    DESENPEÑO</th>
                <th id="color" style="width: 30%; font-size: 7px;" class="negrita" class="text-center">
                    RECOMENDACIÓN / COMENTARIO</th>
                <th id="color" style="width: 11%; font-size: 7px;" class="negrita" class="text-center">
                    CALIFICACIÓN DEL CURSO</th>
                <th id="color" style="width: 11%; font-size: 7px;" class="negrita" class="text-center">
                    CALIFICACIÓN PARA EL SISTEMA DE EDUCACIÓN SUPERIOR</th>
            </tr>
        </thead>
        <tbody>
            @php
                $cont = 0;
                $ponderado = 0;
                $contNulls = 0;
            @endphp
            {{-- @dump($nullsCursos) --}}
            @foreach ($query as $notas)
                @php
                    $cont++;
                    $ponderado = $notas->total + $ponderado;
                @endphp
                <tr id="color">
                    <td rowspan="{{ 3 - $nullsCursos[$contNulls] }}" id="color" style="width: 3%; font-size: 7px;"
                        class="negrita" class="text-center">{{ $cont }}
                    </td>
                    <td rowspan="{{ 3 - $nullsCursos[$contNulls] }}" id="color"
                        style="width: 29%; font-size: 6px; font-weight: bold;">
                        {{ $notas->nombre_curso }}</td>
                    <td id="color" style="width: 9%; font-size: 5px;" class="negrita" class="text-center">
                        {{ $notas->com1 }}</td>
                    <td id="color" style="width: 9%; font-size: 7px;" class="negrita" class="text-center">
                        {{ $notas->cal1 }}
                    </td>
                    <td id="color" style="width: 30%; font-size: 6px;" class="negrita" class="text-center">
                        {{ $notas->recomendacion_nota1 }}</td>
                    <td rowspan="{{ 3 - $nullsCursos[$contNulls] }}" id="color"
                        style="width: 9%; font-size: 6px;" class="negrita" class="text-center">
                        {{-- @dump($notas->total) --}}
                        @php
                            if ($notas->total == 20 && !is_null($notas->total)) {
                                $not = 'DESTACADO';
                            } elseif (
                                ($notas->total == 15 && !is_null($notas->total)) ||
                                ($notas->total == 16 && !is_null($notas->total)) ||
                                ($notas->total == 18 && !is_null($notas->total))
                            ) {
                                $not = 'LOGRADO';
                            } elseif (
                                ($notas->total == 11 && !is_null($notas->total)) ||
                                ($notas->total == 12 && !is_null($notas->total)) ||
                                ($notas->total == 13 && !is_null($notas->total))
                            ) {
                                $not = 'EN PROCESO';
                            } elseif (
                                ($notas->total == 6 && !is_null($notas->total)) ||
                                ($notas->total == 7 && !is_null($notas->total)) ||
                                ($notas->total == 9 && !is_null($notas->total))
                            ) {
                                $not = 'INICIO';
                            } elseif (
                                ($notas->total == 4 && !is_null($notas->total)) ||
                                ($notas->total == 1 && !is_null($notas->total))
                            ) {
                                $not = 'PREVIO AL INICIO';
                            } else {
                                $not = '---';
                            }
                        @endphp
                        {{ $not }}
                    </td>
                    <td rowspan="{{ 3 - $nullsCursos[$contNulls] }}" id="color"
                        style="width: 11%; font-size: 7px;" class="negrita" class="text-center">{{ $notas->total }}
                    </td>
                </tr>

                @if ($nullsCursos[$contNulls] == 1)
                    <tr>
                        <td id="color" style="width: 9%; font-size: 5px;" class="negrita" class="text-center">
                            {{ $notas->com2 }}</td>
                        <td id="color" style="width: 9%; font-size: 7px;" class="negrita" class="text-center">
                            {{ $notas->cal2 }}
                        </td>
                        <td id="color" style="width: 30%; font-size: 6px;" class="negrita" class="text-center">
                            {{ $notas->recomendacion_nota2 }}</td>
                    </tr>
                @endif

                @if ($nullsCursos[$contNulls] == 0)
                    <tr>
                        <td id="color" style="width: 9%; font-size: 5px;" class="negrita" class="text-center">
                            {{ $notas->com2 }}</td>
                        <td id="color" style="width: 9%; font-size: 7px;" class="negrita" class="text-center">
                            {{ $notas->cal2 }}
                        </td>
                        <td id="color" style="width: 30%; font-size: 6px;" class="negrita" class="text-center">
                            {{ $notas->recomendacion_nota2 }}</td>
                    </tr>
                    <tr>
                        <td id="color" style="width: 9%; font-size: 5px;" class="negrita" class="text-center">
                            {{ $notas->com3 }}</td>
                        <td id="color" style="width: 9%; font-size: 7px;" class="negrita" class="text-center">
                            {{ $notas->cal3 }}
                        </td>
                        <td id="color" style="width: 30%; font-size: 6px;" class="negrita" class="text-center">
                            {{ $notas->recomendacion_nota3 }}</td>
                    </tr>
                @endif

                @php
                    $contNulls++;
                @endphp
            @endforeach

        </tbody>
    </table>



    <div style="position: fixed; bottom: 0; width:100%;">
        <table id="color" style="width:70%;">
            <tr class="negrita" style="line-height:6px;">
                <td id="color" style="font-size: 9px; width: 60%">
                    PROMEDIO PONDERADO DEL PERIODO ACADÉMICO</td>
                <td id="color" style="font-size: 9px; width: 40%">
                    {{ round($ponderado / $cont, 3) }}</td>
            </tr>
        </table>
        <br>
        <table style="width:100%;">
            <tr style="line-height:6px;">
                <td style="font-size: 9px; width: 5%">
                    El/La estudiante aprobó más del 75% de créditos, podrá ser promovido al siguiente semestre.</td>
            </tr>
            <tr style="line-height:6px;">
                <td style="font-size: 9px; width: 5%; font-weight: bold;">
                    Para el caso de los estudiantes del segundo al décimo ciclo académico, deben cumplir con los
                    siguientes requisitos.</td>
            </tr>

            <tr style="line-height:6px;">
                <td style="font-size: 9px; width: 5%">
                    - Haber aprobado el setenta y cinco por ciento (75%) o más de los créditos del segundo al séptimo
                    ciclo para matricularse en el siguiente..
                </td>
            </tr>
            <tr style="line-height:6px;">
                <td style="font-size: 9px; width: 5%">
                    - Haber aprobado el cien por ciento (100%) de créditos del octavo o noveno ciclos académicos, para
                    matricularse en el noveno o décimo ciclos, respectivamente.</td>
            </tr>
        </table>
        <br><br><br>

        <table style="width:100%;">
            <tr>
                <td colspan="2" style="text-align:center; padding-top:40px; line-height:12px;">
                    ______________________
                    <br><span style="font-size: 10px; font-weight: bold;">{{ $encargados[0]->secre }}</span>
                    <br><span style="font-size: 9px;">Secretario Academico</span>
                    <br><span style="font-size: 9px; font-weight: bold;">Firma, Post Firma y Sello</span>
                </td>
            </tr>
        </table>
    </div>
</body>

</html>
