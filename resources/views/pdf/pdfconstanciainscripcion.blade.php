<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Constancia de Inscripción</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .container {
            width: 100%;
            margin: 10px auto;
            border: 1px solid #000;
            padding: 10px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .photo {
            float: right;
            border: 1px solid #000;
            width: 160px;
            height: 180px;
            text-align: center;
            line-height: 10px;
            margin-top: 30px;
        }
        .info, .instructions, .signatures {
            margin-top: 20px;
        }
        .signatures {
            display: flex;
            justify-content: space-between;
        }
        .signatures div {
            text-align: center;
            margin-top: 20px;
        }
        #imagenpostu {
            border: 1px solid; color: black;            
            width: 160px;
            height: 180px;
        }
        #imagenfirma {
            
            position: absolute;
            top: 850px; 
            left: 400px;
            width: 190px;
            height: auto;
        }
        .titulo {
    margin: 0;
    text-align: left;
}
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            @foreach ($fichainscripcion as $item)
                
            <h4 class="titulo"> SISTEMA DE INFORMACION ACADEMICO DE GMB</h4>
            <h2 class="titulo">{{$item->nombre_proceso}}</h2>
            @endforeach
            <h3 class="titulo">EESP Público "GAMANIEL BLANCO MURILLO"</h3>
            <h3 class="titulo">Constancia de Inscripción</h3>
            @foreach ($fichainscripcion as $item)
            
            @php
                 $formato= str_pad($item->idinscripcion,4,'0', STR_PAD_LEFT);
            @endphp
           

            @if ($item->idcarreras == 1)
            
             <h3 class="titulo">N°{{ date('Y') - 2000 }}INI{{$formato}}</h3>
            @elseif($item->idcarreras == 2)
            <h3 class="titulo">N°{{ date('Y') - 2000 }}PRIM{{$formato}}</h3>
            @elseif($item->idcarreras == 3)
            <h3 class="titulo">N°{{ date('Y') - 2000 }}EPIB{{$formato}}</h3>
            @elseif($item->idcarreras == 4)
            <h3 class="titulo">N°{{ date('Y') - 2000 }}EF{{$formato}}</h3>
            @elseif($item->idcarreras == 5)
            <h3 class="titulo">N°{{ date('Y') - 2000 }}CCSS{{$formato}}</h3>
            @elseif($item->idcarreras == 6)
            <h3 class="titulo">N°{{ date('Y') - 2000 }}MAT{{$formato}}</h3>
            @endif
            
            
            <br>
            <p class="titulo">Condición del Postulante: <strong> {{$item->nombre_modalidad}} </strong></p>
            @endforeach
        </div>
        <div class="photo">
            @foreach ($fichainscripcion as $item)
               @php
    $imagen = 'fotos_postulantes/default-user.jpg';
    if (!empty($item->foto_postulante)) {
        $rutaImagen = public_path($item->foto_postulante);
        if (file_exists($rutaImagen)) {
            $imagen = $item->foto_postulante;
        }
    }
@endphp

                <img id="imagenpostu" src="{{public_path($imagen)}}" alt="">

                <!-- <img id="imagenpostu" src="{{ $item->foto_postulante ? public_path($item->foto_postulante) : public_path('fotos_postulantes/default-user.jpg')}}" alt="" > -->
                <p><strong>Código: </strong> {{$item->dni}} </p>
            @endforeach           

       
        </div>
        <div class="info">

            @foreach ($fichainscripcion as $item)
       
            
            <p style="margin: 0;"><strong>Apellido Paterno:</strong> {{$item->apellidos_pater_postulante}}</p>
            <p style="margin: 0;"><strong>Apellido Materno:</strong> {{$item->apellidos_mater_postulante}}</p>
            <p style="margin: 0;"><strong>Nombres:</strong> {{$item->nombres_postulante}}</p>
            <p style="margin: 0;"><strong>Especialidad:</strong> {{$item->nombre_de_carrera}}</p>
            <p style="margin: 0;"><strong>Documento de Identidad:</strong> DNI {{$item->dni}}</p>
            @if($item->genero_postulante == 1)
                <p style="margin: 0;"><strong>Sexo:</strong> Mujer</p>
            @elseif($item->genero_postulante == 2)
                <p style="margin: 0;"><strong>Sexo:</strong> Hombre</p>
            @endif
            @if ($item->discapacidad == 0)
            <p style="margin: 0;"><strong>Discapacidad:</strong> NO</p>  
            <p style="margin: 0;"><strong>con Discapacidad:</strong> NO</p>              
            @else
            <p style="margin: 0;"><strong>Discapacidad:</strong> SI</p>            
            <p style="margin: 0;"><strong>Con Discapacidad:</strong> {{$item->tipo_discapacidad}}</p>
            @endif          
            <p style="margin: 0;"><strong>Domicilio:</strong> {{$item->direccion_domicilio}}</p>
            <p style="margin: 0;"><strong>Teléfono:</strong> {{$item->celular}}</p>
            <p style="margin: 0;"><strong>Correo Electrónico:</strong>{{$item->correo}}</p>
            <p style="margin: 0;"><strong>Fecha y Lugar de Nacimiento:</strong>{{$item->fecha_de_nacimiento_postu}} {{$item->departamento_nacimiento}}  {{$item->provincia_nacimiento}} {{$item->distrito_nacimiento}}</p>
            <p style="margin: 0;"><strong>Fecha de Inscripcion:</strong> {{$item->Fecha_inscripcion}}</p>

            @endforeach 
        </div>
        <div class="instructions">
            <p><strong>Indicaciones:</strong></p>
            <ul>
                <li>Presentarse en la sede de aplicación con 1 hora de anticipación a la hora establecida.</li>
                <li>Portar su documento de identificación (DNI) al ingresar al local.</li>
                <li>Presentar esta constancia(con foto, sello institucional y firmas del postulante y director(a) de la Institución de Educación Superior.</li>
            </ul>
        </div>

        <div class="instructions">
            <p><strong>Sobre los resultados:</strong></p>
            <ul>
                <li>Finalizado el proceso de admisión puedes consultar el resultado en la siguiente dirección.</li>
                <li>https://sia.pedagogicos.pe/site/ConsultaAdmision.</li>
              
            </ul>
        </div>

        <div class="instructions">
            
            <ul>
                <li>ESTE ES EL ÚNICO DOCUMENTO QUE LO ACREDITA COMO POSTULANTE CORRECTAMENTE REGISTRADO EN EL SISTEMA DE INFORMACIÓN ACADEMIA DEL MINEDU Y PERMITE SU ACCESO AL LOCAL PARA RENDIR LAS PRUEBAS</li>
               
              
            </ul>
        </div>
    <br>
    <br>
    <br>
    <br>
    <br>
    <img id="imagenfirma" src="{{public_path('firmas/firmadirector.png')}}" alt="" >  
        <div style="text-align: center;">
            <table>
                <tr>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;__________________________</td>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;__________________________</td>
                </tr>
                <tr>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Firma del Postulante</td>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Firma del Director(a)</td>
                </tr>
            </table>
        </div>

       
    </div>
</body>
</html>