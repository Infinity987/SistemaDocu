<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>FICHA DE MATRÍCULA</title>

  <style>

    @page {
  margin-top: 20px;
  margin-bottom: 20px; 
   
}   
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
  h2 {
    text-align: center;
    font-size: 12px;
    font-weight: normal;
    margin-bottom: 18px;
  }
  table.bordeado {
    border-collapse: collapse;
    width: 100%;
    margin-bottom: 18px;
  }
  table.bordeado th,
  table.bordeado td {
    border: 1px solid #000;
    padding: 4px 6px;
    text-align: center;
    vertical-align: middle;
  }
  .seccion-titulo {
    font-weight: bold;
    font-size: 11px;
    text-transform: uppercase;
    margin-top: 22px;
    margin-bottom: 6px;
    border-bottom: 1px solid #000;
    padding-bottom: 2px;
  }
</style>

 
<div style="display: flex; align-items: center; margin-bottom: 10px;">
  <div style="width: 120px;">
    <img src="{{ public_path('logos/logo.png') }}" alt="Logo" style="width: 100px;">
  </div>
  <div style="flex-grow: 1; padding-left: 15px;">
    <h1 style="margin: 0; font-size: 16px; line-height: 1;">Ficha de Matrícula</h1>
  </div>
</div>


  <br>
  <table class="bordeado">
    <tr>
        <td rowspan="2" colspan="2" style="background-color: #a7a7a79b;"><strong>Nombre de la Institución</strong></td>
        <td rowspan="2" colspan="3" >GAMANIEL BLANCO MURILLO</td>
        <td style="background-color: #a7a7a79b;"><strong>DRE</strong></td>
         <td colspan="2">PASCO</td>
    </tr>
    <tr>
       <td style="background-color: #a7a7a79b;"><strong>UGEL</strong></td>
         <td colspan="2">DREP-PASCO</td>
    </tr>
    <tr>
        <td style="background-color: #a7a7a79b;"><strong>Código Modular</strong></td>
        <td style="background-color: #a7a7a79b;"><strong>Denominación</strong></td>
        <td style="background-color: #a7a7a79b;"><strong>Gestión</strong></td>
        <td style="background-color: #a7a7a79b;"><strong>D.S /R.M. de Creación y R.D. de Revalidación</strong></td>
        <td style="background-color: #a7a7a79b;"><strong>Dirección</strong></td>
        <td colspan="3">AV. LOS PROCERES N° 872</td>
    </tr>
    <tr>
        <td>0575779</td>
         <td>EESP</td>
          <td>Público</td>
           <td>R.M. N° 0205 - 1981</td>
            <td style="background-color: #a7a7a79b;"><strong>Provincia</strong></td>
             <td>PASCO</td>
              <td style="background-color: #a7a7a79b;"><strong>Distrito</strong></td>
               <td>YANACANCHA</td>
    </tr>
  </table>
  <br>

   <table class="bordeado">
    <tr>
        <td style="background-color: #a7a7a79b;"><strong>Programa de estudios/ Turno</strong></td>
        <td> {{ $carrera->nombre_malla_curricular ?? '—' }}</td>
        <td style="border: none;"></td>
        <td style="background-color: #a7a7a79b;"><strong>Periodo Académico</strong></td>
        <td>{{ $semestre->año ?? '—' }} - {{ $semestre->periodo ?? '—' }}</td>
    </tr>

    <tr>
        <td style="background-color: #a7a7a79b;"><strong>Resolucion de Autorizacion</strong></td>
        <td>RD</td>
        <td style="border: none;"></td>
        <td style="background-color: #a7a7a79b;"><strong>Ciclo - Sección</strong></td>
        <td>{{ $ciclo->nombre_ciclo ?? '—' }} - {{ $seccion->nom_seccion ?? '—' }}</td>

    </tr>

     <tr>
        <td style="border: none;"></td>
        <td style="border: none;"></td>
        <td style="border: none;"></td>
        <td style="border: none;"></td>
        <td style="border: none;"></td>
    </tr>
     <tr>
        <td style="background-color: #a7a7a79b;"><strong>Nombre y Apellidos</strong></td>
        <td>{{ $alumno->apellidos_pater_postulante ?? '' }}
      {{ $alumno->apellidos_mater_postulante ?? '' }},
      {{ $alumno->nombres_postulante ?? '' }}
