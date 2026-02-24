<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        @page {
            margin-left: 0.15cm;
            margin-right: 0.15cm;
            margin-top: 0.15cm;    
        margin-bottom: 0.15cm; 
        }
    </style>
    
    <style>
        body {
        font-family: Arial, sans-serif;
        margin: 0; /* Elimina los márgenes externos */
        padding: 0; /* Elimina cualquier espacio interno */
        background-color: #fdd1a3; /* Fondo de color */
        min-height: 100vh; /* Asegura que cubre toda la altura de la hoja */
    }
    .container {
        max-width: 600px;
        margin: auto;
        padding: 20px;
        border: 1px solid #ccc;
        border-radius: 10px;
        background: white; /* Opcional: añade fondo blanco al contenido para contraste */
    }
        h1 {
            text-align: center;
            margin-bottom: 20px;
        }
        .field {
            margin: 10px 0;
        }
        .label {
            font-weight: bold;
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
        #imagenpostu {
            border: 1px solid; color: black;
            position: absolute;

            top: 80px; 
            left: 600px;
            /* width: 90px; */

            max-width: 100px; /* Ancho máximo */
            max-height: 100px; /* Altura máxima */
            width: auto; /* Mantiene la proporción */

            height: auto;
        }
        .center-text {
            text-align: center;
        }
    </style>
    
    <title>Ficha de Inscripción</title>
</head>
<body>

    <img id="logopri" src="{{public_path('logos/logo.png')}}" alt="" > 
    <img id="logominedu" src="{{public_path('logos/minedu.png')}}" alt="" > 
   <center> <h5 style="margin: 0;">ESCUELA DE EDUCACION SUPERIOR PEDAGOGICA PUBLICA </h4></center>
   <center> <h5 style="margin: 0;">GAMANIEL BLANCO MURILLO" </h4></center>
   <center> <h5  style="margin: 0;">T.f (063) 421558 - SAN JUAN - CERRO DE PASCO </h4></center>
   <center> <h5 style="margin: 0;">R.M N°283.2020-MINEU 2007-20</h4></center>
    <div></div>
    <h6 style="margin: 0;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Institucion licenciada de excelencia en la formacion inicial docente y continua</h4>
<br>

<h6 style="margin-left: 2cm;" >I. DATOS PERSONALES</h6>
    @foreach ($fichainscripcion as $item)
        
   
    <TABLE BORDER style="margin-left: 2cm;" >
        <TR>
            <TD style='font-size: 10px; font-weight: bold; font-weight: bold; ' width="150px" ROWSPAN=2 ><center>1. DOCUMENTO DE IDENTIDAD</center></TD>
            <TD style='font-size: 10px; font-weight: bold;' width="130px"><center>{{$item->dni}}</center></TD> 
            <TD style='font-size: 10px; font-weight: bold;' width="70px" rowspan="2"><center>2. EDAD</center></TD>
            <TD style='font-size: 10px; font-weight: bold;' width="70px" rowspan="2"><center>{{$item->edad_postulante}}</center></TD>
        </TR>
        <TR>
            <TD style='font-size: 10px; font-weight: bold;' ><center>N° DE DNI</center></TD> 
            
        </TR>
    </TABLE  >
        
    @php
    $imagen = 'fotos_postulantes/default-user.jpg';
    if (!empty($item->foto_postulante)) {
        $rutaImagen = public_path($item->foto_postulante);
        if (file_exists($rutaImagen)) {
            $imagen = $item->foto_postulante;
        }
    }
@endphp

