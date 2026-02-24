<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Nómina de Matrícula</title>
  <style>
    body {
      font-family: sans-serif;
      font-size: 11px;
      margin: 25px;
    }
    img#logopri {
      width: 75px;
    }
    table {
  
      border-collapse: collapse;
    }
    h1 {
      font-size: 16px;
      margin: 6px 0;
      text-align: center;
    }
    .info-table td {
      border: 1px solid #000;
      padding: 5px;
      font-size: 10px;
      vertical-align: middle;
    }
    .info-table td.label {
      font-weight: bold;
      background-color: #f0f0f0;
    }
    .data-header {
      background-color: #ddd;
      font-weight: bold;
    }
    .spacer {
      height: 10px;
      border: none;
    }
    .center {
      text-align: center;
    }
  </style>
</head>
<body>

 <div style="display: flex; justify-content: center; align-items: center; gap: 15px; margin-bottom: 10px;">
  <!-- Logo izquierdo -->
  <img src="{{ public_path('logos/logo.png') }}" alt="Logo" style="height: 70px; position: relative; rigth: -20px; ">
  
  <!-- Texto en el centro -->
<span style="font-weight: bold; font-size: 16px; position: relative; top: -30px; margin-right: 180px; margin-left: 80px; "> 
 NOMINA DE MATRÍCULA
</span>  
  <!-- Logo derecho -->
  <img src="{{ public_path('logos/ministerio.png') }}" alt="Ministerio" style="height: 50px; float: right; position: relative; top: -80px;">
</div>

  <table width="95%" class="info-table" style="margin-top: 15px;">
    <tr>
      <td colspan="2" rowspan="2" style="font-size: 9px;"><strong>Nombre de la Institución</strong></td>
      <td colspan="3" rowspan="2" style="font-size: 8px;"><center> ESCUELA DE EDUCACIÓN SUPERIOR PEDAGOGICA "GAMANIEL BLANCO MURILLO"</center></td>
      <td class="label"><center>DRE</center></td>
      <td colspan="2" style="font-size: 8px;"><center>PASCO</center></td>
    </tr>
    <tr>
      <td class="label" ><center>UGEL</center></td>
      <td colspan="2" style="font-size: 8px;"><center>DREP-PASCO</center></td>
    </tr>
    <tr style="text-align: center;">
      <td class="label" style="font-size: 8px;">Código Modular</td>
      <td class="label" style="font-size: 8px;">Denominación</td>
      <td class="label" style="font-size: 8px;">Gestión</td>
      <td class="label" style="font-size: 8px;">D.S. / R.M. de Creación y R.D.de Revalidación</td>
      <td class="label" style="font-size: 8px;">Dirección</td>
      <td colspan="3">AV. LOS PROCERES N° 872</td>
    </tr>
    <tr style="text-align: center;">
      <td>0575779</td>
      <td>EESP</td>
      <td>Público</td>
      <td>R.M. N° 0205 -1981</td>
      <td class="label">Provincia</td>
      <td>PASCO</td>
      <td class="label">Distrito</td>
      <td style="font-size: 8px;">YANACANCHA</td>
    </tr>
  
    </table>
    <table width="95%" class="info-table" style="margin-top: 15px;">
    <tr>
      <td colspan="2" class="label" style="font-size: 8px;">Programa de estudio / Turno</td>
       @foreach ($datospdfglobal as $global)
      <td colspan="3" style="text-align: center; font-size: 8px;" >{{$global->nombre_malla_curricular}} / TURNO: {{strtoupper($global->nombre_turno)}}</td>
           @endforeach 
      <td class="spacer" style="border-top: none; border-right: none; border-bottom: none; border-left: none;"></td>
      <td class="label"  style="text-align: center; font-size: 8px;">Periodo Académico</td>
       @foreach ($datospdfglobal as $global)
    <TD style="text-align: center;">{{$global->año}} - {{$global->periodo}}</TD>
     @endforeach 
     
    </tr>
    <tr>
      <td colspan="2" class="label" style="font-size: 8px;">Resolución de Autorización</td>
      
      @foreach ($datospdfglobal as $global)

      @if ($global->idmalla_curricular == 4 || $global->idmalla_curricular == 3 || $global->idmalla_curricular == 7 || $global->idmalla_curricular == 6 )

         <td colspan="3" style="text-align: center;" style="font-size: 8px;"><center>R.D. 0875 - 2001 - ED</center></td>

      @elseif($global->idmalla_curricular == 5)
      <td colspan="3" style="text-align: center;" style="font-size: 8px;"><center>R.D. 1809 - 2003 - ED</center></td>

      @elseif($global->idmalla_curricular == 2)
      <td colspan="3" style="text-align: center;" style="font-size: 8px;"><center>R.D. 0552 - 2013 - ED</center></td>
      
      @elseif($global->idmalla_curricular == 9)
      <td colspan="3" style="text-align: center;" style="font-size: 8px;"><center>R.D. 1113 - 2004 - ED</center></td>
      
      @endif
          
      @endforeach



   
      <td class="spacer" style="border-top: none; border-right: none; border-bottom: none; border-left: none;"></td>
      <td class="label" style="text-align: center; font-size: 8px;">Ciclo - Sección</td>
       @foreach ($datospdfglobal as $global)
    <TD style="text-align: center;">{{$global->nombre_ciclo}} - {{$global->nom_seccion}}</TD>
     @endforeach 
  
    </tr>
    <tr><td colspan="8"  style="border-top: none; border-right: none; border-bottom: none; border-left: none;"></td></tr>
    <tr>
      <td colspan="2" class="label" style="font-size: 8px;">Director(a) General</td>
      <td colspan="3" style="text-align: center;" style="font-size: 8px;"><center>TRINIDAD EGUSQUIZA, HUGO</center></td>
      <td class="spacer" style="border-top: none; border-right: none; border-bottom: none; border-left: none;"> </td>
      <td class="label" style="font-size: 8px;">R.D. Encargatura</td>
      <td style="font-size: 8px;" ><center>RDR 0181-2025-DREP</center></td>
    </tr>
 
    
  </table>

  
  <table width="95%"  class="info-table" style="margin-top: 15px;">
    <tr class="data-header center">
        <td>N° Orden</td>
        <td>N° Matrícula</td>
        <td colspan="2">Apellidos y Nombres (Orden Alfabético)</td>
        <td>Gratuito/Pagante</td>
        <td>Sexo H / M</td>
        <td>Fecha de Nacimiento</td>
        <td>Edad</td>
    </tr>