</td>
        <td style="border: none;"></td>
        <td style="background-color: #a7a7a79b;"><strong>Código</strong></td>
        <td>{{ $dni }}</td>
    </tr>   
  </table>
<br>
  <table class="bordeado">
    <tr>
        <td style="background-color: #a7a7a79b;">N°</td>
        <td style="background-color: #a7a7a79b;">MATRICULA REGULAR CURSOS</td>
        <td style="background-color: #a7a7a79b;">HORAS</td>
        <td style="background-color: #a7a7a79b;">CRÉDITOS</td>       
    </tr>

    @php
  $totalCreditosRegulares = $cursosRegulares->sum('credito');
  $totalHorasRegulares = $cursosRegulares->sum('horas');
@endphp
 @foreach($cursosRegulares as $curso)

<tr>
  <td>{{ $loop->iteration }}</td>
  <td style="font-size: 9px;  text-align: left; ">{{ $curso->nombre_curso }}</td>
  <td>{{ $curso->horas ?? '—' }}</td>

  <td>{{ $curso->credito }}</td>
</tr>
@endforeach>
   <tr>
  <td></td>
  <td><strong>TOTAL</strong></td>
  <td><strong>{{ $totalHorasRegulares }}</strong></td>
  <td><strong>{{ $totalCreditosRegulares }}</strong></td>
</tr>
  </table>
<br>
<br>
  <table class="bordeado">
    <tr>
        <td style="background-color: #a7a7a79b;">N°</td>
        <td style="background-color: #a7a7a79b;">MATRICULA SUBSANACION CURSOS</td>
        <td style="background-color: #a7a7a79b;">HORAS</td>
        <td style="background-color: #a7a7a79b;">CRÉDITOS</td>       
    </tr>

    @php
  $totalCreditosSubsanacion = $cursosSubsanacion->sum('credito');
  $totalHorasSubsanacion = $cursosSubsanacion->sum('horas');
@endphp
    
@foreach($cursosSubsanacion as $curso)

     <tr>
        <td>{{ $loop->iteration }}</td>
  <td>{{ $curso->nombre_curso }}</td>
  <td>{{ $curso->horas ?? '—' }}</td>

  <td>{{ $curso->credito }}</td>
    </tr>
    @endforeach

   <tr>
  <td></td>
  <td><strong>TOTAL</strong></td>
  <td><strong>{{ $totalHorasSubsanacion }}</strong></td>
  <td><strong>{{ $totalCreditosSubsanacion }}</strong></td>
</tr>
  </table>
<br>
<br><br>
<br>

<table width="100%" cellspacing="0" cellpadding="0" style="border-collapse: collapse;">
  <tr>
    <td><center>________________________</center></td>
    <td><center>_____________________________</center></td>
    <td><center>________________________________</center></td>
  </tr>
  <tr>
    <td style="font-size: 8px; padding: 0;"><center>TRINIDAD EGUSQUIZA, HUGO</center></td>
    <td style="font-size: 8px; padding: 0;"><center>MAURICIO ATENCIO, MORFE BENITO</center></td>
    <td style="font-size: 8px; padding: 0;"><center>{{ $alumno->apellidos_pater_postulante ?? '' }} {{ $alumno->apellidos_mater_postulante ?? '' }}, {{ $alumno->nombres_postulante ?? '' }}</center></td>
  </tr>
  <tr>
    <td style="font-size: 8px; padding: 0;"><center>DIRECTOR(A) GENERAL</center></td>
    <td style="font-size: 8px; padding: 0;"><center>SECRETARIO(A) ACADÉMICO</center></td>
    <td style="font-size: 8px; padding: 0;"><center>ESTUDIANTE</center></td>
  </tr>
  <tr>
    <td style="font-size: 8px; padding: 0;"><center>Firma, Post Firma y Sello</center></td>
    <td style="font-size: 8px; padding: 0;"><center>Firma, Post Firma y Sello</center></td>
    <td style="font-size: 8px; padding: 0;"></td>
  </tr>
</table>





</body>
</html>