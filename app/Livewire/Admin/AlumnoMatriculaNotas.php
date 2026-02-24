<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class AlumnoMatriculaNotas extends Component
{
    public string $dni = '';
    public $matriculas;
    public $alumno;

    public function buscar()
    {
        $this->matriculas = DB::connection('mysql_segunda')->table('matricula AS m')
                ->join('semestre_academico AS sa', 'm.idsemestre_academico', '=', 'sa.idsemestre_academico')
                ->join('ciclos AS ci', 'm.ciclo_matricula', '=', 'ci.idciclos')
                ->where('id_alumno','=', $this->dni)->orderBy('m.idmatricula', 'desc')->get();
        $this->alumno = DB::table('postulante')->select('apellidos_pater_postulante', 'apellidos_mater_postulante', 'nombres_postulante')
                ->where('idpostulante' , $this->dni)
                ->first();
    }

    public function irPdf($id_alumno, $idmatricula)
    {
        // dd($id_alumno);
        $url = route('pdf.pdfcalifiCurso', ['id_alumno' => $id_alumno, 'idmatricula' => $idmatricula]);
        $this->dispatch('abrirPdf', url: $url);
    }
    public function render()
    {
        return view('livewire.Admin.alumno-matricula-notas');
    }
}
