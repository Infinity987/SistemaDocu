<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Nómina de Matrícula</title>
  <style>

     @page {
  margin-top: 40px;
  margin-bottom: 40px; 
    margin-left: 50px; 
   
}   

     header {
            position: fixed;
            top: -20px;
            left: 93%;
            right: 0;
            height: 0.5cm; /* Ajusta la altura según sea necesario */            
            padding: 0.5cm;
        }


         footer {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    height: 1cm;
    font-size: 10px;
    font-family: sans-serif;
  }



 .verticalText div {
        writing-mode: vertical-rl;
    transform: rotate(-90deg);
    transform-origin: center;   
    white-space: normal; 
    max-width: 30px;
    
      
}   
    .verticalText2 div {
    transform: rotate(-90deg);
    transform-origin: center;
    width: max-content;
    padding: 0px;
    max-width: 50px;
               /* Controla el ancho real de la celda */
      
}
    body {
      font-family: sans-serif;
      font-size: 11px;
      margin: 25px;
    }
    img#logopri {
      width: 90px;
    }
    table {
     
      border-collapse: collapse;
    }
    h1 {
      font-size: 14.75px;
      margin: 6px 0;
      text-align: center;
    }
    .info-table td {
      border: 1px solid #000;
      padding: 5px;
      font-size: 10px;
     
    }
    .info-table td.label {
      font-weight: bold;
      background-color: #f0f0f0;
    }


    .info-table3 td {
      border: 1px solid #000;

      font-size: 8px;
     
    }
    .info-table3 td.label {
 
      background-color: #f0f0f0;
    }

