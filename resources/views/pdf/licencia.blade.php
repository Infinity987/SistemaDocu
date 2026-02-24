<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Licencia PDF</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .titulo { text-align: center; font-weight: bold; margin-bottom: 20px; }
        .seccion { margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="titulo">RESOLUCIÓN DIRECTORAL NRO. {{ $resolucion }}</div>
    <div class="seccion">Institución: {{ $institucion }}</div>
    <div class="seccion">Estudiante: {{ $postulante->apellidos_pater_postulante }} {{ $postulante->apellidos_mater_postulante }}, {{ $postulante->nombres_postulante }}</div>
    <div class="seccion">DNI: {{ $postulante->idpostulante }}</div>
    <div class="seccion">Motivo de licencia: {{ $licencia->motivo_licencia }}</div>
    <div class="seccion">Inicio: {{ $semestre_inicio }}</div>
    <div class="seccion">Fin: {{ $semestre_fin }}</div>
    <div class="seccion">Semestre de reincorporación: {{ $semestre_reincorporacion }}</div>
    <div class="seccion">Cantidad de semestres: {{ $licencia->cantidad_semestres }}</div>
    <br>
    <div class="seccion">El estudiante tiene derecho a reservar matrícula por causas justificadas según el Decreto Supremo N° 010-2017-MINEDU.</div>
    <br>
    <div class="seccion">Firma: ____________________________</div>
</body>
</html>