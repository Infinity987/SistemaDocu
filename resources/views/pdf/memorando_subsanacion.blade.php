<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>{{ $titulo }}</title>
    <style>
      @page{
         margin-top: 70px;
  margin-bottom: 10px; 
  
      }
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .titulo { text-align: center; font-weight: bold; margin-bottom: 20px; }
        .referencia { margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; }
        th { background-color: #f0f0f0; }
        .firma { margin-top: 40px; text-align: right; }
         .sin-borde td {
    border: none;
    padding: 2px;
  }
  #logopri {
            float: right; 
            display: inline-block;
            vertical-align: top;
            width: 100px;
            height: auto;
        }
        #logominedu {
            float: left; 
            display: inline-block;
            vertical-align: top;
            width: 100px;
            height: auto;
        }
    </style>
</head>
<body>

<img id="logopri" src="{{public_path('logos/logo.png')}}" alt="" > 
    <img id="logominedu" src="{{public_path('logos/minedu.png')}}" alt="" > 

<div><H1 style="font-size: 10PX; margin:0; padding:0;"> <CEnter>ESCUELA DE EDUCACION SUPERIOR PEDAGÓGICA PÚBLICA</CEnter></H1>
      <H1 style="font-size: 10PX; margin:0; padding:0;"><center> "Gamaniel Blanco Murillo"</center></H1>
      <h1 style="font-size: 10PX; margin:0; padding:0;"><center>Resolucion de Creación: R.M.0205-81-ED: 18-03-1981</center></h1>
      <h1 style="font-size: 10PX; margin:0; padding:0;"><center>CERRO DE PASCO - PERÚ</center></h1>
</div>
<br>


    <div class="titulo"><p style="text-decoration: underline ">{{ $titulo }}</p></div>

    <table   class="sin-borde">
      <tr>
        <td>A</td><td>:</td><td>Manuel, VELAZQUEZ ATENCIO</td>
      </tr>
       <tr>
        <td></td><td>:</td><td>jefe de Unidad Administrativa del IESPP "GMB"</td>
      </tr>
       <tr>
        <td>ASUNTO</td><td>:</td><td>evaluación Del Curso de Subsanación</td>
      </tr>
       <tr>
        <td>Ref</td><td>:</td><td>{{ $referencia }}</td>
      </tr>
        <tr>
        <td>Fecha</td><td>:</td><td>Cerro de Pasco,</td>
      </tr>
    </table>

   

    <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        Sírvase dar cumplimiento a la resolución de la referencia con el(la) Prof.(a) {{ $docente }},
        por haber cumplido con la evaluación y entrega de actas, registros y firmas correspondientes de la prueba de subsanación
        a Secretaría Académica, del periodo de {{ $periodo }} de los siguientes estudiantes:
    </p>

    <table style="font-size: 7px">
        <thead>
            <tr>
                <th>N°</th>
                <th>Alumno</th>
                <th>Curso</th>
                <th>Ciclo</th>
                <th>Carrera</th>
            </tr>
        </thead>
        <tbody>
            @foreach($alumnos as $i => $a)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $a->nombre }}</td>
                    <td>{{ $a->curso }}</td>
                    <td>{{ $a->ciclo }}</td>
                    <td>{{ $a->carrera }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div><P><center>Atentamente,</center></P>    </div>
</body>
</html>