.info-table2 td {
      border: 1px solid #000;
      padding: 2.5px;
      font-size: 10px;
     
    }
    .info-table2 td.label {

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
   .sideways-lr {
  writing-mode: vertical-rl;
  text-orientation: mixed;
}
.verticalText {
  writing-mode: vertical-rl;
  text-orientation: mixed;
  white-space: normal;   /* permite saltos */
  word-break: break-word; /* fuerza a cortar si es largo */
  max-height: 150px; /* limita la altura de la celda */
}



  </style>
</head>
<body>
<header style="font-size: 8px">{{ \Carbon\Carbon::now()->format('d/m/Y') }}</header>

  

  <table>
    <tr>
      <td style="width: 15%; text-align: center;">
        <img id="logopri" src="{{ public_path('logos/logo.png') }}" alt="Logo">
      </td>
      <td style="width: 85%;">
        <h1>ACTA CONSOLIDADA DE EVALUACIÓN DEL DESEMPEÑO ACADÉMICO DEL CICLO</h1>
        @if ($tipoMatricula == 2)
  <h1>PROCESO DE SUBSANACIÓN</h1>
@endif
         
      </td>
    </tr>
  </table>

  
 <table class="info-table2" style="margin-top: 15px; width: 18.2cm;"  >
  <tr >
    <td style="height:8px; font-size: 8px"><strong>Nombre de la Institución</strong></td>
    <td style="height:8px; font-size: 8px"><center>GAMANIEL BLANCO MURILLO</td>
    <td style="height:8px; font-size: 8px"><strong>Código Modular</strong></td>
    <td style="height:8px; font-size: 8px"><center>0575779</td>    
  </tr>
  <tr>
    <td style="height:8px; font-size: 8px"><STROng>R.M. Licenciamiento | R.D. Revalidación</STROng></td>
    <td style="height:8px; font-size: 8px"><center>R.M. N°283-2020-MINEDU</td>
    <td style="height:8px; font-size: 8px"><STROng>Dirección</STROng></td>
    <td style="height:8px; font-size: 8px"><center>AV. LOS PROCERES N° 777</td>    
  </tr>
  <tr>
    <td style="height:8px; font-size: 8px"><strong>Dirección General</strong></td>
    <td style="height:8px; font-size: 8px"><center>TRINIDAD EGUSQUIZA, HUGO</td>
    <td style="height:8px; font-size: 8px"><strong>RD. Encargatura</strong></td>
    <td style="height:8px; font-size: 8px"><center>RDR 0181-2025-DREP</td>    
  </tr>
  <tr>
    <td style="height:8px; font-size: 8px"><strong>Programa de Estudios</strong></td>
    @foreach ($datospdfglobal as $global)
         <td style="height:8px; font-size: 8px"><center>{{$global->nombre_malla_curricular}}</td>
    @endforeach
   
    <td style="height:8px; font-size: 8px"><strong>Periodo Académico</strong></td>
      @foreach ($datospdfglobal as $global)
         <td style="height:8px; font-size: 8px"><center>{{$global->año}}-{{$global->periodo}}</td>
    @endforeach  
  </tr>
  <tr>
    <td style="height:8px; font-size: 8px"><strong>Ciclo - Sección</strong></td>
    
     @foreach ($datospdfglobal as $global)
         <td style="height:8px; font-size: 8px"><center>{{$global->nombre_ciclo}} - "A"</td>
    @endforeach 
    <td style="height:8px; font-size: 8px"><strong>Número de Estudiantes</strong></td>
      @foreach ($datospdfglobal as $global)
         <td style="height:8px; font-size: 8px"><center>{{$global->cantidad_alumnos}}</td>
    @endforeach 
    
  </tr>
  <tr>
    <td style="height:8px; font-size: 8px"><strong>Modalidad de Estudios</strong></td>
    <td style="height:8px; font-size: 8px"><center>PRESENCIAL</td>
    <td style="height:8px; font-size: 8px"><strong>Turno</strong></td>
      @foreach ($datospdfglobal as $global)
         <td style="height:8px; font-size: 8px"><center>{{$global->nombre_turno}}</td>
    @endforeach    
  </tr>

 </table>

  <table class="info-table" style="margin-top: 15px;" WIDTH="100%">
 <tr>
  <td rowspan="6" style="font-size: 7px"><strong><center>N° de orden</strong></td>
  <td rowspan="6" style="font-size: 7px"><strong><center>N° de Matrícula</strong></td>
  <td rowspan="6" style="font-size: 7px" width="200px" ><CENter><strong>APELLIDOS Y NOMBRES</strong></CENter></td>
  <td colspan="{{($cantidadCursos->total_cursos * 3) + 4 }} style="font-size: 8px""><center><strong>ASIGNATURAS / ÁREAS</strong></center></td>
 </tr>

 <tr>
  @php $contadorparacursos = 1; @endphp

  @foreach ($alumnoscurso as $curso)
    <td colspan="3"><strong><center>{{ $contadorparacursos }}</center></strong></td>
    
  @php $contadorparacursos++; @endphp

  @endforeach


      

  <td rowspan="5" class="verticalText2" style="font-size: 8px"><div><strong>Puntaje del Semestre Académico</strong></div></td>

  <td rowspan="5" class="verticalText2" style="font-size: 8px"><div><strong>Crédito del Semestre Académico</strong></div></td>
  <td rowspan="5" class="verticalText2" style="font-size: 8px"><div><strong>Promedio Ponderado Semestre Académico</strong></div></td>
  <td rowspan="5" ><strong>Observaciones</strong></td>
</tr>


<tr>
  @foreach ($alumnoscurso as $curso)
      <td colspan="3" class="verticalText2" style="font-size: 4.2; height: 80px; " width="50px"><div><center>{{ $curso->nombre_curso }}</center></div></td>
  @endforeach
</tr>
<tr>
  @foreach ($alumnoscurso as $curso)
    <td colspan="3" style="font-size: 7px"><strong><center>Créditos</center></strong></td>
  @endforeach
</tr>

<tr>
  @foreach ($alumnoscurso as $curso)
   <td colspan="3" style="font-size: 7px"><center>{{ $curso->credito }}</center></td>
  @endforeach
</tr>
<tr>
  @foreach ($alumnoscurso as $curso)
   <td style="font-size: 7px"><strong>C</strong></td>
    <td style="font-size: 7px"><strong>CS</strong></td>
    <td style="font-size: 7px"><strong>PTJ</strong></td>  
  @endforeach
</tr>

@php $contador = 1; @endphp
@foreach ($notasAgrupadas as $i => $alumno)
@php
  $puntaje1 = $alumno->cursos->sum(function ($curso) {
      return $curso->total * $curso->credito;
  });
  
  $puntaje2 = $alumno->cursos->sum('credito');

  $puntaje3 = $puntaje2 > 0 ? number_format($puntaje1 / $puntaje2, 2) : '0.00';

 
@endphp
<tr>
  <td style="font-size: 7px"><center>{{ $contador }}</td>
  <td style="font-size: 7px"><center>{{ $alumno->id_alumno }}</td>
  <td style="font-size: 7px">{{ $alumno->apellidos_pater_postulante }} {{ $alumno->apellidos_mater_postulante }}, {{ $alumno->nombres_postulante }}</td>

  @if($alumno->resolucion_licencia)
   
    @foreach ($alumnoscurso as $curso)
      <td style="font-size: 7px"></td>
      <td style="font-size: 7px"></td>
      <td style="font-size: 7px"></td>
    @endforeach

    
    <td style="font-size: 7px"></td>
    <td style="font-size: 7px"></td>
    <td style="font-size: 7px"></td>

  
    <td style="font-size: 5px">
       {{ $alumno->resolucion_licencia }}
    </td>
  @else
   
    @foreach ($alumnoscurso as $curso)
      @php
        $notaCurso = $alumno->cursos->firstWhere('nombre_curso', $curso->nombre_curso);
        $nivel = '';
        if ($notaCurso) {
          $nota = $notaCurso->total;
          $nivel = match(true) {
            $nota >= 19 => 'D',
            $nota >= 16 => 'L',
            $nota >= 11 => 'E',
            $nota >= 6  => 'I',
            default     => 'P',
          };
        }
      @endphp
      <td style="font-size: 8px">{{ $nivel }}</td>
      <td style="font-size: 8px">{{ $notaCurso ? $notaCurso->total : '' }}</td>
      <td style="font-size: 8px"><strong>{{ $notaCurso ? $notaCurso->total * $notaCurso->credito : '' }}</strong></td>
    @endforeach

    <td style="font-size: 8px"><center>{{ $puntaje1 }}</td>
    <td style="font-size: 8px"><center>{{ $puntaje2 }}</td>
    <td style="font-size: 8px"><center>{{ $puntaje3 }}</td>
    <td style="font-size: 8px"></td>
  @endif
</tr>

  @php $contador++; @endphp

@endforeach

</table>

<table style="margin-top: 30px; width: 30%; border-collapse: collapse; text-align: left; font-size: 10px;">
  <thead>
    <tr>
      <th style="border: none; padding: 2px 4px; text-align: left; line-height: 1;">Leyenda Calificación del Curso / Módulo.</th>
    </tr>
  </thead>
  <tbody>
    <tr><td style="border: none; padding: 0px 4px; line-height: 1;"><span style="font-size: 12px;">•</span><strong>&nbsp;P : Previo al inicio</strong></td></tr>
    <tr><td style="border: none; padding: 0px 4px; line-height: 1;"><span style="font-size: 12px;">•</span><strong>&nbsp;I : Inicio</strong></td></tr>
    <tr><td style="border: none; padding: 0px 4px; line-height: 1;"><span style="font-size: 12px;">•</span><strong>&nbsp;E : En proceso</strong></td></tr>
    <tr><td style="border: none; padding: 0px 4px; line-height: 1;"><span style="font-size: 12px;">•</span><strong>&nbsp;L : Logrado</strong></td></tr>
    <tr><td style="border: none; padding: 0px 4px; line-height: 1;"><span style="font-size: 12px;">•</span><strong>&nbsp;D : Destacado</strong></td></tr>
  </tbody>
</table>

<table class="info-table3" style="margin-top: 15px;" width="10cm">
    <thead>
        <tr>
            <td width="40px"><strong><center>N° de Área</center></strong></td>
            <td width="140px" ><strong><center>APELLIDOS Y NOMBRES DEL DOCENTE A CARGO</center></strong></td>
            <td width="50px"><strong><center>FIRMA</center></strong></td>
            
        </tr>
    </thead>
    <tbody>
        @php $contador = 1; @endphp
        @foreach($alumnoscurso as $curso)
            <tr>
                <td style="height: 10px;"> <center>&nbsp;{{ $contador++ }}</center></td>
                <td style="height: 10px;">{{ $curso->nombre_docente ?? '—' }}</td>
                <td style="height: 30px;"></td>
            </tr>
        @endforeach
    </tbody>
</table>







<div style="text-align: right; margin-bottom: 10px;">
 <STRong>CERRO DE PASCO, 01 DE AGOSTO DE 2025.</STRong>
</div>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>



<table   WIDTH="100%">
  <tr>
    <td><CENTEr>_____________________</CENTEr></td>
     <td><center>__________________________</center></td>
      <td><center>___________________________</center></td>
  </tr>
  <tr>
    <td style="font-size: 8px;"><CENTEr>TRINIDAD EGUSQUIZA, HUGO</CENTEr></td>
     <td style="font-size: 8px;"><center>MAURICIO ATENCIO, MORFE BENITO</center></td>
     <td style="font-size: 8px;"><center>V°B° DREP</center></td>
  </tr>
  <tr>
    <td style="font-size: 8px;"><center>DIRECTOR(A) GENERAL</center></td>
     <td style="font-size: 8px;"><center>SECRETARIO ACADÉMICO</center></td>
  </tr>

   <tr>
    <td style="font-size: 8px;"><center>Firma, Post Firma y Sello</center></td>
     <td style="font-size: 8px;"><center>Firma, Post Firma y Sello</center></td>
  </tr>

</table>




</body>


</html> 