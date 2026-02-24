<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>FICHA DE SUBSANACION</title>
  <style>
    body {
      font-family: DejaVu Sans, sans-serif;
      font-size: 10.5px;
      color: #000000;
      margin: 25px 35px;
      line-height: 1.4;
    }

    h1 {
      text-align: center;
      font-size: 14px;
      font-weight: bold;
      text-transform: uppercase;
      margin-bottom: 4px;
    }

    h4 {
      text-align: center;
      font-size: 10px;
      margin-bottom: 6px;
    }

    /* 🎯 Estilo general para todas las tablas */
    table {
      border-collapse: collapse;
      width: 100%;
      margin-bottom: 18px;
    }

    /* 🎯 Celdas con borde */
    table th, table td {
      border: 1px solid #000;
      padding: 4px 6px;
      text-align: center;
      vertical-align: middle;
    }

    /* 🎯 Tabla sin borde (para cabeceras) */
    table.sin-borde td {
      border: none;
    }

    /* 🎯 Columnas flotadas lado a lado */
    .tabla-izquierda {
      float: left;
      width: 70%;
      margin-right: 15px;
    }

    .tabla-derecha {
      float: left;
      width: 30%;
    }

    /* 🎯 Línea encima del texto (compatible con DomPDF) */
    .firma-texto {
      display: inline-block;
      border-top: 1px solid #000;
      padding-top: 2px;
    }

  </style>
</head>
<body>

  <div style="display: flex; align-items: center; margin-bottom: 10px;">
    <div style="width: 120px;">
      <img src="{{ public_path('logos/logo.png') }}" alt="Logo" style="width: 100px;">
    </div>
    <div style="flex-grow: 1; padding-left: 15px;">
      <h1 style="margin: 0; padding: 0;">ESCUELA DE EDUCACIÓN SUPERIOR PEDAGÓGICICA PÚBLICA</h1>
      <h1 style="margin: 0; padding: 0;">Gamaniel Blanco Murillo</h1>
      <h4 style="margin: 0; padding: 0;">TF. (063)421558 - SAN JUAN - YANACANCHA - PASCO - R.M. N° 0205-81-ED 18-03-81</h4>
      <h4 style="margin: 0; padding: 0;">INSTITUCION INNOVADORA FORMANDO DOCENTES LIDERES Y ACREDITADOS</h4>
    </div>
  </div>

 <table class="si">
  <tr>
    <td><strong>Apellido Paterno</strong></td>
    <td><strong>Apellido Materno</strong></td>
    <td><strong>Nombres</strong></td>
    <td></td>
    <td><strong>Programa de Estudios</strong></td>
    <td><strong>Semestre</strong></td>
    <td></td>
    <td><strong>N° de Matrícula</strong></td>
  </tr>
  <tr>
    <td>{{ $alumno->apellidos_pater_postulante ?? '' }}</td>
    <td>{{ $alumno->apellidos_mater_postulante ?? '' }}</td>
    <td>{{ $alumno->nombres_postulante ?? '' }}</td>
    <td></td>
    <td>{{ $carrera->nombre_malla_curricular ?? '—' }}</td>
    <td>{{ $semestre->año ?? '—' }} - {{ $semestre->periodo ?? '—' }}</td>
    <td></td>
    <td>{{ $dni }}</td>
  </tr>
</table>


  <h1>FICHA POR SUBSANACION</h1>

  <table class="tabla-izquierda">
    <tr><td colspan="5"><strong>ÁREAS EN QUE SE INSCRIBE:</strong></td></tr>
    <tr>
      <td><strong>N°</strong></td><td><strong>Nombre y docente del Área</strong></td><td><strong>Horas</strong></td><td><strong>Crédito</strong></td><td><strong>ciclo</strong></td>

 @php
  $totalCreditosSubsanacion = $cursosSubsanacion->sum('credito');
  $totalHorasSubsanacion = $cursosSubsanacion->sum('horas');
@endphp

      @foreach ($cursosSubsanacion as $curso)
    

  <tr>
  <td>{{ $loop->iteration }}</td>
  <td>
    <strong>curso:</strong> {{ $curso->nombre_curso }}<br>
    <strong>docente:</strong> <em>{{ $curso->nombre }}</em>
  </td>
  <td>{{ $curso->horas ?? '—' }}</td>
  <td>{{ $curso->credito }}</td>
  <td>{{ $curso->ciclo_matricula }}</td>
</tr>


    @endforeach


    <tr>
      <td colspan="2">ÁREAS DE SUBSANACIÓN</td><td></td><td></td><td></td>
    </tr>
    <tr>
      <td colspan="2" height=18px ></td><td></td><td></td><td></td>
    </tr>
    <tr>
      <td colspan="2"  height=18px style="border-top: none; border-right: none; border-bottom: none; border-left: none;"></td><td></td><td></td><td></td>
    </tr>
    <tr>
      <td colspan="3"  height=18px style="border-top: none; border-right: none; border-bottom: none; border-left: none; text-align:right;">Total Créditos</td><td>{{ $totalCreditosSubsanacion }}</td><td></td>
    </tr>
  </table>

<table class="tabla-derecha">
  <tr>
   <td colspan="4" style="height: 70px; position: relative;">
  <div style="position: absolute; bottom: 0; width: 100%; text-align: center;">
    <span class="firma-texto">Firma del Alumno</span>
  </div>
</td>
  </tr>
  <tr>
    <td rowspan="2">Fecha</td><td>{{ now()->day }}</td><td>{{ now()->month }}</td><td>{{ now()->year }}</td>
  </tr>
  <tr>
    <td>Día</td><td>Mes</td><td>Año</td>
  </tr>
  <tr><td colspan="4" style="height 15px;"></td></tr>
  <tr><td colspan="4" style="height: 70px; position: relative;">
  <div style="position: absolute; bottom: 0; width: 100%; text-align: center;">
    <span class="firma-texto">Secretario Académico</span>
  </div>
</td>
</tr>
</table>


</body>
</html>