@php $contador = 1; @endphp
  @foreach($alumnos->sortBy('apellidos_pater_postulante') as $alumno)
<tr class="center">
    <td>{{ $contador }}</td>
    <td>{{ $alumno->idpostulante }}</td>
    <td  colspan="2" style="font-size: 8px;text-align: left;">
        {{ strtoupper($alumno->apellidos_pater_postulante) }}
        {{ strtoupper($alumno->apellidos_mater_postulante) }}
        {{ strtoupper($alumno->nombres_postulante) }}
    </td>
    <td>P</td>
 <td>{{ $alumno->genero_postulante == 1 ? 'M' : ($alumno->genero_postulante == 2 ? 'F' : '') }}</td>
    <td>{{ $alumno->fecha_de_nacimiento_postu }}</td>
    <td>{{ $alumno->edad_postulante }}</td>
</tr>
@php $contador++; @endphp
@endforeach
</table>

@php
   $totalHombres = $alumnos->where('genero_postulante', 1)->count();
    $totalMujeres = $alumnos->where('genero_postulante', 2)->count();
    $totalAlumnos = $alumnos->count();
    $totalGratuitos = 0; 
    $totalPagantes = $totalAlumnos; 
@endphp

<table class="info-table" style="margin-top: 15px;" width="20%">
  <tr>
    <td colspan="2"><strong>Resumen</strong></td>
    <td><strong>Total</strong></td>
  </tr>
  <tr>
    <td>Hombres</td>
    <td>{{ $totalHombres }}</td>
    <td rowspan="2">{{ $totalHombres + $totalMujeres }}</td>
  </tr>
  <tr>
    <td>Mujeres</td>
    <td>{{ $totalMujeres }}</td>
  </tr>
  <tr>
    <td>Gratuitos</td>
    <td>{{ $totalGratuitos }}</td>
    <td rowspan="2">{{ $totalGratuitos + $totalPagantes }}</td>
  </tr>
  <tr>
    <td>Pagantes</td>
    <td>{{ $totalPagantes }}</td>
  </tr>
</table>


@php
    use Carbon\Carbon;
    $fechaHora = Carbon::now()->format('d-m-Y (H:i:s)');
@endphp

<div style="text-align: right; margin-top: 30px;font-size: 8px;" >
    YANACANCHA, {{ $fechaHora }}
</div>
<BR>
  <BR>
    <BR>
  <BR>
    <BR>
  <BR>
     <BR>
    <BR>
  <BR>
      <BR>
     <BR>
    <BR>
  <BR>

<table   WIDTH="100%">
  <tr>
    <td><CENTEr>__________________________________</CENTEr></td>
     <td><center>________________________________</center></td>
      <td><center>________________________________</center></td>
  </tr>
  <tr>
    <td><CENTEr>TRINIDAD EGUSQUIZA, HUGO</CENTEr></td>
     <td><center>MAURICIO ATENCIO, MORFE BENITO</center></td>
     <td><center>V°B° DRE/UGEL</center></td>
  </tr>
  <tr>
    <td><center>DIRECTOR(A) GENERAL</center></td>
     <td><center>SECRETARIO ACADÉMICO</center></td>
  </tr>

   <tr>
    <td><center>Firma, Post Firma y Sello</center></td>
     <td><center>Firma, Post Firma y Sello</center></td>
  </tr>

</table>
<br><br><br><br>




<table class="info-table" style="text-align: left;">
  <tr>
    <td style="border-bottom: none;">
      1) La inscripción de los alumnos se hará en forma clara y en riguroso orden alfabético, cuidando de anotar. 1° Apellido Paterno, 2° Apellido Materno y 3° los nombres del matriculado, tal como figura
en su DNI o partida de nacimiento.
    </td>
    </tr>
    <tr>
    <td style="border-top: none;  border-bottom: none;">
      2) Las nóminas se confeccionarán por duplicado y se remitirán para su visación a la Dirección Regional de Educación o Unidad de Gestión Educativa Local, en caso de delegación.
    </td>
    </tr>
    
    <tr>
    <td style="border-top: none;  ">
3) Estado del registro: De acuerdo a la fecha de cierre su registro es oportuno.
    </td>
  </tr>
</table>


</body>
</html>