<img id="imagenpostu" src="{{ public_path($imagen) }}" alt="Foto del postulante">  
      <br>
      <br>
      
    <TABLE BORDER style="margin-left: 2cm;" >
        <TR>
            <TD style='font-size: 10px; font-weight: bold;' width="316px" ><center>{{$item->apellidos_pater_postulante}}<</center></TD>
            <TD style='font-size: 10px; font-weight: bold;' width="316px"><center>{{$item->apellidos_mater_postulante}}<</center></TD> 
           
        </TR>
        <TR>
            <TD style='font-size: 10px; font-weight: bold;' ><center>3. APELLIDO PATERNO</center></TD>
            <TD style='font-size: 10px; font-weight: bold;' ><center>4. APELLIDO MATERNO</center></TD>  
            
        </TR>
    </TABLE> 
    <br> 
    <TABLE BORDER style="margin-left: 2cm;"" >
        <TR>
            <TD style='font-size: 10px; font-weight: bold;' width="638px" ><center>{{$item->nombres_postulante}}<</center></TD>          
        </TR>
        <TR>           
            <TD style='font-size: 10px; font-weight: bold;' ><center>5. NOMBRES</center></TD>              
        </TR>
    </TABLE> 
    <br> 
    <TABLE BORDER  style="margin-left: 2cm;">
        <TR>
            <TD style='font-size: 10px; font-weight: bold;' width="90px" ROWSPAN=2 ><center>6. LUGAR Y FECHA DE NACIMIENTO</center></TD>
            <TD style='font-size: 10px; font-weight: bold;' width="90px"><center>{{$item->fecha_de_nacimiento_postu}}</center></TD>
            <TD style='font-size: 10px; font-weight: bold;' width="144px"><center>{{$item->distrito_nacimiento}}</center></TD>
            <TD style='font-size: 10px; font-weight: bold;'  width="144px"><center>{{$item->provincia_nacimiento}}</center></TD>
            <TD style='font-size: 10px; font-weight: bold;' width="144px"><center>{{$item->departamento_nacimiento}}</center></TD>
            
        </TR>
        <TR>
            <TD style='font-size: 10px; font-weight: bold;' ><center>FECHA</center></TD> 
            <TD style='font-size: 10px; font-weight: bold;' ><center>DISTRITO</center></TD>
            <TD style='font-size: 10px; font-weight: bold;' ><center>PROVINCIA</center></TD>
            <TD style='font-size: 10px; font-weight: bold;' ><center>DEPARTAMENTO</center></TD>
            
        </TR>
    </TABLE> 
    <br> 
    <TABLE BORDER style="margin-left: 2cm;" >
        <TR>
            <TD style='font-size: 10px; font-weight: bold;' width="155px"  ><center>7. LENGUA MATERNA</center></TD>
            <TD style='font-size: 10px; font-weight: bold;' width="155px" ><center>{{$item->nombre}}</center></TD>
            <TD style='font-size: 10px; font-weight: bold;' width="155px"  ><center>8. LENGUA SECUNDARIA</center></TD>
            <TD style='font-size: 10px; font-weight: bold;' width="155px"><center>{{$item->lengua_secun}}</center></TD>           
        </TR>
        <TR>           
        </TR>
    </TABLE>
    <br> 
    <TABLE BORDER  style="margin-left: 2cm;">
        <TR>
            <TD style='font-size: 10px; font-weight: bold;' width="100px" ROWSPAN=3 ><center>9. DIRECCION DOMICILIARIA</center></TD>
            <TD style='font-size: 10px; font-weight: bold;' width="174px"  ><center>{{$item->distrito_domicilio}}</center></TD> 
            <TD style='font-size: 10px; font-weight: bold;' width="174px" ><center>{{$item->provincia_domicilio}}</center></TD>
            <TD style='font-size: 10px; font-weight: bold;' width="170px" ><center>{{$item->departamento_domicilio}}</center></TD>
        </TR>
        <TR>
                        <TD style='font-size: 10px; font-weight: bold;' ><center>DISTRITO:</center></TD>
                        <TD style='font-size: 10px; font-weight: bold;' ><center>PROVINCIA:</center></TD>  
                        <TD style='font-size: 10px; font-weight: bold;' ><center>DEPARTAMENTO:</center></TD>               
        </TR>
        <TR>
            <TD style='font-size: 10px; font-weight: bold;' ><center>DIRECCION DOMICILIARIA:</center></TD>
            <TD style='font-size: 10px; font-weight: bold;' colspan="2" width="170px"><center>{{$item->direccion_domicilio}}</center></TD>  
                    
        </TR>
    </TABLE> 
    <br> 
    <TABLE BORDER style="margin-left: 2cm;">
        <TR>
            <TD  style='font-size: 10px; font-weight: bold;' width="80px" ROWSPAN=2 ><center>10. TELEFONO Y CORREO ELECTRONICO</center></TD>
            <<TD style='font-size: 10px; font-weight: bold;' width="102px"  ><center></center></TD> 
            <TD  style='font-size: 10px; font-weight: bold;' width="218px" ><center>{{$item->celular}}</center></TD>
            <TD style='font-size: 10px; font-weight: bold;' width="218px" ><center>{{$item->correo}}</center></TD>
        </TR>
        <TR>
                        <TD style='font-size: 10px; font-weight: bold;' ><center>FIJO:</center></TD>
                        <TD style='font-size: 10px; font-weight: bold;' ><center>CELULAR:</center></TD>  
                        <TD style='font-size: 10px; font-weight: bold;' ><center>EMAIL:</center></TD>  
            
        </TR>
    </TABLE> 
  
    <h6 style="margin-left: 2cm;">II. DATOS DE POSTULACION</h6>

    <TABLE BORDER  style="margin-left: 2cm;">
        <TR>
            <TD style='font-size: 10px; font-weight: bold;'  width="317px"  ><center>11. ESPECIALIDAD/PROGRAMA DE ESTUDIOS</center></TD>
            <TD style='font-size: 10px; font-weight: bold;' width="317px" ><center>{{$item->nombre_de_carrera}}</center></TD>
                     
        </TR>
        <TR>           
        </TR>
    </TABLE>
    
    <h6 style="margin-left: 2cm;">III. ESTUDIOS DE EDUCACION SECUNDARIA:</h6>

    <TABLE BORDER style="margin-left: 2cm;" >
        <TR>
            <TD style='font-size: 10px; font-weight: bold;' width="80px" ROWSPAN=7 ><center>12. INSTITUCION EDUCATIVA DE PROCEDENCIA</center></TD>
            <TD style='font-size: 10px; font-weight: bold;' width="80px"><center>NOMBRE:</center></TD> 
            <TD style='font-size: 10px; font-weight: bold;' colspan="4" width="230px"><center> {{$item->colegio}}</center></TD>   
                  
        </TR>
        <TR>
            <TD style='font-size: 10px; font-weight: bold;' width="80px" ><center>CODIGO MODULAR:</center></TD>
            <TD style='font-size: 10px; font-weight: bold;' width="185PX"><center>DIRECCION:</center></TD>
            <TD style='font-size: 10px; font-weight: bold;' colspan="3" width="200PX"><center>AÑO DE PROMOCION:</center></TD> 
        </TR>

        <TR>
            <TD style='font-size: 10px; font-weight: bold;' ><center> {{$item->codigo_modular}}</center></TD>
            <TD style='font-size: 10px; font-weight: bold;' ><center> {{$item->direccion_colegio}}</center></TD>
            <TD style='font-size: 10px; font-weight: bold;' colspan="3" ><center> {{$item->año_de_termino_colegio}}</center></TD> 
        </TR>

        <TR>
            <TD style='font-size: 10px; font-weight: bold;' ROWSPAN=2 width="50px" ><center>LUGAR</center></TD>
            <TD style='font-size: 10px; font-weight: bold;' width="80px" ><center>{{$item->distrito_colegio}}</center></TD>
            <TD style='font-size: 10px; font-weight: bold;' width="70PX"><center>{{$item->provincia_colegio}}</center></TD>
            <TD style='font-size: 10px; font-weight: bold;'  colspan="2" width="50PX"><center>{{$item->departamento_colegio}}</center></TD> 
        </TR> 

        <TR>
           
            <TD style='font-size: 10px; font-weight: bold;'><center>DISTRITO:</center></TD>
            <TD style='font-size: 10px; font-weight: bold;'><center>PROVINCIA:</center></TD>  
            <TD style='font-size: 10px; font-weight: bold;'  colspan="2"><center>DEPARTAMENTO:</center></TD>    
        </TR>
       
       
        
        @if ($item->idtipo_colegio = 1)
        <TR>
            <TD style='font-size: 10px; font-weight: bold;' ROWSPAN=2 width="50px" ><center>GESTION</center></TD>
            <TD style='font-size: 10px; font-weight: bold;' width="80px" ><center>X</center></TD>
            <TD style='font-size: 10px; font-weight: bold;' width="50PX"><center></center></TD>
            <TD  style='font-size: 10px; font-weight: bold;'width="10PX"><center></center></TD>
            <TD  style='font-size: 10px; font-weight: bold;'width="10PX"><center></center></TD>  
        </TR>   
        @elseif ($item->idtipo_colegio = 2)
            
        <TR>
            <TD style='font-size: 10px; font-weight: bold;' ROWSPAN=2 width="50px" ><center>GESTION</center></TD>
            <TD style='font-size: 10px; font-weight: bold;' width="80px" ><center></center></TD>
            <TD style='font-size: 10px; font-weight: bold;' width="50PX"><center>x</center></TD>
            <TD style='font-size: 10px; font-weight: bold;' width="800PX"><center></center></TD>
            <TD  style='font-size: 10px; font-weight: bold;'width="10PX"><center></center></TD>  
        </TR>
            
        @endif
        

        <TR>
           
            <TD style='font-size: 10px; font-weight: bold;'><center>ESTATAL</center></TD>
            <TD style='font-size: 10px; font-weight: bold;' width="120PX"><center>PARTICULAR</center></TD>  
            <TD style='font-size: 10px; font-weight: bold;' width="60X"><center>PARROQUIAL</center></TD>
            <TD style='font-size: 10px; font-weight: bold;' width="88PX"><center>OTROS</center></TD>     
        </TR>


    </TABLE>   
    @endforeach

    <h6 style="text-align:right;">
        Cerro de Pasco, 
        <?php 
            setlocale(LC_TIME, 'es_ES.UTF-8'); // Configurar el idioma en español
            echo strftime('%d de %B del %Y'); // Formato: "12 de marzo del 2025"
        ?>
    </h6>
    

    
    <br>
    <br>
    
    


    @if ($item->edad_postulante<18)
    <table>
        <tr>
            <th style='font-size: 10px; font-weight: bold;' width="0PX">_________________________________________</th>
            <th style='font-size: 10px; font-weight: bold;' width="400PX">_________________________________________</th>
        </tr>
        <tr>
            <th style='font-size: 10px; font-weight: bold;' width="0PX">FIRMA DEL (LA) POSTULANTE</th>
            <th style='font-size: 10px; font-weight: bold;' width="600PX">FIRMA DEL APODERADO</th>
        </tr>
        <tr>
            <th style='font-size: 10px; font-weight: bold;' width="0PX"></th>
            <TH style='font-size: 10px; font-weight: bold;' >(SI EL POSTULANTE ES MENOR DE EDAD)</TH>
        </tr>
            <tr>
                <th style='font-size: 10px; font-weight: bold;' width="0PX"></th>
            <TH style='font-size: 10px; font-weight: bold;'>AP. Y NOMBRES:........................................</TH>
        </tr>
    </table>
    @else
    <table style="margin-left: 2cm;">
        <tr>
            <th style='font-size: 10px; font-weight: bold;' width="650PX">_________________________________________</th>
            
        </tr>
        <tr>
            <th style='font-size: 10px; font-weight: bold;' width="650PX">FIRMA DEL (LA) POSTULANTE</th>
           
        </tr>
        <tr>
            <th style='font-size: 10px; font-weight: bold;' width="650PX"></th>
          
        </tr>
            <tr>
                <th style='font-size: 10px; font-weight: bold;' width="650PX"></th>
            
        </tr>
    </table>
        
    @endif
   
</body>
</html>
